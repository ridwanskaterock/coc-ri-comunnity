<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Migration
*/
class Migrate extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('migration');
	}

	public function index()
	{
		$list_file = $this->migration->list_file_migration();
		$i=1;
		foreach($list_file as $file)
		{
			echo '- ';
			$this->migration->migrate_file($file);
			echo "<br>";
		}
	}	

	public function reset()
	{
		$this->migration->reset_list_file_migrated_data();
	}
}

/* End of file migrate.php */
/* Location: ./application/controllers/migration/migrate.php */