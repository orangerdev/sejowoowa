<?php

namespace Sejowoowa\Admin;

use Sejowoowa\Admin\Setting;
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
class Service {

	private $auth_key;

	private $endpoint;

	private $body;

	private $timeout;

	private $selected_service;

	private $selected_key;

	public function __construct( $service_name ) {

		if( $service_name == 'woowaserver' ) {

			$this->set_woowa_server_params();

		} elseif ( $service_name == 'woowandroidv2' ) {

			$this->set_woowandroidv2_params();

		}

		$this->selected_service = $service_name;

		$this->timeout 	= 360;
	}

	public function set_woowa_server_params() {

		$this->endpoint 	= 'http://116.203.191.58/api/async_send_message';

		$sjw_setting 		= new Setting();

		$this->selected_key = $sjw_setting->get_option_value('partner_key');
	}

	public function set_woowandroidv2_params() {

		$this->endpoint 	= 'https://fcm.googleapis.com/fcm/send';
		$this->auth_key 	= 'AIzaSyCyXH1aC4rWgMQhaJuQLUTDXfWRBgrCZF4';

		$sjw_setting 		= new Setting();

		$this->selected_key = $sjw_setting->get_option_value('csid');
	}

	private function post() {

		if( $this->selected_service == 'woowaserver' ) {

			$post = wp_remote_request( $this->endpoint, [
				'headers' => [
					'Content-Type' 		=> 'application/json', 
					'Content-Length' 	=> strlen( $this->body )
				],
				'method' 			=> 'POST',
				'timeout'			=> $this->timeout,
				'body'				=> $this->body,
				'sslverify'			=> false
			]);

		} elseif ( $this->selected_service == 'woowandroidv2' ) {
			
			$post = wp_remote_post( $this->endpoint, [
				'headers' => [
					'Content-Type' 		=> 'application/json; charset=utf-8', 
					'Authorization' 	=> 'Bearer '.$this->auth_key
				],
				'method' 			=> 'POST',
				'timeout'			=> $this->timeout,
				'body'				=> $this->body
			]);

		}

		return $post;
	}

	public function do_post( $message, $number, $type = 'auto' ) {

		if( $this->selected_service == 'woowaserver' ) {

			$param = array(
			    "phone_no"		=> $number,
			    "key"			=> $this->selected_key,
			    "message"		=> strip_tags( $message )
			);

		} elseif( $this->selected_service == 'woowandroidv2' ) {

			$param = array(
			    "to" 	=> $this->selected_key,
			    "data" 	=> array(
			        "message"   => strip_tags( $message ),
			        "number"    => $number,
			        "whatsapp"  => $type // bisnis, auto
			    )
			);

		} 
		
		// error_log( __METHOD__.': param ' . print_r( $param, true ) ); //debug

		$this->body = json_encode( $param );

		$response = $this->post();

		// error_log( __METHOD__.': response ' . print_r( $response, true ) ); //debug

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		error_log( __METHOD__.': data ' . print_r( $data, true ) ); //debug

		return $data;
	}

}