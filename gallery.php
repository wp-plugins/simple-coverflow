<?php
    /**
    * The functions file for for making the coverflow
    *
    * @package simple-coverflow
    */





    class simple_coverflow{


        function simple_coverflow_default_settings( $id) {


            /* Default gallery settings. */
            $defaults = array(
            'order' => 'ASC',
            'orderby' => 'menu_order ID',
            'id' =>  $id,
            'link' => 'large',
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
           
            $defaults['order'] = ( ( simple_coverflow_get_setting( 'order' ) ) ? simple_coverflow_get_setting( 'order' ) : 'ASC' );
            $defaults['orderby'] = ( ( simple_coverflow_get_setting( 'orderby' ) ) ? simple_coverflow_get_setting( 'orderby' ) : 'menu_order ID' );
            $defaults['size'] = ( ( simple_coverflow_get_setting( 'size' ) ) ? simple_coverflow_get_setting( 'size' ) : 'thumbnail' );
            $defaults['link'] = ( ( simple_coverflow_get_setting( 'image_link' ) ) ? simple_coverflow_get_setting( 'image_link' ) : '' );

            return $defaults;
        }


        function javascript_and_css(){   

            $id='.simscoverflow';
            global $content_width;   //get content width from theme   
            if (! isset( $content_width ) ){
                //$content_width = 640;
            }

            global $some_id;

            echo '<style type="text/css">  

            .simple_coverflow{

            width: '.intval(($content_width)).'px;
            }

            #content .simple_coverflow .simple_coverflow-item{
            width: '.intval(($content_width/4)).'px;
            float:left;
            </style>
            ';

            echo '<script type="text/javascript">
            //<![CDATA[
            var simple_cover_content_width=  \''.$content_width.'\';                            
            var simple_cover_flow_id =  \''.$id.'\';                            
            //]]>
            </script>';


            return ;

        }


        function resizedImage($rg){

            $tt=explode('.',$rg['0']);
            // print_r($tt);
            return implode('-'.$rg['1'].'x'.$rg['2'].'.',$tt);

        }

        /**
        * Load the Thickbox JavaScript if needed.
        *
        * @since 0.8
        */
        function simple_coverflow_enqueue_script() {

            wp_enqueue_script('jquery');
            add_action('wp_print_scripts', array($this,'javascript_and_css'));
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

            global $unike_id;
            $unike_id++; // set unike id for use in post
            //  echo $unike_id;


            global $post;

            /* Orderby. */
            if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                if ( !$attr['orderby'] )
                    unset( $attr['orderby'] );
            }



            $defaults=$this->simple_coverflow_default_settings($post->ID);

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
            'post_mime_type' => 'image',
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


            /* Class and rel attributes. */
            $attributes ='class="thickbox" rel="clean-gallery-'.$gallery_id.'"';

            $this->generateResize($attachments);
            
            $output=$this->render($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);

            return $output;

        }


        function generateResize($attachments){

            if($_GET['gen']==''){ //only generate if gen is set

                foreach ( $attachments as $id => $attachment ) {


                    simsMakeThumbs($id,array('simple_coverflow_thumb','large')); //makes resized images

                }
            }            
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
        function render($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes){
            

            //$numberOfImages= count($attachments);
            /* $r='<div style=\"position: relative;z-index:20; width:100%\"> 
            <div style=\"position: absolute;z-index:1000; margin-top:160px; width:100%\"> 
            <div id=\"left\"  ></div>
            <div id=\"right\"  ></div> 
            </div></div>';
            */

            $output = "\t\t\t


            <div id='simple_coverflow-{$unike_id}' class=' length simple_coverflow  simple_coverflow-{$id}'>";

            $output .= "\n\t\t\t\t<div  class='simscoverflow simple_coverflow-row   clear'>";


            /* Loop through each attachment. */
            foreach ( $attachments as $id => $attachment ) {




                /* Get the caption and title. */
                $caption = esc_html( $attachment->post_excerpt );
                $title = esc_attr( $attachment->post_title );

                if ( empty( $caption ) )
                    $caption = $title;




                /* Open each gallery item. */
                $output .= "\n\t\t\t\t\t<{$itemtag} class='simple_coverflow-item '>";

                /* Open the element to wrap the image. */
                $output .= "\n\t\t\t\t\t\t<{$icontag} class='simple_coverflow-icon'>";

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

                    /* Output the link. */
                    $output .= '<a href="' .  $img_src['0'].'" title="' . $title . '"' . $attributes . '>';

                    $size='simple_coverflow_thumb';
                    $img = wp_get_attachment_image_src( $id, $size );
                    $output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

                    $output .= '</a>';



                }

                /* Close the image wrapper. */
                $output .= "</{$icontag}>";

                /* If image caption is set. */
                if ( $captiontag && $caption ) {
                    $output .= "\n\t\t\t\t\t\t<{$captiontag} class='simple_coverflow-caption'>";

                    if ( simple_coverflow_get_setting( 'caption_link' ) )
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
            <div class="left" id="left_'.$unike_id.'"  ></div>
            <div class="right" id="right_'.$unike_id.'"  ></div> 
            </div>';

            /* Return out very nice, valid XHTML gallery. */
            return $output;
        }



    }
?>