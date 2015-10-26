<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends front
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_base');
	}

	public function index()
	{
		$data = array();
		$data['title'] = 'Base';
		$data['type'] = isset($_GET['type']) ? $_GET['type'] : 'news';
		$data['result'] = $this->model_base->get_base(NULL, 10, 0);

		$this->template->render('front/base/base-list', $data);
	}

}

/* End of file Base.php */