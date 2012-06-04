<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Functions class
 */
class Functions
{
	var $CI;

	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function validDomain()
	{
		$query = $this->CI->db->select('*'); 
		$query = $this->CI->db->from('accounts');
		$query = $this->CI->db->where('name', SUBDOMAIN);
		$query = $this->CI->db->join('memberships', 'accounts.membership = memberships.mid');
		$query = $this->CI->db->get();
		
		if( ! $query->num_rows() > 0) {
			return null;
		}
		return array_shift($query->result());
	}
	
	function validate() 
	{
		$domain = $this->validDomain();
		if($domain) {
			if (!$this->CI->tank_auth->is_logged_in()) {
				redirect('/auth/login/');
			}
		} else {
			redirect('/error/invalid_domain');
		}
		/*if($this->CI->tank_auth->is_logged_in()) {
			$user = $this->CI->tank_auth->get_user();
			if($domain->aid !== $user['account']) {
				redirect('/error/wrong_account');
			}
		}*/
		return $domain;
	}
		
	function get_val($var)
	{
		$query = $this->CI->db->get_where('settings', array('name'=>$var));
		$result = $query->result();
		return $result[0]->value;
	}
	
	function time_ago($stamp)
	{
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   	$lengths = array("60","60","24","7","4.35","12","10");
	
	   	$now = time();
	
	       $difference     = $now - strtotime($stamp);
	       $tense         = "ago";
	
	   	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	       $difference /= $lengths[$j];
	   	}
	
	   	$difference = round($difference);
	
	   	if($difference != 1) {
	       $periods[$j].= "s";
	   	}
	
	   	return "$difference $periods[$j] ago";
	}
	
	function scrub_input($str)
	{
		/* Remove any dangerous javascript in elements */
			// realign javascript href to onclick 
			$str = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "onclick=' $2 '", $str); 
			
			//remove javascript from tags 
			while( preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str)) 
			$str = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str); 
			             
			// dump expressions from contibuted content 
			if(0) $str = preg_replace("/:expression\(.*?((?>[^(.*?)]+)|(?R)).*?\)\)/i", "", $str); 
			
			while( preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str)) 
			$str = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str); 
			        
			// remove all on* events    
			while( preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $str) ) 
			$str = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $str); 
		
		/* Cleanup */
			// Clean out '\'s that strip_text uses around href's
			$str = str_replace( '\\', '', $str);
			               
            // Strip out all paragraphs with attributes
            $str = preg_replace( "/<p[^>]*>/", '<p>', $str );
         
        /* Remove MS word paste stuff */
        	$search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u'); 
	        $replace = array('\'', '\'', '"', '"', '-'); 
	        $str = preg_replace($search, $replace, $str); 
	        
	        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	        
	        if(mb_stripos($str, '/*') !== FALSE){ 
                $str = mb_eregi_replace('#/\*.*?\*/#s', '', $str, 'm'); 
            }
            
            // remove whitespaces
            $str = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $str); 
            //strip out inline css and simplify style tags 
            $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu'); 
            $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>'); 
            $str = preg_replace($search, $replace, $str); 
       
       /* Remove unwanted tags */
       		$allowed_tags = "<b><i><sup><sub><em><strong><u><br><a>";
       		$str = strip_tags($str, $allowed_tags);
       		
       return $str;
		
	}
	
	function ellipsis($string, $length)
	{
		$end='...';
	  	if (strlen($string) > $length)	{
		    $length -=  strlen($end);
		    $string  = substr($string, 0, $length);
		    $string .= $end;
		}
	  	return $string;
	}
	
	function create_thumbnail($path)
	{
		$config['image_library'] = 'gd2';
		$config['source_image'] = $path;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 200;
		$config['height'] = 200;
		$this->CI->load->library('image_lib', $config);
		
		return $this->CI->image_lib->resize();
	}
	
	function calculate_time_past($start_time, $end_time, $format = "d") { 
	    $time_span = strtotime($end_time) - strtotime($start_time); 
	    if ($format == "s") { // is default format so dynamically calculate date format 
	        if ($time_span > 60) { $format = "i:s"; } 
	        if ($time_span > 3600) { $format = "H:i:s"; } 
	    } 
	    return gmdate($format, $time_span); 
	} 
	

}