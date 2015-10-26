<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Team extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_team");
		$this->config->load("auth");
	}

	public function index()
	{
		$this->auth->check_access("team");

		$this->template->title(APP_NAME, "team List");

		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_team->find_all();
		$data['result_enginer'] = $this->model_team->get_where('user', array('user_group' => 'enginer'));

		$this->renderAdmin("admin/team/team_list", $data);
	}

	public function add($id = NULL)
	{
		$this->auth->check_access("team_add");
		
		$this->template->title(APP_NAME, "team Add");

		$this->form_validation->set_rules("team_name", "Team Name", "required");
		$this->form_validation->set_rules("leader_id", "Leader Name", "required");

		if($this->form_validation->run())
		{
			$data['team_name'] = $this->input->post("team_name");
			$data['leader_id'] = $this->input->post("leader_id");

			$this->model_team->store($data);

			set_flashdata("notif", alert("berhasil menambah data"));

			redirect("admin/team");
		}

		$data["result_enginer"] = $this->model_team->get_where('user', array('user_group' => 'enginer'));

		$this->renderAdmin("admin/team/team_add", $data);
	}

	public function update($id = NULL)
	{
		$this->auth->check_access("team_update");
		
		$this->template->title(APP_NAME, "team Update");
		
		$this->form_validation->set_rules("team_name", "Team Name", "required");
		$this->form_validation->set_rules("leader_id", "Leader Name", "required");

		if($this->form_validation->run())
		{
			$data['team_name'] = $this->input->post("team_name");
			$data['leader_id'] = $this->input->post("leader_id");

			$this->model_team->change($id, $data);

			set_flashdata("notif", alert("berhasil update data"));

			redirect("admin/team");
		}

		$data["result_enginer"] = $this->model_team->get_where('user', array('user_group' => 'enginer'));

		$data["team_level"] = $this->config->item("level");
		$data['row'] = $this->model_team->get_team_single($id);

		$this->renderAdmin("admin/team/team_update", $data);
	}

	public function delete($id = NULL)
	{
		$this->auth->check_access("team_delete");

		$this->model_team->remove($id);

		set_flashdata("notif", alert("Berhasil menghapus data team", "success"));

		redirect("admin/team");
	}

	public function ajax_view_team_member($id = NULL)
	{
		$query = $this->db->get_where('user');
		foreach ($query->result() as $row) {
			?>
			<tr>
				<td><?= $row->user_id; ?></td>
				<td><?= $row->user_full_name; ?></td>
			</tr>
			<?php
		}
	}

	public function export_excel()
    {
    	$this->load->library("excel");

		$query = $this->db->query("SELECT 
			u.team_name as `teamname`,
		 	u.team_full_name as `Full Name`,
		 	u.team_group as `Group`,
		 	u.team_email as `Email`,
		 	u.team_date_active as `Date Active`,
		 	u.team_status as `Status`

		 	FROm team u
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

		$this->excel->getActiveSheet()->setTitle('team');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=protelindo-team-' . date("Y-M-d") . '.xslx');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');

    }

}


/* End of file team.php */
/* Location: ./application/controllers/admin/team.php */