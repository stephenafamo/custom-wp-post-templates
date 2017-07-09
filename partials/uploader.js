jQuery( document ).ready( function( $ ) {
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    console.log(wp_media_post_id);
    // Restore the main ID when the add media button is pressed
    // jQuery( 'a.add_media' ).on( 'click', function() {
    //     wp.media.model.settings.post.id = wp_media_post_id;
    // });
});


    function delete_template(event, prefix = "", suffix = "")
    {
        var nothing = '';
        document.getElementById(prefix + 'agadyn_custom_template_path' + suffix ).value = nothing;
        document.getElementById(prefix + 'agadyn_custom_template_id' + suffix ).value = nothing;
    }

    function select_template(event, prefix = "", suffix = "")
    {
    	var file_frame;
	    var wp_media_post_id = wp.media.model.settings.post.id; 
	    var set_to_post_id = document.getElementById(prefix + 'agadyn_custom_template_id' + suffix).value; // Set this

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
            document.getElementById(prefix + 'agadyn_custom_template_path' + suffix ).value = attachment.url;
            document.getElementById(prefix + 'agadyn_custom_template_id' + suffix ).value = attachment.id;
            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;
        });
        // Finally, open the modal
        file_frame.open();
    }