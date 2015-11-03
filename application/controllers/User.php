<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends front
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_user');
	}

	public function login(){
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean|valid_email');

		if($this->form_validation->run())
		{
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$result = $this->model_user->cek_login($email, enkripsi($password));

			if($result)
			{
				if($result->user_status == 'active')
				{
					set_userdata('session_user', $result);
					set_userdata('user_level', 'member');

					$data['flag'] = 1;
				}
				elseif($result->user_status == 'blocked')
				{
					$data['msg'] = 'Your account blocked';
					$data['flag'] = 0;
				}
				elseif($result->user_status == 'pending')
				{
					$data['msg'] = 'You have not completed the registration process';
					$data['flag'] = 0;
				}
				elseif($result->user_status == 'delete')
				{
					$data['msg'] = 'Your account has been deleted';
					$data['flag'] = 0;
				}
			}
			else
			{
				$data['msg'] = 'invalid email or password';
				$data['flag'] = 0;
			}

		}
		else
		{
			$data['msg'] = 'invalid email or password';
			$data['flag'] = 0;
		}


		echo json_encode($data);
	}


	public function register()
	{
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|is_unique[user.user_name]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|is_unique[user.user_email]');

		$this->form_validation->set_message('is_unique', 'The %s has been registered');

		if($this->form_validation->run())
		{
			$code = generate_code(12);
			$name = $this->input->post('name');
			$password = $this->input->post('password');
			$email = $this->input->post('email');

			$data = array(
				'user_name' => $name,
				'user_password' => enkripsi($password),
				'user_email' => $email,
				'user_status' => 'active',
				'user_created_date' => now(),
				'user_image' => 'default.jpg',
				'user_registation_reff' => 'web'
			);

			$iduser = $this->model_user->store($data);
			$row = $this->model_user->find($iduser);

			set_userdata('session_user', $row);
			set_userdata('user_level', 'member');

			$outs['msg'] = 'Registration success';
			$outs['flag'] = 1;	

		}
		else
		{
			$outs['msg'] = validation_errors();
			$outs['flag'] = 0;
		}

		echo json_encode($outs);
	}

	public function logout()
	{
		unset_all_userdata();

		redirect('home');
	}

}

/* End of file User.php */
/* Location: ./application/controllers/User.php */