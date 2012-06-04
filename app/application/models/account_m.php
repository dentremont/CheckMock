<?php
class Account_m extends CI_Model {
    
    var $table = 'accounts';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_num_projects($aid)
    {
        $this->db->select('COUNT(pid) AS p_count')->from('projects')->where('aid',$aid);
        $query = $this->db->get();
        
        return $query->result();
    }
    
    function get_account_size($aid)
    {
    	$this->db->select('SUM(size) AS size');
    	$this->db->from('mockups');
    	$this->db->join('projects','mockups.pid = projects.pid');
    	$this->db->where('projects.aid',$aid);
    	$query = $this->db->get();
    	
    	return $query->result();
    }
    
    function create_account($domain, $title, $owner)
    {
    	$data = array(
    		'name' 			=> $domain,
    		'title'			=> $title,
    		'owner'			=> $owner,
    		'membership'	=> 1
    	);
    	if ($this->db->insert($this->table, $data)) {      	
        	return $this->db->insert_id();
        }

        return NULL;
    }

}