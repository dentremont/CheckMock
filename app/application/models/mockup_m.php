<?php
class Mockup_m extends CI_Model {
    
    var $table = 'mockups';
    var $aid;
    var $access = 'AKIAJGDM57TE6JH5FIFA';
    var $secret = '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';
    var $bucket = 'checkmockassets';
    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->aid = $this->session->userdata('account');
    }
    
    function get_project_mockups($pid)
    {
        $query = $this->db->get_where($this->table, array('pid' => $pid));
        return $query->result();
    }
    
    function get_mockup($id)
    {
    	$query = $this->db->get_where($this->table, array('nid' => $id), 1);
    	return array_shift($query->result());
    }

    function insert($file)
    {       
        $data = array(
        	'name' 				=> $this->input->post('mockup_name'),
        	'pid'				=> $this->input->post('project'),
        	'full_path'			=> 'http://s3.amazonaws.com/'.$file['main_s3'],
        	'thumbnail_path'	=> 'http://s3.amazonaws.com/'.$file['thumb_s3'],
        	'width'				=> $file['thumbnail']->width,
        	'height'			=> $file['thumbnail']->height,
        	'bg_color'			=> '#ffffff',
        	'bg_image'			=> '',
        	'bg_repeat'			=> '',
        	'margin'			=> $this->input->post('margin'),
        	'shadow'			=> '0',
        	'size'				=> $file['main']['file_size'],
        	'file_type'			=> $file['main']['image_type'],
        	'weight'			=> 0,
        	'created'			=> date('Y-m-d H:i:s')
        );

        if ($this->db->insert($this->table, $data)) {      	
        	return $this->db->insert_id();
        }
        return false;
    }
    
    function create_thumbnail($path)
	{
    	$x = 200;
    	$y = 200;
    	
    	$this->load->library('image_moo');
    	$thumbnail = $this->image_moo->load($path)->resize_crop($x,$y)->save_pa(null,'_thumb',true);
    	
    	$parts = pathinfo($path);
    	$new = str_replace('.'.$parts['extension'],'_thumb.'.$parts['extension'], $parts['basename']);
    	
    	$thumbnail->full_path = $parts['dirname'].'/'.$new;
    	$thumbnail->basename = $new;
    	
    	return $thumbnail;
    }
    
    function upload_s3($path, $name, $type)
    {
    	/* Start S3 */
    	$s3 = new S3($this->access,$this->secret);
    	
    	/* Organize File */
    	if(strlen($path) > 1 && strlen($name) > 1) {
	    	$upload = $s3->putObjectFile($path, $this->bucket, baseName($name), S3::ACL_PUBLIC_READ, $metaHeaders = array(), $type);
    	}

    	if( ! $upload) {
    		return false;
    	}
    	
    	return $this->bucket.'/'. baseName($name);
    }
    
    function upload_temp($name)
    {
    	$config['upload_path'] = './uploads/';
    	$config['file_name'] = $name;
    	$config['allowed_types'] = $this->functions->get_val('allowed_uploads');
    	$config['max_size'] = $this->functions->get_val('max_upload');
    	$this->load->library('upload', $config);					
    	
    	if( ! $this->upload->do_upload('image') ) {
    		return false;
    	} else {
    		return $this->upload->data();
    	}
    }
    
    function delete_temp($files) 
    {
    	if(count($files) > 0) {
    		foreach ($files as $file) {
    			if(!unlink($file)) {
    				return false;
    			}
    		}
    	}
    	return true;
    }
    
    function set_status($nid, $status)
    {
    	$data = array(
    		'status' => $status
    	);
    	$this->db->update($this->table, $data, array('nid'=>$nid));
    }
    
    function delete($node)
    {
    	
    	/*$query = $this->db->delete($this->table, array('nid',$node->nid));
    	if ($this->db->affected_rows() > 0) {
    		return TRUE;
    	}
    	return FALSE;*/
    }

}