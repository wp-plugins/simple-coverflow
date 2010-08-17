<?php
    /**
    * The functions file for for making the coverflow
    *
    * @package simple-coverflow
    */



    
    
    class simple_coverflow{



        /**
        * Load the Thickbox JavaScript if needed.
        *
        * @since 0.8
        */
        function simple_coverflow_enqueue_script() {

            wp_enqueue_script('jquery');
            //wp_enqueue_script('j', WP_CONTENT_URL . '/plugins/sims/jquery.js',array('jquery'));
            wp_enqueue_script('simple_coverflow_js', WP_CONTENT_URL . '/plugins/simple-coverflow/javascript.js', array('jquery'));
            wp_enqueue_style('simple_coverflow_style', WP_CONTENT_URL . '/plugins/simple-coverflow/style.css');

            wp_enqueue_script( 'thickbox' );
        }

        /**
        * Constructor
        * 
        */
        function simple_coverflow(){

            /* Load any scripts needed. */
            add_action( 'template_redirect', array($this,'simple_coverflow_enqueue_script' ));


            return;
        }

        function cowerflow($attr) {


            global $post;

            /* Orderby. */
            if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                if ( !$attr['orderby'] )
                    unset( $attr['orderby'] );
            }

            /* Default gallery settings. */
            $defaults = array(
            'order' => 'ASC',
            'orderby' => 'menu_order ID',
            'id' => $post->ID,
            'link' => 'full',
            'itemtag' => 'dl',
            'icontag' => 'dt',
            'captiontag' => 'dd',
            'columns' => 3,
            'size' => 'thumbnail',
            'include' => '',
            'exclude' => '',
            'numberposts' => -1,
            'offset' => ''
            );


            /* Merge the defaults with user input. Make sure $id is an integer. */
            extract( shortcode_atts( $defaults, $attr ) );

            /* $arg= shortcode_atts( $defaults, $attr ) ;
            $arg['order'];
            $arg['orderby'];
            $arg['id'];
            $arg['link'];
            $arg['itemtag'];
            $arg['icontag'];
            $arg['captiontag'];
            $arg['columns'];
            $arg['size'];
            $arg['include'];
            $arg['exclude'];
            $arg['numberposts'];
            $arg['offset'];
            */
            $id = intval( $id );

            /* Arguments for get_children(). */
            $children = array(
            'post_parent' => $id,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            //'post_mime_type' => 'image',
            //'post_mime_type' => 'audio',
            'post_mime_type' => '',
            'order' => $order,
            'orderby' => $orderby,
            'exclude' => $exclude,
            'include' => $include,
            'numberposts' => $numberposts,
            'offset' => $offset,
            );


            /* Get image attachments. If none, return. */
            $attachments = get_children( $children );



            //print_r($attachments);
            if ( empty( $attachments ) )
                return '';

            /* If is feed, leave the default WP settings. We're only worried about on-site presentation. */
            if ( is_feed() ) {
                $output = "\n";
                foreach ( $attachments as $id => $attachment )
                    $output .= wp_get_attachment_link( $id, $size, true ) . "\n";
                return $output;
            }

            /* Set up some important variables. */
            $itemtag = tag_escape( $itemtag );

            $captiontag = tag_escape( $captiontag );
            $columns = intval( $columns );
            $itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
            $i = 0;

            /*
            * Remove the style output in the middle of the freakin' page.
            * This needs to be added to the header.
            * The width applied through CSS but limits it a bit.
            */

            /* Make sure posts with multiple galleries have different IDs. */
            $gallery_id = cleaner_gallery_id( $id );

            /* Class and rel attributes. */
            $attributes ='class="thickbox" rel="clean-gallery-'.$gallery_id.'"';


            $output=$this->render($attachments,$gallery_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);
            return $output;

        }


        /**
        * Display
        * 
        * @param mixed $attachments
        * @param mixed $gallery_id
        * @param mixed $itemtag
        * @param mixed $icontag
        * @param mixed $id
        * @param mixed $title
        * @param mixed $size
        * @param mixed $link
        * @param mixed $attributes
        */
        function render($attachments,$gallery_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes){


            /* Open the gallery <div>. */


            //$numberOfImages= count($attachments);
            /* $r='<div style=\"position: relative;z-index:20; width:100%\"> 
            <div style=\"position: absolute;z-index:1000; margin-top:160px; width:100%\"> 
            <div id=\"left\"  ></div>
            <div id=\"right\"  ></div> 
            </div></div>';
            */

            $output = "\t\t\t


            <div id='gallery-{$gallery_id}' class=' sims gallery  gallery-{$id}'>";

            $output .= "\n\t\t\t\t<div  class='simscoverflow gallery-row   clear'>";


            /* Loop through each attachment. */
            foreach ( $attachments as $id => $attachment ) {




                /* Get the caption and title. */
                $caption = esc_html( $attachment->post_excerpt );
                $title = esc_attr( $attachment->post_title );

                if ( empty( $caption ) )
                    $caption = $title;




                /* Open each gallery item. */
                $output .= "\n\t\t\t\t\t<{$itemtag} class='gallery-item '>";

                /* Open the element to wrap the image. */
                $output .= "\n\t\t\t\t\t\t<{$icontag} class='gallery-icon'>";

                /* If user links to file. */
                if ( 'file' == $link ) {

                    $output .= '<a href="' .  wp_get_attachment_url( $id ) . '" title="' . $title . '"' . $attributes . '>';

                    $img = wp_get_attachment_image_src( $id, $size );
                    $output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

                    $output .= '</a>';
                }



                /* Link to attachment page. */
                elseif ( empty( $link ) || 'attachment' == $link ) {
                    $output .= wp_get_attachment_link( $id, $size, true, false );



                }

                /* If user wants to link to neither the image file nor attachment. */
                elseif ( 'none' == $link ) {
                    $img = wp_get_attachment_image_src( $id, $size );
                    $output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';
                }

                /* If 'image_link' is set to full, large, medium, or thumbnail. */
                elseif ( 'full' == $link || in_array( $link, get_intermediate_image_sizes() ) ) {
                    $img_src = wp_get_attachment_image_src( $id, $link );


                    simsMakeThumbs($id); //makes resized images


                    if($attachment->post_mime_type=='audio/mpeg'){
                        $output .= '<a href="' .  $attachment->guid . '" title="' . $title . '"' . $attributes . '>'.$title.'</a>';
                    }else{

                        /* Output the link. */
                        $output .= '<a href="' .  $img_src[0] . '" title="' . $title . '"' . $attributes . '>';

                        $size='simple_coverflow_thumb';
                        $img = wp_get_attachment_image_src( $id, $size );

                        $output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

                        $output .= '</a>';

                    }

                }

                /* Close the image wrapper. */
                $output .= "</{$icontag}>";

                /* If image caption is set. */
                if ( $captiontag && $caption ) {
                    $output .= "\n\t\t\t\t\t\t<{$captiontag} class='gallery-caption'>";

                    if ( cleaner_gallery_get_setting( 'caption_link' ) )
                        $output .= '<a href="' . get_attachment_link( $id ) . '" title="' . $title . '">' . $caption . '</a>';

                    else
                        $output .= $caption;

                    $output .= "</{$captiontag}>";
                }

                /* Close individual gallery item. */
                $output .= "\n\t\t\t\t\t</{$itemtag}>";


            }

            /* Close gallery and return it. */
            if ( $columns > 0 && $i % $columns !== 0 )
                $output .= "\n\t\t\t</div>";

            $output .= "\n\t\t\t</div></div>\n";

            $output.='<div style="padding:0px;height:40px;margin-top:0px;border:1px solid #999" >&nbsp 
            <div id="left"  ></div>
            <div id="right"  ></div> 
            </div>';

            /* Return out very nice, valid XHTML gallery. */
            return $output;
        }



    }
?>