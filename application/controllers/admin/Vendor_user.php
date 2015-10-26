<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vendor_user extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_vendor_user");
		$this->load->model("admin/model_vendor_user_region");
		$this->config->load("auth");
	}

	public function index()
	{
		$this->auth->check_access("vendor_user");

		$this->template->title(APP_NAME, "Vendor User List");
	
		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_vendor_user->get_vendor_user();

		$this->renderAdmin("admin/vendor_user/vendor_user_list", $data);
	}

	public function add($id = NULL)
	{
		$this->auth->check_access("vendor_user_add");
		
		$this->template->title(APP_NAME, "Vendor User Add");

		$this->form_validation->set_rules("username", "username", "required");
		$this->form_validation->set_rules("fullname", "full name", "required");
		$this->form_validation->set_rules("password", "password", "required|matches[repassword]|min_length[6]");
		$this->form_validation->set_rules("repassword", "re type password", "required");
		$this->form_validation->set_rules("email", "Email", "required|valid_email|is_unique[vendor_user.email]");
		$this->form_validation->set_rules("phone", "phone", "required");


		if($this->form_validation->run())
		{
			$config['upload_path'] = './asset/vendor-user-image/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']  = '999999';
			$config['max_width']  = '999999';
			$config['max_height']  = '999999';
			
			$this->load->library('upload', $config);
			
			if($_FILES AND $_FILES['avatar']['name'])
			{
				if ( ! $this->upload->do_upload("avatar"))
				{
					$error = $this->upload->display_errors();
					set_flashdata("notif", alert($error, "danger"));
					redirect("admin/vendor_user");
				}
				else
				{
					$data_upload = $this->upload->data();

					$data['avatar'] = $data_upload['file_name'];
				}
			}
			else
			{
				$data['avatar'] = "default.jpg";
			}

			$data['username'] = $this->input->post("username").'.'.user_admin('idvendor');
			$data['password'] = enkripsi($this->input->post("password"));
			$data['fullname'] = $this->input->post("fullname");
			$data['idvendor'] = user_admin('idvendor');
			$data['email'] = $this->input->post("email");
			$data['phone'] = $this->input->post("phone");
			$data['om_create_date'] = date("Y-m-d H:i:s");
			$data['om_create_by'] = user_admin('iduser');
			$data['om_deletion_flag'] = 0;
			$data['access_pm'] = $this->input->post("access_pm");
			$data['access_tt'] = $this->input->post("access_tt");
			$data['imei_number'] = $this->input->post("imei_number");

			$idvendor_user = $this->model_vendor_user->store($data);

			//insert multiple user region
			$data_region = array();
			$region_id = $this->input->post('region_id');
			if(is_array($region_id))
			{
				foreach($region_id as $region)
				{
					$data_region = array('vendor_user_idvendor_user' =>  $idvendor_user, 'idregion' => $region);
					$this->model_vendor_user_region->store($data_region);
				}
			}


			set_flashdata("notif", alert("berhasil menambah data"));
			redirect("admin/vendor_user");
		}

		$data["result_vendor"] = $this->model_vendor_user->get_all_data('OMSubcontractor');
		$data["result_region"] = $this->model_vendor_user->get_all_data('Region');

		$this->renderAdmin("admin/vendor_user/vendor_user_add", $data);
	}

	public function update($id = NULL)
	{
		$this->auth->check_access("vendor_user_update");
		
		$this->template->title(APP_NAME, "vendor user Update");
		
		$this->form_validation->set_rules("username", "vendor_username", "required");
		$this->form_validation->set_rules("fullname", "full name", "required");
		$this->form_validation->set_rules("phone", "phone", "required");
		$this->form_validation->set_rules("email", "email", "required|valid_email");


		if($this->form_validation->run())
		{
			$config['upload_path'] 		= './asset/vendor-user-image/';
			$config['allowed_types'] 	= 'gif|jpg|png';
			$config['max_size']  		= '9999999';
			$config['max_width']  		= '9999999';
			$config['max_height']  		= '9999999';
			
			$this->load->library('upload', $config);

			if($_FILES AND $_FILES['avatar']['name'])
			{
				if ( ! $this->upload->do_upload("avatar"))
				{
					$error = $this->upload->display_errors();
					set_flashdata("notif", alert($error, "danger"));
					redirect("admin/vendor_user");
				}
				else
				{
					$data_upload = $this->upload->data();
					$data['avatar'] = $data_upload['file_name'];
				}
			}

			$data['username'] = $this->input->post("username");
			$data['fullname'] = $this->input->post("fullname");
			$data['idvendor'] = user_admin('idvendor');
			$data['email'] = $this->input->post("email");
			$data['phone'] = $this->input->post("phone");
			$data['om_update_date'] = date("Y-m-d H:i:s");
			$data['om_update_by'] = user_admin('iduser');
			$data['om_deletion_flag'] = 0;
			$data['access_pm'] = $this->input->post("access_pm");
			$data['access_tt'] = $this->input->post("access_tt");
			$data['imei_number'] = $this->input->post("imei_number");

			$this->model_vendor_user->change($id, $data);

			$this->model_vendor_user_region->delete_region_byid_vendor($id);

			//insert multiple user region
			$data_region = array();
			$region_id = $this->input->post('region_id');
			if(is_array($region_id))
			{
				foreach($region_id as $region)
				{
					$data_region = array('vendor_user_idvendor_user' =>  $id, 'idregion' => $region);
					$this->model_vendor_user_region->store($data_region);
				}
				
			}


			set_flashdata("notif", alert("Berhasil mengupdate data vendor user"));

			redirect("admin/vendor_user");
		}

		$data['notif'] = flashdata("notif");
		$data["result_vendor"] = $this->model_vendor_user->get_all_data('OMSubcontractor');
		$data["vendor_user_level"] = $this->config->item("level");
		$data['row'] = $this->model_vendor_user->find($id);
		$data["result_region"] = $this->model_vendor_user->get_all_data('Region');
		$data["result_region_selected"] = $this->model_vendor_user_region->get_region_byid_vendor($id);

		$this->renderAdmin("admin/vendor_user/vendor_user_update", $data);
	}

	public function delete($id = NULL)
	{
		$this->auth->check_access("vendor_user_delete");

		$this->model_vendor_user->remove($id);

		set_flashdata("notif", alert("Berhasil menghapus data vendor user", "success"));

		redirect("admin/vendor_user");
	}

	public function change_password($id = NULL)
	{
		$this->auth->login_scurity();
		$this->template->title(APP_NAME, "Change Password");
		
		$this->form_validation->set_rules("newpassword", "New Password", "required");
		$this->form_validation->set_rules("renewpassword", "Re New Password", "required|matches[newpassword]");

		if($this->form_validation->run())
		{

			$data['password'] = enkripsi($this->input->post("newpassword"));

			$this->model_vendor_user->change($id, $data);

			set_flashdata("notif", alert("Berhasil mereset password"));

			redirect("admin/vendor_user/update/".$id);
		}


		$data['row'] = $this->model_vendor_user->find($id);

		$this->renderAdmin("admin/vendor_user/vendor_user_change_password", $data);
	}

}

/* End of file vendor_user.php */
/* Location: ./application/controllers/admin/vendor_user.php */



