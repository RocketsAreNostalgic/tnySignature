.0.1.3 - cleanup and add namespacing - DONE

.0.1.4.1 - admin page with image uploader
       permissions, user creates their own signature
       update_user_option or update_user_meta
       add to user profile or to an options page?
                
       http://wordpress.stackexchange.com/questions/252606/user-specific-settings-limited-by-role
           
       https://codex.wordpress.org/Roles_and_Capabilities
       https://codex.wordpress.org/Function_Reference/update_user_option
       https://codex.wordpress.org/Creating_Options_Pages
       https://www.inkthemes.com/code-to-integrate-wordpress-media-uploader-in-plugintheme/

.0.1.4.2 - Image uploader, resize image parameters 

        resize signature image to defined size

.0.1.4.3 - Add admin text field for default sign-off

.0.1.4.4 - Update shortcode
           
        We will need to pass the user ID
        Allong with the default signoff
        Image and params can be fetched from the shortcode function and hardcoded into the css. 
 
.0.1.4.5 - Dismissible notice on posts and to signature image? 
            
.0.1.4.6 - If a custom image has not been defined, provide notice in tinyMCE icon dropdown
        
        Add it as a pop up modal? Or perhaps as a notice in a dropdown?
        https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
        https://codex.wordpress.org/TinyMCE_Custom_Buttons
    
.0.1.4.7 - What the default behavior of the shortcode be when an image is not defined. 


TODO:
Add something that hides the shortcode if the user hasn't specified a handle or signature?
