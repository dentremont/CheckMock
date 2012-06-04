<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	var $data;
	
	public function __construct() {
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		
		if($this->data['account'] = $this->functions->validate()) {
			$this->data['user']	= $this->tank_auth->get_user();
		}
		
		$this->data['title'] = "Dashboard View";
	}
	
	private function output()
	{
		$this->data['partial'] = 'dashboard/dashboard';
		$this->load->view('template/main', $this->data);
	}
	
	public function index()
	{
		$object = $this->Account_m->get_account_size(1);
		$object = $this->Account_m->get_num_projects(1);
		
		$projects = $this->Project_m->get_projects();
		foreach($projects as $p){
			$p->nodes = $this->Mockup_m->get_project_mockups($p->pid);
		}
		//print_r($projects);
		$this->data['projects'] = $projects;
		$this->output();
	}
}