<?php
    /**
    * Plugin Name: Simple coverflow
    * Version: 0.0
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
    * @version 0.8
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


   
    /* Set up the plugin. */
    add_action( 'plugins_loaded', 'simple_coverflow_setup' );
    add_action( 'after_setup_theme', 'simple_coverflow_img_size' );
    add_shortcode('coverflow','coverflow_handler');

    if(!is_admin()){ //only include for frontend

        $obj=new simple_coverflow();
    }





    function coverflow_handler($attr){
        global $obj;
        return $obj->cowerflow($attr); 

    }



    function simple_coverflow_img_size() {
        add_image_size('simple_coverflow_thumb',160,160, true);

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



?>