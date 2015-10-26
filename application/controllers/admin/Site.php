<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_site");
		$this->config->load("auth");

	}

	public function index($offset = 0)
	{
		$this->auth->check_access("site");

		$this->template->title(APP_NAME, "site List");


	    $limit = isset($_GET['limit'])?$_GET['limit']:10;
	    $q = isset($_GET['q'])?$_GET['q']:'';

	    $config['base_url']     = "admin/site/index/";
	    $config['total_rows']   = $this->model_site->count_all($q);
	    $config['per_page']     = $limit;
	    $config['uri_segment']  = 4;

	    $data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		$data['result']			= $this->model_site->get_site($offset, $limit, $q);
		$data['total_rows'] 	= $config['total_rows'];

		$data['notif'] = flashdata("notif");

		$this->renderAdmin("admin/site/site_general", $data);
	}


	public function delete($id = NULL)
	{
		$this->auth->check_access("site_delete");

		$this->model_site->remove($id);
		set_flashdata("notif", alert("Berhasil menghapus data site", "success"));

		redirect("admin/site");
	}

	public function get_site()
	{
		$this->auth->login_scurity();
		
		$idsite = $this->input->get('idsite');
		$data['location'] = $this->model_site->find($idsite);
		$this->load->view('admin/site/site_general_template', $data);
	}


	public function get_site_info()
	{
		$this->auth->login_scurity();

		$idsite = $this->input->get('siteid');
		$data['id_site'] = $idsite;
		$data['site_info'] = $this->model_site->find($idsite);
		$this->load->view('admin/site/site_info_template', $data);
	}

	public function get_site_autocomplete()
	{
		$q = $this->input->get('term');

		$this->db->select('SiteId');
		$this->db->where("SiteId LIKE '%{$q}%'");
		$query = $this->db->get('GeneralInfo');

		$outs = array();

		foreach($query->result() as $row)
		{
			$outs[] = $row->SiteId;
		}

		echo json_encode($outs);

	}


}

/* End of file site.php */
/* Location: ./application/controllers/admin/site.php */



