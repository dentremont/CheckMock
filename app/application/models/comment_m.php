<?php
class Comment_m extends CI_Model {
    
    var $table = 'comments';
    var $aid;
    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_comments($nid)
    {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('users', 'comments.uid = users.id');
		$this->db->where('nid', $nid);
		
		$query = $this->db->get();
        return $query->result();
    }
    
    function get_comment($cid)
    {
    	$query = $this->db->get_where($this->table, array('cid' => $cid), 1);
    	return array_shift($query->result());
    }

    function insert()
    {       
		$data = array(
			'uid'			=> $this->input->post('user'),
			'nid'			=> $this->input->post('nid'),
			'comment'		=> $this->input->post('comment_text')
		);
        if ($this->db->insert($this->table, $data)) {      	
        	return $this->db->insert_id();
        }

        return NULL;
    }

}