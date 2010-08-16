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
    function sims_generate_attachment_metadata( $attachment_id, $file ) {
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


                if($s=='simple_coverflow_thumb' ){

                    $sizes[$s] = array( 'width' => '', 'height' => '', 'crop' => FALSE );
                    if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
                        $sizes[$s]['width'] = intval( $_wp_additional_image_sizes[$s]['width'] ); // For theme-added sizes

                    if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
                        $sizes[$s]['height'] = intval( $_wp_additional_image_sizes[$s]['height'] ); // For theme-added sizes
                    if (isset( $_wp_additional_image_sizes[$s]['crop'] ) )
                        $sizes[$s]['crop'] = intval( $_wp_additional_image_sizes[$s]['crop'] ); // For theme-added sizes
                }
            }
            $sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );

            foreach ($sizes as $size => $size_data ) {


                $fileparts=explode(".",$file);
                //print_r($imgg);
                $width=$size_data['width'];
                $height=$size_data['height'];
                $name= $fileparts['0'].'-'.$width.'x'.$height.'.'.$fileparts['1'];

                //check if file already exist
                if (!file_exists( $name)){


                    echo $name.'<br>';

                    $resized = image_make_intermediate_size( $file, $size_data['width'], $size_data['height'], $size_data['crop'] );

                }else{

                    $resized = array(
                    'file' => basename( $name ),
                    'width' => $size_data['width'],
                    'height' => $size_data['height'],
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


    /**
    * makes resized images if there is no one
    * 
    * @param mixed $id
    */

    include('wp-admin/includes/image.php');
    function simsMakeThumbs($id){
        $fullsizepath = get_attached_file( $id );

        if ( false === $fullsizepath || !file_exists($fullsizepath) )
            die('-1');
        //safe mode dont use set_time_limit( 60 );

        if (  wp_update_attachment_metadata( $id, sims_generate_attachment_metadata( $id, $fullsizepath ) ) )
            return;


    }



?>
