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

/**
 * Verify behavior through a real installed WordPress runtime.
 */
final class WordPressBehaviorTest extends TestCase {
	/** User IDs created by each test.
	 *
	 * @var list<int>
	 */
	private array $users = array();

	/** Post IDs created by each test.
	 *
	 * @var list<int>
	 */
	private array $posts = array();

	/**
	 * Remove WordPress state created by the preceding test.
	 */
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
		unset( $GLOBALS['user_id'] );
		wp_dequeue_style( 'signature_admin_css' );
		wp_deregister_style( 'signature_admin_css' );
		wp_reset_postdata();
		parent::tearDown();
	}

	/** Verify the WordPress activation adapter preserves default initialization. */
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

	/** Verify empty names fall back to the user's nickname. */
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

	/** Verify settings sanitization retains only registered post types. */
	public function test_settings_sanitization_keeps_known_post_types_and_drops_unknown_values(): void {
		self::assertSame(
			array( 'post_types' => array( 'post', 'page' ) ),
			Admin\settings_sanitize(
				array( 'post_types' => array( 'post', 'not-a-post-type', 'page' ) )
			)
		);
	}

	/** Verify settings registration preserves the sanitizer through modern args. */
	public function test_settings_registration_uses_the_expected_sanitizer(): void {
		Admin\register_settings_init();

		try {
			$registered = get_registered_settings();
			self::assertArrayHasKey( 'ran-tnysig_options', $registered );
			self::assertSame(
				'RAN\\TnySignature\\Admin\\settings_sanitize',
				$registered['ran-tnysig_options']['sanitize_callback']
			);
		} finally {
			unregister_setting( 'ran-tnysig_options', 'ran-tnysig_options' );
		}
	}

	/** Verify profile assets register and enqueue for an editor-capable user. */
	public function test_profile_asset_loader_registers_and_enqueues_the_admin_styles(): void {
		$created_user_id = $this->create_user();
		wp_set_current_user( $created_user_id );

		self::assertTrue( Admin\load_custom_css( 'profile.php' ) );
		self::assertTrue( wp_style_is( 'signature_admin_css', 'registered' ) );
		self::assertTrue( wp_style_is( 'signature_admin_css', 'enqueued' ) );
		self::assertSame( array(), wp_styles()->registered['signature_admin_css']->deps );
	}

	/** Verify WordPress hooks dispatch void adapters while direct status APIs remain callable. */
	public function test_wordpress_actions_dispatch_void_adapters_without_breaking_direct_status_calls(): void {
		$user_id = $this->create_user();

		wp_set_current_user( 0 );
		self::assertFalse( Admin\load_custom_css( 'post.php' ) );
		self::assertFalse( Admin\load_custom_profile_js() );
		self::assertFalse( UserProfile\save_additional_user_meta( $user_id ) );

		self::assertSame(
			10,
			has_action( 'admin_enqueue_scripts', 'RAN\\TnySignature\\Admin\\load_custom_css_action' )
		);
		self::assertSame(
			11,
			has_action( 'admin_print_scripts-profile.php', 'RAN\\TnySignature\\Admin\\load_custom_profile_js_action' )
		);
		self::assertSame(
			11,
			has_action( 'admin_print_scripts-user-edit.php', 'RAN\\TnySignature\\Admin\\load_custom_profile_js_action' )
		);
		self::assertSame(
			10,
			has_action( 'personal_options_update', 'RAN\\TnySignature\\UserProfile\\save_additional_user_meta_action' )
		);
		self::assertSame(
			10,
			has_action( 'edit_user_profile_update', 'RAN\\TnySignature\\UserProfile\\save_additional_user_meta_action' )
		);

		wp_set_current_user( $user_id );
		set_current_screen( 'profile' );

		try {
			do_action( 'admin_enqueue_scripts', 'profile.php' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- intentionally dispatch the WordPress core hook.
			self::assertTrue( wp_style_is( 'signature_admin_css', 'enqueued' ) );

			do_action( 'admin_print_scripts-profile.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- intentionally dispatch the WordPress core hook.
			self::assertTrue( wp_script_is( 'signature_user_profile_js', 'enqueued' ) );

			$_POST['ran_tnysig_nonce'] = wp_create_nonce( 'ran_tnysig_user_profile_update' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- the nonce is created and consumed by the integration fixture.
			$_POST['signature_name']   = 'Hook Saved Name'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- consumed through the real hooked saver above.
			do_action( 'personal_options_update', $user_id ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- intentionally dispatch the WordPress core hook.
		} finally {
			unset( $_POST['ran_tnysig_nonce'], $_POST['signature_name'] );
			unset( $GLOBALS['current_screen'] );
		}

		self::assertSame( 'Hook Saved Name', get_user_meta( $user_id, 'ran-tnysig_name', true ) );
	}

	/** Verify notice assets use the authenticated user, not a legacy page global. */
	public function test_editor_notice_asset_lookup_uses_the_current_user(): void {
		$current_user_id = $this->create_user();
		$other_user_id   = $this->create_user();
		wp_set_current_user( $current_user_id );
		update_user_meta( $current_user_id, 'ran-tnysig_editor_notice-dismissed', true );

		$GLOBALS['user_id'] = $other_user_id; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- regression fixture for the legacy page global.

		self::assertTrue( Admin\load_custom_css( 'post.php' ) );
		self::assertTrue( wp_style_is( 'signature_admin_css', 'registered' ) );
		self::assertFalse( wp_style_is( 'signature_admin_css', 'enqueued' ) );
	}

	/** Verify string-backed attachment meta still renders a profile image. */
	public function test_profile_image_meta_accepts_wordpress_string_storage(): void {
		$user_id       = $this->create_user();
		$attachment_id = $this->create_attachment();
		wp_set_current_user( $user_id );
		update_user_meta( $user_id, 'ran-tnysig_image_id', (string) $attachment_id );

		$observed_id = null;
		$filter      = static function ( $downsize, $id ) use ( &$observed_id, $attachment_id ) {
			if ( $attachment_id === (int) $id ) {
				$observed_id = $id;
				return array( 'https://example.test/signature.png', 120, 40, true );
			}
			return $downsize;
		};
		add_filter( 'image_downsize', $filter, 10, 2 );

		$user = get_userdata( $user_id );
		self::assertInstanceOf( WP_User::class, $user );

		try {
			ob_start();
			UserProfile\user_profile_fields( $user );
			$html = (string) ob_get_clean();
		} finally {
			remove_filter( 'image_downsize', $filter, 10 );
		}

		self::assertSame( $attachment_id, $observed_id );
		self::assertStringContainsString( 'signature-image-preview', $html );
		self::assertStringContainsString( 'signature.png', $html );
	}

	/** Verify numeric image dimensions preserve rendered shortcode output. */
	public function test_shortcode_renders_numeric_image_dimensions_as_attributes(): void {
		$user_id       = $this->create_user( array( 'nickname' => 'Image Author' ) );
		$attachment_id = $this->create_attachment();
		update_user_meta( $user_id, 'ran-tnysig_image_id', (string) $attachment_id );

		$post_id = wp_insert_post(
			array(
				'post_author'  => $user_id,
				'post_status'  => 'publish',
				'post_title'   => 'Image signature integration fixture',
				'post_content' => '[signature]Regards[/signature]',
			)
		);
		self::assertIsInt( $post_id );
		self::assertGreaterThan( 0, $post_id );
		$this->posts[] = $post_id;

		$filter = static function ( $downsize, $id ) use ( $attachment_id ) {
			if ( $attachment_id === (int) $id ) {
				return array( 'https://example.test/signature.png', 120, 40, true );
			}
			return $downsize;
		};
		add_filter( 'image_downsize', $filter, 10, 2 );

		global $post;
		$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		try {
			$html = Shortcode\shortcode( array(), 'Regards' );
		} finally {
			remove_filter( 'image_downsize', $filter, 10 );
		}

		self::assertStringContainsString( 'height: 40px; width: 120px;', $html );
	}

	/** Verify shortcode rendering uses the current post author and farewell. */
	public function test_shortcode_renders_current_post_author_and_farewell(): void {
		$user_id = $this->create_user(
			array(
				'first_name' => 'Ada',
				'last_name'  => 'Lovelace',
			)
		);
		update_user_meta( $user_id, 'ran-tnysig_name', 'Ada Lovelace' );

		$post_id = wp_insert_post(
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
	 * Create a minimal attachment post for image-behavior tests.
	 */
	private function create_attachment(): int {
		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/png',
				'post_status'    => 'inherit',
				'post_title'     => 'Signature image fixture',
			)
		);
		self::assertIsInt( $id );
		self::assertGreaterThan( 0, $id );
		$this->posts[] = $id;
		return $id;
	}

	/**
	 * Create an author user for an integration test.
	 *
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
