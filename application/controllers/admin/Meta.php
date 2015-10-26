<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Meta extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_meta");
		$this->config->load("auth");
	}

	public function index($offset = 0)
	{
		$this->auth->check_access("meta_list");
		$this->template->title(APP_NAME, "Meta List");

    $this->load->library('pagination');

    $limit = isset($_GET['limit'])?$_GET['limit']:10;
    $q = isset($_GET['q'])?$_GET['q']:'';

    $config['base_url']     = "admin/meta/index/";
    $config['total_rows']   = $this->model_meta->count_all($q);
    $config['per_page']     = $limit;
    $config['uri_segment']  = 4;

    $data['pagination']    = $this->pagination($config);
		$data['notif']          = flashdata("notif");
		$data['result']         = $this->model_meta->get_meta($offset, $limit, $q);

		$this->renderAdmin("admin/meta/meta_list", $data);
	}

    public function add()
    {
        $this->auth->check_access("meta_add");
        
        $this->template->title(APP_NAME, "Meta Add");

        $this->form_validation->set_rules("meta_name", "Name", "required");
        $this->form_validation->set_rules("meta_value", "Value", "required");

        if($this->form_validation->run())
        {
            
            $data['meta_name'] = $this->input->post("meta_name");
            $data['meta_value'] = $this->input->post("meta_value");
            $data['meta_table_reff'] = $this->input->post("meta_table_reff");
            $data['meta_table_reff_id'] = $this->input->post("meta_table_reff_id");

            $this->model_meta->store($data);


            set_flashdata("notif", alert("berhasil menambah data"));

            redirect("admin/meta");
        }

        $this->renderAdmin("admin/meta/meta_add");
    }

    public function update($id = NULL)
    {
        $this->auth->check_access("meta_update");
        
        $this->template->title(APP_NAME, "Meta update");

        $this->form_validation->set_rules("meta_name", "Name", "required");
        $this->form_validation->set_rules("meta_value", "Value", "required");
        $data['meta_table_reff'] = $this->input->post("meta_table_reff");
        $data['meta_table_reff_id'] = $this->input->post("meta_table_reff_id");

        if($this->form_validation->run())
        {

            $data['meta_name'] = $this->input->post("meta_name");
            $data['meta_value'] = $this->input->post("meta_value");
            $data['meta_table_reff'] = $this->input->post("meta_table_reff");
            $data['meta_table_reff_id'] = $this->input->post("meta_table_reff_id");

            $this->model_meta->change($id, $data);

            set_flashdata("notif", alert("Berhasil Update Data"));

            redirect("admin/meta");
        }

        $data['row'] = $this->model_meta->find($id);
        $data['table_reff_id'] = $this->model_meta->get_meta_by_table_reff(isset($data['row']->meta_table_reff)?$data['row']->meta_table_reff:"");
        $this->renderAdmin("admin/meta/meta_update", $data);
    }

    public function delete($id = NULL)
    {
        $this->auth->check_access("meta_delete");

        $this->model_meta->remove($id);

        set_flashdata("notif", alert("Berhasil menghapus data meta", "success"));

        redirect("admin/meta");
    }

    public function get_meta_by_table_reff()
    {

        $table_reff = $this->input->get("table_reff");

        $result = $this->model_meta->get_meta_by_table_reff($table_reff);

        if($result->num_rows()>0)
        {
            foreach ($result->result() as $row) {
              switch ($table_reff) {
                  case 'PM':
                      ?>        
                         <option value="<?= $row->idpm; ?>">PM ID : <?= $row->idpm; ?></option>
                      <?php
                      break;

                  case 'TT':
                      ?>        
                         <option value="<?= $row->idtt; ?>">TT ID : <?= $row->idtt; ?></option>
                      <?php
                      break;
                  
                  default:
                      ?>        
                         <option>Terjadi Kesalahan Memuat Data</option>
                      <?php
                      break;
              }
            }
        }
        else
        {
             ?>        
                 <option value="">Data <?=$table_reff; ?> Tidak Tersedia</option>
              <?php
        }
    }

    public function test()
    {
      $table_reff = $this->input->get('table_reff');
      $id = $this->input->get('id');
      $meta_name = $this->input->get('meta_name');
      $input_type = $this->input->get('input_type');

      $result = $this->model_meta->get_meta_single($table_reff, $id, $meta_name, $input_type );
      print_pre($result);

    }
}


/* End of file meta.php */
/* Location: ./application/controllers/admin/meta.php */