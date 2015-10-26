<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_task");
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_user");
		$this->load->model("admin/model_vendor_user");
		$this->load->model("admin/model_site");
        $this->load->model('admin/model_meta');
        $this->load->model('admin/model_pdf_historical');
		$this->load->model("admin/model_vendor_user_region");
		$this->load->helper("string");
		$this->load->library('Round_robin');

	
	}

	public function index( $offset = 0 )
	{
		$this->auth->check_access("task");

		$this->load->library('pagination');
		$limit = isset($_GET['limit'])?$_GET['limit']:10;
		$q = isset($_GET['q'])?$_GET['q']:'';

		$data_filter = array();

		if(isset($_GET['siteid']) AND !empty($_GET['siteid']))
		{
			$data_filter['pmp_idsite'] = $_GET['siteid'];
		}

		if(isset($_GET['region']) AND !empty($_GET['region']))
		{
			$data_filter['Region'] = $_GET['region'];
		}

		if(isset($_GET['priority']) AND !empty($_GET['priority']))
		{
			$data_filter['pmp_priority'] = $_GET['priority'];
		}

		if(isset($_GET['status']) AND !empty($_GET['status']))
		{
			$data_filter['pmp_status'] = $_GET['status'];
		}

		if(isset($_GET['period']) AND !empty($_GET['period']))
		{
			$data_filter['pm_period_idpm_period'] = (int) $_GET['period'];
		}

		if(isset($_GET['task_id']) AND !empty($_GET['task_id']))
		{
			$data_filter['idpm_plan_detil'] = (int) $_GET['task_id'];
		}

		if(isset($_GET['type']) AND !empty($_GET['type']))
		{
			$type = $_GET['type'];
			$idprotelqc = $this->input->get('idprotelqc');
			if($type == 'allqc')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
			elseif($type == 'qccompletion')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
			elseif($type == 'qcwaitingapproval')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
		}

		$config['base_url']     = "admin/task/index/";
		$config['total_rows']   = $this->model_task->count_all($data_filter);
		$config['per_page']     = $limit;
		$config['uri_segment']  = 4;

		$data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		$data['result']			= $this->model_task->get_pm($offset, $limit, $data_filter);
		$data['result_region'] 	= $this->model_task->get_all_data('Region');
		$data['result_vendor'] 	= $this->model_task->get_vendor();
		$data['result_status'] 	= $this->model_pm->get_status();
		$data['result_period'] 	= $this->model_pm->get_all_data('pm_period');
		$data['total_rows']		= $config['total_rows'];


		if($this->input->get('template') == 'no')
		{
			$this->load->view("admin/task/task_list", $data);
		}
		else
		{
			$this->renderAdmin("admin/task/task_list", $data);
		}

	}

	public function add()
	{
		$this->auth->check_access("task_add");
		$this->template->title(APP_NAME, "task Add");
		$this->form_validation->set_rules("project_id", "Project ID", "required");
		$this->form_validation->set_rules("task_type", "Task Type", "required");
		$this->form_validation->set_rules("plan_date", "Plan Date", "required");
		$this->form_validation->set_rules("remark", "Remark", "required");

		if(isset($_POST['idsite']))
		{
			$idsite = $_POST['idsite'];
			set_userdata("idsite", $idsite);
		}

		if($this->form_validation->run())
		{
			$data['pm_idsite'] = $this->input->post("idsite");
			$data['pm_plan'] = $this->input->post("plan_date");
			$data['pm_date_added'] = date("Y-m-d H:i:s");
			$this->model_task->store($data);
			set_flashdata("notif", alert("berhasil menambah data"));
			unset_userdata('idsite');
			redirect("admin/user");
		}

		$data = array();

		if(userdata('idsite'))
		{
			$data['result_site'] = userdata('idsite');
		}
		else
		{
			$data['result_site'] = array();
		}

		$this->renderAdmin('admin/task/task_add', $data);
	}

	public function delete($idpm)
	{
		$this->auth->login_scurity();
		
		$result = $this->model_pm->soft_remove($idpm);

		set_flashdata('notif', alert('berhasil menghapus data', 'success'));

		redirect('admin/task');
	}


	public function add_site()
	{

		$data = array(
			"site_name"         => $this->input->post('site_name'),
			"site_date_created" => $this->input->post('site_plan_date')
			);

		echo curlPost("http://localhost/protelindo/api/site/insert", $data);
	}

	
	public function get_tenant()
	{
		$this->auth->login_scurity();

		$idpm = $this->input->get('idpm');
		$siteid = $this->input->get('siteid');

		$result = $this->model_pm->get_tenant_bysiteid($siteid);

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
							<?= $row->ClientId; ?> |  <?= $row->SiteId; ?>
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

	public function delete_site($idsite = NULL)
	{
		$data = array(
			"idsite"         => $idsite
			);

		echo curlPost("http://localhost/protelindo/api/site/delete", $data);
	}

	public function action()
	{
		$this->auth->login_scurity();

		$id = $this->input->post("pm_id");
		$type = $this->input->post("type");
		$remark = $this->input->post("remark");
		$level = $this->auth->level_name_by_idlevel(user_admin('level'));

		//approval melalui open last pdf

		if(isset($_POST['approval']))
		{
			if($_POST['approval'] == 'Approve')
			{
				$type = 'approve';
			}
			else
			{
				$type = 'rejected';
			}
		}

		$message = NULL;

		switch ($level) {
			case 'pm_vendor_qc': case 'pm_vendor_manager': case 'tt_vendor_manager': case 'tt_vendor_helpdesk' : case 'pm_tt_vendor_manager' :
			$qc_protelindo 		= NULL;
			$db_qc_protel 		= $this->model_task->get_single('pm_plan_detail', 'idpm_plan_detil' , $id);
			$check_qc_protel 	= ($db_qc_protel->pmp_qc_protelindo == 0 AND empty($db_qc_protel->pmp_qc_protelindo)) ? TRUE : FALSE ;

			if($type == 'approve'){
				$type = 'Need Approval by Protelindo';
				if($check_qc_protel)
				{
					$qc_protelindo = $this->round_robin->get_next_protel_user();

					//jika gagal mendapatkan id Protel QC
					if(!$qc_protelindo)
					{
						set_flashdata('notif', alert('Error Assign QC for task.' . $id . '. please try again ' . $qc_protelindo, 'danger'));
						redirect_back('admin/task');
					}

					$user = $this->model_user->find($qc_protelindo);
					$message = ', And task assign to protel qc : '.@$user->username;
				}
				else
				{
					$qc_protelindo = $db_qc_protel->pmp_qc_protelindo;
				}
			}
			else
			{
				$type = 'Rejected by Vendor';
			}

			$data_plan_detil = array(
				"om_update_date" 	=> date("Y-m-d H:i:s"),
				"om_update_by" 		=> user_admin('iduser'),
				"pmp_status" 		=> $type,
				"pmp_qc_protelindo" => $qc_protelindo,
				"pmp_remark" 		=> $remark,
				);

			break;

			case 'pm_protel_qc' : case 'tt_protel_manager' : case 'super_admin':
			
			if($type == 'approve'){
				$type = 'Approve by Protelindo';
			}
			else
			{
				$type = 'Rejected by Protelindo';
			}

			$data_plan_detil = array(
				"om_update_date" => date("Y-m-d H:i:s"),
				"om_update_by" => user_admin('iduser'),
				"pmp_status" => $type,
				"pmp_remark" => $remark,
				"flag_last_checked" => 1
				);
			break;

			case 'pm_protel_manager' : 
			if($type == 'approve'){
				$type = 'Approve by Manager';
			}
			else
			{
				$type = 'Rejected by Manager';
			}

			$data_plan_detil = array(
				"om_update_date" => date("Y-m-d H:i:s"),
				"om_update_by" => user_admin('iduser'),
				"pmp_status" => $type,
				"pmp_remark" => $remark,
				);
			break;


			default:
			$data_plan_detil = array(
				"om_update_date" => date("Y-m-d H:i:s"),
				"om_update_by" => user_admin('iduser'),
				"pmp_remark" => $remark,
				);
			break;
		}

		$this->model_pm->update('pm_plan_detail', $data_plan_detil, 'idpm_plan_detil', $id);

		$data = array(
			"pmh_remark" 					=> $this->input->post("remark"),
			"pmh_status" 					=> $type,
			"pmh_by" 						=> user_admin('iduser'),
			"pmh_date" 						=> date("Y-m-d H:i:s"),
			"PM_PLAN_DETIL_idpm_plan" 		=> $id
			);


		$result_history = $this->model_task->insert('pm_history', $data);
		
		$data = array(
			"om_update_date" 	=> date('Y-m-d H:i:s'),
			"om_update_by" 		=> user_admin('iduser')
			);

		$this->model_task->update('pm', $data, 'idpm', $id);

		set_flashdata("notif", alert("Success {$type} data ".$message, "success"));
		redirect_back("admin/task");
	}

	public function select_pic($idpm = NULL, $vendor = NULL)
	{
		$this->auth->login_scurity();

		
		$data_plan_detil = array(
			"om_update_date" 	=> date("Y-m-d H:i:s"),
			"om_update_by" 		=> user_admin('iduser'),
			"pmp_status" 		=> "Waiting PIC submission",
			"vendor_user_idvendor_user_pic" 		=> $vendor,
		);
			
		$this->model_pm->update('pm_plan_detail', $data_plan_detil, 'idpm_plan_detil', $idpm);

		$data = array(
			"pmh_remark" 					=> $this->input->post("remark"),
			"pmh_status" 					=> "Waiting PIC Submission",
			"pmh_by" 						=> user_admin('iduser'),
			"pmh_date" 						=> date("Y-m-d H:i:s"),
			"PM_PLAN_DETIL_idpm_plan" 		=> $idpm
		);

		$this->model_task->insert('pm_history', $data);

		set_flashdata("notif", alert("Success select PIC ", "success"));
       
		
		redirect_back("admin/task");
	}

	public function get_pic()
	{
		$this->auth->login_scurity();

		$idpm = $this->input->get('idpm');
		$result = $this->model_vendor_user->get_data_vendor_user_bycurrentvendor();


		$flag = TRUE;
		$msg = "Tidak bisa memilih PIC : <br>";

		if($idpm)
		{
			$task = $this->model_task->get_single('pm_plan_detail', 'idpm_plan_detil', $idpm);
		}
		else
		{
			die('id task kosong');
		}

		if($task)
		{
			$site = $this->model_site->get_single('GeneralInfo', 'SiteId', $task->pmp_idsite);
		}
		else
		{
			die('task dengan id '. $idpm. ' tidak di temukan');
		}

		if($site)
		{
        	$result_tenant = $this->model_pm->get_tenant_bysiteid($task->pmp_idsite)->result();
		}
		else
		{
			die('General Info dengan idsite ' . $task->pmp_idsite . ' tidak di temukan ' );
		}

        $site_type = $site->Sitetype;

     

        foreach($result_tenant as $tenant)
        {
        	$equipment_type = $tenant->EquipmentType;
        	if($equipment_type == 0 OR $equipment_type == 3)
        	{
        		if(!empty($equipment_type))
        		{
	        		$flag = FALSE;
	        		$msg .= "TENANT : ".$tenant->ClientId." Equipment Type : ".equipment_type($tenant->EquipmentType)."<br>";
        		}
        	}

        	if(empty($equipment_type))
    		{
    			echo alert("TENANT : ".$tenant->ClientId." Empty Equipment Type ", 'warning');
    		}
        }

        if(!$flag)
        {
        	echo alert($msg, 'danger');
        	exit();
        }

        if($site_type != 1 AND $site_type != 2)
        {
        	echo alert("<b>Warning</b> <br> Site Type : ".$site_type, 'warning');
        }

		$idpm = $this->input->get('idpm');
		?>
		<iframe src="<?= site_url('admin/task/pic_iframe/'.$idpm); ?>" style="width:100%; height:600px; border:none; "></iframe>
		<?php
	}


	public function pic_iframe($idpm = NULL)
	{
		$data['idpm'] = $idpm;
		$data_filter = array();
		if(isset($_GET['area_code']) AND !empty($_GET['area_code']))
		{
			$data_filter['idregion'] = $this->input->get('area_code', TRUE);
		}
		if(isset($_GET['username']) AND !empty($_GET['username']))
		{
			$data_filter['username'] = $this->input->get('username', TRUE);
		}
		$data['pm'] = $this->model_task->get_single('pm_plan_detail', 'idpm_plan_detil', $idpm);
		$data["result_region"] = $this->model_vendor_user->get_all_data('Region');
		$data['result'] = $this->model_vendor_user->get_data_vendor_user_bycurrentvendor($data_filter);
		$this->load->view('admin/task/task_select_pic', $data);
	}


	public function vendor_deadline_date()
	{
		$this->auth->check_access("pm_set_vendor_deadline_date");

		$datePost = $this->input->post('date');
		$idtask = $this->input->post('idtask');
		$date = explode('/', $datePost);

		print_pre($date);

		$tahun = $date[0];
		$bulan = $date[1];
		$hari = $date[2];

		$dateDb = "{$tahun}/{$bulan}/{$hari}";
		$data = array('pmp_vendor_deadline_date' => $dateDb);

		$this->model_task->update('pm_plan_detail', $data, 'idpm_plan_detil', $idtask);

		set_flashdata("notif", alert("Success SET ETA ", "success"));
		redirect_back("admin/task");

	}

	public function assign_multiple_site()
	{
		$this->auth->check_access("pm_assign_multiple_site");

		$data['notif'] 			= flashdata("notif");
		$data['result'] 		= $this->load_task_site_more(0, 5, true);

		$this->renderAdmin("admin/task/task_assign_multiple_site", $data);
	}

	public function load_task_site_more($offset, $limit = 10, $bolean = false)
	{
		$this->auth->check_access("pm_assign_multiple_site");

		$data['result']	= $this->model_task->get_task_more($offset, $limit);

		return $this->load->view("admin/task/template_load_site_more", $data, $bolean);

	}

	public function action_assign_multiple_site()
	{
		$this->auth->login_scurity();

		$vendor = $this->input->get('idpic');

		$data_plan_detil = array(
			"om_update_date" 				=> date("Y-m-d H:i:s"),
			"om_update_by" 					=> user_admin('iduser'),
			"pmp_status" 					=> "Waiting PIC submission",
			"vendor_user_idvendor_user_pic" => $vendor,
		);
			
		$this->model_task->update_multiple_site($data_plan_detil);

		$idtaskarr = $this->input->get('idtask');

		foreach($idtaskarr as $idtask)
		{
			$data = array(
				"pmh_remark" 					=> $this->input->post("remark"),
				"pmh_status" 					=> "Waiting PIC Submission",
				"pmh_by" 						=> user_admin('iduser'),
				"pmh_date" 						=> date("Y-m-d H:i:s"),
				"PM_PLAN_DETIL_idpm_plan" 		=> $idtask
			);

			$this->model_task->insert('pm_history', $data);
		}

		exit();
	}


	public function multiple_get_pic()
	{
		$this->auth->login_scurity();

		$idpm = $this->input->get('idpm');
		$result = $this->model_vendor_user->get_data_vendor_user_bycurrentvendor();
		?>
		<div style='overflow:auto; max-height:400px'>
		<table class="table table-border" id="data_table">
			<tr>
				<th>Username</th>
				<th>Action</th>
			</tr>

			<?php
			foreach($result as $row)
			{
				?>
				<tr>
					<th><?= $row->username; ?></th>
					<th><a href="#"  data-dismiss="modal" aria-hidden="true" idpic="<?= $row->idvendor_user; ?>"  picname="<?= $row->username; ?>" class='btn btn-default btn-xs btn-select-pic'><i class="fa fa-cog"></i> Select PIC</a></th>
				</tr>
				<?php
			}
			?>
			 <?php if(count($result)<=0): ?>
             <tr>
                <th colspan="20"><center>No Result</center> </th>
            </tr>
        <?php endif; ?>
		</table>
		</div>
		<?php
	}

	public function test_limit($offset = 0)
	{
		$limit = 4;
		$sql = "
		WITH Result AS
		(
		    SELECT
		        ROW_NUMBER() OVER (ORDER BY PM_idpm) AS RowNum
		    	FROM pm
		    	LEFT JOIN [pm_plan_detail] ON [pm].[idpm] = [pm_plan_detail].[pm_idpm] LEFT JOIN [generalinfo] ON [pm_plan_detail].[pmp_idsite] = [GeneralInfo].[siteId] 
		)
		SELECT *
		FROM Result
		WHERE RowNum >= {$offset}
		AND RowNum < {$offset} + {$limit}
		";

		$result = $this->db->query($sql);

		print_pre($result->result());
	}

	public function get_pdf_history()
	{
		$idpm = $this->input->get('idpm');
		$data['history'] = $this->model_pdf_historical->get_historical($idpm, 'pm');
		$this->load->view('admin/task/task_pdf_history', $data);
	}


	public function create_pm_plan()
	{
		$data_pm = array(
			"pm_vendor" => "KJN",
			"pm_plan_name" => "plan Q2",
			"pm_description" => "",
			"pm_period_idpm_period" => "1011",
			"om_create_date" => date('Y-m-d H:i:s'),
			"om_update_date" => date('Y-m-d H:i:s'),
			"om_deletion_flag" => 0,
		);

		$idpm = $this->db->insert('pm', $data_pm);

		$data_pm_plan = array(
			"PM_idpm" => $idpm,
			"pmp_vendor" => "KJN",
			"pmp_idsite" => "JAW-CCJ-1996-H-B",
			"pmp_status" => "Missing PIC Assignment",
			"pmp_task_type" => "PM",
			"pmp_blocked_access" => 0,
			"om_created_date" => date('Y-m-d H:i:s'),
			"om_update_date" => date('Y-m-d H:i:s'),
			"om_deletion_flag" => 0,
			"pmp_vendor_deadline_date" => date('Y-m-d H:i:s'),
			"pmp_tenant" => "",
		);

		$idpm_plan_detil = $this->db->insert('pm_plan_detail', $data_pm_plan);

		if($idpm <> false AND $idpm_plan_detil <> false)
		{
			echo alert('berhasil membuat data pm plan', 'success');

			print_pre($this->model_pm->find($idpm));
			print_pre($this->model_pm->get_single('pm_plan_detail', 'idpm_plan_detil', $idpm_plan_detil));
		}
		else
		{
			echo alert('gagal membuat data pm plan', 'danger');
		}

	}



	public function vendor()
	{
		$result = $this->db->get('Client')->result();

		foreach($result as $row)
		{
			echo $row->ClientID.' , ';
		}
	}

	public function reporting()
    {
    	$this->load->library("excel");

    	$q = isset($_GET['q'])?$_GET['q']:'';

		$data_filter = array();
		if(isset($_GET['siteid']) AND !empty($_GET['siteid']))
		{
			$data_filter['pmp_idsite'] = $_GET['siteid'];
		}

		if(isset($_GET['region']) AND !empty($_GET['region']))
		{
			$data_filter['Region'] = $_GET['region'];
		}

		if(isset($_GET['priority']) AND !empty($_GET['priority']))
		{
			$data_filter['pmp_priority'] = $_GET['priority'];
		}

		if(isset($_GET['status']) AND !empty($_GET['status']))
		{
			$data_filter['pmp_status'] = $_GET['status'];
		}

		if(isset($_GET['period']) AND !empty($_GET['period']))
		{
			$data_filter['pm_period_idpm_period'] = (int) $_GET['period'];
		}

		if(isset($_GET['task_id']) AND !empty($_GET['task_id']))
		{
			$data_filter['idpm_plan_detil'] = (int) $_GET['task_id'];
		}

		if(isset($_GET['type']) AND !empty($_GET['type']))
		{
			$type = $_GET['type'];
			$idprotelqc = $this->input->get('idprotelqc');
			if($type == 'allqc')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
			elseif($type == 'qccompletion')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
			elseif($type == 'qcwaitingapproval')
			{
				$data_filter['pmp_qc_protelindo'] = (int) $idprotelqc;
			}
		}


		$result = $this->model_task->reporting($data_filter);

		$this->excel->setActiveSheetIndex(0);

		$fields = $result->list_fields();

		$setting_dimension = array(
			'A' => 40,
			'B' => 25,
			'C' => 25,
			'D' => 25,
			'E' => 25,
			'F' => 25,
			'G' => 25,
			'H' => 25,
			'I' => 25,
			'J' => 25,
			'K' => 25,
			'L' => 25,
			'M' => 25,
			);


		foreach($setting_dimension as $col => $width)
		{
			$this->excel->getActiveSheet()->getColumnDimension($col)->setWidth($width);
		}


		//styling
		$this->excel->getActiveSheet()->getStyle('A1:M1')->applyFromArray(
		    array(
		        'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => 'DA3232')
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    )
		);

		$phpColor = new PHPExcel_Style_Color();
		$phpColor->setRGB('FFFFFF');  

		$this->excel->getActiveSheet()->getStyle('A1:M1')->getFont()->setColor($phpColor);

		$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);

		$this->excel->getActiveSheet()->getStyle('A1:M1')
    	->getAlignment()->setWrapText(true); 



        $col = 0;
        foreach ($fields as $field)
        {
        	
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        }
 
        $row = 2;
        foreach($result->result() as $data)
        {
            $col = 0;
            foreach ($fields as $field)
            {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
                $col++;
            }
 
            $row++;
        }


        //set border
    	$styleArray = array(
		      'borders' => array(
		          'allborders' => array(
		              'style' => PHPExcel_Style_Border::BORDER_THIN
		          )
		      )
		  );
		$this->excel->getActiveSheet()->getStyle('A1:M'.$row)->applyFromArray($styleArray);


		$this->excel->getActiveSheet()->setTitle('TT');


		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=PM-Reporting-' . url_title(user_admin('username'))  . '-' . date("Y-M-d") . '.xls');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');



		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');

    }

    public function approve_all_checked()
    {
    	$this->auth->login_scurity();


    	$idtask = $this->input->post('idtask');


    	if(is_array($idtask))
    	{
    		foreach($idtask as $id)
    		{
    			$data_plan_detil = array(
					"om_update_date" => date("Y-m-d H:i:s"),
					"om_update_by" => user_admin('iduser'),
					"pmp_status" => 'Approve by Protelindo',
					"flag_last_checked" => 0
				);


				$result = $this->model_task->update('pm_plan_detail', $data_plan_detil, 'idpm_plan_detil', $id);

				$data = array(
					"pmh_status" 					=> 'Approve by Protelindo',
					"pmh_by" 						=> user_admin('iduser'),
					"pmh_date" 						=> date("Y-m-d H:i:s"),
					"PM_PLAN_DETIL_idpm_plan" 		=> $id
					);

				$result_history = $this->model_task->insert('pm_history', $data);

				set_flashdata("notif", alert("Success Approve All Selected Task ", "success"));
				redirect_back("admin/task");			
    		}
    	}

    	set_flashdata("notif", alert("Error Approve All Selected Task ", "danger"));
		redirect_back("admin/task");
    	
    }


}


/* End of file task.php */
/* Location: ./application/controllers/admin/task.php */