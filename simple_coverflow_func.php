<?php

    /**
    * Generate post thumbnail attachment meta data.
    *
    * @since 2.1.0
    *
    * @param int $attachment_id Attachment Id to process.
    * @param string $file Filepath of the Attached image.
    * @return mixed Metadata for attachment.
    */
    function sims_generate_attachment_metadata( $attachment_id, $file,$sizeTomake ) {
        $attachment = get_post( $attachment_id );

        $metadata = array();
        if ( preg_match('!^image/!', get_post_mime_type( $attachment )) && file_is_displayable_image($file) ) {
            $imagesize = getimagesize( $file );
            $metadata['width'] = $imagesize[0];
            $metadata['height'] = $imagesize[1];
            list($uwidth, $uheight) = wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
            $metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";

            // Make the file path relative to the upload dir
            $metadata['file'] = _wp_relative_upload_path($file);

            // make thumbnails and other intermediate sizes
            global $_wp_additional_image_sizes;

            foreach ( get_intermediate_image_sizes() as $s ) {

                $sizes[$s] = array( 'width' => '', 'height' => '', 'crop' => FALSE );
                if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
                    $sizes[$s]['width'] = intval( $_wp_additional_image_sizes[$s]['width'] ); // For theme-added sizes
                else
                    $sizes[$s]['width'] = get_option( "{$s}_size_w" ); // For default sizes set in options
                if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
                    $sizes[$s]['height'] = intval( $_wp_additional_image_sizes[$s]['height'] ); // For theme-added sizes
                else
                    $sizes[$s]['height'] = get_option( "{$s}_size_h" ); // For default sizes set in options
                if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )
                    $sizes[$s]['crop'] = intval( $_wp_additional_image_sizes[$s]['crop'] ); // For theme-added sizes
                else
                    $sizes[$s]['crop'] = get_option( "{$s}_crop" ); // For default sizes set in options

            }
            $sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );

            foreach ($sizes as $size => $size_data ) {

                //sims to do ; error handling  // I use wordpres own function to calculalte
                $fileparts=explode(".",$file);
                $imgSize = @getimagesize( $file );
                list($orig_w, $orig_h, $orig_type) = $imgSize;
                $dims = image_resize_dimensions($orig_w, $orig_h, $size_data['width'], $size_data['height'],  $size_data['crop'] );
                list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

                if($dst_w){   //it only gives us dst_w if width is smaler than original so we make this (if)
                    $name= $fileparts['0'].'-'.$dst_w.'x'.$dst_h.'.'.$fileparts['1'];
                }else{
                    $name=$file;    
                }
                //check if file already exist
                if (!file_exists( $name)){

                    echo $name.' Generated<br>';

                    $resized = image_make_intermediate_size( $file, $size_data['width'], $size_data['height'], $size_data['crop'] );

                    print_r($resized);
                }else{

                    $resized = array(
                    'file' => basename( $name ),
                    'width' => $dst_w,
                    'height' => $dst_h,

                    );

                }


                if ( $resized )
                    $metadata['sizes'][$size] = $resized;
            }

            // fetch additional metadata from exif/iptc
            $image_meta = wp_read_image_metadata( $file );
            if ( $image_meta )
                $metadata['image_meta'] = $image_meta;

        }

        return apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment_id );
    }




    include('wp-admin/includes/image.php');

    /**
    * Resize function
    * 
    * @param mixed $id
    * @param mixed $sizesTomake
    */
    function simsMakeThumbs($id,$sizesTomake){
        $fullsizepath = get_attached_file( $id );

        if ( false === $fullsizepath || !file_exists($fullsizepath) )
            die('-1');
        //safe mode dont use set_time_limit( 60 );

        if (  wp_update_attachment_metadata( $id, sims_generate_attachment_metadata( $id, $fullsizepath,$sizesTomake ) ) )
            return;


    }



?>
