<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ACL_Woo_Onepage_Settings {

	/**
	 * The single instance of WOOSC_Plugin_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'acl_woosc_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
        /**
         * Have to include all others page here .
         */
        //Info page
        require_once( 'woosc-info-page.php');
        //Add-on Page
        require_once( 'woosc-addons.php');
		// Add settings link to plugins page
		//add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings (){
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
        add_menu_page('Woo OnePage Checkout Shop', 'OnePage Shop', 'manage_options', 'woocommerce-one-page-checkout-shop', '', 'dashicons-list-view', 25);
        add_submenu_page(
            'woocommerce-one-page-checkout-shop',
            __( 'Woo OnePage Checkout Shop Settings', 'woocommerce-one-page-checkout-shop' ),
            __( 'Settings', 'woocommerce-one-page-checkout-shop' ),
            'manage_options',
            'woocommerce-one-page-checkout-shop',
            array( $this, 'settings_page' )
        );
        add_submenu_page( 'woocommerce-one-page-checkout-shop', 'Templates', 'Templates', 'import', 'admin.php?page=woocommerce-one-page-checkout-shop&tab=woosc_ui');
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'woocommerce-one-page-checkout-shop' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	public function settings_fields () {

		$settings['woosc_general'] = array(
			'title'					=> __( 'General', 'woocommerce-one-page-checkout-shop' ),
			'description'			=> __( 'All General setting for Woo OnePage Checkout Shop.', 'woocommerce-one-page-checkout-shop' ),
			'fields'				=> array(
                array(
                    'id' 			=> 'product_per_page',
                    'label'			=> __( 'Product Per Page'  , 'woocommerce-one-page-checkout-shop' ),
                    'description'	=> __( 'To display products per on the shop page.Example : 5,6,8,10 etc', 'woocommerce-one-page-checkout-shop' ),
                    'type'			=> 'text',
                    'default'		=> '10',
                    'placeholder'	=> __('Product Per Page', 'woocommerce-one-page-checkout-shop' )
                ),
                array(
                    'id' 			=> 'show_search_form',
                    'label'			=> __( 'Show Search Box', 'woocommerce-one-page-checkout-shop' ),
                    'description'	=> __( 'Enable to show search box on the top of the product list/grid (it can be override by shortcode parameter)', 'woocommerce-one-page-checkout-shop' ),
                    'type'			=> 'checkbox',
                    'default'		=> 'on'
                ),
                array(
                    'id' 			=> 'show_loarmore',
                    'label'			=> __( 'Show Load More Button', 'woocommerce-one-page-checkout-shop' ),
                    'description'	=> __( 'Enable to show load more button at the bottom of the product list/grid (it can be override by shortcode parameter)', 'woocommerce-one-page-checkout-shop' ),
                    'type'			=> 'checkbox',
                    'default'		=> 'on'
                )
			    /*array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'This is a standard text field.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'woocommerce-one-page-checkout-shop' )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'This is a standard password field.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'woocommerce-one-page-checkout-shop' )
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'woocommerce-one-page-checkout-shop' )
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'This is a standard text area.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'woocommerce-one-page-checkout-shop' )
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'A standard select box.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress'
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'A standard set of radio buttons.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman'
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' )
				)*/
			)
		);
        $settings['woosc_ui'] = array(
            'title'					=> __( 'Templates', 'woocommerce-one-page-checkout-shop' ),
            'description'			=> __( 'Select below template to display the products as default template', 'woocommerce-one-page-checkout-shop' ),
            'fields'				=> array(
                array(
                    'id' 			=> 'templates',
                    'label'			=> __( 'Templates', 'woocommerce-one-page-checkout-shop' ),
                    'description'	=> __( '', 'woocommerce-one-page-checkout-shop' ),
                    'type'			=> 'template',
                    'options'		=> array( '1' => 'Template 01', '2' => 'Template 02',),
                    'default'		=> '1'
                ),

            ),
        );
		$settings['woosc_custom_style'] = array(
            'title'					=> __( 'Custom Style', 'woocommerce-one-page-checkout-shop' ),
            'description'			=> __( 'Design your store by overriding default with your own style (CSS).', 'woocommerce-one-page-checkout-shop' ),
            'fields'				=> array(
				array(
					'id' 			=> 'custom_css',
					'label'			=> __( 'Custom CSS' , 'woocommerce-one-page-checkout-shop' ),
					'description'	=> __( '', 'woocommerce-one-page-checkout-shop' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( '', 'woocommerce-one-page-checkout-shop' )
				),

            ),
        );

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Woo OnePage Checkout Shop Settings' , 'woocommerce-one-page-checkout-shop' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}
        if ( isset( $_GET['tab'] ) && 'woosc_store' == $_GET['tab'] ) {
            $html .= '<img src="'.ACL_WOOSC_IMG_URL.'drop-shipping-pro.png" alt="Drop Shipping Settings Pro Features">' . "\n";
        }else {
            $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

            // Get settings fields
            ob_start();
            settings_fields($this->parent->_token . '_settings');
            do_settings_sections($this->parent->_token . '_settings');
            $html .= ob_get_clean();

            $html .= '<p class="submit">' . "\n";
            $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
            $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'woocommerce-one-page-checkout-shop')) . '" />' . "\n";
            $html .= '</p>' . "\n";
            $html .= '</form>' . "\n";
            $html .= '</div>' . "\n";
        }

		echo $html;
	}

	/**
	 * Main WOOSC_Plugin_Settings Instance
	 *
	 * Ensures only one instance of WOOSC_Plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WOOSC_Plugin()
	 * @return Main WOOSC_Plugin_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}

