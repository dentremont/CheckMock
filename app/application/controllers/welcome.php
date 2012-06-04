<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('tank_auth');
	}

	function index()
	{
		$s3 = new S3('AKIAJGDM57TE6JH5FIFA','BBId77ypzmQvizNxC72PAoew+URFESB5HNSGboMH');
		$bucket = 'checkmockassets';
		
		if(isset($_POST['submit'])) {
			//retreive post variables
			$fileName = $_FILES['image']['name'];
			$uploadFile = $_FILES['image']['tmp_name']; 

			/*if($s3->putObjectFile($uploadFile, $bucket, baseName($fileName), S3::ACL_PUBLIC_READ)){
			 echo "File Uploaded!";
			}*/
			
			$this->functions->create_thumbnail($_FILES['image']['tmp_name']);
			print_r($_FILES);
		}
	    
	    echo form_open_multipart();
	    echo form_upload('image');
	    echo form_submit('submit','Upload');
	    echo form_close();
	}
}
