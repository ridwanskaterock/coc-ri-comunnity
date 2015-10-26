<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_home");
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_tt");
		$this->load->model("admin/model_periode");
		$this->load->model("admin/model_user");
		$this->load->model("admin/model_log");
	}

	public function index()
	{
		$this->template->title(APP_NAME, "Home");
		$this->renderAdmin("public/home");
	}

	public function pm()
	{
		$this->auth->login_scurity();

		$this->template->title(APP_NAME, "Home");
		$data_graph = array();

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_graph['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['periode']) && !empty($_GET['periode']))
		{
			$data_graph['pm_period_idpm_period'] = $_GET['periode'];
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_graph['Region'] = $_GET['region'];
		}

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_graph['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}
		
		$data['result_graph_commulative'] = $this->model_home->get_graph_commulative($data_graph);


		$data['result_graph_daily'] = $this->model_home->get_graph_daily($data_graph);


		$last_period = $this->model_periode->get_periode(0,2, null);

		$data['last_period'] = $last_period;

		$data_period_tmp1 = $this->model_home->get_pm_weekly_progress($data_graph, $last_period[0]->idpm_period);
		$data_period_tmp2 = $this->model_home->get_pm_weekly_progress($data_graph, $last_period[1]->idpm_period);

		$data_period_arr_1 = array();
		$data_period_arr_2 = array();

		foreach($data_period_tmp1 as $row)
		{
			$data_period_arr_1[] = $row->jumlah;
		}

		foreach($data_period_tmp2 as $row)
		{
			$data_period_arr_2[] = $row->jumlah;
		}

		$data['result_weekly_progress'] = array(
			$data_period_arr_1
			, 
			$data_period_arr_2
			);



		$result_vendor_submission = $this->model_home->get_vendor_submission($data_graph);

		$idx = 0;

		foreach($result_vendor_submission as $v)
		{
			$result_vendor_submission[$idx]->not_submitted_yet = 100 - $result_vendor_submission[$idx]->submitted;
			$idx++;
		}

		$data['result_vendor_submission'] = $result_vendor_submission;
		$data['result_blocked_access_list'] = $this->model_home->get_pm_blocked_access($data_graph);

		/*graph QC performance*/

		$all_user_qc = $this->model_user->get_all_user_qc();
		$all_qc_performance = array();

		foreach($all_user_qc as $userqc)
		{
			$result_per_user_qc = $this->model_home->get_graph_qc_performance_byiduser($data_graph, $userqc->iduser);
			if($result_per_user_qc)
			{
				$all_qc_performance[] = $result_per_user_qc;
			}
			else
			{
				$result_per_user_qc = new stdClass();
				$result_per_user_qc->username = $userqc->username;
				$result_per_user_qc->on_review = 0;
				$result_per_user_qc->rejected = 0;
				$result_per_user_qc->approved = 0;
				$all_qc_performance[] = $result_per_user_qc;
			}
		}


		$data['result_qc_performance'] = $all_qc_performance;

		$data['result_periode'] = $this->model_pm->get_all_data('pm_period');

		$data['result_pm'] = $this->model_home->get_all_data("pm");
		$data['result_periode'] = $this->model_pm->get_all_data('pm_period');
		$data['result_vendor'] = $this->model_pm->get_all_vendor();
		$data['result_region'] = $this->model_pm->get_all_data('Region');
		$data['result_team'] = $this->model_pm->get_all_data('vendor_user');
		$data['result_status'] = $this->model_pm->model_pm->get_status();


		$this->renderAdmin("admin/home", $data);
		
	}



	public function reporting()
	{
		$this->auth->login_scurity();

		$this->template->title(APP_NAME, "Home");
		$data_graph = array();

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_graph['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['periode']) && !empty($_GET['periode']))
		{
			$data_graph['pm_period_idpm_period'] = $_GET['periode'];
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_graph['Region'] = $_GET['region'];
		}

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_graph['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}
		
		$data['result_graph_commulative'] = $this->model_home->get_graph_commulative($data_graph);

		$data['result_graph_daily'] = $this->model_home->get_graph_daily($data_graph);


		$data['result_pm'] = $this->model_home->get_all_data("pm");
		$data['result_periode'] = $this->model_pm->get_all_data('pm_period');
		$data['result_vendor'] = $this->model_pm->get_all_vendor();
		$data['result_region'] = $this->model_pm->get_all_data('Region');
		$data['result_team'] = $this->model_pm->get_all_data('vendor_user');
		$data['result_status'] = $this->model_pm->model_pm->get_status();


		$this->renderAdmin("admin/home", $data);
		
	}


	public function tt()
	{
		$this->auth->check_access('tt_access');

		$this->template->title(APP_NAME, "Home");
		$data_graph = array();

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_graph['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['periode']) && !empty($_GET['periode']))
		{
			$data_graph['pm_period_idpm_period'] = $_GET['periode'];
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_graph['Region'] = $_GET['region'];
		}

		if(isset($_GET['trouble']) && !empty($_GET['trouble']))
		{
			$data_graph['suspect_trouble'] = $_GET['trouble'];
		}

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_graph['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}


		$data_create = $this->model_home->get_tt_create_and_close($data_graph, 'create');
		$data_close = $this->model_home->get_tt_create_and_close($data_graph, 'close');

		$data_create_arr = array();
		$data_close_arr = array();

		foreach($data_create as $row)
		{
			$data_create_arr[] = $row->jumlah;
		}

		foreach($data_close as $row)
		{
			$data_close_arr[] = $row->jumlah;
		}

		$data['result_tt_create_and_close'] = array(
			$data_create_arr
			, 
			$data_close_arr
			);



		$data['result_graph_commulative'] = $this->model_home->get_graph_commulative_tt($data_graph);
		$data['result_graph_daily'] = $this->model_home->get_graph_daily_tt($data_graph);
		$data['result_achievement'] = $this->model_home->get_graph_achievement($data_graph);

		$data['result_tt_aging'] = $this->model_home->get_tt_aging_per_region($data_graph);



        $this->config->load('tt_suspect_trouble');

        $suspect_trouble = $this->config->item('type');


		$data['result_open_status'] = $this->model_home->result_graph_open_status($data_graph, $suspect_trouble);


		$data['result_pm'] = $this->model_home->get_all_data("pm");
		$data['result_periode'] = $this->model_tt->get_all_data('pm_period');
		$data['result_vendor'] = $this->model_pm->get_all_vendor();
		$data['result_region'] = $this->model_tt->get_all_data('Region');
		$data['result_team'] = $this->model_tt->get_all_data('vendor_user');
		$data['result_status'] = $this->model_tt->get_status();
		$data['result_suspect_trouble'] = $this->model_tt->get_suspect_trouble();

		$this->renderAdmin("admin/tt/dashboard", $data);
	}

}

/* End of file home.php */
/* Location: ./application/controllers/admin/home.php */
