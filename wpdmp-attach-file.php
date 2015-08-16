<?php    
    require_once ABSPATH . '/wp-admin/includes/file.php';
    require_once ABSPATH . '/wp-admin/includes/image.php';
     
    
function wpdmp_create_attachment_from_file($filepath){    
     
	if ( empty( $filepath ) ) {
		return 0;
	}
	
    global $current_user;
    get_currentuserinfo();
    $logged_in_user = $current_user->ID;
     
    // load up a variable with the upload direcotry
    $uploads = wp_upload_dir();
     
    $file = array(
    'name' => strtolower(pathinfo($filepath, PATHINFO_FILENAME) . "." . pathinfo($filepath, PATHINFO_EXTENSION)),
    //'type'	=> 'png',
    'tmp_name'	=> $filepath,
    //'error'	=> '',
    //'size'	=> '',
    );
     
    $filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ copy( $file['tmp_name'], $new_file ) ) {
		if ( 0 === strpos( $uploads['basedir'], ABSPATH ) )
			$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
		else
			$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];

		return sprintf( __('The uploaded file could not be moved to %s.','wp-design-maps-and-places' ), $error_path ) ;
	}

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	// checks the file type and stores in in a variable
    $wp_filetype = wp_check_filetype( basename( $new_file ), null );	
     
    // set up the array of arguments for "wp_insert_post();"
    $attachment = array(
	    'post_mime_type' => $wp_filetype['type'],
	    'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_file ) ),
	    'post_content' => '',
	    'post_author' => $logged_in_user,
	    'post_status' => 'inherit',
	    'post_type' => 'attachment',
	    'guid' => $url
    );
     
    // insert the attachment post type and get the ID
    //$attachment_id = wp_insert_post( $attachment );
    $attachment_id = wp_insert_attachment( $attachment, $new_file );
     
    // generate the attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attachment_id, $new_file );
     
    // update the attachment metadata
    wp_update_attachment_metadata( $attachment_id, $attach_data );
    
    return $attachment_id;
}
    ?>