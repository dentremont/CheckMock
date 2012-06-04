<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mockup extends CI_Controller {
	
	var $data;
	
	public function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		
		if($this->data['account'] = $this->functions->validate()) {
			$this->data['user']	= $this->tank_auth->get_user();
		}
		$this->load->library('form_validation');
		
		$this->data['title'] = "Mockups";
	}
	
	private function output($part)
	{
		$this->data['partial'] = 'mockup/'.$part;
		$this->load->view('template/main', $this->data);
	}
	
	public function index()
	{
		$mockup['file_ext'] = '.jpeg';
		$mockup['path'] = '/home/mock/public_html/app/uploads/1/13/mr-westin.jpeg';
		echo str_replace($mockup['file_ext'],'_thumb'.$mockup['file_ext'], $mockup['path']);
		//$this->output();
	}
	
	public function view($mid)
	{
		if($this->input->post('comment')) {
			$this->comment();
		}
		if($this->input->post('delete')) {
			if($this->delete()) {
				redirect('/');
			}
		}		
		if($this->input->post('approve')) {
			$this->Mockup_m->set_status($mid,1);
		} elseif($this->input->post('unapprove')) {
			$this->Mockup_m->set_status($mid,0);
		}
		
		$this->data['mockup'] = $this->Mockup_m->get_mockup($mid);
		$this->data['comments'] = $this->Comment_m->get_comments($mid);
		$this->output('mockup');
	}
	
	private function comment()
	{
		$nid = $this->input->post('nid');
		
		$this->form_validation->set_rules('comment_text', 'Comment', 'required');
		
		if ($this->form_validation->run() == FALSE) {
		} else {
			$this->Comment_m->insert();
		}
	}
	
	public function create()
	{
		$projects = $this->Project_m->get_projects();
		$select = array();
		foreach($projects as $p) { $select[$p->pid] = $p->name;}
		$this->data['projects'] = $select;
		
		if($this->input->post('add')) {
			/* --- Validation --- */
			$this->form_validation->set_rules('mockup_name', 'Mockup Name', 'required');
			$this->form_validation->set_rules('project', 'Project', 'required');
			
			if ($this->form_validation->run() == FALSE) {
			} else {
				$mockup=false;
				$dir = './uploads/'.$this->data['dom']->aid.'/'.$this->input->post('project');
				if($_FILES['image']['size'] > 0) {
					
					/* --- Upload Image --- */
					$config['upload_path'] = $dir;
					$config['allowed_types'] = $this->functions->get_val('allowed_uploads');
					$config['max_size'] = $this->functions->get_val('max_upload');
					$this->load->library('upload', $config);					
					if( ! $this->upload->do_upload('image') ) {
						$this->session->set_flashdata('message', $this->upload->display_errors());
					} else {
						$mockup = $this->upload->data();
						$start = stripos($mockup['full_path'], "uploads/");
						$mockup['path'] = substr($mockup['full_path'], $start-1);
						
						/* --- Make thumbnail --- */
						$thumb = $this->functions->create_thumbnail($mockup['full_path']);
						if (!$thumb) {
							$this->session->set_flashdata('message', $this->upload->display_errors());
						}
						$mockup['thumbnail'] = str_replace($mockup['file_ext'],'_thumb'.$mockup['file_ext'], $mockup['path']);
						if($this->Mockup_m->insert($mockup)) {
							$this->session->set_flashdata('message', 'Mockup <em>'.$this->input->post('name').'</em> added.');
							redirect('project/view/'.$this->input->post('project'));
						} else {
							$this->session->set_flashdata('message', 'Ooops! There was a problem adding your mockup. Please try again.');
						}
					}
				} else {
					$this->session->set_flashdata('message', 'You must select an image to upload.');
				}
			}
		}		
		
		$this->output('mockup_new');
	}
	
	public function upload()
	{
		$projects = $this->Project_m->get_projects();
		$select = array();
		foreach($projects as $p) { $select[$p->pid] = $p->name;}
		$this->data['projects'] = $select;
		
		if($this->input->post('add')) {
			/* --- Validation --- */
			$this->form_validation->set_rules('mockup_name', 'Mockup Name', 'required');
			$this->form_validation->set_rules('project', 'Project', 'required');
			
			if ($this->form_validation->run() == FALSE) {
			} else {
				// Do Upload
				if($_FILES['image']['size'] > 0) {
					$upload = array();
					$name = time();
					
					// Upload main image
					$upload['main'] = $this->Mockup_m->upload_temp($name);
					
					// Create Thumbnail
					$upload['thumbnail'] = $this->Mockup_m->create_thumbnail($upload['main']['full_path']);
					
					// Upload to S3
					$upload['main_s3'] = $this->Mockup_m->upload_s3($upload['main']['full_path'], $upload['main']['orig_name'], $upload['main']['file_type']);
					$upload['thumb_s3'] = $this->Mockup_m->upload_s3($upload['thumbnail']->full_path, $upload['thumbnail']->basename, $upload['main']['file_type']);
					
					// Delete Temp
					$files = array($upload['main']['full_path'],$upload['thumbnail']->full_path);
					$this->Mockup_m->delete_temp($files);
					
					// Insert to DB
					if($upload['main_s3'] && $upload['thumb_s3']) {
						$this->Mockup_m->insert($upload);
					}
				}
			}
		}
		
		redirect('/');
	}
	
	public function test()
	{
		$files = array('/home/mock/public_html/app/uploads/social_01.jpg','/home/mock/public_html/app/uploads/social_01_thumb.jpg');
		$this->Mockup_m->delete_temp($files);
	}
	
	public function delete($mid)
	{
		$this->output();
	}
	
	public function share($mid)
	{
		$this->output();
	}
}