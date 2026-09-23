<?php
/**
 * WordPress-backed behavior coverage for the legacy Tny Signature runtime.
 *
 * @package TNY_SIGNATURE
 */

declare(strict_types = 1);

namespace RAN\TnySignature\Tests\Integration;

use PHPUnit\Framework\TestCase;
use RAN\TnySignature\Activation;
use RAN\TnySignature\Admin;
use RAN\TnySignature\Shortcode;
use RAN\TnySignature\UserProfile;
use WP_User;

final class WordPressBehaviorTest extends TestCase {
	/** @var list<int> */
	private array $users = array();

	/** @var list<int> */
	private array $posts = array();

	protected function tearDown(): void {
		foreach ( $this->posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
		foreach ( $this->users as $user_id ) {
			wp_delete_user( $user_id );
		}
		$this->posts = array();
		$this->users = array();
		delete_option( 'ran-tnysig_options' );
		wp_set_current_user( 0 );
		wp_dequeue_style( 'signature_admin_css' );
		wp_deregister_style( 'signature_admin_css' );
		wp_reset_postdata();
		parent::tearDown();
	}

	public function test_activation_hook_uses_a_wordpress_adapter_and_initializes_defaults(): void {
		$hook = 'activate_' . plugin_basename( TNYSIGNATURE_PLUGIN );

		self::assertSame(
			10,
			has_action( $hook, 'RAN\\TnySignature\\Activation\\activation_hook' )
		);

		delete_option( 'ran-tnysig_options' );
		Activation\activation_hook( false );

		self::assertSame(
			array( 'post_types' => array( 'post', 'page' ) ),
			get_option( 'ran-tnysig_options' )
		);
	}

	public function test_profile_uses_nickname_when_first_and_last_name_are_empty(): void {
		$user_id = $this->create_user(
			array(
				'nickname'   => 'Fallback Nickname',
				'first_name' => '',
				'last_name'  => '',
			)
		);
		wp_set_current_user( $user_id );

		$user = get_userdata( $user_id );
		self::assertInstanceOf( WP_User::class, $user );

		ob_start();
		UserProfile\user_profile_fields( $user );
		$html = (string) ob_get_clean();

		self::assertStringContainsString( 'placeholder="Fallback Nickname"', $html );
	}

	public function test_settings_sanitization_keeps_known_post_types_and_drops_unknown_values(): void {
		self::assertSame(
			array( 'post_types' => array( 'post', 'page' ) ),
			Admin\settings_sanitize(
				array( 'post_types' => array( 'post', 'not-a-post-type', 'page' ) )
			)
		);
	}

	public function test_profile_asset_loader_registers_and_enqueues_the_admin_styles(): void {
		$created_user_id = $this->create_user();
		wp_set_current_user( $created_user_id );
		global $user_id;
		$user_id = $created_user_id;

		self::assertTrue( Admin\load_custom_css( 'profile.php' ) );
		self::assertTrue( wp_style_is( 'signature_admin_css', 'registered' ) );
		self::assertTrue( wp_style_is( 'signature_admin_css', 'enqueued' ) );
	}

	public function test_shortcode_renders_current_post_author_and_farewell(): void {
		$user_id = $this->create_user(
			array(
				'first_name' => 'Ada',
				'last_name'  => 'Lovelace',
			)
		);
		update_user_meta( $user_id, 'ran-tnysig_name', 'Ada Lovelace' );

		$post_id       = wp_insert_post(
			array(
				'post_author'  => $user_id,
				'post_status'  => 'publish',
				'post_title'   => 'Signature integration fixture',
				'post_content' => '[signature]Regards[/signature]',
			)
		);
		self::assertIsInt( $post_id );
		self::assertGreaterThan( 0, $post_id );
		$this->posts[] = $post_id;

		global $post;
		$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		$html = Shortcode\shortcode( array(), 'Regards' );

		self::assertStringContainsString( 'Regards', $html );
		self::assertStringContainsString( 'Ada Lovelace', $html );
	}

	/**
	 * @param array<string, string> $overrides User fields to override.
	 */
	private function create_user( array $overrides = array() ): int {
		$seed = count( $this->users ) + 1;
		$id   = wp_insert_user(
			array_merge(
				array(
					'user_login' => 'tny-integration-' . $seed . '-' . wp_generate_password( 8, false ),
					'user_pass'  => wp_generate_password( 20, true ),
					'user_email' => 'tny-integration-' . $seed . '-' . wp_generate_password( 6, false ) . '@example.test',
					'role'       => 'author',
					'nickname'   => 'Integration Author',
				),
				$overrides
			)
		);
		if ( is_wp_error( $id ) ) {
			self::fail( $id->get_error_message() );
		}
		$this->users[] = $id;
		return $id;
	}
}
