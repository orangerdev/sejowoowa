<?php

namespace Sejowoowa\Admin;

use Sejowoowa\Admin\Setting;
use Sejowoowa\Admin\Service;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woow-wa.com
 * @since      1.0.0
 *
 * @package    Sejowoowa
 * @subpackage Sejowoowa/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejowoowa
 * @subpackage Sejowoowa/admin
 * @author     Woo-Wa and Sejoli <orangerdigiart@gmail.com>
 */
class Commission {

	private $option_name;
	private $option_page;
	private $section_name;
	private $option_group_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->option_name 			= 'sejowoowa_wa_commission_options';
		$this->option_page 			= 'wa_commission_page';
		$this->section_name 		= 'wa_commission_section';
		$this->option_group_name 	= 'sejowoowa_wa_commission_options';
	}

	public function get_option_fields() {

		$fields = array();

		// Active field
		$fields[] = array(
			'name' 	=> 'wa_commission_active', 			//Name
			'label' => 'Aktifkan/Nonaktifkan', 			//Label
			'desc' 	=> 'Aktifkan notifikasi ini?', 		//Description
			'type' 	=> 'checkbox', 						//Input type
		);

		// Message field
		$fields[] = array(
			'name' 	=> 'wa_commission_message', 		//Name
			'label' => 'Message', 						//Label
			'desc' 	=> 'Shortcode yang tersedia: <code>{site_title}</code>, <code>{site_url}</code>, <code>{order_date}</code>, <code>{order_number}</code>, <code>{total_commission}</code>, <code>{detail_commission}</code>', //Description
			'type' 	=> 'textarea', 						//Input type
		);

		return $fields;
	}

	public function set_default_option_fields() {

		$default = array(
        	'wa_commission_active' 		=> true,
    		'wa_commission_message' 	=> 'Alhamdulillah, ada komisi affiliasi untuk anda dari {site_title} ({site_url}) order #{order_number} tanggal {order_date} total komisi sebesar {total_commission} dengan rincian {detail_commission}'
	    );

	    return $default;
	}

	public function is_active() {

		$field_name = 'wa_commission_active';
	    $get_options = get_option( $this->option_name );

	    return array_key_exists($field_name,$get_options) ? true : false ;
	}

	public function get_option_value( $field_name ) {

		$get_options = get_option( $this->option_name );

		if( array_key_exists( $field_name, $get_options ) ) {
			return $get_options[ $field_name ];
		}

		return false;
	}

	/* ------------------------------------------------------------------------ *
	 * Setting Registration
	 * ------------------------------------------------------------------------ */

	/**
	 * Initialize whatsapp commission options
	 */
	public function initialize_options() {

	    // Register a section
	    add_settings_section(
	        $this->section_name,  						// ID used to identify this section and with which to register options
	        'Komisi Affiliasi',           				// Title to be displayed on the administration page
	        array($this,'section_callback'),  			// Callback used to render the description of the section
	        $this->option_page    						// Page on which to add this section of options
	    );

	    foreach ($this->get_option_fields() as $option) {

	    	// Create the settings
		    add_settings_field( 
		        $option['name'],       								// ID used to identify the field throughout the theme
		        $option['label'],   								// The label to the left of the option interface element
		        array( $this, 'field_callback' ),		// The name of the function responsible for rendering the option interface
		        $this->option_page,    					// The page on which this option will be displayed
		        $this->section_name,					// The name of the section to which this field belongs
		    	$option									// Arguments pass to callback function
		    );
	    }

	    // Register the fields with WordPress 
	    register_setting(
	        $this->option_group_name,     		// A settings group name
	        $this->option_name      			// The name of an option to sanitize and save
	    );
	}

	/* ------------------------------------------------------------------------ *
	 * Section Callbacks
	 * ------------------------------------------------------------------------ */

	public function section_callback() {

		//Set default values
		if ( false == get_option( $this->option_name ) ) {
		    add_option( $this->option_name, $this->set_default_option_fields() );
		}

		$description = '<p>Notifikasi yang berisi data komisi untuk affiliasi.</p>';
		echo $description;
	}

	/* ------------------------------------------------------------------------ *
	 * Field Callbacks
	 * ------------------------------------------------------------------------ */

	public function field_callback( $args ) {

		$field_name 	= $args['name'];
		$label_text 	= $args['label'];
		$description 	= $args['desc'];
		$type 			= $args['type'];
		$option_value 	= $this->get_option_value( $field_name );
		$html 			= '';

		if( $type == 'checkbox' ) {
		    
		    $html .= '<input type="checkbox" name="' . $this->option_name . '[' . $field_name . ']" value="1"' . checked( 1, $option_value, false ) . '/>';
		    $html .= '<label for="' . $field_name . '">' . $description . '</label>';
		
		} elseif ( $type == 'textarea' ) {
		    
		    $html .= '<textarea id="' . $field_name . '" name="' . $this->option_name . '[' . $field_name . ']" rows="10" cols="50">' . sanitize_text_field($option_value) .'</textarea>';
		    $html .= '<p class="description">' . $description . '</p>';

		}
		
	    echo $html;
	}

	/* ------------------------------------------------------------------------ *
	 * Hooks
	 * ------------------------------------------------------------------------ */

	public function send_message( \WC_Order $order, array $commission_data ) {

		if( ! $this->is_active() ) { return; }
		error_log( __METHOD__.': is_active '.$this->is_active() ); //debug

	    // Generate proper data
		$total_commission 			= 0;
		$commission_string_array 	= array();

		if( count( $commission_data ) > 0 ) {

			foreach ( $commission_data as $data ) {
				$commission_data = $data['commissions'];
			}

			foreach ( $commission_data as $commission ) {
				
				$item = $commission['item'];

				// Total Commission
				$total_commission += $commission['value'];
				$detail_commission = '';

				// Qty
				$qty          = $item->get_quantity();
				$refunded_qty = $order->get_qty_refunded_for_item( $item->get_id() );

				if ( $refunded_qty ) {
					$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
				} else {
					$qty_display = esc_html( $qty );
				}

				$detail_commission .= wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
				$detail_commission .= ' ';

				// Product name
				$detail_commission .= wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
				$detail_commission .= ' ';

				// Commission value
				$detail_commission .= 'komisi ';
				$detail_commission .= wp_kses_post( wc_price( $commission['value'] ) );
				$detail_commission .= ' ';

				// Commission tier
				$detail_commission .= 'tier ';
				$detail_commission .= wp_kses_post( $commission['tier'] );
				$detail_commission .= ' ';

				// Store in array
				$commission_string_array[] = $detail_commission;
			}
		}

	    // Get message content
	    $message_content = $this->get_option_value( 'wa_commission_message' );

	    $find = array(
	    	'{site_title}', 
	    	'{site_url}',
	    	'{order_date}',
	    	'{order_number}',
	    	'{total_commission}',
	    	'{detail_commission}'
	    );

	    $replace = array(
	    	get_bloginfo('name'),
	    	get_bloginfo('url'),
	    	$order->get_date_created()->format ('d-m-Y'),
	    	$order->get_order_number(),
	    	strip_tags( wc_price( $total_commission ) ),
	    	"\n\n".implode("\n", $commission_string_array)
	   	);

	    // Replace shortcode placeholders
	    $replaced_message 	= str_replace( $find, $replace, $message_content );

	    // Get main settings
	    $sjw_setting 		= new Setting();
	    $service 			= $sjw_setting->get_option_value('woowa_service');
	    $csid 				= $sjw_setting->get_option_value('csid');
	    $phone_number 		= $sjw_setting->get_option_value('admin_phone');
	    
	    // Create class
		$api 				= new Service( $service );
		$result 			= $api->do_post( $csid, $replaced_message, $phone_number );
	    return $result;
	}

}