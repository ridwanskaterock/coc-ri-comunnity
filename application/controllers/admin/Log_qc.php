<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class log_qc extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_log_qc");
		$this->config->load("auth");

	}

	public function index($offset = 0)
	{
		$this->auth->check_access("log_qc");

		$this->template->title(APP_NAME, "Log QC List");
	    $limit = isset($_GET['limit'])?$_GET['limit']:10;
	    $q = isset($_GET['q'])?$_GET['q']:'';

	    $config['base_url']     = "admin/log_qc/index/";
	    $config['total_rows']   = $this->model_log_qc->count_all($q);
	    $config['per_page']     = $limit;
	    $config['uri_segment']  = 4;

	    $data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		$data['result']			= $this->model_log_qc->get_log_qc($offset, $limit, $q);
		$data['total_rows'] = $config['total_rows'];

		$data['notif'] = flashdata("notif");

		$this->renderAdmin("admin/log_qc/log_qc_list", $data);
	}
}

/* End of file Log_qc.php */
/* Location: ./application/controllers/admin/Log_qc.php */