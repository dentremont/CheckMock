<?php
class Project_m extends CI_Model {
    
    var $table = 'projects';
    var $aid;
    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->aid = $this->session->userdata('account');
    }
    
    function get_projects()
    {
		$query = $this->db->get_where($this->table, array('aid' => $this->aid,'archived' => false), 10);
		return $query->result();
    }

    function get_project($id)
    {
    	$query = $this->db->get_where($this->table, array('pid' => $id), 1);
    	return array_shift($query->result());
    }

    function insert($data)
    {
        $data['aid'] = $this->aid;
        $data['created'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $data)) {
        	$pid = $this->db->insert_id();
        	return $pid;
        }

        return NULL;
    }
    
    function set_archive($pid, $bool = true) {
    	$this->db->where('pid',$pid);
    	$this->db->update($this->table, array('archived'=>$bool));
    	if ($this->db->affected_rows() > 0) {
    		return true;
    	}
    	return false;
    }


}