<?php

    /* Load any scripts needed. */
    
    if(!is_admin()){ //we only want to include for frontend
        add_action('wp_print_scripts',array('defView','javascript_and_css'));
        add_action( 'template_redirect', array('defView','include_css_and_js_scripts'));
    }


    /**
    * the view class
    */

    class defView{



        /**
        * load css and js scripts
        * 
        */
        static public function include_css_and_js_scripts(){
            //wp_enqueue_script('jquery');
            //wp_enqueue_script('some_js', WP_CONTENT_URL . '/plugins/simple-coverflow/views/def_view/some_js.js', array('jquery')); 
            wp_enqueue_style('simple_coverflow_defView_style', WP_CONTENT_URL . '/plugins/simple-coverflow/views/def_view/style.css');

        }


        /**
        * insert styles and js in head
        * 
        */
        static public function javascript_and_css(){   

            echo '<style type="text/css"> 
            

            </style>
            ';

            return ;

        }

        public function __construct($attachments){
            $this->attachments=$attachments;          


        }

        public function render() {

           $imageRender=new simple_coverflow();
            
            $output='<div style="width:700px">';
            foreach ($this->attachments as $id => $attachment ) {
                $img = wp_get_attachment_image_src( $id, 'large');

                /* Output the link. */
                $output .= '<div class="sc_def_view" style="border:0px solid black; float:left"><a rel="clean-gallery-" class="thickbox"   href="' .  $img['0'].'" title="' . $title . '"' . $attributes . '>';



                $thumb= $imageRender->getThumbnail($img['0'], 120, 120, 'jpg','hej');

                $output .= $thumb;


                $output .= '</a></div>';

            }

            
            $output.='</div >';
            return $output;
        }


    }





?>
