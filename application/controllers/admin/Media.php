<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends Admin {

	public function __construct()
	{
		parent::__construct();
        $this->load->model("admin/model_media");
		$this->config->load("auth");
	}

	public function index($offset = 0)
	{
		$this->auth->check_access("media");
		$this->template->title(APP_NAME, "media List");

        $this->load->library('pagination');

        $limit = isset($_GET['limit'])?$_GET['limit']:10;
        $q = isset($_GET['q'])?$_GET['q']:'';

        $config['base_url']     = "admin/media/index/";
        $config['total_rows']   = $this->model_media->count_all($q);
        $config['per_page']     = $limit;
        $config['uri_segment']  = 4;
        
        $data['pagination'] = $this->pagination($config);

		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_media->get_media($offset, $limit, $q);

		$this->renderAdmin("admin/media/media_list", $data);
	}

    public function add()
    {
        $this->auth->check_access("media_add");
        
        $this->template->title(APP_NAME, "media Add");

        $this->form_validation->set_rules("photo_name", "Name", "required");
        $this->form_validation->set_rules("photo_desc", "Descripion", "required");
        $this->form_validation->set_rules("photo_status", "Status", "required");
        $this->form_validation->set_rules("photo_table_reff", "Table Reff", "required");
        $this->form_validation->set_rules("photo_table_reff_id", "Table Reff ID", "required");

        if($this->form_validation->run())
        {
            $config['upload_path'] = './asset/photo/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']  = '999999';
            $config['max_width']  = '999999';
            $config['max_height']  = '999999';
            
            $this->load->library('upload', $config);
            
            if($_FILES AND $_FILES['photo']['name'])
            {
                if ( ! $this->upload->do_upload("photo"))
                {
                    $error = $this->upload->display_errors();
                    set_flashdata("notif", alert($error, "danger"));
                    redirect("admin/media");
                }
                else
                {
                    $data_upload = $this->upload->data();

                    $data['photo_url'] = BASE_ASSET . 'photo/' . $data_upload['file_name'];
                }
            }
            else
            {
                $data['photo_url'] = BASE_ASSET . 'photo/' . "default.jpg";
            }

            $data['photo_name'] = $this->input->post("photo_name");
            $data['photo_desc'] = $this->input->post("photo_desc");
            $data['photo_date'] = date("Y-m-d H:i:s");
            $data['photo_table_reff'] = $this->input->post("photo_table_reff");
            $data['photo_table_reff_id'] = $this->input->post("photo_table_reff_id");
            $data['photo_date_added'] = date("Y-m-d H:i:s");
            $data['photo_status'] = $this->input->post("photo_status");

            $this->model_media->store($data);

            set_flashdata("notif", alert("berhasil menambah data"));

            redirect("admin/media");
        }

        $this->renderAdmin("admin/media/media_add");
    }

    public function update($id = NULL)
    {
         $this->auth->check_access("media_update");
        
        $this->template->title(APP_NAME, "media update");

        $this->form_validation->set_rules("photo_name", "Name", "required");
        $this->form_validation->set_rules("photo_desc", "Descripion", "required");
        $this->form_validation->set_rules("photo_status", "Status", "required");
        $this->form_validation->set_rules("photo_table_reff", "Table Reff", "required");
        $this->form_validation->set_rules("photo_table_reff_id", "Table Reff ID", "required");


        if($this->form_validation->run())
        {
            $config['upload_path'] = './asset/photo/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']  = '999999';
            $config['max_width']  = '999999';
            $config['max_height']  = '999999';
            
            $this->load->library('upload', $config);
            
            if($_FILES AND $_FILES['photo']['name'])
            {
                if ( ! $this->upload->do_upload("photo"))
                {
                    $error = $this->upload->display_errors();
                    set_flashdata("notif", alert($error, "danger"));
                    redirect("admin/media");
                }
                else
                {
                    $data_upload = $this->upload->data();

                    $data['photo_url'] = BASE_ASSET . 'photo/' . $data_upload['file_name'];
                }
            }
            else
            {
                $data['photo_url'] = BASE_ASSET . 'photo/' . "default.jpg";
            }

            $data['photo_name'] = $this->input->post("photo_name");
            $data['photo_desc'] = $this->input->post("photo_desc");
            $data['photo_date'] = date("Y-m-d H:i:s");
            $data['photo_table_reff'] = $this->input->post("photo_table_reff");
            $data['photo_table_reff_id'] = $this->input->post("photo_table_reff_id");
            $data['photo_date_added'] = date("Y-m-d H:i:s");
            $data['photo_status'] = $this->input->post("photo_status");

            $this->model_media->change($id, $data);

            set_flashdata("notif", alert("Berhasil Update Data"));

            redirect("admin/media");
        }

        $data['row'] = $this->model_media->find($id);
        $data['table_reff_id'] = $this->model_media->get_media_by_table_reff(isset($data['row']->photo_table_reff)?$data['row']->photo_table_reff:"");
        $this->renderAdmin("admin/media/media_update", $data);
    }

    public function delete($id = NULL)
    {
        $this->auth->check_access("media_delete");

        $this->model_media->remove($id);

        set_flashdata("notif", alert("Berhasil menghapus data media", "success"));

        redirect("admin/media");
    }

    public function get_media_by_table_reff()
    {

        $table_reff = $this->input->get("table_reff");

        $result = $this->model_media->get_media_by_table_reff($table_reff);

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
}


/* End of file media.php */
/* Location: ./application/controllers/admin/media.php */