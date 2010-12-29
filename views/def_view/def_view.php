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
            //wp_enqueue_style('simple_coverflow_defView_style', WP_CONTENT_URL . '/plugins/simple-coverflow/style.css');

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

            foreach ($this->attachments as $id => $attachment ) {
                $img = wp_get_attachment_image_src( $id, 'medium');

                /* Output the link. */
                $output .= '<div style="border:4px solid black; float:left"><a  href="' .  $img['0'].'" title="' . $title . '"' . $attributes . '>';



                $thumb= simple_coverflow::getThumbnail($img['0'], 110, 148, 'jpg');

                $output .= '<img src="' . $thumb . '" alt="' . $title . '" style="float:left" title="' . $title . '" />';


                $output .= '</a></div>';

            }

            return $output;
        }


    }





?>
