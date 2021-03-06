<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	Copyright (c) 2013 Virgiliu D

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

class aws extends Admin_Controller {

	//--------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict('AmazonUpload.Aws.View');
		$this->load->model('amazonupload_model', null, true);
		$this->lang->load('amazonupload');
		
		Template::set_block('sub_nav', 'aws/_sub_nav');
	}

	//--------------------------------------------------------------------


	/*
		Method: index()

		Displays a list of form data.
	*/
	public function index()
	{
		//user id
		$user_id = $this->current_user->id;
				
		// Deleting anything?
		if (isset($_POST['delete']))
		{
			$checked = $this->input->post('checked');
			
			if (is_array($checked) && count($checked))
			{
				//get filenames of record ids checked
				$filenames = $this->amazonupload_model->filenames($checked, $user_id);			
				
				//create a multidimensional array with S3 object keys (to be used in API request for deleting objects)
				$keys_array = array();
				
				foreach ($filenames as $i => $values) 
				{
					foreach ($values as $key => $value) 
					{
						$keys_array[] = array(
							'key' => $value
						);
					}
				}
				
				//load aws library						
				$this->load->library('awslib');
						
				//initiate the class
				$s3 = new AmazonS3();
				
				$bucket = 'your_bucket_name'; //add your bucket name
				
				//delete checked files from S3
				$response = $s3->delete_objects ($bucket, array(
					'objects' => $keys_array
				));
				
				// If succesful delete db records.
				if($response->isOK()) //Note: If deleting an object that does not exist, Amazon S3 returns a success message, not an error message.
				{
					$result = FALSE;
					foreach ($checked as $pid)
					{
						$result = $this->amazonupload_model->delete($pid); //delete db records
					}
	
					if ($result)
					{
						Template::set_message(count($checked) .' '. lang('amazonupload_delete_success'), 'success');
					}
					else
					{
						Template::set_message(lang('amazonupload_delete_failure') . $this->amazonupload_model->error, 'error');
					}
				}
				else
				{
					$result = FALSE;
				}
			
			}
		}
		
		//get records that belong to the user logged in
		$records = $this->amazonupload_model->find_all_by('amazonupload_user_id', $user_id); 

		Template::set('records', $records);
		Template::set('toolbar_title', 'Manage AmazonUpload');
		Template::render();
	}

	//--------------------------------------------------------------------



	/*
		Method: create()

		Creates a aws object.
	*/
	public function create()
	{		
		$this->auth->restrict('AmazonUpload.Aws.Create');

		if ($this->input->post('save'))
		{
			if ($insert_id = $this->save_aws())
			{
				// Log the activity
				$this->activity_model->log_activity($this->current_user->id, lang('amazonupload_act_create_record').': ' . $insert_id . ' : ' . $this->input->ip_address(), 'amazonupload');

				Template::set_message(lang('amazonupload_create_success'), 'success');
				Template::redirect(SITE_AREA .'/aws/amazonupload');
			}
			else
			{
				Template::set_message(lang('amazonupload_create_failure') . $this->amazonupload_model->error, 'error');
			}
		}
		Assets::add_module_js('amazonupload', 'amazonupload.js');

		Template::set('toolbar_title', lang('amazonupload_create') . ' AmazonUpload');
		Template::render();
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/*
		Method: save_aws()

		Does the actual validation and saving of form data.

		Parameters:
			$type	- Either "insert" or "update"
			$id		- The ID of the record to update. Not needed for inserts.

		Returns:
			An INT id for successful inserts. If updating, returns TRUE on success.
			Otherwise, returns FALSE.
	*/
	private function save_aws($type='insert', $id=0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

		$this->form_validation->set_rules('userfile', 'Image', 'xss_clean'); //added rules for upload
		$this->form_validation->set_rules('amazonupload_user_id','User ID','integer|max_length[11]');
		$this->form_validation->set_rules('amazonupload_filename','Filename','trim|xss_clean|alpha_extra|max_length[550]');

		if ($this->form_validation->run() === FALSE)
		{
			return FALSE;
		}
		
		$data = array();
		
		//Handle upload
		if ($_FILES['userfile']['error'] !== 4 && $_FILES['userfile']['error'] == 0) //if new file is selected and no uploading errors
		{
			//sanitize the file name
			$this->security->sanitize_filename($_FILES['userfile']['name']); 
			
			//validate file
			$config['upload_path'] = './s3_images/';
			$config['allowed_types'] = 'jpg|png';
			$config['encrypt_name'] = TRUE;
			$config['max_size']	= '1500'; //in KB 
			
			$this->load->library('upload', $config);	
						
			if (! $this->upload->do_upload())
			{
				//to fix: show error in template instead of printing
				$error = array('error' => $this->upload->display_errors());
				print "<br /><br /><br /><br /><br /><br />";
				print_r($error);
				
				return FALSE;	
			}
			else
			{
				if ($this->upload->is_image = 1) //if file is an image (extra validation)
				{
					//user id
					$user_id = $this->current_user->id;
					
					//create file path
					$filename = $this->upload->file_name;
					$filepath = $this->upload->upload_path.$filename;
					
					//load aws library
					$this->load->library('awslib');
					
					//initiate the class
					$s3 = new AmazonS3();
					
					$bucket = 'your_bucket_name'; //your bucket name
					
					//Optional: Check if object exists in S3 bucket to avoid overwritting. 
					//a bettter solution would be check in your DB table and not make an extra API request.
					$object_exists = $s3->if_object_exists($bucket, $filename);
					
					if ($object_exists == TRUE)
					{
						echo "<br /><br /><br /><br /><br /> Dublicate file name!";
						return FALSE;
					}
					
					/*
					Upload the file to your S3 bucket.
					
					Parameters explained:
					 
					acl: File is made public.
					storage: Uses Reduced Redundancy Storage to reduce storage cost.
					Cache-Control: Cache files. Visitors won't make a new GET request every time they view the same web image.
					user_id: Add user id in the object meta because it might be handy in the future. 
					*/	
					$response = $s3->create_object($bucket, $filename, array(
						'fileUpload' => $filepath,
						'acl' => AmazonS3::ACL_PUBLIC,
						'contentType' => $this->upload->file_type,
						'storage'     => AmazonS3::STORAGE_REDUCED,
						'headers'     => array( // raw headers
							  'Cache-Control'    => 'max-age=315360000', //verify if correct format
							  'Expires' => gmdate("D, d M Y H:i:s T", strtotime("+5 years")) //verify if correct format
						),
						'meta' => array(
							 'user_id' => $user_id
						)
						
					));
			
					//if upload succesful
					if ($response->isOK())
					{
						//Gather upload data for the database
						$data['amazonupload_user_id'] = $user_id;
						$data['amazonupload_filename'] = $filename;

					}
					else
					{
						//Error creating object. To add: record API error in log.
						return FALSE;
					}
				}
				else
				{
					//The file you're trying to upload is not an image
					return FALSE;
				}
			}
			
		}
	
		if ($type == 'insert')
		{
			$id = $this->amazonupload_model->insert($data);

			if (is_numeric($id))
			{
				$return = $id;
			} else
			{
				$return = FALSE;
			}
		}
		else if ($type == 'update')
		{
			$return = $this->amazonupload_model->update($id, $data);
		}

		return $return;
	}

	//--------------------------------------------------------------------



}