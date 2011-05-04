<?php
    /**
    * The functions file for for making the coverflow
    *
    * @package simple-coverflow
    */

    /* Load any scripts needed. */

    if(!is_admin()){ //we only want to include for frontend
        add_action('wp_print_scripts',array('simple_coverflow','javascript_and_css'));
        add_action( 'template_redirect', array('simple_coverflow','include_css_and_js_scripts'));
    }




    class simple_coverflow{

        private $settings;


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

            $defaults['order'] = ( ( $this->settings->get_setting( 'order' ) ) ? $this->settings->get_setting( 'order' ) : 'ASC' );
            $defaults['orderby'] = ( ( $this->settings->get_setting( 'orderby' ) ) ? $this->settings->get_setting( 'orderby' ) : 'menu_order ID' );
            $defaults['size'] = ( ( $this->settings->get_setting( 'size' ) ) ? $this->settings->get_setting( 'size' ) : 'thumbnail' );
            $defaults['link'] = ( ( $this->settings->get_setting( 'image_link' ) ) ? $this->settings->get_setting( 'image_link' ) : '' );
            $defaults['cView'] = ( ( $this->settings->get_setting( 'cView' ) ) ? $this->settings->get_setting( 'cView' ) : '' );

            return $defaults;
        }


        /**
        * css an js in the header
        * 
        */
        function javascript_and_css(){   

            $settings=new simple_coverflow_settings();

            $id='.simscoverflow';
            $content_width=$settings->get_setting('coverflow_width');   //get content width from theme   

            $itemwidth=$settings->get_setting('itemWidth');
            $borderWidth=$settings->get_setting('border');

            $bordercolor=$settings->get_setting('frameColor');

            $frame=$settings->get_setting('frame');
            if($frame){

                $css='.simple_coverflow{

                width: '.intval(($content_width)).'px;
                border-left:'.$borderWidth.'px solid '.$bordercolor.';
                border-right:'.$borderWidth.'px solid '.$bordercolor.';
                background-color:'.$bordercolor.';

                }

                #content .simple_coverflow .simple_coverflow-item{
                width: '.intval(($itemwidth)).'px;
                background-color:'.$bordercolor.';
                float:left;
                }
                ';

            }else{
                $css='.simple_coverflow{

                width: '.intval(($content_width)).'px;

                }

                #content .simple_coverflow .simple_coverflow-item{
                width: '.intval(($itemwidth)).'px;
                float:left;
                }

                ';    
            }

            echo '<style type="text/css">  

            '.$css.'



            #content .simple_coverflow .simple_coverflow-icon{
            background-color:#fff;
            display: table-cell; /* to make vertical-align work*/
            vertical-align: middle;
            /* line-height:'.intval(($itemwidth)).'px;*/
            width:'.intval(($itemwidth)).'px;
            height:'.intval(($itemwidth)).'px;
            }
            #content .simple_coverflow .simple_coverflow-icon img{
            vertical-align: middle;
            }

            </style>
            ';

            echo '<script type="text/javascript">
            //<![CDATA[
            var simple_cover_content_width=  \''.$content_width.'\';                            
            var simple_cover_flow_id =  \''.$id.'\';
            var simple_cover_border =  \''.$borderWidth.'\';                            
            //]]>
            </script>';

            return ;

        }



        /**
        * Load the Thickbox JavaScript if needed.
        *
        * @since 0.8
        */
        function include_css_and_js_scripts() {
            wp_enqueue_style( 'thickbox' );     
            wp_enqueue_script('jquery');
            wp_enqueue_script('simple_coverflow_js2', WP_CONTENT_URL . '/plugins/simple-coverflow/views/first_view/javascript.js', array('jquery'));
            wp_enqueue_style('simple_coverflow_style', WP_CONTENT_URL . '/plugins/simple-coverflow/views/first_view/style.css');

            wp_enqueue_script( 'thickbox' );
        }


        function __construct(){
            $this->settings=new simple_coverflow_settings();

            return ;
        }
        /**
        * Constructor
        * 
        */
        function simple_coverflow(){


            $this->__construct();

            return;
        }





        function cowerflow($attr) {

            global $unike_id;
            $unike_id++; // set unike id for use in post

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


            if($cView=='hideArrows'){
                $output=$this->renderHideArrows($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);

            }else{
                $output=$this->render($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);

            }


            return $output;

        }



        function getBorder(){

            $borderWidth=$this->settings->get_setting('border');  

            return ' style="padding-top:'.$borderWidth.'px;padding-bottom:'.$borderWidth.'px; padding-right:'.$borderWidth.'px" ';
        }


        /**
        * Gets the thumbnail.
        *
        * @param $postID The post ID of the thumbnail.
        * @param $width The width of the thumbnail (optional)
        * @param $height The height of the thumbnail (optional)
        * @param $fileType The file type of the thumbnail; jpg, png, or gif (optional)
        * @return The URL of the thumbnail.
        */
        function getThumbnail($filePath, $width="", $height="", $fileType="" ,$alt)
        {

            
            return $this->getImg($filePath,$width,$height ,$alt);
          /*  
            if ( empty($width) )
                if ( empty($height) )
                    if ( empty($fileType) )
                        $fileType = "jpg";

                    return WP_PLUGIN_URL . "/simple-coverflow/timthumb.php?src=" .
            $filePath. "&amp;w=" . $width . "&amp;h=" . $height . "&amp;zc=1&amp;ft=" . $fileType;
      
        */
        }




        function getContentWidth(){

            global $content_width; //get width from theme

            $w=$this->settings->get_setting('coverflow_width' );
            if($w){   //if content width is set in backend
                $content_width=$w;
            }

            return $content_width;
        }






        function renderCoverflowItems($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes){


            $padding=$this->getBorder();

            /* Loop through each attachment. */
            foreach ( $attachments as $id => $attachment ) {

                /* Get the caption and title. */
                $caption = esc_html( $attachment->post_excerpt );
                $title = esc_attr( $attachment->post_title );

                if (empty($caption )){
                    $caption = $title;
                }

                /* Open each gallery item. */
                $output .= "\n\t\t\t\t\t<{$itemtag} ".$padding." class='simple_coverflow-item '>";

                /* Open the element to wrap the image. */
                $output .= "\n\t\t\t\t\t\t<{$icontag} class='simple_coverflow-icon'>";

                /* If user links to file. */
                if ( 'file' == $link ) {

                    $img_src = wp_get_attachment_image_src( $id, $size );
                    $linkto=$img_src['0'];

                }


                /* Link to attachment page. */
                elseif ( empty( $link ) || 'attachment' == $link ) {

                    $linkto= wp_get_attachment_url( $id, $size, true, false );
                }

                /* If user wants to link to neither the image file nor attachment. */
                elseif ( 'none' == $link ) {
                    $img_src  = wp_get_attachment_image_src( $id, $size );
                    $linkto=$img_src['0'];

                }

                /* If 'image_link' is set to full, large, medium, or thumbnail. */
                elseif ( 'full' == $link || in_array( $link, get_intermediate_image_sizes() ) ) {

                    $img_src = wp_get_attachment_image_src( $id, $link );

                    $linkto=$img_src['0'];
                }
                /* Output the link. */

                $img_src = wp_get_attachment_image_src( $id, 'large');

                $output .= '<a href="' . $linkto .'" title="' . $title . '" ' . $attributes . '>';
                if($r=true)$this->firstImg=$img_src['0']; //make link to first image
                $r=false;
                //echo $img_src['0'];
                $thumb= $this->getThumbnail($img_src['0'], $this->settings->get_setting('itemWidth'), $this->settings->get_setting('itemWidth'), 'jpg',$title);

                $output .= $thumb;

                $output .= '</a>';



                /* Close the image wrapper. */
                $output .= "</{$icontag}>";

                /* If image caption is set. */
                if ( $captiontag && $caption ) {
                    $output .= "\n\t\t\t\t\t\t<{$captiontag} class='simple_coverflow-caption'>";

                    if ( $this->settings->get_setting( 'caption_link' )){
                        $output .= '<a href="' . get_attachment_link( $id ) . '" title="' . $title . '">' . $caption . '</a>';

                    }else{
                        $output .= $caption;
                    }
                    $output .= "</{$captiontag}>";
                }

                /* Close individual gallery item. */
                $output .= "\n\t\t\t\t\t</{$itemtag}>";

            }

            /* Close gallery and return it. */
            if ( $columns > 0 && $i % $columns !== 0 )
                $output .= "\n\t\t\t</div>";

            return $output;
        }


        function imgWidth(){
            return  $this->settings->get_setting('coverflow_width' )/4;  
        }

        /**
        * put your comment there...
        * 
        * @param mixed $attachments
        * @param mixed $unike_id
        * @param mixed $itemtag
        * @param mixed $icontag
        * @param mixed $id
        * @param mixed $title
        * @param mixed $size
        * @param mixed $link
        * @param mixed $attributes
        */
        function renderHideArrows($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes){

            $pos=($this->imgWidth()/2)-20;

            $imgs=$this->renderCoverflowItems($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);
            $buttons='
            <div id="buttons_'.$unike_id.'" style="height:0px;display:none;position:relative;z-index:20; width:100%"> 
            <div style="height:0px;position: absolute;z-index:100; width:100%">
            <div class="left" style="margin-top:'.$pos.'px;float:right" id="left_'.$unike_id.'"  ></div>
            <div class="right" style="float:left;margin-top:'.$pos.'px;" id="right_'.$unike_id.'"  ></div> 
            </div>
            </div>';


            $output = "\t\t\t

            <div id='simple_coverflow-{$unike_id}' class=' length simple_coverflow  simple_coverflow-{$id}'>

            <div  id='frame-{$unike_id}'><img src=\"".$this->firstImg."\" style='width:{$this->settings->get_setting('coverflow_width')}px'  /></div>
            ". $buttons;

            $output .= "\n\t\t\t\t<div  class='simscoverflow simple_coverflow-row   clear'>";

            $output.=$imgs;

            $output .= "\n\t\t\t</div></div>\n";

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
        function render($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes){

            $output = "\t\t\t<div id='simple_coverflow-{$unike_id}' class=' length simple_coverflow  simple_coverflow-{$id}'>";

            $output .= "\n\t\t\t\t<div  class='simscoverflow simple_coverflow-row   clear'>";

            $output.=$this->renderCoverflowItems($attachments,$unike_id,$itemtag,$icontag,$id,$title,$size,$link,$attributes);

            $output .= "\n\t\t\t</div>\n";

            $output.='<div style="padding:0px;height:40px;margin-top:0px;border:1px solid #999" >&nbsp 
            <div class="left" id="left_'.$unike_id.'"  ></div>
            <div class="right" id="right_'.$unike_id.'"  ></div> 
            </div></div>';

            /* Return out very nice, valid XHTML gallery. */
            return $output;
        }

        function convertUrlToServerPath($src){

            $srcRg= split('uploads',$src);
            $uploads=  wp_upload_dir();
            $src=$uploads['basedir'].$srcRg[1];
            return $src;


        }


        /**
        * specifie both width and height will cropscale. Only specifying one will scale
        * 
        * @param mixed $src
        * @param mixed $width
        * @param mixed $height
        * @param mixed $alt
        */

        function getImg($src,$width,$height, $alt=""){



            $filename=SIMPLE_COVERLOW_CACHE.$width.'x'.$height.basename($src);
            //$src=CMS_PACK_CACHE.basename($src);

            if(strstr($src,'http://')){        
                

                $src=$this->convertUrlToServerPath($src);
            }

            if(file_exists($src) and is_file($src)){


                if(!file_exists($filename)){

                    $image = new SimpleImage();
                    $image->load($src);
                    $image->resize($width,$height);
                    $image->save($filename);

                }

                return '<img src="'.SIMPLE_COVERLOW_CACHE_URL.$width.'x'.$height.basename($src).'" alt="'.$alt.'" />';
            }   


        }


        function getImgById($id,$width,$height, $alt=""){

            $src=get_attached_file( $id,'full'); 
            $filename=SIMPLE_COVERLOW_CACHE.$width.'x'.$height.basename($src);
            //$src=CMS_PACK_CACHE.basename($src);
            if(file_exists($src) and is_file($src)){


                if(!file_exists($filename)){

                    $image = new SimpleImage();
                    $image->load($src);
                    $image->resize($width,$height);
                    $image->save($filename);

                }

                return '<img src="'.SIMPLE_COVERLOW_CACHE_URL.$width.'x'.$height.basename($src).'" alt="'.$alt.'" />';
            }   


        }

    }
?>