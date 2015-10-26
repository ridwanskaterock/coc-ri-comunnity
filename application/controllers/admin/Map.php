
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends Admin {

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		$this->load->model("admin/model_map");
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_tt");
		$this->load->model("admin/model_periode");

	}

	public function index( $offset = 0 )
	{
		$this->auth->check_access("map");
		
		$this->template->title(APP_NAME, "map");
		
		$data_filter = array();

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_graph['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_graph['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['periode']) && !empty($_GET['periode']))
		{
			$data_graph['pm_period_idpm_period'] = $_GET['periode'];
		}
		else
		{
			$last_period = $this->model_periode->get_last_period();

			if($last_period)
			{
				$data_graph['pm_period_idpm_period'] = $last_period->idpm_period;	
			}
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_graph['Region'] = $_GET['region'];
		}

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_graph['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}

		$data['result_pm'] = $this->model_map->get_all_data("pm");
		$data['result_map_mark'] = $this->model_map->get_data_map($data_graph);

		$data['count_map_mark'] = count($data['result_map_mark']);
		$data['result_periode'] = $this->model_periode->get_all_period();
		$data['result_vendor'] = $this->model_pm->get_all_vendor();
		$data['result_region'] = $this->model_pm->get_all_data('Region');
		$data['result_status'] = $this->model_pm->model_pm->get_status();
		$data['result_tenant'] = $this->model_tt->get_tenant('Client');
		$data['result_team'] = $this->model_pm->get_all_data('vendor_user');

		$this->renderAdmin("admin/map/map", $data);

	}

	public function incident( $offset = 0 )
	{
		$this->auth->check_access("map_incident");
		
		$this->template->title(APP_NAME, "map");
				
		$data_filter = array();

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_filter['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_filter['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['periode']) && !empty($_GET['periode']))
		{
			$data_filter['pm_period_idpm_period'] = $_GET['periode'];
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_filter['Region'] = $_GET['region'];
		}

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_filter['vendor_user_idvendor_user_pic'] = $_GET['team'];
		}

		if(isset($_GET['tenant']) && !empty($_GET['tenant']))
		{
			$data_filter['Anchor'] = $_GET['tenant'];
		}

		$data['result_pm'] = $this->model_map->get_all_data("pm");
		$data['result_map_mark'] = $this->model_map->get_data_map($data_filter);
		$data['result_periode'] = $this->model_pm->get_all_data('pm_period');
		$data['result_vendor'] = $this->model_pm->get_all_vendor();
		$data['result_region'] = $this->model_pm->get_all_data('Region');
		$data['result_status'] = $this->model_pm->get_status();
		$data['result_team'] = $this->model_pm->get_all_data('vendor_user');
		$data['result_tenant'] = $this->model_tt->get_tenant('Client');

		$this->renderAdmin("admin/map/map_incident", $data);

	}

	public function tt_map()
	{
		$this->auth->check_access("map_incident");
		
		$this->template->title(APP_NAME, "map");
	
		$data_filter = array();

		if(isset($_GET['team']) && !empty($_GET['team']))
		{
			$data_filter['tt_idpic'] = $_GET['team'];
		}

		if(isset($_GET['vendor']) && !empty($_GET['vendor']))
		{
			$data_filter['OMCode'] = $_GET['vendor'];
		}

		if(isset($_GET['trouble']) && !empty($_GET['trouble']))
		{
			$data_filter['suspect_trouble'] = $_GET['trouble'];
		}

		if(isset($_GET['region']) && !empty($_GET['region']))
		{
			$data_filter['Region'] = $_GET['region'];
		}

		if(isset($_GET['trouble_type']) && !empty($_GET['trouble_type']))
		{
			$data_filter['trouble_type'] = $_GET['trouble_type'];
		}

		if(isset($_GET['tenant']) && !empty($_GET['tenant']))
		{
			$data_filter['tenant'] = $_GET['tenant'];
		}


		if(isset($_GET['status_description']) AND !empty($_GET['status_description']))
		{
			$data_filter['tt_status_description'] = $this->input->get('status_description', TRUE);
		}
		else
		{
			$data_filter['tt_status_description'] = 'Activity on Progress';
		}


		$data['result_pm'] = $this->model_map->get_all_data("pm");
		$data['result_map_mark'] = $this->model_map->get_tt_map($data_filter);
		$data['count_map_mark'] = count($data['result_map_mark']);
		$data['result_periode'] = $this->model_tt->get_all_data('pm_period');
		$data['result_vendor'] = $this->model_tt->get_all_data('OMSubcontractor');
		$data['result_region'] = $this->model_tt->get_all_data('Region');
		$data['result_status'] = $this->model_tt->get_status();
		$data['result_team'] = $this->model_tt->get_all_data('vendor_user');
		$data['result_tenant'] = $this->model_tt->get_tenant('Client');
		$data['result_suspect_trouble'] = $this->model_tt->get_suspect_trouble();
		$data['result_trouble_type'] = $this->model_tt->get_trouble_type();
		$data['result_status_description'] 	= $this->model_tt->get_status_description();

		$this->renderAdmin("admin/map/tt_map", $data);
	}

	public function map_detail()
	{
		$data['Latitude'] = $this->input->get('Latitude');
		$data['Longitude'] = $this->input->get('Longitude');

		$this->load->view('admin/map/map_detail', $data);
	}

}

/* End of file map.php */
/* Location: ./application/controllers/admin/map.php */