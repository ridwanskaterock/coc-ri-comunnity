<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Pm extends Admin{

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_pm_history");

	}

	public function index( $offset = 0 )
	{
		$this->auth->check_access("pm");
		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_pm->get_pm();
		$this->renderAdmin("admin/pm/pm_list", $data);
	}

	public function plan( $offset = 0 )
	{
		$this->auth->check_access("pm_plan");
		$data = [];
		$this->renderAdmin("admin/pm/pm_plan", $data);
	}

	public function pdf_detail($id = NULL)
	{
    	if(!$this->auth->is_allowed("pm_pdf_detail"))
    		exit("Maaf anda tidak berhak untuk melihat ini");

    	$this->load->library("HtmlPdf");


		$data['row'] = $this->model_pm->find($id);


    	$config = array(
    		"orientation" => "p",
    		"format" => "A4",
    		"marges" => array(5, 5, 5, 5)
    		);

    	$this->pdf = new HtmlPdf($config);

    	$content = $this->pdf->loadHtmlPdf("admin/pm/pm_pdf", $data, TRUE);

    	$this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output("protel" . "-" . date('Y-m-d') . '.pdf');
	 
	}


	public function reject_remark()
	{
		$this->auth->check_access("pm_reject_remark");

		$id = $this->input->post("pm_id");

		$data = array(
			"pmh_remark" 	=> $this->input->post("remark"),
			"pmh_status" 	=> "rejected",
			"pmh_by" 		=> user_admin("user_name"),
			"pmh_date" 		=> date("Y-m-d H:i:s"),
			"PM_idpm" 		=> $id

		);

	
		$result = $this->model_pm_history->store($data);
	

		set_flashdata("notif", alert("Success rejected & remark data", "success"));
		redirect("admin/pm");
	}

	public function approve_remark()
	{
		$this->auth->check_access("pm_approve_remark");

		$id = $this->input->post("pm_id");

		$data = array(
			"pmh_remark" 	=> $this->input->post("remark"),
			"pmh_status" 	=> "Approve Protelindo",
			"pmh_by" 		=> user_admin("user_name"),
			"pmh_date" 		=> date("Y-m-d H:i:s"),
			"PM_idpm" 		=> $id

		);
		$result = $this->model_pm_history->store($data);

		set_flashdata("notif", alert("Success approve & remark data", "success"));
		redirect("admin/pm");
	}


	public function approve($id = NULL)
	{
		$this->auth->check_access("pm_approve");
		
		$data = array(
			"pmh_remark" 	=> "",
			"pmh_status" 	=> "Approve Protelindo",
			"pmh_by" 		=> user_admin("user_name"),
			"pmh_date" 		=> date("Y-m-d H:i:s"),
			"PM_idpm" 		=> $id

		);

		$result = $this->model_pm_history->store($data);

		set_flashdata("notif", alert("Success approve data", "success"));
		redirect("admin/pm");
	}

	public function get_tenant()
	{
		$idpm = $this->input->get('idpm');
		$result = $this->model_pm->get_tenant_by_idpm($idpm);

		if($result->num_rows()>0)
		{
			?>
			<ul>
			<?php
			foreach($result->result() as $row)
			{

				?>
	               <li>
		               <label>
		               		<?= $row->idtenant; ?>
		               </label>
	               </li>
				<?php
			}
			?>
			</ul>
			<?php
		}
		else
		{
			?>
			<ul>
               <li>
	               <label>
	               		No Data Tenant
	               </label>
               </li>
			</ul>
			<?php
		}
	}

	public function get_history()
	{
		$idpm = $this->input->get('idpm');
		$data['history'] = $this->model_pm->get_pm_history_by_idpm($idpm);

		$this->load->view('admin/pm/pm_history_template', $data);

	}

	public function export_excel()
    {

    	$this->load->library("excel");

		$query = $this->db->get("pm");

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

		$this->excel->getActiveSheet()->setTitle('pm');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=protelindo-pm-' . date("Y-M-d") . '.xslx');
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


/* End of file pm.php */
/* Location: ./application/controllers/admin/pm.php */