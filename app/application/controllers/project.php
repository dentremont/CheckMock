<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
	
	var $data;
	
	public function __construct() {
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		if($this->data['account'] = $this->functions->validate()) {
			$this->data['user']	= $this->tank_auth->get_user();
		}
		$this->load->library('form_validation');
		
		$this->data['title'] = "Projects";
	}
	
	private function output($part)
	{
		$this->data['partial'] = 'project/'.$part;
		$this->load->view('template/main', $this->data);
	}
	
	public function index()
	{
		$this->output();
	}
	
	public function view($pid)
	{
		if($this->input->post('archive')) {
			$this->archive($pid);
		}
		
		$this->data['project'] = $this->Project_m->get_project($pid);
		if($this->data['project'] == NULL) {
			redirect('dashboard');
		}
		$this->data['mockups'] = $this->Mockup_m->get_project_mockups($pid);
		
		$this->output('project');
	}
	
	public function create()
	{		
		if($this->input->post('create')) {
			/* Validation */
			$this->form_validation->set_rules('project_name', 'Project Name', 'required');
			
			if ($this->form_validation->run() == FALSE) {
			} else {
				/* Insert project */
				$project = array(
					'name'		=> $this->input->post('project_name'),
					'due_date'	=> $this->input->post('due_date')
				);
				if($this->Project_m->insert($project)) {
					$this->session->set_flashdata('message', 'Project <em>'.$project['name'].'</em> added.');
					redirect('/');
				} else {
					$this->session->set_flashdata('message', 'Ooops! There was a problem adding your project. Please try again.');
				}
			}
		}
		$this->output('project_new');
	}
	
	private function archive($pid)
	{
		$bool = true;
		if($this->input->post('archive') == 'Unarchive') {
			$bool = false;
		}
		$this->Project_m->set_archive($pid, $bool);
	}
	
	public function delete()
	{
		$this->output();
	}
	
}