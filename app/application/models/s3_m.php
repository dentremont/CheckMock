<?php
class S3_m extends CI_Model {
    
    var $table = 'mockups';
    var $access = 'AKIAJGDM57TE6JH5FIFA';
    var $secret = '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';
    var $bucket = 'checkmockassets';
    var $s3;
    
    function __construct()
    {
        parent::__construct();
        $this->s3 = new S3($this->access,$this->secret);
    }
    


}