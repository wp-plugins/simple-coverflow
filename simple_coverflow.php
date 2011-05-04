<?php
    /*
    Plugin Name: Simple coverflow
    Plugin URI: 
    Description: 
    Version: 1.6.0
    Author: Simon Hansen
    Author URI: http://simonhans.dk/
    */



    include('SimpleImage.php');

    define( 'SIMPLE_COVERFLOW_DIR', plugin_dir_path( __FILE__ ) );
    define( 'SIMPLE_COVERFLOW_URL', plugin_dir_url( __FILE__ ) );


    $pos = strpos(WP_CONTENT_URL, $_SERVER['HTTP_HOST']) + strlen($_SERVER['HTTP_HOST']);

    define("SIMPLE_COVERLOW_CACHE", WP_CONTENT_DIR . '/uploads/simple-coverflow-cache/'); // thumbnail url
    define("SIMPLE_COVERLOW_CACHE_URL", substr(WP_CONTENT_URL, $pos)  . '/uploads/simple-coverflow-cache/'); // thumbnail url


    if( !is_dir( SIMPLE_COVERLOW_CACHE) ){
        @mkdir( SIMPLE_COVERLOW_CACHE );
        chmod(SIMPLE_COVERLOW_CACHE,0777);

    }



    if(!is_admin()){ //only for frontend

        // include view
        require_once( dirname (__FILE__) . '/views/first_view/gallery.php' );

        //include_once(dirname(__FILE__)."/views/def_view/def_view.php");

    }else // only for backend
    {
        require_once( SIMPLE_COVERFLOW_DIR . 'admin.php' );
    }





    class simple_coverflow_controller{
        var $obj;

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
        * get images from post
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

            if($attr['view']=="def" and class_exists('defView')){
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



    class simple_coverflow_settings{

        private $settings;

        function __CONSTRUCT(){

            $this->settings = get_option( 'simple_coverflow_settings' );
            $this->set_setting();

        }



        /**
        * calculate widths and add to settings array
        * 
        */
        function set_setting(){

            $content_width=$this->get_setting('coverflow_width' );
            $frame=$this->get_setting('frame');
            if($frame){

                //skal kunne divideres med 4
                $borderWidth=$this->get_setting('border' );
                $imgWidth=($content_width-5*$borderWidth)/4;
                $widthOfCoverflow= 4*floor($imgWidth)+3*$borderWidth;

            }else{

                //skal kunne divideres med 4
                $borderWidth=$this->get_setting('border' );
                $imgWidth=($content_width-3*$borderWidth)/4;
                $widthOfCoverflow= 4*floor($imgWidth)+3*$borderWidth;

            }

            //override the set width to make it dividebel by 4
            $this->settings['coverflow_width']=$widthOfCoverflow;
            $this->settings['itemWidth']=($widthOfCoverflow-3*$this->get_setting('border'))/4;

        }

        function get_setting( $option = '' ) {
            if ( !$option ){
                return false;
            }


            if ( $option=='coverflow_width' ){

                if($this->settings['coverflow_width']==''){
                    return $this->settings['coverflow_width']=400; //default value 
                }  
            }                                 

            return $this->settings[$option];



        }
    }






    new simple_coverflow_controller();




?>
