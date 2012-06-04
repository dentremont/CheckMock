<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	var $data;
	
	public function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		
		if($this->data['account'] = $this->functions->validate()) {
			$this->data['user']	= $this->tank_auth->get_user();
		}
		
		$this->load->library('chargify');
		
		$this->data['title'] = "User Settings";
	}
	
	private function output($part)
	{
		$this->data['partial'] = 'user/'.$part;
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
		/* Chargify */
		$this->output('settings');
	}
}