<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Support extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_support");

	}

	public function index( $offset = 0 )
	{
		$this->auth->check_access('support');
		$data = array(
			"result" => $this->model_support->get_all_data("pm")
			);

		$this->renderAdmin("admin/support/support_list", $data);

	}

}



/* End of file support.php */
/* Location: ./application/controllers/admin/support.php */