<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_s3 extends Migration {

	public function up()
	{
		$prefix = $this->db->dbprefix;

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'auto_increment' => TRUE,
			),
			'amazonupload_user_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				
			),
			'amazonupload_filename' => array(
				'type' => 'VARCHAR',
				'constraint' => 550,
				
			),
			'created_on' => array(
				'type' => 'datetime',
				'default' => '0000-00-00 00:00:00',
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('s3');

	}

	//--------------------------------------------------------------------

	public function down()
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->drop_table('s3');

	}

	//--------------------------------------------------------------------

}