<?php
    /**
    * Plugin Name: Simple coverflow
    * Version: 1.0.0
    * Author: Simon Hansen
    * Author URI: http://simonhans.dk
    *
    * 
    *
    * Developers can learn more about the WordPress shortcode API:
    * @link http://codex.wordpress.org/Shortcode_API
    *
    * This plugin has been tested and integrates with these scripts:
    * @link http://jquery.com/demo/thickbox
    *
    * @copyright 2010- 2010
    * @version 1.0.0
    * @author Simon Hansen
    * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
    *
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    *
    * @package simple-coverflow
    * 
    * 
    */



    require_once( dirname (__FILE__) . '/gallery.php' );




    class simple_coverflow_controller{
        var $obj;
        var $content_width;

        function __construct() {



            /* Set up the plugin. */
            add_action( 'plugins_loaded', array($this,'simple_coverflow_setup' ));
            add_action( 'after_setup_theme', array($this,'simple_coverflow_img_size' ));
            add_shortcode('coverflow',array($this,'coverflow_handler'));

            if(!is_admin()){ //only include for frontend

                $this->obj=new simple_coverflow();
            }
        }
        function simple_coverflow_handler(){
            $this->__construct();
        }


        function coverflow_handler($attr){

            return $this->obj->cowerflow($attr); 

        }

        function setContentWidth($content_width){

            $this->content_width=$content_width;   
        }


        function getContentWidth(){

            global $content_width; //get width from theme

            $w=simple_coverflow_get_setting('coverflow_width' );
            if($w){   //if content width is set in backend
                $content_width=$w;
            }

            // $simple_coverflow->settings['content_width']=$content_width;
            return $content_width;
        }





        function getCoverflowThumbImgWidth(){
            $border=simple_coverflow_get_setting('border' );
            $width=($this->getContentWidth()-3*$border)/4;
            return $width; 
        }


        /**
        * register the thumbnal size. Used to resize when upload and if image does not exist
        * 
        */
        function simple_coverflow_img_size() {


            $width=$this->getCoverflowThumbImgWidth();
            add_image_size('simple_coverflow_thumb',$width,$width, true);

        }



        /**
        * Sets up the Simple coverflow  plugin and loads files at the appropriate time.
        *
        * @since 0.1
        */
        function simple_coverflow_setup() {

            /* Set constant path to the  simple_coverflow plugin directory. */

            define( 'SIMPLE_COVERFLOW_DIR', plugin_dir_path( __FILE__ ) );

            /* Set constant path to the Cleaner Gallery plugin URL. */
            define( 'SIMPLE_COVERFLOW_URL', plugin_dir_url( __FILE__ ) );

            if(!is_admin()){ //only include for frontend

                require_once (dirname (__FILE__) . '/simple_coverflow_func.php');
            }else      {
                require_once( SIMPLE_COVERFLOW_DIR . 'admin.php' );
            }


            do_action( 'simple_coverflow_loaded' );
        }
    }





    function simple_coverflow_set_setting(){
        global $simple_coverflow;

        //set itemwidth vvar


        global $content_width; //get width from theme

        $w=simple_coverflow_get_setting('coverflow_width' );
        if($w){   //if content width is set in backend
            $content_width=$w;
        }else{

            $simple_coverflow->settings['coverflow_width']=640;
        }
        $simple_coverflow->settings['itemWidth']=($content_width-3*simple_coverflow_get_setting('border'))/4;

    }

    function simple_coverflow_get_setting( $option = '' ) {
        global $simple_coverflow;
        if ( !$option )
            return false;

        if ( !isset( $simple_coverflow->settings ) )
            $simple_coverflow->settings = get_option( 'simple_coverflow_settings' );

        return $simple_coverflow->settings[$option];
    }




    $r=new simple_coverflow_controller();
    simple_coverflow_set_setting();




?>