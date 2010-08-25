<?php
    /**
    * Plugin Name: Simple coverflow
    * Version: 0.9.2
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
    * @version 0.9.2
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

        
        /**
        * register the thumbnal size. Used to resize when upload and if image does not exist
        * 
        */
        function simple_coverflow_img_size() {
            global $content_width; //get width from theme
            $this->setContentWidth($content_width);
            $width=$this->content_width/4;
            $height=$this->content_width/4;
            add_image_size('simple_coverflow_thumb',$width,$height, true);

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

            /*if(is_super_admin($user_id)){
            echo 222;
            }*/

            if(!is_admin()){ //only include for frontend

                require_once (dirname (__FILE__) . '/simple_coverflow_func.php');
            }


            do_action( 'simple_coverflow_loaded' );
        }
    }


    $r=new simple_coverflow_controller();



?>