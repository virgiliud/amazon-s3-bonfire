<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Amazonupload_model extends BF_Model {

	protected $table		= "s3";
	protected $key			= "id";
	protected $soft_deletes	= false;
	protected $date_format	= "datetime";
	protected $set_created	= true;
	protected $set_modified = false;
	protected $modified_field = "created_on";
	
	/*
		Returns a multidimensional array with filenames uploaded by the user logged in.
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
