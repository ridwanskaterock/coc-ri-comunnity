<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends front
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_base');
		$this->load->model('model_comment');
		$this->load->helper('download_helper');
	}

	public function index()
	{
		$data['title'] = 'Base';
		$data['type'] = isset($_GET['type']) ? $_GET['type'] : 'news';
		$data['result'] = $this->model_base->get_base(NULL, 10, 0);

		$this->template->render('front/base/base-list', $data);
	}

	public function detail($idbase = null)
	{
		$data['title'] = 'Detail';
		$data['row'] = $this->model_base->find_base_by_idbase($idbase);
		$data['result_comment'] = $this->model_comment->find_comment_by_idbase($idbase);
		$data['total_comment'] = count($data['result_comment']);
		$data['user_comment'] = $this->model_comment->find_comment_user_base_by_idbase($data['row']->idbase, user_member('iduser'));

		$this->template->render('front/base/base-detail', $data);
	}

	public function download_base_image($idbase)
	{
		$base = $this->model_base->find($idbase);

		$image_name = $base->base_image;
		$file = FCPATH . 'asset/base-image/'.$image_name;
		$filename = url_title($base->base_name . '-TH' . $base->base_town_hall);

		force_download($filename,$file);
	}

	public function create()
	{
		$this->auth->allow(array('member'));

		$this->form_validation->set_rules('base_name', 'Base Name', 'required|min_length[5]');

		if($this->form_validation->run()) {

			$base_name = $this->input->post('base_name');
			$base_desc = $this->input->post('base_desc');
			$base_town_hall = $this->input->post('base_town_hall');

			$data = array(
				'base_name' => $base_name,
				'base_desc' => $base_desc,
				'base_town_hall' => $base_town_hall,
				'base_created_date' => now(),
				'base_created_by' => user_member('iduser')
			);

			$config['upload_path'] = FCPATH . 'asset/base-image/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']  = '999999';
			$config['max_width']  = '999999';
			$config['max_height']  = '999999';
			$config['filename'] = 'tes.jpg';

			$this->load->library('upload', $config);
			
			if($_FILES AND $_FILES['base_image']['name'])
			{
				if ( ! $this->upload->do_upload("base_image"))
				{
					$error = $this->upload->display_errors();
					set_flashdata("notif", $error);
					redirect_back();
				}
				else
				{
					$data_upload = $this->upload->data();

					$data['base_image'] = $data_upload['file_name'];
					set_flashdata("notif", 'Success share base');
				}
			}
			else
			{
				set_flashdata("notif", 'Please choose once image.');
				redirect_back();
			}

			$idbase = $this->model_base->store($data);

			redirect('base/detail/'.$idbase);

		}

		$data = array();

		$this->template->render('front/base/create-base', $data);
	}
}

/* End of file Base.php */