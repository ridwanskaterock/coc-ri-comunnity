<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_user extends MY_Model {

	private $primary_key 	= "iduser";
	private $table_name 	= "user";
	private $field_search 	= array('user_name', 'user_email');

	public function __construct()
	{
		$config = array(
			"primary_key" 	=> $this->primary_key,
		 	"table_name" 	=> $this->table_name
		 	);

		parent::__construct($config);
	}

	public function cek_login($email, $password)
	{
		$sql = "SELECT * FROM {$this->table_name} WHERE (user_email = '{$email}' OR user_name = '{$email}' ) AND user_password = '".$password."'";
		$query = $this->db->query($sql);

		if($query->num_rows() > 0)
		{
			return $query->row();
		}
		else
		{
			return false;
		}
	}


}

/* End of file Model_user.php */
/* Location: ./application/models/Model_user.php */