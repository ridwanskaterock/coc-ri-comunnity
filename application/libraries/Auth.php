<?php

/**
* @author Muhamad Ridwan
* @since 2014
*/

class Auth {

	protected $ci;

	var $table_login_name 			= "user";
	var $table_lost_password_name 	= "lost_password";	
	var $field_username 			= "username";
	var $field_password 			= "password";
	var $session_level_name 		= "user_level";
	var $session_user 				= "session_user";
	var $redirect_url 				= "admin/user/login";
	var $access 					= array();

	public function __construct()
	{
		$this->ci =& get_instance();
		//resource akses
		$this->access = userdata('resource_access');
	}

	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if(isset($this->$key))
				$this->$key = $val;
		}
	}


	/**
	* @param username string
	* @param password string
	*/
 
	public function login($username = NULL, $password = NULL)
	{
		$data = array(
			$this->field_username => $username,
			$this->field_password => $password
			);

		$result = $this->ci->db->get_where($this->table_login_name, $data);

		if($result->num_rows()>0)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}

	}

	/**
	* @param level string or array
	*/

	public function allow($level = array(), $redirect = TRUE)
	{
		$this->login_scurity($redirect);
		$session_level = userdata($this->session_level_name);

		if(is_array($level))
		{			
			if(!in_array($session_level, $level))
			{
				set_flashdata('show_login', 'ok');
				
				if($redirect)
				{
					redirect($this->redirect_url);
				}
				else
				{
					return FALSE;
				}
			}
		}
		else
		{
			if($level != $session_level)
			{
				set_flashdata('show_login', 'ok');
				if($redirect)
				{
					redirect($this->redirect_url);
				}
				else
				{
					return FALSE;
				}
			}
		}
	}

	public function generete_key()
	{
		$key = md5(time() . $_SERVER['REMOTE_ADDR']);
		return $key;
	}

	public function login_scurity($redirect = TRUE)
	{
		if(!userdata($this->session_user))
		{
			set_flashdata('show_login', 'ok');
			if(isset($_SERVER['HTTP_REFERER']))
		    {
		        set_flashdata('redirect', $_SERVER['HTTP_REFERER']);
		    }
		    set_jnotif('Warning', 'You need to login first', 'warning');

			if($redirect)
			{
				redirect($this->redirect_url);
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	public function login_user($redirect = FALSE)
	{
		if(userdata($this->session_user))
		{			
			return TRUE;
		}
		else
		{
			if($redirect)
			{
				if(isset($_SERVER['HTTP_REFERER']))
			    {
			        set_flashdata('redirect', $_SERVER['HTTP_REFERER']);
			    }
			    set_jnotif('Warning', 'You need to login first', 'warning');
				set_flashdata('show_login', 'ok');
				redirect('skeddoer/login');
			}
			else
			{
				return false;
			}
		}
	}
}