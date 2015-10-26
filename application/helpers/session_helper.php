<?php

/**
* @name : Session Helper
* @author : Muhamad Ridwan
*/


define("FLASHDATA_KEY", "SESSION_FLASHDATA");
define("USERDATA_KEY", 	"SESSION_USERDATA");


function set_userdata($session_name = NULL, $session_value = NULL, $suffix = NULL)
{
	$_SESSION[USERDATA_KEY . $session_name . $suffix] = $session_value;
}


function userdata($session_name = NULL, $suffix = NULL)
{
	if(isset($_SESSION[USERDATA_KEY . $session_name . $suffix])){
		return $_SESSION[USERDATA_KEY . $session_name . $suffix];
	}else{
		return FALSE;
	}
}

function unset_userdata($session_name = NULL, $suffix = NULL)
{
	unset($_SESSION[USERDATA_KEY . $session_name . $suffix]);
}

function set_flashdata($session_name = NULL, $session_value = NULL)
{
	$_SESSION[FLASHDATA_KEY][$session_name] = $session_value;
}

function flashdata($session_name = NULL)
{
	if(isset($_SESSION[FLASHDATA_KEY][$session_name]))
	{
		$flashdata = $_SESSION[FLASHDATA_KEY][$session_name];
		unset($_SESSION[FLASHDATA_KEY][$session_name]);
	}else
	{
		$flashdata = FALSE;
	}

	return $flashdata;
}

function unset_all_userdata()
{
	session_destroy();
}
