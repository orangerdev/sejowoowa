<?php
namespace Sejowoowa\Admin;
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
class Sejowoowa_Woowandroidv2 {

	private $auth_key;
	private $endpoint;
	private $body;
	private $timeout;

	public function __construct() {

		$this->endpoint = 'https://fcm.googleapis.com/fcm/send';
		$this->auth_key = 'AIzaSyCyXH1aC4rWgMQhaJuQLUTDXfWRBgrCZF4';
		$this->timeout 	= 75;
	}

	private function post() {

		return wp_remote_post( $this->endpoint, [
			'headers' => [
				'Content-Type' 	=> 'application/json; charset=utf-8', 
				'Authorization' => 'Bearer '.$this->auth_key
			],
			'method' 			=> 'POST',
			'timeout'			=> $this->timeout,				    
			'body'				=> $this->body
		]);	
	}

	public function do_post( $csid, $message, $number, $type = 'auto' ) {

		$param = array(
		    "to" 	=> $csid,
		    "data" 	=> array(
		        "message"   => strip_tags($message),
		        "number"    => $number,
		        "whatsapp"  => $type // bisnis, auto
		    )
		);

		$this->body = json_encode( $param );
		$response = $this->post();

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return $data;
	}

}