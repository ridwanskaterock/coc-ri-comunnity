<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Crazy Migration
*/
class Migration
{
	protected $ci;

	private $migraton_dir;

	private $list_used_file_migration;

	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->helper('file_helper');
		$this->ci->load->database();

		$this->migration_dir = FCPATH.'migration/';
		$this->list_used_file_migration = FCPATH.'migration/list_file_used_migrate.json';
	}

	public function list_file_migration()
	{
		$list_file_of_directory = scandir($this->migration_dir);
		$list_file = array();

		foreach ($list_file_of_directory as $file) 
		{
			$file_path = $this->migration_dir .$file;
			if(is_file($file_path))
			{
				$path_info = pathinfo($file_path);
				if($path_info['extension'] == 'php')
				{
					$list_file[] = $file_path;
				}
			}
		}

		return $list_file;
	}

	public function is_file_used_on_migrate($file = NULL)
	{
		$list_used_file = $this->get_list_file_used_migrate();

		if(in_array($file, $list_used_file))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function get_list_file_used_migrate()
	{
		$filename = $this->list_used_file_migration;

		if(file_exists($filename))
		{
			$content = file_get_contents($filename, 999999);
		}
		else
		{
			$content = null;
		}

		if(empty($content))
		{
			file_put_contents($filename, json_encode(array()));

			return array();
		}
		else
		{
			$list_file = json_decode($content);

			return $list_file;
		}
	}

	public function reset_list_file_migrated_data()
	{
		$filename = $this->list_used_file_migration;

		if(file_exists($filename))
		{
			file_put_contents($filename, '[]');
		}
	}

	public function add_file_to_list_migrated($file)
	{
		$filename = $this->list_used_file_migration;
		$content = file_get_contents($filename, 999999);
		$arr_list = json_decode($content);
		$arr_list[] = $file;

		file_put_contents($filename, json_encode($arr_list));

	}

	public function migrate_file($file)
	{
		$file_content = file_get_contents($file, 999999);
		
		if(!$this->is_file_used_on_migrate($file))
		{
			$this->ci->db->db_debug = FALSE;

			$query_result = $this->ci->db->query($file_content);



			if($query_result)
			{
				$this->add_file_to_list_migrated($file);
				echo ' <b style="color:#5DB528">Success Migrated. </b>'.'<i><u>'.basename($file).'</i></u>' ;
				return TRUE;
			}
			else
			{
				echo ' <b style="color:#F36868">Query error when migrating </b> <i>'.'<i><u>'.basename($file).'</i></u> <span style="background:#F36868; color:#fff;">'.$this->ci->db->error()['message'].'</span></i>';
				return FALSE;
			}
		}
		else
		{
			echo '</i></u>' . ' <b style="color:#F09911">The file has been migrated. </b> '.'<i><u>'.basename($file);
			return FALSE;
		}

	}
}

/* End of file Migration.php */
/* Location: ./application/libraries/Migration.php */