<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Amazonupload_model extends BF_Model {

	protected $table		= "s3";
	protected $key			= "id";
	protected $soft_deletes	= false;
	protected $date_format	= "datetime";
	protected $set_created	= false;
	protected $set_modified = true;
	protected $modified_field = "modified_on";
	
	/*
		Returns a multidimensional array of filenames uploaded by the user logged in.
	*/
	function filenames($checked, $user_id) 
    {
		$query = $this->db
		->select('amazonupload_filename')
		->where_in('id', $checked)
		->where('amazonupload_user_id', $user_id)
		->get('s3');
		
		return $query->result_array();
    }
}
