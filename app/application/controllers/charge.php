<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Charge extends CI_Controller
{
	
	var $data;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('Chargify');
	}

	function index()
	{
		$this->data['products'] = $this->chargify->get_products();
		$this->output();
	}
	
	function subscribe()
	{
		if($this->input->post('submit')) {
			// Create Subscription
			$subscription = array(
				'product_id' 			=> $this->input->post('product'),
				'customer_attributes' 	=> array(
					'first_name' 		=> $this->input->post('firstName'),
					'last_name' 		=> $this->input->post('lastName'),
					'organization' 		=> $this->input->post('company'),
					'address' 			=> $this->input->post('address'),
					'address_2' 		=> $this->input->post('address2'),
					'city' 				=> $this->input->post('city'),
					'state' 			=> $this->input->post('state'),
					'zip' 				=> $this->input->post('zip'),
					'country' 			=> $this->input->post('country'),
					'email' 			=> $this->input->post('email'),
					'phone' 			=> '0',
					'reference' 		=> '0'
				),
				'credit_card_attributes'=> array(
					'first_name' 		=> $this->input->post('firstName'),
					'last_name' 		=> $this->input->post('lastName'),
					'billing_address' 	=> $this->input->post('address'),
					'billing_address_2' => $this->input->post('address2'),
					'billing_city' 		=> $this->input->post('city'),
					'billing_state' 	=> $this->input->post('state'),
					'billing_zip' 		=> $this->input->post('zip'),
					'billing_country' 	=> $this->input->post('country'),
					'full_number' 		=> $this->input->post('ccNumber'),
					'expiration_month' 	=> $this->input->post('expMonth'),
					'expiration_year' 	=> $this->input->post('expYear'),
					'cvv' 				=> $this->input->post('cvv')
				)
			);
			$plan = $this->chargify->create_subscription($subscription);
			print_r($plan);
		}
	}
	
	private function output()
	{
		//$this->data['partial'] = 'charge/new';
		//$this->load->view('template/main', $this->data);
		$this->load->view('charge/new', $this->data);
	}
}
