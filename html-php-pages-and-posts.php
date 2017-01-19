<?php
/*
Plugin Name: HTML/PHP Pages and Posts
Plugin URI: http://www.github.com/stephenafamo/html-php-pages-and-posts
Description: Use uploaded html or php files as templates for pages and posts.
Version: 1.0.0
Author: Stephen Afam-Osemene
Author URI: https://www.stephenafamo.com/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!class_exists("CustomPagesAndPosts")) {
    
    class CustomPagesAndPosts {

        public function custom_upload_mimes( $existing_mimes ) {
            // add webm to the list of mime types
            $existing_mimes['htm|html'] = 'text/html';
            $existing_mimes['php'] = 'php';
            $existing_mimes['css'] = 'text/css';
            $existing_mimes['js'] = 'application/javascript';
            // return the array back to the function with our added mime type
            return $existing_mimes;
        }

        public function load_custom_template ($single_template){

            global $post;

            $file = wp_upload_dir()['basedir'] . '/' . get_post_meta($post->ID, '_agadyn_custom_template_path', true);

            if (is_file($file)){

                $options = get_post_meta($post->ID, '_agadyn_custom_template_options', true);

                switch ($options) {

                    case 'overwrite_all':
                        $single_template = $file;
                        break;

                    case 'overwrite_content':
                        $post->post_content = '[html_php_page_post]';
                        break;

                    case 'below_content':
                        $post->post_content .= '[html_php_page_post]';
                        break;

                    case 'above_content':
                        $post->post_content = '[html_php_page_post]'.$post->post_content;
                        break;
                }
            }
            return $single_template;
        }

        public function shortcode_parser( $atts, $content = null ) {

            global $post;
            $file = wp_upload_dir()['basedir'] . '/' . get_post_meta($post->ID, '_agadyn_custom_template_path', true);
            if (is_file($file)){
                // return file_get_contents($file);
                ob_start();
                    require_once $file;
                return ob_get_clean();
            }
        }

        public function add_custom_meta_box(){
            add_meta_box( 
                'agadyn_custom_template', 
                'Custom HTML or PHP', 
                array($this, 'custom_meta_box_markup'),
                ["post", "page"],
                'normal',
                'high');

        }

        public function custom_meta_box_markup($object) {

            $template_post_id = get_post_meta($object->ID, "_agadyn_custom_template_id", true);

            if (!$template_post_id) $template_post_id = 0;

            wp_nonce_field(basename(__FILE__), "meta-box-nonce");

            ?>

                <div>
                    <label for="agadyn_custom_template">Link to custom template</label>


                    <input name="agadyn_custom_template_name" id="agadyn_custom_template_name" type="text" value="<?php echo get_post_meta($template_post_id, "_wp_attached_file", true);; ?>" readonly> 
                    <br/>
                    <input id="upload_template_button" type="button" class="button" value="<?php _e( 'Upload template' ); ?>" />
                    <input id="delete_template_button" type="button" class="button" value="<?php _e( 'Delete template' ); ?>" />
                    <br/>
                    <input name="agadyn_custom_template_id" id="agadyn_custom_template_id" type="hidden" value="<?php echo $template_post_id; ?>">

                    <br>

                    <label for="agadyn_custom_template_options">Options</label>
                    <select name="agadyn_custom_template_options">
                        <?php 
                            $option_values = [
                                'overwrite_all' => 'Overwrite All', 
                                'overwrite_content' => 'Overwrite Content', 
                                'below_content' => 'Below Content', 
                                'above_content' => 'Above Content'];

                            foreach($option_values as $key => $value) 
                            {
                                if($key == get_post_meta($object->ID, "_agadyn_custom_template_options", true))
                                {
                                    ?>
                                        <option value="<?php echo $key?>" selected><?php echo $value; ?></option>
                                    <?php    
                                }
                                else
                                {
                                    ?>
                                        <option value="<?php echo $key?>"><?php echo $value; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>

                    <br>

                </div>

                <script type='text/javascript'>
                    jQuery( document ).ready( function( $ ) {
                        // Uploading files
                        var file_frame;
                        var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                        var set_to_post_id = <?php echo $template_post_id; ?>; // Set this
                        jQuery('#upload_template_button').on('click', function( event ){
                            event.preventDefault();
                            // If the media frame already exists, reopen it.
                            if ( file_frame ) {
                                // Set the post ID to what we want
                                file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                                // Open frame
                                file_frame.open();
                                return;
                            } else {
                                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                                wp.media.model.settings.post.id = set_to_post_id;
                            }
                            // Create the media frame.
                            file_frame = wp.media.frames.file_frame = wp.media({
                                title: 'Select a template to upload',
                                button: {
                                    text: 'Use this template',
                                },
                                multiple: false // Set to true to allow multiple files to be selected
                            });
                            // When an image is selected, run a callback.
                            file_frame.on( 'select', function() {
                                // We set multiple to false so only get one image from the uploader
                                attachment = file_frame.state().get('selection').first().toJSON();
                                // Do something with attachment.id and/or attachment.url here
                                $( '#agadyn_custom_template_name' ).val( attachment.url);
                                $( '#agadyn_custom_template_id' ).val( attachment.id );
                                // Restore the main post ID
                                wp.media.model.settings.post.id = wp_media_post_id;
                            });
                                // Finally, open the modal
                                file_frame.open();
                        });
                        jQuery('#delete_template_button').on('click', function( event ){
                            var nothing = '';
                            $( '#agadyn_custom_template_name' ).val( nothing );
                            $( '#agadyn_custom_template_id' ).val( nothing );
                        });
                        // Restore the main ID when the add media button is pressed
                        jQuery( 'a.add_media' ).on( 'click', function() {
                            wp.media.model.settings.post.id = wp_media_post_id;
                        });
                    });
                </script>

            <?php
        }

        public function save_custom_meta_box($post_id, $post){

            if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
                return $post_id;

            if(!current_user_can("edit_post", $post_id))
                return $post_id;

            if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
                return $post_id;

            if(isset($_POST["agadyn_custom_template_id"]))
            {
                $agadyn_custom_template_id = $_POST["agadyn_custom_template_id"];
                $agadyn_custom_template_path = get_post_meta( $_POST["agadyn_custom_template_id"], '_wp_attached_file', true );
            }   
            update_post_meta($post_id, "_agadyn_custom_template_id", $agadyn_custom_template_id);
            update_post_meta($post_id, "_agadyn_custom_template_path", $agadyn_custom_template_path);

            if(isset($_POST["agadyn_custom_template_options"]))
            {
                $agadyn_custom_template_options = $_POST["agadyn_custom_template_options"];
            }   
            update_post_meta($post_id, "_agadyn_custom_template_options", $agadyn_custom_template_options);
        }
    } // end class 
    
} // end check for class

// Begin!!!
$class = new CustomPagesAndPosts();

if (isset($class)) {

    add_filter( 'mime_types', array($class, 'custom_upload_mimes' ), 1);
    add_filter( 'single_template', array($class, 'load_custom_template' ), 111, 1);
    add_filter( 'template_include', array($class, 'load_custom_template' ), 111, 1);
    add_filter( 'add_meta_boxes', array($class, 'add_custom_meta_box' ));
    add_action( 'save_post', array($class, 'save_custom_meta_box' ), 10, 2);       
    add_shortcode( 'html_php_page_post', array($class, 'shortcode_parser' )); 

}