<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {
	
	var $data;
	
	public function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		
		if($this->data['account'] = $this->functions->validate()) {
			$this->data['user']	= $this->tank_auth->get_user();
		}
		
		$this->load->library('Chargify');
		
		$this->data['title'] = "Account Settings";
	}
	
	private function output($part)
	{
		$this->data['partial'] = 'account/'.$part;
		$this->load->view('template/main', $this->data);
	}
	
	public function index()
	{
		$this->output('settings');
	}
	
	public function settings()
	{
		$this->output('settings');
	}
	
	public function subscription()
	{
		$sub = $this->chargify->get_subscription($this->data['account']->subscription_id);
		
		$sub->time_left = $this->functions->calculate_time_past(gmdate("Y-m-d\TH:i:s\Z"), $sub->trial_ended_at);

		$this->data['sub'] = $sub;
		
		$this->output('settings');
	}
	
}