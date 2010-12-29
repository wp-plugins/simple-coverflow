<?php
    /**
    * Plugin Name: Simple coverflow
    * Version: 1.5.0
    * Author: Simon Hansen
    * Author URI: http://www.simonhans.dk
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



    define( 'SIMPLE_COVERFLOW_DIR', plugin_dir_path( __FILE__ ) );
    define( 'SIMPLE_COVERFLOW_URL', plugin_dir_url( __FILE__ ) );



    if(!is_admin()){ //only for frontend

        // include view
        require_once( dirname (__FILE__) . '/views/first_view/gallery.php' );

        //include_once(dirname(__FILE__)."/views/def_view/def_view.php");
        simple_coverflow_set_setting();

    }else // only for backend
    {
        require_once( SIMPLE_COVERFLOW_DIR . 'admin.php' );
    }





    class simple_coverflow_controller{
        var $obj;
        var $content_width;

        function __construct() {

            /* Set up the plugin. */
            add_action( 'plugins_loaded', array($this,'simple_coverflow_setup' ));
            add_shortcode('coverflow',array($this,'shortcode_handler'));

        }
        /**
        * php 4 constructer
        * 
        */
        function simple_coverflow_handler(){
            $this->__construct();
        }


        /**
        * get images fron post
        * 
        */
        function data() {
            global $post;
            $children = array(
            'post_parent' => $post->ID,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            );


            /* Get image attachments. If none, return. */
            $attachments = get_children( $children );
            return $attachments ;
        }

        /**
        * method called by shortcode 
        * 
        * @param mixed $attr
        */

        function shortcode_handler($attr){

            if($attr['view']=="def"){
                $attachments=$this->data();
                $r=new defView($attachments);
                return $r->render();

            }else{

                $obj=new simple_coverflow();
                return $obj->cowerflow($attr); 
            }
        }

        function setContentWidth($content_width){

            $this->content_width=$content_width;   
        }



        function simple_coverflow_setup() {

            do_action( 'simple_coverflow_loaded' );
        }
    }





    function simple_coverflow_set_setting(){
        global $simple_coverflow;

        //set itemwidth vvar


        global $content_width; //get width from theme

        $content_width=simple_coverflow_get_setting('coverflow_width' );


        $frame=simple_coverflow_get_setting('frame');
        if($frame){

            //skal kunne divideres med 4
            $borderWidth=simple_coverflow_get_setting('border' );
            $imgWidth=($content_width-5*$borderWidth)/4;
            $widthOfCoverflow= 4*floor($imgWidth)+3*$borderWidth;

        }else{

            //skal kunne divideres med 4
            $borderWidth=simple_coverflow_get_setting('border' );
            $imgWidth=($content_width-3*$borderWidth)/4;
            $widthOfCoverflow= 4*floor($imgWidth)+3*$borderWidth;

        }

        //override the set width to make it dividebel by 4
        $simple_coverflow->settings['coverflow_width']=$widthOfCoverflow;
        $simple_coverflow->settings['itemWidth']=($widthOfCoverflow-3*simple_coverflow_get_setting('border'))/4;

    }

    function simple_coverflow_get_setting( $option = '' ) {
        global $simple_coverflow;
        if ( !$option )
            return false;

        if ( !isset( $simple_coverflow->settings ) )
            $simple_coverflow->settings = get_option( 'simple_coverflow_settings' );

        return $simple_coverflow->settings[$option];
    }



    new simple_coverflow_controller();




?>
