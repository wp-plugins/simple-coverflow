<?php
    /**
    * Administration functions for loading and displaying the settings page and saving settings 
    * are handled in this file.
    *
    * @package simple-coverflow
    */

    /* Initialize the theme admin functionality. */
    add_action( 'init', 'simple_coverflow_init' );


    function simple_coverflow_init() {
        add_action( 'admin_menu', 'simple_coverflow_settings_page_init' );

        add_action( 'simple_coverflow_update_settings_page', 'simple_coverflow_save_settings' );
    }

    /**
    * Sets up the Simple Coverflow settings page and loads the appropriate functions when needed.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_page_init() {
        global $simple_coverflow;

        /* Create the theme settings page. */
        $simple_coverflow->settings_page = add_theme_page( __( 'Simple Coverflow', 'simple-coverflow' ), __( 'Simple Coverflow', 'simple-coverflow' ), 'edit_theme_options', 'simple-coverflow', 'simple_coverflow_settings_page' );

        /* Register the default theme settings meta boxes. */
        add_action( "load-{$simple_coverflow->settings_page}", 'simple_coverflow_create_settings_meta_boxes' );

        /* Make sure the settings are saved. */
        add_action( "load-{$simple_coverflow->settings_page}", 'simple_coverflow_load_settings_page' );

        /* Load the JavaScript and stylehsheets needed for the theme settings. */
        add_action( "load-{$simple_coverflow->settings_page}", 'simple_coverflow_settings_page_enqueue_script' );
        add_action( "load-{$simple_coverflow->settings_page}", 'simple_coverflow_settings_page_enqueue_style' );
        add_action( "admin_head-{$simple_coverflow->settings_page}", 'simple_coverflow_settings_page_load_scripts' );
    }

    /**
    * Returns an array with the default plugin settings.
    *
    * @since 0.8
    */
    function simple_coverflow_settings() {
        $settings = array(
        'image_link' => 'attachment',
        'caption_remove' => false,
        'caption_link' => true,
        'caption_title' => false,
        'size' => 'thumbnail',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'thickbox_js' => false,
        'thickbox_css' => false,
        'image_script' => false,
        'coverflow_width'=>false,
        );
        return apply_filters( 'simple_coverflow_settings', $settings );
    }

    /**
    * Function run at load time of the settings page, which is useful for hooking save functions into.
    *
    * @since 0.8
    */
    function simple_coverflow_load_settings_page() {

        /* Get theme settings from the database. */
        $settings = get_option( 'simple_coverflow_settings' );

        /* If no settings are available, add the default settings to the database. */
        if ( empty( $settings ) ) {
            add_option( 'simple_coverflow_settings', simple_coverflow_settings(), '', 'yes' );

            /* Redirect the page so that the settings are reflected on the settings page. */
            wp_redirect( admin_url( 'themes.php?page=simple-coverflow' ) );
            exit;
        }

        /* If the form has been submitted, check the referer and execute available actions. */
        elseif ( isset( $_POST['simple-coverflow-settings-submit'] ) ) {

            /* Make sure the form is valid. */
            check_admin_referer( 'simple-coverflow-settings-page' );

            /* Available hook for saving settings. */
            do_action( 'simple_coverflow_update_settings_page' );

            /* Redirect the page so that the new settings are reflected on the settings page. */
            wp_redirect( admin_url( 'themes.php?page=simple-coverflow&updated=true' ) );
            exit;
        }
    }

    /**
    * Validates the plugin settings.
    *
    * @since 0.8
    */
    function simple_coverflow_save_settings() {

        /* Get the current theme settings. */
        $settings = get_option( 'simple_coverflow_settings' );

        $settings['image_link'] = esc_html( $_POST['image_link'] );
        $settings['size'] = esc_html( $_POST['size'] );
        $settings['orderby'] = esc_html( $_POST['orderby'] );
        $settings['order'] = esc_html( $_POST['order'] );
        $settings['image_script'] = esc_html( $_POST['image_script'] );
        $settings['caption_remove'] = ( ( isset( $_POST['caption_remove'] ) ) ? true : false );
        $settings['caption_link'] = ( ( isset( $_POST['caption_link'] ) ) ? true : false );
        $settings['caption_title'] = ( ( isset( $_POST['caption_title'] ) ) ? true : false );
        $settings['thickbox_js'] = ( ( isset( $_POST['thickbox_js'] ) ) ? true : false );
        $settings['thickbox_css'] = ( ( isset( $_POST['thickbox_css'] ) ) ? true : false );
        $settings['coverflow_width'] = esc_html( $_POST['coverflow_width'] ); 
        $settings['cView'] = esc_html( $_POST['cView'] );
        $settings['border'] = esc_html( $_POST['border'] ); 
        $settings['frame'] = ( ( isset( $_POST['frame'] ) ) ? true : false );
        $settings['frameColor'] = esc_html( $_POST['frameColor'] ); 

        
        /* Update the theme settings. */
        $updated = update_option( 'simple_coverflow_settings', $settings );
    }

    /**
    * Registers the plugin meta boxes for use on the settings page.
    *
    * @since 0.8
    */
    function simple_coverflow_create_settings_meta_boxes() {
        global $simple_coverflow;

        add_meta_box( 'simple-coverflow-about-meta-box', __( 'About Simple Coverflow', 'simple-coverflow' ), 'simple_coverflow_about_meta_box', $simple_coverflow->settings_page, 'normal', 'high' );

        add_meta_box( 'simple-coverflow-general-meta-box2', __( 'Coverflow Settings', 'simple-coverflow' ), 'simple_coverflow_general_meta_box', $simple_coverflow->settings_page, 'normal', 'high' );

    }

    /**
    * Displays the about meta box.
    *
    * @since 0.8
    */
    function simple_coverflow_about_meta_box() {
        $plugin_data = get_plugin_data( SIMPLE_COVERFLOW_DIR . 'simple_coverflow.php' ); ?>

    <table class="form-table">
        <tr>
            <th><?php _e( 'Plugin:', 'simple-coverflow' ); ?></th>
            <td><?php echo $plugin_data['Title']; ?> <?php echo $plugin_data['Version']; ?></td>
        </tr>
        <tr>
            <th><?php _e( 'Author:', 'simple-coverflow' ); ?></th>
            <td><?php echo $plugin_data['Author']; ?></td>
        </tr>
        <tr>
            <th><?php _e( 'Description:', 'simple-coverflow' ); ?></th>
            <td><?php echo $plugin_data['Description']; ?></td>
        </tr>
    </table><!-- .form-table --><?php
    }

    /**
    * Displays the gallery settings meta box.
    *
    * @since 0.8
    */
    function simple_coverflow_general_meta_box() {

        foreach ( get_intermediate_image_sizes() as $size ){
            $image_sizes[$size] = $size;

            $image_link = array( '' => '', 'none' => __( 'None', 'simple-coverflow' ), 'attachment' => __( 'Attachment Page', 'simple_coverflow' ) );
            $image_link = array_merge( $image_link, $image_sizes );
            $image_link['full'] = 'full'; 
        }
    ?>

    <table class="form-table">

        <tr>
            <th><?php _e( 'Coverflow Width:', 'simple-coverflow' ); ?></th>
            <td>
                <input id="coveflow-width" name="coverflow_width" type="input"  value="<?php echo simple_coverflow_get_setting( 'coverflow_width' ); ?>" /> 
                <label for="coveflow-width"><?php _e( 'Set width of your coverflow', 'simple-coverflow' ); ?></label>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Space Width:', 'simple-coverflow' ); ?></th>
            <td>
                <input id="border-width" name="border" type="input"  value="<?php echo simple_coverflow_get_setting( 'border' ); ?>" /> 
                <label for="coveflow-width"><?php _e( 'Set space width between images', 'simple-coverflow' ); ?></label>
            </td>
        </tr>

        <tr>
            <th><?php _e( 'Frame around images:', 'simple-coverflow' ); ?></th>
            <td>

            
                <input id="frame" name="frame" type="checkbox" <?php checked( simple_coverflow_get_setting( 'frame' ), true ); ?> value="true" /> 

                <label for="coveflow-width"><?php _e( 'Frame around images', 'simple-coverflow' ); ?></label>

                <input id="frame-color" name="frameColor" type="input"  value="<?php echo simple_coverflow_get_setting( 'frameColor' ); ?>" /> 
                <label for="coveflow-width"><?php _e( 'HEX color of frame', 'simple-coverflow' ); ?></label>

            </td>
        </tr>
        
                <tr>
            <th><?php _e( 'Select view:', 'simple-coverflow' ); ?></th>
            <td>
                <?php _e( 'Select default view or hide Arrows', 'simple_coverflow' ); ?>
                <br />
                <select id="cView" name="cView">
                    <option <?php selected( 'default', simple_coverflow_get_setting( 'cView' ) ); ?> value="<?php echo 'default'; ?>"><?php echo 'default' ?></option>
                    <option <?php selected( 'hideArrows', simple_coverflow_get_setting( 'cView' ) ); ?> value="<?php echo 'hideArrows'; ?>"><?php echo 'hide Arrows'; ?></option>

                </select>
            </td>
        </tr>
        
        <tr>
        
        
        
            <th><?php _e( 'Captions:', 'simple-coverflow' ); ?></th>
            <td>
                <input id="caption_remove" name="caption_remove" type="checkbox" <?php checked( simple_coverflow_get_setting( 'caption_remove' ), true ); ?> value="true" /> 
                <label for="caption_remove"><?php _e( 'Do you want to remove captions from your galleries?', 'simple-coverflow' ); ?></label>
                <br />
                <input id="caption_title" name="caption_title" type="checkbox" <?php checked( simple_coverflow_get_setting( 'caption_title' ), true ); ?> value="true" /> 
                <label for="caption_title"><?php _e( 'Use the image title as a caption if there is no caption available?', 'simple-coverflow' ); ?></label>
                <br />
                <input id="caption_link" name="caption_link" type="checkbox" <?php checked( simple_coverflow_get_setting( 'caption_link' ), true ); ?> value="true" /> 
                <label for="caption_link"><?php _e( 'Link captions to the image attachment page?', 'simple-coverflow' ); ?></label>
            </td>
        </tr>

        <tr>
            <th><?php _e( 'Default Image Link:', 'simple-coverflow' ); ?></th>
            <td>
                <?php _e( 'Where or what should your gallery images link to?  Leave blank for the WordPress default.', 'simple_coverflow' ); ?>
                <br />
                <select id="image_link" name="image_link">
                    <?php foreach ( $image_link as $option => $option_name ) { ?>
                        <option <?php selected( $option, simple_coverflow_get_setting( 'image_link' ) ); ?> value="<?php echo $option; ?>"><?php echo $option_name; ?></option>
                        <?php } ?>
                </select>
            </td>
        </tr>






        <!-- <tr>
        <th><?php _e( 'Default Image Size:', 'simple-coverflow' ); ?></th>
        <td>
        <select name="size" id="size">
        <?php foreach ( get_intermediate_image_sizes() as $size ) { ?>
            <option value="<?php echo $size; ?>" <?php selected( $size, simple_coverflow_get_setting( 'size' ) ); ?>><?php echo $size; ?></option>
            <?php } ?>
        </select>
        </td>
        </tr>
        -->
        <tr>
            <th><?php _e( 'Default Order:', 'simple-coverflow' ); ?></th>
            <td>
                <select name="order" id="order">
                    <?php foreach ( array( 'ASC', 'DESC' ) as $order ) { ?>
                        <option value="<?php echo $order; ?>" <?php selected( $order, simple_coverflow_get_setting( 'order' ) ); ?>><?php echo $order; ?></option>
                        <?php } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php _e( 'Default Orderby:', 'simple-coverflow' ); ?></th>
            <td>
                <select name="orderby" id="orderby">
                    <?php $orderby_options = array( 'comment_count' => __( 'Comment Count', 'simple-coverflow' ), 'date' => __( 'Date', 'simple-coverflow' ), 'ID' => __( 'ID', 'simple-coverflow' ), 'menu_order' => __( 'Menu Order', 'simple-coverflow' ), 'none' => __( 'None', 'simple-coverflow' ), 'rand' => __( 'Random', 'simple-coverflow' ), 'title' => __( 'Title', 'simple-coverflow' ) ); ?>
                    <?php foreach ( $orderby_options as $option => $option_name ) { ?>
                        <option value="<?php echo $option; ?>" <?php selected( $option, simple_coverflow_get_setting( 'orderby' ) ); ?>><?php echo $option_name; ?></option>
                        <?php } ?>
                </select>
            </td>
        </tr>
    </table><!-- .form-table --><?php
    }

    /**
    * Displays a settings saved message.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_update_message() { ?>
    <p class="updated fade below-h2" style="padding: 5px 10px;">
        <strong><?php _e( 'Settings saved.', 'simple-coverflow' ); ?></strong>
    </p><?php
    }

    /**
    * Outputs the HTML and calls the meta boxes for the settings page.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_page() {
        global $simple_coverflow;

        $plugin_data = get_plugin_data( SIMPLE_COVERFLOW_DIR . 'simple_coverflow.php' ); ?>

    <div class="wrap">

        <h2><?php _e( 'Simple Coverflow Settings', 'simple-coverflow' ); ?></h2>

        <?php if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) simple_coverflow_settings_update_message(); ?>

        <div id="poststuff">

            <form method="post" action="<?php admin_url( 'themes.php?page=simple-coverflow' ); ?>">

                <?php wp_nonce_field( 'simple-coverflow-settings-page' ); ?>
                <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

                <div class="metabox-holder">
                    <div class="post-box-container column-1 normal"><?php do_meta_boxes( $simple_coverflow->settings_page, 'normal', $plugin_data ); ?></div>
                    <div class="post-box-container column-2 advanced"><?php do_meta_boxes( $simple_coverflow->settings_page, 'advanced', $plugin_data ); ?></div>
                    <div class="post-box-container column-3 side"><?php do_meta_boxes( $simple_coverflow->settings_page, 'side', $plugin_data ); ?></div>
                </div>

                <p class="submit" style="clear: both;">
                    <input type="submit" name="Submit"  class="button-primary" value="<?php _e( 'Update Settings', 'simple-coverflow' ); ?>" />
                    <input type="hidden" name="simple-coverflow-settings-submit" value="true" />
                </p><!-- .submit -->

            </form>

        </div><!-- #poststuff -->

    </div><!-- .wrap --><?php
    }

    /**
    * Loads the scripts needed for the settings page.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_page_enqueue_script() {
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'wp-lists' );
        wp_enqueue_script( 'postbox' );
    }

    /**
    * Loads the stylesheets needed for the settings page.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_page_enqueue_style() {
        wp_enqueue_style( 'simple-coverflow-admin', SIMPLE_COVERFLOW_URL . 'admin.css', false, 0.7, 'screen' );
    }

    /**
    * Loads the metabox toggle JavaScript in the settings page head.
    *
    * @since 0.8
    */
    function simple_coverflow_settings_page_load_scripts() {
        global $simple_coverflow; ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
            postboxes.add_postbox_toggles( '<?php echo $simple_coverflow->settings_page; ?>' );
        });
        //]]>
    </script><?php
    }

?>