<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_user");
		$this->load->model("admin/modeL_vendor_user");
		$this->load->model("admin/Model_reset_password");
		$this->load->model("admin/model_task");
		$this->load->model("admin/model_log_qc");
		$this->load->library("mailer");
		$this->load->library("round_robin");
		$this->load->helper("string");
		$this->config->load("auth");

		$this->level = user_admin('level_name');
	}

	public function index($offset = 0)
	{
		$this->auth->check_access("user");
		$this->template->title(APP_NAME, "User List");

		$limit = isset($_GET['limit'])?$_GET['limit']:10;
	    $q = isset($_GET['q'])?$_GET['q']:'';


		$data_filter = array();
		if(isset($_GET['vendor']) AND !empty($_GET['vendor']))
		{
			$data_filter['idvendor'] = $this->input->get('vendor', TRUE);
		}

		if(isset($_GET['tenant']) AND !empty($_GET['tenant']))
		{
			$data_filter['idtenant'] = $this->input->get('tenant', TRUE);
		}

		if(isset($_GET['level']) AND !empty($_GET['level']))
		{
			$data_filter['level'] = $this->input->get('level', TRUE);
		}


		$config['base_url']     = "admin/user/index/";
	    $config['total_rows']   = $this->model_user->count_all($q, $data_filter);
	    $config['per_page']     = $limit;
	    $config['uri_segment']  = 4;

	    $data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		$data['result']			= $this->model_user->get_user($offset, $limit, $q, $data_filter);
		$data['total_rows'] = $config['total_rows'];

		$data['notif'] = flashdata("notif");
		$data["result_vendor"] = $this->model_user->get_all_vendor();
		$data["result_tenant"] = $this->model_user->get_all_data('Client');
		$data["user_level"] = $this->model_user->load_level();

		$this->renderAdmin("admin/user/user_list", $data);
	}

	public function add($id = NULL)
	{
		$this->auth->check_access("user_add");
		
		$this->template->title(APP_NAME, "User Add");

		$this->form_validation->set_rules("username", "username", "required|is_unique[user.username]");
		$this->form_validation->set_rules("fullname", "full name", "required");
		$this->form_validation->set_rules("email", "Email", "required|valid_email|is_unique[user.email]");
		$this->form_validation->set_rules("password", "password", "required|matches[repassword]|min_length[6]");
		$this->form_validation->set_rules("repassword", "re type password", "required");

		if($this->form_validation->run())
		{
			$config['upload_path'] = FCPATH.'asset/user-image/';
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
					redirect("admin/user");
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

			$data['username'] = $this->input->post("username");
			$data['password'] = enkripsi($this->input->post("password"));
			$data['fullname'] = $this->input->post("fullname");
			$data['level'] = $this->input->post("level");
			$data['email'] = $this->input->post("email");
			
			$data['is_idap'] = $this->input->post("is_idap");
			$data['last_login'] = date("Y-m-d H:i:s");
			$data['om_create_date'] = date("Y-M-d H:i:s");
			$data['om_create_by'] = user_admin('iduser');
			$data['om_deletion_flag'] = '0';
			$data['idvendor'] 	= $this->input->post("idvendor");
			$data['idtenant'] 	= $this->input->post("idtenant");
			$data['is_idap'] 	= $this->input->post("is_idap");

			$this->model_user->store($data);

			set_flashdata("notif", alert("berhasil menambah data"));

			redirect("admin/user");
		}

		$data["user_level"] = $this->model_user->load_level();
		$data["result_vendor"] = $this->model_user->get_all_vendor();
		$data["result_tenant"] = $this->model_user->get_all_data('Client');

		$this->renderAdmin("admin/user/user_add", $data);
	}

	public function add_resource()
	{
		$this->auth->check_access("user_group_right");
		
		$this->form_validation->set_rules("resource_name", "resource_name", "required|is_unique[resource.resource_name]");


		if($this->form_validation->run())
		{

			$data['resource_name'] = $this->input->post("resource_name");

			$this->model_user->insert('resource', $data);

			set_flashdata("notif", alert("berhasil menambah data resource"));

		}
		else
		{
			set_flashdata('notif', alert(validation_errors(), 'error'));
		}

		redirect("admin/user/group");
	}


	public function group()
	{

		$this->auth->check_access("user_group_right");
		$this->template->title(APP_NAME, "User Group Right");
		$this->form_validation->set_rules("level", "Group Name", "required");

		if($this->form_validation->run())
		{
			$level = $this->input->post('level');

			set_flashdata("notif", alert("berhasil merubah access"));

			$this->model_user->delete_privilage_by_level($level);
			foreach($this->input->post('resource')  as $res)
			{
				foreach($res as $key => $val)
				{
					$data['level'] = $this->input->post("level");
					$data['resource_idresource'] = $val;
					$result = $this->model_user->insert('privilage', $data);
					if(!$result)
					{
						set_flashdata("notif", alert("Ada beberapa data yang gagal "));
					}
				}
			}

			redirect("admin/user/group?level={$level}");
		}

		$level = $this->input->get('level');
		$data["level"] = $this->config->item("level");
		$data["notif"] = flashdata('notif');
		$data["user_level"] = $this->auth->load_level();
		$data['resource'] = $this->model_user->get_resource($level);
		
		$this->renderAdmin("admin/user/user_group", $data);
	}

	public function get_resource()
	{
		$level = $this->input->get('level');
		$resource = $this->model_user->get_resource($level);
		foreach($resource as $res): ?>
		<label style="margin:0px !important; padding:0px !important;">
			<input <?= in_array(@$_GET['level'], explode(",", $res->level))  ?'checked':''; ?> type="checkbox" class="" value="<?= $res->idresource; ?>" name="resource[][<?= $res->idresource; ?>]">  <?= ucwords(str_replace('_',' ',$res->resource_name)); ?><br/>
		</label>
		<div class=" clearfix" style=" border-bottom:1px solid #ccc; padding:3px 0 3px 0; margin-bottom:3px"></div>
	<?php endforeach; 
	}

	public function update($id = NULL)
	{
		$this->auth->check_access("user_update");
		
		$this->template->title(APP_NAME, "User Update");
		
		$this->form_validation->set_rules("username", "username", "required");
		$this->form_validation->set_rules("fullname", "full name", "required");
		$this->form_validation->set_rules("email", "Email", "required|valid_email");

		if($this->form_validation->run())
		{
			$config['upload_path'] 		= './asset/user-image/';
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
					redirect("admin/user");
				}
				else
				{
					$data_upload = $this->upload->data();
					$data['avatar'] = $data_upload['file_name'];
				}
			}


			$data['username'] = $this->input->post("username");
			$data['level'] = $this->input->post("level");
			$data['fullname'] = $this->input->post("fullname");
			$data['email'] = $this->input->post("email");
			$data['om_update_date'] 			= date("Y-M-d H:i:s");
			$data['om_update_by'] 				= user_admin('iduser');
			$data['om_deletion_flag'] 			= '0';
			$data['idvendor'] = $this->input->post("idvendor");
			$data['idtenant'] 	= $this->input->post("idtenant");
			$data['is_idap'] 	= $this->input->post("is_idap");

			$this->model_user->change($id, $data);

			set_flashdata("notif", alert("Berhasil mengupdate data user"));

			redirect("admin/user");
		}


		$data["user_level"] = $this->model_user->load_level();
		$data['row'] = $this->model_user->find($id);
		$data["result_vendor"] = $this->model_user->get_all_vendor();
		$data["result_tenant"] = $this->model_user->get_all_data('Client');
		$this->renderAdmin("admin/user/user_update", $data);
	}

	public function profile()
	{
		$this->auth->check_access("user_profile");
		$id = userdata('session_user')->iduser;
		
		$this->template->title(APP_NAME, "Profile");
		
		$this->form_validation->set_rules("username", "username", "required");
		$this->form_validation->set_rules("fullname", "full name", "required");

		if($this->form_validation->run())
		{
			$config['upload_path'] 		= './asset/user-image/';
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
					redirect("admin/user");
				}
				else
				{
					$data_upload = $this->upload->data();
					$data['avatar'] = $data_upload['file_name'];
				}
			}

			$data['username'] = $this->input->post("username");
			$data['fullname'] = $this->input->post("fullname");

			$this->model_user->change($id, $data);

			set_flashdata("notif", alert("Berhasil mengupdate profile"));

			redirect("admin/user/profile");
		}


		$data["user_level"] = $this->config->item("level");
		$data['row'] = $this->model_user->find($id);
		$data['notif'] = flashdata("notif");

		$this->renderAdmin("admin/user/user_profile", $data);
	}

	public function change_password()
	{
		$this->auth->check_access("user_change_password");
		$id = userdata('session_user')->iduser;
		
		$this->template->title(APP_NAME, "Change Password");
		
		$this->form_validation->set_rules("oldpassword", "Old Password", "required|callback_check_old_password");
		$this->form_validation->set_rules("newpassword", "New Password", "required");
		$this->form_validation->set_rules("renewpassword", "Re New Password", "required|matches[newpassword]");

		if($this->form_validation->run())
		{

			$data['password'] = enkripsi($this->input->post("newpassword"));

			$this->model_user->change($id, $data);

			set_flashdata("notif", alert("Berhasil mereset password"));

			redirect("admin/user/profile");
		}


		$data["user_level"] = $this->config->item("level");
		$data['row'] = $this->model_user->find($id);

		$this->renderAdmin("admin/user/user_change_password", $data);
	}

	public function check_old_password($oldpassword)
	{
		$username = user_admin('username');
		if(!$row = $this->model_user->cek_login($username, enkripsi($oldpassword)))
		{	
			$this->form_validation->set_message('check_old_password', 'The %s Is Invalid');
			return false;
		}
		else
		{
			return true;
		}
	}


	public function delete($id = NULL)
	{
		$this->auth->check_access("user_delete");

		$this->model_user->soft_remove($id);

		set_flashdata("notif", alert("Berhasil menghapus data user", "success"));

		redirect("admin/user");
	}


	public function delete_resource($id = NULL)
	{
		$this->auth->check_access("user_group_right");

		$this->model_user->delete('resource', 'idresource', $id);

		set_flashdata("notif", alert("Berhasil menghapus data resource", "success"));

		redirect("admin/user/group");
	}


	public function login()
	{

		if(userdata("admin_id")){
			redirect("admin/home");
		}

		$access = $this->model_user->get_access();

		set_userdata('resource_access', $access);
		$username = $this->input->post("user_name");
		$password = $this->input->post("user_password");

		$this->form_validation->set_rules("user_name", "username", "required|xss_clean");
		$this->form_validation->set_rules("user_password", "password", "required|xss_clean");

		if($this->form_validation->run())
		{
			if(!$row = $this->model_user->cek_login($username, enkripsi($password)))
			{
				echo "<div class='text-danger'>Invalid username or password</div>";				
			}
			else
			{

				set_userdata("user_level", "admin");
				set_userdata('session_user', $row);
				set_userdata("admin_id", $row->iduser );
			
				redirect("admin/home");				
			}
		}

		$data['notif'] = flashdata("notif");
		$this->load->view("admin/user/login", $data);
		
	}

	public function logout()
	{

		unset_userdata('resource_access');
		unset_userdata('session_admin');
		unset_userdata("user_level");
		unset_userdata("admin_id");
		unset_userdata('access');

		unset_all_userdata();
		redirect("admin/user/login");
	}


	public function ajax_login()
	{
		$username = $this->input->get("user_name");
		$password = $this->input->get("user_password");

		$row = $this->model_user->get_single('user', 'username', $username);

		if(count($row) > 0)
		{
			if($row->is_idap)
			{
				$_GET['username'] = $username;
				$_GET['password'] = $password;
				//akses menggunakan LDAP
				$result = $this->check_idap($username, $password);

				if($result == 'berhasil')
				{
					$row->level_name = $this->auth->level_name_by_idlevel($row->level);
					set_userdata('session_user', $row);
					set_userdata("admin_id", $row->iduser );
					$this->model_user->change($row->iduser, array('last_login' => date('Y-m-d H:i:s')));
					echo json_encode(array("message"=>"<div class='text-successs'>Success Login with LDAP</div>", "result" => 1));	
				}
				else
				{
					echo json_encode(array("message"=>"<div class='text-danger'>Invalid username or password</div>", "result" => 0));
				}

			}
			else
			{
				//akses tidak menggunakan LDAP
				if(!$row = $this->model_user->cek_login($username, enkripsi($password)))
				{			
					echo json_encode(array("message"=>"<div class='text-danger'>Invalid username or password</div>", "result" => 0));
				}
				else
				{
					$row->level_name = $this->auth->level_name_by_idlevel($row->level);
					set_userdata('session_user', $row);
					set_userdata("admin_id", $row->iduser );

					$this->model_user->change($row->iduser, array('last_login' => date('Y-m-d H:i:s')));
					echo json_encode(array("message"=>"<div class='text-successs'>Success Login</div>", "result" => 1));	
				}
			}
		}
		else
		{
			echo json_encode(array("message"=>"<div class='text-danger'>Invalid username or password</div>", "result" => 0));
		}

	}

	public function export_excel()
    {
    	$this->load->library("excel");

		$query = $this->db->query("SELECT 
			u.user_name as `Username`,
		 	u.user_full_name as `Full Name`,
		 	u.user_group as `Group`,
		 	u.user_email as `Email`,
		 	u.user_date_active as `Date Active`,
		 	u.user_status as `Status`

		 	FROm user u
		 	");

		$this->excel->setActiveSheetIndex(0);

		$fields = $query->list_fields();

        $col = 0;
        foreach ($fields as $field)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        }
 
        $row = 2;
        foreach($query->result() as $data)
        {
            $col = 0;
            foreach ($fields as $field)
            {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
                $col++;
            }
 
            $row++;
        }

		$this->excel->getActiveSheet()->setTitle('user');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=protelindo-user-' . date("Y-M-d") . '.xslx');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');

    }

    public function lupa_password()
    {
    	if(userdata("admin_id")){
			redirect("admin/home");
		}

    	$this->form_validation->set_rules("email", "Email", "required|valid_email|callback_cek_mail");

        if($this->form_validation->run())
        {
            $email = $this->input->post('email');
            $key = $this->auth->generete_key();

            $this->config->load('mailer');

            $data_mailer = array(
                "from"       => $this->config->item('email_from'),
                "to"         => $email,
                "subject"    => "Reset Password",
                "module"     => "vendorUser@resetPassword",
                "message"    => "Klik tautan berikut ini untuk reset password <a href='".site_url('admin/user/reset_password/'.$key)."'>Reset Password</a>"
            );

            $idmailer = $this->mailer->save($data_mailer);

            $this->mailer->send_mail($idmailer);

            $data_reset_password = array(
                "key" => $key,
                "date_created" => date("Y-m-d H:i:s"),
                "email" => $email,
                "table" => "user"
                );

            $this->model_user->insert('reset_password', $data_reset_password);
            set_flashdata('notif', alert('Konfirmasi reset password telah di kirim ke '.$email));

            redirect(current_url());
        }

        $data['notif'] = flashdata('notif');
        $this->load->view('admin/user/reset_password', $data);
    }

    public function cek_mail($mail)
    {
    	$result = $this->db->get_where('user', array('email' => $mail));
    	if($result->num_rows() > 0)
    	{
    		return true;
    	}
    	else
    	{
    		$this->form_validation->set_message('cek_mail', 'Email tidak terdaftar');
    		return false;
    	}
    }

    public function reset_password($key = NULL)
	{

		if($key)
		{
			$row = $this->Model_reset_password->get_reset_password('user', $key);
			if($row)
			{
				$today = date('Y-m-d H:i:s');
				$expired = $row->date_created;
				$hourdiff = round((strtotime($today) - strtotime($expired)) / 3600, 1);

				if ($hourdiff >= 24)
				{
					$data['message'] = 'Batas waktu telah berakhir';
				}
				else
				{
					$random_password = substr(md5(time()), 0,6);

					$data_mailer = array(
						"from"       => $this->config->item('email_from'),
						"to"         => $row->email,
						"subject"    => "Success Reset Password",
						"module"     => "vendorUser@SuccessResetPassword",
						"message"    => "Password anda berhasil di reset, silahkan login ke akun anda dengan password ".$random_password
					);

					$idmailer = $this->mailer->save($data_mailer);

            		$this->mailer->send_mail($idmailer);

					$data_user = array(
						"password" => enkripsi($random_password)
						);

					$result = $this->model_user->update_byemail($row->email, $data_user);

					$result = $this->Model_reset_password->change($row->idreset_password, array("status" => "done"));

					if($result)
					{
						$data['message'] = 'Berhasil Reset Password, password akan segera di kirim ke email anda';
					}
					else
					{
						$data['message'] = 'Gagal Merese Password';
					}
				}
			}
			else
			{
				$data['message'] = 'Link reset password tidak valid';
			}
		}
		else
		{
			$data['message'] = 'Key Kosong';
		}

		$data['title'] = 'Reset Password';
		$this->load->view('public/message', $data);
	}


	public function add_group()
	{
		$this->auth->check_access("user_group_add");

		$this->form_validation->set_rules("group_name", "Group Name", "required|is_unique[group.group_name]");

		$this->form_validation->set_error_delimiters('','');

		if($this->form_validation->run())
		{
			$data['group_name'] = $this->input->post("group_name");
			
			$this->model_user->insert('group', $data);

			set_flashdata("notif", alert("berhasil menambah group"));

		}
		else
		{
			set_flashdata("notif", alert(validation_errors(), "danger"));
		}
		redirect("admin/user/group");
	}

	
	public function check_idap($username = '', $password = '')
	{
		$_GET['username'] = $_GET['username'];
		$_GET['password'] = $_GET['password'];
		ob_start();
		$response = require_once FCPATH. 'sandbox/radiusphp/loginPost.php';
		$buffer = ob_get_contents();
		@ob_end_clean();
		
		return $response;

	}

	public function qc_transfer($offset = 0)
	{
		$this->auth->is_allowed('qc_transfer');

		$this->template->title(APP_NAME, "User QC List");

		$limit = isset($_GET['limit'])?$_GET['limit']:10;
		$q = isset($_GET['q'])?$this->input->get('q', TRUE):'';


		$config['base_url']     = "admin/tt/index/";
		$config['total_rows']   = $this->model_user->count_all_user_qc($q);
		$config['per_page']     = $limit;
		$config['uri_segment']  = 4;

		$data['pagination']    	= $this->pagination($config);
	
		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_user->get_user_qc($offset, $limit, $q);
		$data['total_rows'] = $config['total_rows'];

		$this->renderAdmin("admin/user/user_qc_transfer", $data);
	}


	public function set_qc_status()
	{
		$this->auth->is_allowed('qc_transfer');

		$this->form_validation->set_rules("status", "status", "required");
		$this->form_validation->set_rules("iduser", "iduser", "required");

		if($this->input->post('status')  == 'unavailable')
		{
			$this->form_validation->set_rules("remark", "remark", "required");
		}
		if($this->form_validation->run())
		{
			$status = $this->input->post("status");
			$iduser = $this->input->post('iduser');
			$data['user_status'] = $status ;
			$this->model_user->change($iduser, $data);

			if($status == 'unavailable')
			{
				$result_task = $this->model_task->get_task_pending_byuserqc($iduser);

				foreach($result_task as $task)
				{
					$qc_protelindo = $this->round_robin->get_next_protel_user();
					$user = $this->model_user->find($qc_protelindo);

					$data_log_qc = array(
						'task_type' => 'pm',
						'idtask' => $task->idpm_plan_detil,
						'log_created_date' => date('Y-m-d H:i:s'),
						'idqc_before' => $iduser,
						'idqc_after' => $qc_protelindo,
						'iduser_admin' => user_admin('iduser'),
						'remark' => $this->input->post('remark')
						);

					$this->model_log_qc->store($data_log_qc);


					$data_task = array(
						'pmp_qc_protelindo' => $qc_protelindo, 
						'om_update_by' => user_admin('iduser'),
						'om_update_date' => date('Y-m-d H:i:s')
					);

					$result_update_task = $this->model_task->update('pm_plan_detail', $data_task, 'idpm_plan_detil', $task->idpm_plan_detil);

					if(!$result_update_task)
					{
						set_flashdata("notif", alert("Gagal menetapkan task", 'danger'));
						redirect_back();
					}
				}
			}
			set_flashdata("notif", alert("Berhasil merubas status ke ".$status));
		}
		else
		{
			set_flashdata("notif", alert("Gagal merubah status ".validation_errors(), 'danger'));
		}
		redirect_back();

	}
}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */



