<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Pm extends Admin{

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_pm_history");
		$this->load->model("admin/model_user");
		$this->load->model("admin/model_vendor_user");
		$this->load->model("admin/model_site");
		$this->load->model("admin/model_periode");

		set_time_limit(360);

	}

	public function index()
	{
		$this->auth->check_access("pm");

		$data['notif'] = flashdata("notif");
		$data['result'] = $this->model_pm->get_pm();
		$this->renderAdmin("admin/pm/pm_list", $data);
	}

	public function plan()
	{
		$this->auth->check_access("pm_plan");

		$this->form_validation->set_rules("periode", "Previous Period", "required");
		$this->form_validation->set_rules("new_periode", "New Period", "required");

		if($this->form_validation->run())
		{
			$periode 			= $this->input->post('periode');
			$vendor 			= $this->input->post('vendor');
			$result_periode 	= $this->model_pm->get_single('pm_period', 'idpm_period', $periode);
			$result_task_site 	= $this->get_not_pm($periode, $vendor);
			$all_task_site 		= array();
			$not_check 			= array();

			//print_pre($result_task_site); exit;

			foreach($result_task_site as $site)
			{
				$all_task_site[] = $site->idpm;
			}

			$site_check = $this->input->post('site');

			if(!$site_check)
			{
				$site_check = array();
			}

			foreach($all_task_site as $site_id)
			{
				if(!in_array($site_id, $site_check))
				{
					$not_check[] = $site_id;
				}
			}

			//print_pre($not_check); exit();

			$result_pm = $this->model_pm->get_site_periode_nocheck($periode, $vendor, $not_check);



			//print_pre($result_pm); exit();

			foreach($result_pm as $pm)
			{
				$data 								= array();
				$data['pm_parent'] 					= $pm->idpm;
				$data['pm_vendor'] 					= $pm->pm_vendor;
				$data['pm_plan_name'] 				= $pm->pm_plan_name;
				$data['user_iduser'] 				= $pm->user_iduser;
				$data['pm_period_idpm_period'] 		= $this->input->post('new_periode');
				$data['pm_description'] 			= $pm->pm_description;
				$data['om_create_date'] 			= date("Y-M-d H:i:s");
				$data['om_create_by'] 				= user_admin('iduser');
				$data['om_deletion_flag'] 			= '0';

				$idpm = $this->model_pm->store($data);

				//

				$result_info = $this->model_pm->get_info($pm->pmp_idsite);
				
				$data = array(
					"pm_idpm" 							=> $idpm,
					"pmp_idsite" 						=> $pm->pmp_idsite,
					"pmp_vendor" 						=> $pm->pmp_vendor,
					"vendor_user_idvendor_user_pic" 	=> '',
					"pmp_idsite" 						=> $pm->pmp_idsite,	
					"pmp_status" 						=> "Missing PIC Assignment",
					"pmp_remark" 						=> "",
					"pmp_task_type" 					=> $pm->pmp_task_type,
					"pmp_priority" 						=> $pm->pmp_priority,
					"pmp_blocked_access" 				=> $pm->pmp_blocked_access,
					"om_created_date" 					=> date("Y-m-d H:i:s"),
					"om_update_date" 					=> date("Y-m-d H:i:s"),
					"om_created_by" 					=> user_admin('iduser'),
					"om_deletion_flag" 					=> '0',
					"flag_last_checked"					=> $pm->flag_last_checked
					);

				$idpm_plan_detil = $this->model_pm->insert('pm_plan_detail', $data);

				$data = array(
					"pmh_remark" 				=> "",
					"pmh_status" 				=> "Missing PIC Assignment",
					"pmh_by" 					=> user_admin("iduser"),
					"pmh_date" 					=> date('Y-m-d H:i:s'),
					"PM_PLAN_DETIL_idpm_plan" 	=> $idpm_plan_detil,
					);

				$this->model_pm->insert('pm_history', $data);

			}

			set_flashdata("notif", alert("PM Plan sucess, selanjutnya Meluncur test ke Task list "));
		}

		$data = array();

		$data['notif'] 				= flashdata("notif");
		$data['result_periode'] 	= $this->model_periode->get_periode(0, 100, NULL);
		$data['result_vendor'] 		= $this->model_pm->get_all_data('OMSubcontractor');

		$this->renderAdmin("admin/pm/pm_plan", $data);
	}

	public function get_site_periode()
	{
		$periode = $this->input->post('periode');
		$vendor = $this->input->post('vendor');
		$BaseService = isset($_POST['base_serviece']) ? $_POST['base_serviece'] : null;
		

		if(empty($periode))
			return false;


		$result = $this->model_pm->get_site_periode($periode, $vendor, $BaseService);
		$show = FALSE;
		$no = 1;

		ob_start();
		if(count($result) >0 )
		{
			foreach($result as $row)
			{
				$result_info = $this->model_pm->get_info($row->pmp_idsite);
				$generalinfo = $this->model_site->find($row->pmp_idsite);
				$remark = '';
				$not_pm = NULL;

				$maintained_before = $row->maintained_before;
				$maintained_once = $row->maintained_once;
				$base_serviece = $row->BaseService;
				$vendor_pm = $row->pmp_vendor;
				$vendor_site = $row->OMSubcont;

				$show = FAlSE;
				$show_checklist = FAlSE;
				if($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm == $vendor_site)
				{
					$show = FALSE;
					$remark = '';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm == $vendor_site)
				{
					$show = TRUE;
					$remark = 'Not Maintenance';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm != $vendor_site)
				{
					$show = TRUE;
					$remark = 'Vendor Change';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm != $vendor_site)
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Vendor Change';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Not in previous period, Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm != null)
				{
					$show = TRUE;
					$remark = 'Not in previous period';
					$show_checklist = TRUE;
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 0 )
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Not in previous period';
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm == null )
				{
					$show = TRUE;
					$remark = 'New site, Vendor not Assign';
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm != null )
				{
					$show = TRUE;
					$remark = 'New site';
					$show_checklist = TRUE;
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 0  )
				{
					$show = TRUE;
					$remark = 'New Site, Not Maintenance';
				}
				else
				{
					$show = TRUE;
					$remark = 'Unknown';
				}


				if($show):
				?>
				<tr>
				<td><?= $no++; ?> <?php if($show_checklist): ?>
						<input name="site[]" type="checkbox" value="<?= $row->idpm; ?>" >
					<?php endif; ?></td>
					<td>
					
						<a href='#' class="btn-site-info" pm-id="<?=$row->idpm_plan_detil; ?>" site-id='<?= $row->SiteId; ?>'><?= $row->SiteId; ?></a>
						<span style='color:#fff' ><?=$row->idpm_plan_detil; ?></span>
					</td>
					<td><?= $maintained_once ? 'YES' : 'NO'; ?></td>
					<td><?= $maintained_before ? 'YES' : 'NO'; ?></td>
					<td><?= $base_serviece ? 'YES' : 'NO'; ?></td>
					<td><?= $vendor_pm; ?></td>
					<td><?= $remark;?></td>
				</tr>
				<?php
				endif;
			}

		}
		else
		{
			?>No Result
			<?php
		}
		$buffer = ob_get_contents();
		@ob_end_clean();

		echo json_encode(array('result' => $buffer, 'total_previous_period' => 'Total PM Previous Period <b>'.count($result)));

	}

	public function get_not_pm($periode = NULL, $vendor = NULL, $base_serviece = NULL)
	{
		
		$result = $this->model_pm->get_site_periode($periode, $vendor, $base_serviece);
		$show = FALSE;
		$no = 1;
		$not_pm =array();

		if(count($result) >0 )
		{
			foreach($result as $row)
			{
				$result_info = $this->model_pm->get_info($row->pmp_idsite);
				$generalinfo = $this->model_site->find($row->pmp_idsite);
				$maintained_before = $row->maintained_before;
				$maintained_once = $row->maintained_once;
				$base_serviece = $row->BaseService;
				$vendor_pm = $row->pmp_vendor;
				$vendor_site = $row->OMSubcont;
				
					$show = FAlSE;
				$show_checklist = FAlSE;
							if($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm == $vendor_site)
				{
					$show = FALSE;
					$remark = '';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm == $vendor_site)
				{
					$show = TRUE;
					$remark = 'Not Maintenance';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 1 and $vendor_pm != $vendor_site)
				{
					$show = TRUE;
					$remark = 'Vendor Change';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm != $vendor_site)
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Vendor Change';
				}
				elseif($maintained_once == 1 and $maintained_before == 1 and $base_serviece == 0 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm == null)
				{
					$show = TRUE;
					$remark = 'Not in previous period, Vendor not Assign';
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm != null)
				{
					$show = TRUE;
					$remark = 'Not in previous period';
					$show_checklist = TRUE;
				}
				elseif($maintained_once == 1 and $maintained_before == 0 and $base_serviece == 0 )
				{
					$show = TRUE;
					$remark = 'Not Maintenance, Not in previous period';
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm == null )
				{
					$show = TRUE;
					$remark = 'New site, Vendor not Assign';
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 1 and $vendor_pm != null )
				{
					$show = TRUE;
					$remark = 'New site';
					$show_checklist = TRUE;
				}
				elseif($maintained_once == 0 and $maintained_before == 0 and $base_serviece == 0  )
				{
					$show = TRUE;
					$remark = 'New Site, Not Maintenance';
				}
				else
				{
					$show = TRUE;
					$remark = 'Unknown';
				}

				if($show):
					$not_pm[] = $row;
				endif;
			}
		}

		return $not_pm;

	}

	public function pdf_detail($id = NULL)
	{
		error_reporting(0);

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
		$data['location'] = $this->model_pm->get_site_byidpm($idpm);
		$data['idpm'] = $idpm;
		$this->load->view('admin/pm/pm_history_template', $data);

	}

}


/* End of file pm.php */
/* Location: ./application/controllers/admin/pm.php */

