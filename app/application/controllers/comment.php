<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
	}

	function add()
	{
		if($this->input->post('comment')) {
			$this->form_validation->set_rules('comment_text', 'Comment', 'required');
			
			if ($this->form_validation->run() == FALSE) {
			} else {
			
			}
		}
	}
}

