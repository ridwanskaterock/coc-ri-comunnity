<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Periode extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_periode");
		$this->config->load("auth");
	}

	public function index($offset = 0)
	{
		$this->auth->check_access("periode");
		$this->template->title(APP_NAME, "periode List");

        $this->load->library('pagination');

        $limit = isset($_GET['limit'])?$_GET['limit']:10;
        $q = isset($_GET['q'])?$_GET['q']:'';

        $config['base_url']     = "admin/periode/index/";
        $config['total_rows']   = $this->model_periode->count_all($q);
        $config['per_page']     = $limit;
        $config['uri_segment']  = 4;

        $data['pagination']    = $this->pagination($config);
        $data['notif']          = flashdata("notif");
        $data['result']         = $this->model_periode->get_periode($offset, $limit, $q);
        $data['total_rows'] = $config['total_rows'];

        $this->renderAdmin("admin/periode/periode_list", $data);
    }

    public function add()
    {
        $this->auth->check_access("periode_add");
        
        $this->template->title(APP_NAME, "periode Add");

        $this->form_validation->set_rules("period_name", "Name", "required|callback_valid_periode");
        $this->form_validation->set_rules("period_date", "Start - End date", "required");

        if($this->form_validation->run())
        {
            list($start_date, $end_date) = explode('-', $this->input->post('period_date'));

            $data['period_name'] = $this->input->post("period_name");
            $data['period_start_date'] = trim($start_date);
            $data['period_end_date'] = trim($end_date);

            $this->model_periode->store($data);

            set_flashdata("notif", alert("berhasil menambah data"));

            redirect("admin/periode");
        }

        $this->renderAdmin("admin/periode/periode_add");
    }

    public function update($id = NULL)
    {
        $this->auth->check_access("periode_update");
        
        $this->template->title(APP_NAME, "periode update");

        $this->form_validation->set_rules("period_name", "Name", "required|callback_valid_periode");
        $this->form_validation->set_rules("period_date", "Start - End date", "required");

        if($this->form_validation->run())
        {
            list($start_date, $end_date) = explode('-', $this->input->post('period_date'));

            $data['period_name'] = $this->input->post("period_name");
            $data['period_start_date'] = trim($start_date);
            $data['period_end_date'] = trim($end_date);

            $this->model_periode->change($id, $data);

            set_flashdata("notif", alert("Berhasil Update Data"));

            redirect("admin/periode");
        }

        $data['row'] = $this->model_periode->find($id);
        $this->renderAdmin("admin/periode/periode_update", $data);
    }

    public function delete($id = NULL)
    {
        $this->auth->check_access("periode_delete");

        $this->model_periode->remove($id);

        set_flashdata("notif", alert("Berhasil menghapus data periode", "success"));

        redirect("admin/periode");
    }

    public function valid_periode($periode = NULL)
    {
        if(preg_match("/[\,\<\>\?]/", $periode))
        {
            $this->form_validation->set_message('valid_periode', "disallowed characters the period name ");
            return false;
        }
        else
        {
            return true;
        }
    }

}


/* End of file periode.php */
/* Location: ./application/controllers/admin/periode.php */