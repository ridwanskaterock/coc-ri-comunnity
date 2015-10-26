<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends Admin {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('round_robin');
		$this->load->model('admin/model_user');
		$this->load->model('admin/model_task');
		$this->load->library('table');
	}


	/**
	* untuk mendapatkan iduser Protelindo QC, 
	* #note : value pada application/libraries/Container_id_protel_qc.json akan berubah secara otomatis
	*/
	public function get_next_protel_qc()
	{
		$this->auth->login_scurity();

		$qc_protelindo = $this->round_robin->get_next_protel_user();
		$user = $this->model_user->find($qc_protelindo);

		$html = "<h1>User Protelindo QC </h1>";

		$html .= "<table border='1' style='border-collapse: collapse;'>";
		foreach($user as $key => $val)
		{
			$html .= "<tr>";
			$html .= "<td>".$key."</td>"; 
			$html .= "<td>".$val."</td>"; 
			$html .= "</tr>";
		}

		$html .= "</table>";
		echo $html;
	}


	/**
	* untuk menetapkan user QC protelindo pada task yg di tentukan
	* #note : value pada application/libraries/Container_id_protel_qc.json akan berubah secara otomatis
	*
	* @param idtask integer
	*/
	public function set_qc_for_task($idtask = null)
	{
		$this->auth->login_scurity();
		if(!$idtask) die('Id task kosong');

		$task = $this->model_task->get_single('pm_plan_detail', 'idpm_plan_detil', $idtask);

		if($task)
		{
			if($task->pmp_qc_protelindo != null)
			{
				die('QC Protelindo sudah di tetapkan sebelumnya');
			}
		}

		$qc_protelindo = $this->round_robin->get_next_protel_user();
		$user = $this->model_user->find($qc_protelindo);

		$this->model_task->update('pm_plan_detail', array('pmp_qc_protelindo' => $user->iduser), 'idpm_plan_detil', $idtask);

		$html = "<h1>berhasil menetapkan User Protelindo QC</h1>";

		$html .= "<table border='1' style='border-collapse: collapse;'>";
		foreach($user as $key => $val)
		{
			$html .= "<tr>";
			$html .= "<td>".$key."</td>"; 
			$html .= "<td>".$val."</td>"; 
			$html .= "</tr>";
		}

		$html .= "</table>";

		$html .= "<br> untuk task : ";
		$html .= anchor( site_url('admin/task/index/?idtask='.$idtask));

		echo $html;
	}
}

/* End of file App.php */
/* Location: ./application/controllers/admin/App.php */