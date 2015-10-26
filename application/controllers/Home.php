<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends front
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data = array();

		$this->template->render('front/home/home', $data);
	}

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */