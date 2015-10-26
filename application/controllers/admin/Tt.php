<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tt extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_tt");
		$this->load->model("admin/model_tt");
		$this->load->model("admin/model_user");
		$this->load->model("admin/model_pm");
		$this->load->model("admin/model_vendor_user");
		$this->load->model("admin/model_meta");
		$this->load->model("admin/model_site");
		$this->load->model('admin/model_tt_general');
		$this->load->model('admin/model_pdf_historical');
		$this->load->model("admin/model_vendor_user_region");

		$this->load->library('notification');
	}

	public function index( $offset = 0 )
	{
		$this->auth->check_access("tt_access");
		$this->load->library('pagination');

		$limit = isset($_GET['limit'])?$_GET['limit']:10;
		$q = isset($_GET['q'])?$this->input->get('q', TRUE):'';

		$data_filter = array();
		if(isset($_GET['siteid']) AND !empty($_GET['siteid']))
		{
			$data_filter['protelindo_site_id'] = $this->input->get('siteid', TRUE);
		}

		if(isset($_GET['region']) AND !empty($_GET['region']))
		{
			$data_filter['Region'] = $this->input->get('region', TRUE);
		}

		if(isset($_GET['status']) AND !empty($_GET['status']))
		{
			$data_filter['sla_event_status'] = $this->input->get('status', TRUE);
		}

		if(isset($_GET['desc']) AND !empty($_GET['desc']))
		{
			$data_filter['tt_status_description'] = $this->input->get('desc', TRUE);
		}

		if(isset($_GET['status_description']) AND !empty($_GET['status_description']))
		{
			$data_filter['tt_status_description'] = $this->input->get('status_description', TRUE);
		}

		if(isset($_GET['ttno']) AND !empty($_GET['ttno']))
		{
			$data_filter['tt_no'] = $this->input->get('ttno', TRUE);
		}

		//default tt open
		$data_filter['tt_open'] = $this->input->get('tt_open', TRUE);

		$config['base_url']     = "admin/tt/index/";
		$config['total_rows']   = $this->model_tt->count_all($data_filter);
		$config['per_page']     = $limit;
		$config['uri_segment']  = 4;

		$data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		
		$data['result']			= $this->model_tt->get_tt($offset, $limit, $data_filter);

		$data['result_region'] 	= $this->model_tt->get_all_data('Region');
		$data['result_vendor'] 	= $this->model_tt->get_vendor();
		$data['result_status'] 	= $this->model_tt->get_status();
		$data['result_status_description'] 	= $this->model_tt->get_status_description();
		$data['total_rows'] =  $config['total_rows'];

		$this->renderAdmin("admin/tt/tt_list", $data);
	}

	public function action()
	{
		$this->auth->check_access("tt_action");

		$id = $this->input->post("idtt");
		$type = $this->input->post("type");
		$type_history = $this->input->post("type");
		$remark = $this->input->post("remark");
		$level = $this->auth->level_name_by_idlevel(user_admin('level'));

		$result_tt = $this->model_tt->find($id);
		$row = $result_tt; 

		$current_status = strtolower($result_tt->tt_current_status);
		$sla_status = strtolower($result_tt->sla_event_status);


		//validasi action
        $validasi_action = true;
        if($level == 'pm_vendor_manager' OR $level == 'tt_vendor_manager' OR $level == 'tt_vendor_helpdesk' OR $level == 'pm_tt_vendor_manager')
        {
          if(!in_array(strtolower($row->tt_current_status), array("response", "evidence", "sla break", "resolution", "restore,resolution,evidence", "resolution,evidence")))
          {
            $validasi_action = false;
          }

          if(strtolower($row->tt_status_description) != 'waiting approval vendor')
          {
            $validasi_action = false;
          }

          if(strtolower($row->tt_current_status) == 'response' OR strtolower($row->tt_current_status) == 'restore' )
          {
            $validasi_action = false;
          }
        }

        if(!$validasi_action)
        {
        	set_flashdata("notif", alert("Failed {$type_history} data {$data_tt->tt_no} ", "danger"));
        	redirect_back();
        }

		if($result_tt->om_update_date)
		{
			$activity_date = date("Y-m-d H:i:s", strtotime($result_tt->om_update_date));
		}
		else
		{
			$activity_date = date("Y-m-d H:i:s");
		}

		//jika di reject maka kembali ke status sebelumnya 
		switch ($current_status) {
			case 'response':
			if($type =='rejected' AND $result_tt->tt_current_approval == 'yes')
			{
				$data_update_current_status = array('tt_current_status' => 'response');
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

			}
			break;
			case 'restore':
			if($type =='rejected' AND $result_tt->tt_current_approval == 'yes')
			{
				$data_update_current_status = array('tt_current_status' => 'response');
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

			}
			break;
			case 'resolution':
			if($type =='rejected')
			{
				$data_update_current_status = array('tt_current_status' => 'restore');
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

				$data_status_desc = array(
					'tt_status_description' => "Activity on Progress"
					);

				$this->model_tt->change($id, $data_status_desc);

			}
			elseif($current_status == 'resolution')
			{
				if($type == 'rejected')
				{
					$data_sla_start = array(
						'tt_status_description' => "Activity on Progress"
						);
				}
				else
				{
					$data_sla_start = array(
						'tt_status_description' => "Waiting Approval Protelindo"
						);
				}

				$this->model_tt->change($id, $data_sla_start);
			}
			break;
			case 'evidence':
			if($type =='rejected')
			{
				$data_update_current_status = array('tt_current_status' => 'resolution');
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

				$data_status_desc = array(
					'tt_status_description' => "Activity on Progress"
					);

				$this->model_tt->change($id, $data_status_desc);

			}
			elseif($current_status == 'evidence')
			{
				if($type == 'rejected')
				{
					$data_sla_start = array(
						'tt_status_description' => "Activity on Progress"
						);
				}
				else
				{
					$data_sla_start = array(
						'tt_status_description' => "Waiting Approval Protelindo"
						);
				}

				$this->model_tt->change($id, $data_sla_start);
			}
			break;
			case 'sla break':
			if($type =='rejected')
			{
				$data_update_current_status = array('tt_current_status' => $result_tt->tt_status_before_sla_start);
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

				$data_status_desc = array(
					'tt_status_description' => "Activity on Progress"
					);

				$this->model_tt->change($id, $data_status_desc);

			}
			elseif($current_status == 'sla break')
			{
				$data_sla_start = array(
					'tt_status_description' => "Waiting Approval Protelindo"
					);

				$this->model_tt->change($id, $data_sla_start);

				$current_status = 'SLA Start';
			}
			break;
			//ini untuk combo status
			case 'restore,resolution,evidence': case 'resolution,evidence':
			if($type =='rejected')
			{
				$data_update_current_status = array('tt_current_status' => 'restore');
				$result_update_current_status = $this->model_tt->change($id, $data_update_current_status);

				$data_status_desc = array(
					'tt_status_description' => "Activity on Progress"
					);

				$this->model_tt->change($id, $data_status_desc);

			}
			elseif($current_status == 'restore,resolution,evidence' OR $current_status == 'resolution,evidence')
			{
				if($type == 'rejected')
				{
					$data_sla_start = array(
						'tt_status_description' => "Activity on Progress"
						);
				}
				else
				{
					$data_sla_start = array(
						'tt_status_description' => "Waiting Approval Protelindo"
						);
				}

				$this->model_tt->change($id, $data_sla_start);
			}
			break;
			default:
			$data_update_current_status = array('tt_current_status' => NULL);
			break;
		}

		
		if($type == 'rejected')
		{
			$type = 'yes';

			if(strtolower($current_status) == 'sla start')
			{
				$type = 'no';
			}
		}
		else
		{
			$type = 'yes';
			if(strtolower($current_status) == 'sla start')
			{
				$type = 'no';
			}
		}

		//real approval untuk history
		
		if($type_history == 'rejected')
		{
			$type_history = 'no';
		}
		else
		{
			$type_history = 'yes';
		}


		//jadi ini butuh approval ke protelindo helpdesk, walaupun di approve type tetep no
		if(in_array($current_status, array('resolution', 'evidence', 'sla break', 'restore,resolution,evidence', 'resolution,evidence')))
		{
			if($type == 'yes')
			{
				$type = 'no';
			}
		}
		
		$datatt = array(
			"om_update_date" => date("Y-m-d H:i:s"),
			"om_update_by" => user_admin('iduser'),
			"tt_current_remark" => $remark,
			"tt_current_approval" => $type,
			);

		if($type_history == 'no')
		{
			$datatt["mobile_status_approval"] = "reject";
		}
		else
		{
			$datatt["mobile_status_approval"] = "pending";
		}

		$this->model_tt->update('tt', $datatt, 'idtt', $id);

		$user = $this->model_vendor_user->find($result_tt->tt_idpic);
		
		$meta = $this->model_meta->get_meta_bytask($id, 'tt');
        //$description = @$meta['description']['text'];

		//$result_meta =  $this->model_meta->get_meta_single('tt', $result_tt->idtt, strtolower('tt_'.$result_tt->tt_current_status.'_'.$result_tt->suspect_trouble.'_description'), 'text');
		$result_meta =  $this->model_meta->get_meta_single('tt', $result_tt->idtt, 'tt_description', 'text');

		if($result_meta)
		{
			$meta_description = $result_meta->meta_value;
		}
		else
		{
			$meta_description = 'No description';
		}

		$data = array(
			"tth_remark" 					=> $this->input->post('remark'),
			"tth_approval" 					=> $type_history,
			"tth_action_by" 				=> user_admin('iduser'),
			"tth_submit_date" 				=> date("Y-m-d H:i:s"),
			"tth_activity_date" 			=> $activity_date,
			"tt_idtt" 						=> $id,
			"tth_status"					=> $result_tt->tt_current_status
			);


		$this->model_tt->insert('tt_status_history', $data);

		$description = 'Vendor Remark : '.$this->input->post('remark');

		//untuk combo status kirim sesuai banyak status

		if($current_status == 'restore,resolution,evidence' OR $current_status == 'resolution,evidence' )
		{
			//handle status combo
			
			$combo_statuses = array('Resolution', 'Evidence');
			
			$curl = NULL;
			foreach($combo_statuses as $combo_status)
			{
				$combo_status = ucwords($combo_status);

				//kirim status satu persatu ke om 
				$data_update_status = array(
					"tt_no"                 => $result_tt->tt_no, 
					"type"                  => ucwords($combo_status), 
					"activity_date"         => $activity_date,
					"submission_date"       => date('Y-m-d H:i:s'),
					"description"           => $description, 
					"metadata"              => '', 
					"submittedby"           => user_admin('username')
				);

				$last_tt_general = $this->model_tt_general->find_byidtt($id);
				$data_tt = $this->model_tt->find($id);

				//jika status evidence maka lampirkan pdf metadata
				
				$historical_pdf_by_actvity_status = $this->model_pdf_historical->get_pdf_by_activity_status($data_tt->idtt, strtolower($combo_status));
				if($historical_pdf_by_actvity_status)
				{
					$file = $historical_pdf_by_actvity_status->pdf_file;
					$data_update_status['metadata'] = $file.';'.@(filesize(FCPATH.$file) ? filesize(FCPATH.$file) : 1).';'.date('d-m-Y H:i:s');
				}

				//jika di approve vendor maka curl ke OM
				if($type_history == 'yes')
				{
					$curl .= $combo_status.' '.curl(PROTELINDO_TT_UPDATE_STATUS_API_URL, $data_update_status);
				}

                $this->model_log->save_log('CP01', PROTELINDO_TT_UPDATE_STATUS_API_URL.' -POST : '.json_encode($data_update_status), 'response : '.$curl);
			}
		}
		else
		{
	        //# Update Status ke OM
			$data_update_status = array(
				"tt_no"                 => $result_tt->tt_no, 
				"type"                  => ucwords($current_status), 
				"activity_date"         => $activity_date,
				"submission_date"       => date('Y-m-d H:i:s'),
				"description"           => $description, 
				"metadata"              => '', 
				"submittedby"           => user_admin('username')
				);

			$last_tt_general = $this->model_tt_general->find_byidtt($id);
			$data_tt = $this->model_tt->find($id);

			//jika status evidence, resolution, sla start maka lampirkan pdf metadata
			if(in_array(strtolower($current_status), array('evidence', 'resolution', 'sla start')))
			{
				//buat ambil rawdata sla break 
				$pdf_activity_status = strtolower($data_tt->tt_current_status);

				if($pdf_activity_status == 'sla break')
				{
					$search_pdf_status = 'sla start';
				}
				elseif($pdf_activity_status == 'sla break end')
				{
					$search_pdf_status = 'sla end';
				}
				else
				{
					$search_pdf_status = $data_tt->tt_current_status;
				}

				$historical_pdf_by_actvity_status = $this->model_pdf_historical->get_pdf_by_activity_status($data_tt->idtt, $search_pdf_status);
				if($historical_pdf_by_actvity_status)
				{
					$file = $historical_pdf_by_actvity_status->pdf_file;
					$data_update_status['metadata'] = $file.';'.@(filesize(FCPATH.$file) ? filesize(FCPATH.$file) : 1).';'.date('d-m-Y H:i:s');
				}
			}

			//jika di approve vendor makan curl ke protelndo helpdesk
			if($type_history == 'yes')
			{
				$curl = curl(PROTELINDO_TT_UPDATE_STATUS_API_URL, $data_update_status);
                $this->model_log->save_log('CP01', PROTELINDO_TT_UPDATE_STATUS_API_URL.' -POST : '.json_encode($data_update_status), 'response : '.$curl);

				$om_response = ", OM Response : ".$curl;
			}
			else
			{
				$om_response = "";
			}

		}


		$data_tt = $this->model_tt->find($id);
		$data_pic = $this->model_vendor_user->find($data_tt->tt_idpic); 

       //Kirim GCM

		if($type_history == 'yes')
		{
			$id_gcm = "report_approved";
			$message = 'TT '. $data_tt->tt_no . ' Approve by ' . user_admin('username');
		}
		else
		{
			$id_gcm = "report_rejected";
			$message = 'TT '. $data_tt->tt_no . ' Reject by ' . user_admin('username');
		}

		$regid_devices = array($data_pic->gcm_key);

		$data_gcm = array(
			"pic_recipient" => $data_pic->idvendor_user, 
			"task" => $data_tt->tt_no, 
			"message" => $message,
			"data_tt" => $data_tt 
			);

		$response = $this->notification->send_gcm($regid_devices, $id_gcm, $data_gcm);

		set_flashdata("notif", alert("{$type_history} Approve data {$data_tt->tt_no} {$om_response} ", "success"));
		redirect_back("admin/tt");
	}

	public function select_pic($idtt = NULL, $vendor = NULL, $type = 'Select')
	{
		$this->auth->check_access("tt_select_pic");

		$data_tt = array(
			"om_update_date" 	=> date("Y-m-d H:i:s"),
			"om_update_by" 		=> user_admin('iduser'),
			"tt_idpic" 			=> $vendor,
			);

		$row = $this->model_tt->find($idtt);

		if(strtolower($row->tt_current_status) == 'missing pic assignment' OR strtolower($row->tt_current_status) == 'cancel' )
		{
			$data_tt['tt_current_status'] = 'response';
			$data_tt['tt_current_approval'] = 'yes';
			$type_update_status = 'Response';
		}
		else
		{
			$type_update_status = 'General';
		}

		$data_tt['tt_status_description'] = 'Activity on Progress';

		$result_tt = $this->model_tt->find($idtt);

		if(!empty($result_tt->tt_idpic))
		{
			$type = 'Change';
		}

		$this->model_tt->update('tt', $data_tt, 'idtt', $idtt);

		$user = $this->model_vendor_user->find($vendor);

		$description = 'PIC Name : '.$user->username.', PIC Phone : '.$user->phone.', ETA Time : '.time_full_ago($result_tt->tt_pic_eta_date, $result_tt->trouble_ticket_date)."";


		 //# Update Status ke OM
		$data_update_status = array(
			"tt_no"                 => $result_tt->tt_no, 
			"type"                  => $type_update_status, 
			"activity_date"         => date('Y-m-d H:i:s'),
			"submission_date"       => date('Y-m-d H:i:s'),
			"description"           => $description, 
			"metadata"              => '',
			"submittedby"           => user_admin('username')
			);


		$curl = curl(PROTELINDO_TT_UPDATE_STATUS_API_URL, $data_update_status);

        $this->model_log->save_log('CP01', PROTELINDO_TT_UPDATE_STATUS_API_URL.' -POST : '.json_encode($data_update_status), 'response : '.$curl);

		$data = array(
			"tth_remark" 					=> $this->input->post("remark"),
			"tth_status" 					=> "waiting pic submission",
			"tth_action_by" 				=> user_admin('iduser'),
			"tth_submit_date" 				=> date("Y-m-d H:i:s"),
			"tth_activity_date" 			=> date("Y-m-d H:i:s"),
			"tt_idtt" 						=> $idtt,
			"tth_approval" 					=> '-',
			"tth_status"					=> $type_update_status
			);

		if(strtolower($type_update_status) == 'general')
		{
			$data['tth_remark'] = 'PIC Changed to '.$user->username;
		}
		else
		{
			$data['tth_remark'] = 'PIC Selected to '.$user->username;
		}


		$this->model_tt->insert('tt_status_history', $data);


       //Kirim GCM
		
		$data_tt = $this->model_tt->find($idtt);
		$data_pic = $this->model_vendor_user->find($data_tt->tt_idpic); 

		
		$id_gcm = "new_task";
		$message = 'Yout have new TT '.$data_tt->tt_no .' With ETA Time : '.time_full_ago($result_tt->tt_pic_eta_date, $result_tt->trouble_ticket_date);

		$regid_devices = array($data_pic->gcm_key);

		$data_gcm = array(
			"pic_recipient" => $data_pic->idvendor_user, 
			"task" => $data_tt->tt_no, 
			"message" => $message,
			"data_tt" => $data_tt 
			);

		$response = $this->notification->send_gcm($regid_devices, $id_gcm, $data_gcm);

		set_flashdata("notif", alert("Success select PIC ", "success"));
		redirect_back("admin/tt");
	}

	public function get_history()
	{
		$idtt = $this->input->get('idtt');

		$data['history'] = $this->model_tt->get_history('tt_status_history', array('tt_idtt' => $idtt));
		$data['tt'] = $this->model_tt->find($idtt);
		$data['location'] = $this->model_tt->get_site_byidtt($idtt);
		$this->load->view('admin/tt/tt_history_template', $data);
	}

	public function get_padlock()
	{
		$idtt = $this->input->get('idtt');
		$siteid = $this->input->get('idsite');

		$tt = $this->model_tt->find($idtt);
		$result = $this->model_tt->get_padlock_bysiteid($siteid, 3);

		?>
		<div style="font-size:20px; border:bottom:2px solid #ccc;"><b>TT Number : </b><?= $tt->tt_no; ?> <b>SIte ID : </b> <?= $tt->protelindo_site_id; ?></div>
		<hr>
		<div style='overflow:auto; max-height:400px'>
			<table id="" class="table table-bordered table-striped data_table">
				<thead>
					<tr>
						<th>Date Time</th>
						<th>Padlock</th>
						<th>By</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($result->result() as $row): 
					$user = $this->model_vendor_user->find($row->om_create_by); 

					?>
					<tr>
						<td><?= mss_time($row->om_create_date, 'WIB'); ?></td>
						<td><?= $row->GateId; ?></td>
						<td><?= @$user->username; ?></td>
					</tr>
				<?php endforeach; ?>
				<?php if(($result->num_rows() <=0)): ?>
					<tr>
						<th colspan="20">No Result</th>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php 
}

public function get_pic()
{
	$this->auth->login_scurity();

	$idtt = $this->input->get('idtt');

	$tt = $this->model_tt->find($idtt);

	$ci =& get_instance();
	$ci->config->load('tt_suspect_trouble');

	$trouble_type = strtolower($tt->trouble_type);

	$list_trouble_type_ac = $ci->config->item('type')['AC'];
	$list_trouble_type_ac = array_map('strtolower', $list_trouble_type_ac);

	if(in_array($trouble_type, $list_trouble_type_ac))
	{
		$result_tenant = $this->model_tt->get_tenant_bysiteid($tt->protelindo_site_id, $tt->tenant);

		if($result_tenant->num_rows() <= 0)
		{
			echo alert("TENANT : ".$tt->tenant." NOT Available ", 'danger');
		}
		else
		{
			$tenant = $result_tenant->row();
			if(trim(strtolower($tt->tenant)) == trim(strtolower($tenant->ClientId)))
			{
				if($tenant->EquipmentType <> 1)
				{
					echo alert("TENANT : ".$tenant->ClientId." Equipment Type NOT Indoor ", 'warning');
				}
			}
		}
	}


	?>
	<iframe src="<?= site_url('admin/tt/pic_iframe/'.$idtt); ?>" style="width:100%; height:600px; border:none; "></iframe>
	<?php
}


public function pic_iframe($idtt = NULL)
{
	$data['idtt'] = $idtt;
	$tt = $this->model_tt->find($idtt);
	$data_filter = array();
	if(isset($_GET['area_code']) AND !empty($_GET['area_code']))
	{
		$data_filter['idregion'] = $this->input->get('area_code', TRUE);
	}
	if(isset($_GET['username']) AND !empty($_GET['username']))
	{
		$data_filter['username'] = $this->input->get('username', TRUE);
	}

	$data['validasi_status'] = (strtolower($tt->tt_current_status) == 'missing pic assignment' OR $tt->mobile_status_approval == 'reject');
	$data['tt'] = $tt;
	$data["result_region"] = $this->model_vendor_user->get_all_data('Region');
	$data['result'] = $this->model_vendor_user->get_data_vendor_user_bycurrentvendor($data_filter);
	$this->load->view('admin/tt/tt_select_pic', $data);
}


public function act_sla_status()
{
	$status = $this->input->get('status');
	$idtt = $this->input->get('idtt');

	$datatt = array(
		"tt_current_status" => $status,
		"om_update_date" => date("Y-m-d H:i:s"),
		"om_update_by" => user_admin('iduser'),
		);

	$this->model_tt->change($idtt,  $datatt);

	$data = array(
		"tth_remark" 					=> $this->input->post("remark"),
		"tth_status" 					=> $status,
		"tth_action_by" 				=> user_admin('iduser'),
		"tth_submit_date" 				=> date("Y-m-d H:i:s"),
		"tt_idtt" 						=> $idtt,
		);

	$result = $this->model_tt->insert('tt_status_history', $data);

	set_flashdata("notif", alert("Berhasil merubah status ke ". $status, "success"));
	redirect_back("admin/tt");
}

public function vendor_target_date()
{
	$this->auth->check_access("tt_set_vendor_estimation");

	$datePost = $this->input->post('date');
	$time = $this->input->post('time');
	$idtt = $this->input->post('idtt');
	$date = explode('/', $datePost);

	$tahun = $date[0];
	$bulan = $date[1];
	$hari = $date[2];

	$dateDb = "{$tahun}/{$bulan}/{$hari} ".$time;
	$data = array('tt_pic_eta_date' => $dateDb);

	$this->model_tt->change($idtt, $data);

	redirect_back("admin/tt");

}



public function get_ttno_autocomplete()
{
	$q = $this->input->get('term');

	$this->db->select('tt_no');
	$this->db->where("tt_no LIKE '%{$q}%'");
	$this->db->group_by('tt_no');
	$query = $this->db->get('tt');

	$outs = array();

	foreach($query->result() as $row)
	{
		$outs[] = $row->tt_no;
	}

	echo json_encode($outs);

}


public function get_tt_detail()
{
	$this->auth->login_scurity();

	$idsite = $this->input->get('siteid');
	$idtt = $this->input->get('idtt');
	$row = $this->model_tt->find($idtt);
	?>
	<div style="ma-height:400px; overflow:auto">
		<table id="" class="table table-bordered table-striped" >
			<thead>
				<?php if(!$row): ?>
					<tr>
						<td colspan="20"><b>No Result</b> </td>
					</tr>
				<?php else: ?>
					<tr>
						<td>TT NO</td>
						<td><?= $row->tt_no; ?></td>
					</tr>

					<tr>
						<td>Trouble Ticket Date</td>
						<td><?= $row->trouble_ticket_date; ?></td>
					</tr>
					<tr>
						<td>Protelindo SIte ID</td>
						<td><?= $row->protelindo_site_id; ?></td>
					</tr>
					<tr>
						<td>Proteindo Site Name</td>
						<td><?= $row->protelindo_site_name; ?></td>
					</tr>
					<tr>
						<td>Tenant</td>
						<td><?= $row->tenant; ?></td>
					</tr>
					<tr>
						<td>Tenant Site ID</td>
						<td><?= $row->tenant_site_id; ?></td>
					</tr>
					<tr>
						<td>Tenant Site Name</td>
						<td><?= $row->tenant_site_name; ?></td>
					</tr>
					<tr>
						<td>Address</td>
						<td><?= $row->address; ?></td>
					</tr>
					<tr>
						<td>OMSubcon</td>
						<td><?= $row->om_subcon; ?></td>
					</tr>
					<tr>
						<td>SLA Event Status</td>
						<td><?= $row->sla_event_status; ?></td>
					</tr>
					<tr>
						<td>Trouble Type</td>
						<td><?= $row->trouble_type; ?></td>
					</tr>
					<tr>
						<td>Suspected Trouble</td>
						<td><?= $row->suspect_trouble; ?></td>
					</tr>
					<tr>
						<td>Trouble Detail</td>
						<td><?= $row->trouble_detail; ?></td>
					</tr>
					<tr>
						<td>Resolution Target</td>
						<td><?= mss_time($row->resolution_target); ?></td>
					</tr>
					<tr>
						<td>TT Current Status</td>
						<td><?= $row->tt_current_status; ?></td>
					</tr>
					<tr>
						<td>TT Current Approval</td>
						<td><?= $row->tt_current_approval; ?></td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		<?php
	}


	public function get_pdf_history()
	{
		$idtt = $this->input->get('idtt');
		$data['tt'] = $this->model_tt->find($idtt);
		$data['history'] = $this->model_pdf_historical->get_historical($idtt, 'tt');
		$this->load->view('admin/tt/tt_pdf_history', $data);
	}

	public function reporting()
    {

    	$this->load->library("excel");

    	$q = isset($_GET['q'])?$this->input->get('q', TRUE):'';

		$data_filter = array();
		if(isset($_GET['siteid']) AND !empty($_GET['siteid']))
		{
			$data_filter['protelindo_site_id'] = $this->input->get('siteid', TRUE);
		}

		if(isset($_GET['region']) AND !empty($_GET['region']))
		{
			$data_filter['Region'] = $this->input->get('region', TRUE);
		}

		if(isset($_GET['status']) AND !empty($_GET['status']))
		{
			$data_filter['sla_event_status'] = $this->input->get('status', TRUE);
		}

		if(isset($_GET['desc']) AND !empty($_GET['desc']))
		{
			$data_filter['tt_status_description'] = $this->input->get('desc', TRUE);
		}

		if(isset($_GET['status_description']) AND !empty($_GET['status_description']))
		{
			$data_filter['tt_status_description'] = $this->input->get('status_description', TRUE);
		}

		if(isset($_GET['ttno']) AND !empty($_GET['ttno']))
		{
			$data_filter['tt_no'] = $this->input->get('ttno', TRUE);
		}

		//default tt open
		$data_filter['tt_open'] = $this->input->get('tt_open', TRUE);

		$result = $this->model_tt->reporting($data_filter);

		$this->excel->setActiveSheetIndex(0);

		$fields = $result->list_fields();

		$setting_dimension = array(
			'A' => 30,
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
			'N' => 25,
			'O' => 25,
			'P' => 25,
			'Q' => 25,
			'R' => 25,
			'S' => 25,
			'T' => 25,
			'U' => 25,
			'V' => 25,
			'W' => 25,
			'X' => 25,
			'Y' => 25,
			'Z' => 25,
			'AA' => 25,
			'AB' => 25,
			'AC' => 25,
			'AD' => 25,
			'AE' => 25,
			'AF' => 25,
			'AG' => 25,
			'AH' => 25,
			);

		foreach($setting_dimension as $col => $width)
		{
			$this->excel->getActiveSheet()->getColumnDimension($col)->setWidth($width);
		}

	
		$this->excel->getActiveSheet()->getStyle('A1:AH1')->applyFromArray(
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

		$this->excel->getActiveSheet()->getStyle('A1:AH1')->getFont()->setColor($phpColor);

		$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);

		$this->excel->getActiveSheet()->getStyle('A1:AH1')
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
            	if($col == 19)
            	{
            		$tno = 'TT Number';
            		$tt = $this->model_tt->get_single('tt', 'tt_no', $data->$tno);

              		$activity =  @$this->model_meta->get_meta_single('tt', $tt->idtt, 'tt_description', 'text')->meta_value;

                	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $activity);
            	}
            	elseif($col == 32)
            	{
            		$st = 'TT Status';
            		$susName = 'Suspected Trouble Name';
                	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row,  status_tt($data->$st,NULL,  $data->$susName, NULL, FALSE, TRUE));
            	}
            	elseif($col == 9)
            	{
            		$eqtype = 'Equipment Type';
            		if($data->$eqtype == '1')
                    {
                        $type = 'Indoor';
                    }
                    elseif($data->$eqtype == '2')
                    {
                        $type = 'Outdoor';
                    }
                    elseif($data->$eqtype == '3')
                    {
                        $type = 'Base Only';
                    }
                    elseif($data->$eqtype == '4')
                    {
                        $type = 'Outdoor 2';
                    }
                    else
                    {
                        $type = '-';
                    }
                	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row,  $type);
            	}
            	else
            	{
                	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);

            	}

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
		$this->excel->getActiveSheet()->getStyle('A1:AH'.$row)->applyFromArray($styleArray);



		$this->excel->getActiveSheet()->setTitle('TT');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=TT-Reporting-' . url_title(user_admin('username'))  . '-' . date("Y-M-d") . '.xls');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');

    }


    public function send_general_message()
    {
        //$this->auth->login_scurity();

        $message = $this->input->post('message');
        $idtt = $this->input->post('idtt');

        $result_tt = $this->model_tt->find($idtt);

      	//# Update Status ke OM
		$data_update_status = array(
			"tt_no"                 => $result_tt->tt_no, 
			"type"                  => 'General', 
			"activity_date"         => date('Y-m-d H:i:s'),
			"submission_date"       => date('Y-m-d H:i:s'),
			"description"           => $message, 
			"metadata"              => '', 
			"submittedby"           => user_admin('username')
			);

		$curl = curl(PROTELINDO_TT_UPDATE_STATUS_API_URL, $data_update_status);

		$this->model_log->save_log('CP01', PROTELINDO_TT_UPDATE_STATUS_API_URL.' -POST : '.json_encode($data_update_status), 'response : '.$curl);

		 $data_history = array(
            "tth_status"            => 'General',
            "tth_action_by"         => user_admin('iduser'),
            "tth_approval"          => '-',
            "tth_submit_date"       => date('Y-m-d H:i:s'),
            "tth_activity_date"     => date('Y-m-d H:i:s'),
            "tt_idtt"               => $idtt,
            "tth_remark"            => $message,
        );

       $result_insert_history = $this->model_tt->insert('tt_status_history', $data_history);

       $data_tt = $this->model_tt->find($idtt);
       $data_pic = $this->model_vendor_user->find($data_tt->tt_idpic); 

       //Kirim GCM

       if($data_pic)
       {
	        $regid_devices = array($data_pic->gcm_key);
	        $id_gcm = "general_message";   

	        $data_gcm = array(
		        "pic_recipient" => $data_pic->idvendor_user, 
		        "task" => $data_tt->tt_no, 
		        "message" => $message,
		        "data_tt" => $data_tt 
	        );

       		$response = $this->notification->send_gcm($regid_devices, $id_gcm, $data_gcm);
       }
       else
       {
       		die(json_encode(array('flag' => 0, 'message' => 'Error PIC Not Found' )));
       }

       if($result_insert_history)
       {
       	$outs['flag'] = 1;
       	$outs['message'] = 'General message TT  '. $data_tt->tt_no . ' Has been sent';
       }
       else
       {
       	$outs['flag'] = 0;
       	$outs['message'] = 'Error send general message '. $data_tt->tt_no;
       }

      echo  json_encode($outs);


    }
}


/* End of file tt.php */
/* Location: ./application/controllers/admin/tt.php */