<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Notification extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/model_notification");
		$this->config->load("auth");

	}

	public function index($offset = 0)
	{
		$this->auth->login_scurity();

		$this->template->title(APP_NAME, "Notification List");
	    $limit = isset($_GET['limit'])?$_GET['limit']:10;
	    $q = isset($_GET['q'])?$_GET['q']:'';

		$level = $this->auth->level_name_by_idlevel(user_admin('level'));

		$user_reff = NULL;
		$user_reff_id = NULL;

		if(in_array($level, array('pm_vendor_manager', 'tt_vendor_manager', 'tt_vendor_helpdesk')))
		{
			$user_reff = 'vendor';
			$user_reff_id = user_admin('idvendor');
		}
	    $config['base_url']     = "admin/notification/index/";
	    $config['total_rows']   = $this->model_notification->count_all($user_reff, $user_reff_id);
	    $config['per_page']     = $limit;
	    $config['uri_segment']  = 4;

	    $data['pagination']    	= $this->pagination($config);
		$data['notif'] 			= flashdata("notif");
		$data['result']			= $this->model_notification->get_notification($offset, $limit, $user_reff, $user_reff_id);
		$data['total_rows'] = $config['total_rows'];

		$data['notif'] = flashdata("notif");
		$data['user_reff'] = $user_reff;
		$data['user_reff_id'] = $user_reff_id;

		$this->renderAdmin("admin/notification/notification_list", $data);
	}


	public function detail($uniqid = NULL)
	{
		$this->auth->login_scurity();

		$result = $this->model_notification->find_notification_by_uniqid($uniqid);

		if($result)
		{
			$this->model_notification->open_notif($uniqid);
			redirect($result->action);
		}
		else
		{
			redirect_back();
		}
	}

	public function delete($uniqid = NULL)
	{
		$this->auth->login_scurity();

		$result = $this->model_notification->delete_notif($uniqid);

		set_flashdata("notif", alert("Success remove notification from list"));

		redirect_back();
	}

	public function mark_all($user_reff, $user_reff_id)
	{
		$this->auth->login_scurity();

		$this->model_notification->mark_all($user_reff, $user_reff_id);
		set_flashdata("notif", alert("Success mark as read"));
		redirect_back();
	}

	public function count_notif_notopen()
	{

		$level = $this->auth->level_name_by_idlevel(user_admin('level'));

		$user_reff = NULL;
		$user_reff_id = NULL;

		if(in_array($level, array('pm_vendor_manager', 'tt_vendor_manager', 'tt_vendor_helpdesk')))
		{
			$user_reff = 'vendor';
			$user_reff_id = user_admin('idvendor');
		}

		$count =  $this->model_notification->count_notif_notopen($user_reff, $user_reff_id);

		if($count > 0)
		{
			echo "<span class='label label-info' style='size:18px'>{$count}</span>";
		}

	}

	public function get_notification()
	{
	    $limit = 10;
	    $offset = 0;


		$level = $this->auth->level_name_by_idlevel(user_admin('level'));

		$user_reff = NULL;
		$user_reff_id = NULL;

		if(in_array($level, array('pm_vendor_manager', 'tt_vendor_manager', 'tt_vendor_helpdesk')))
		{
			$user_reff = 'vendor';
			$user_reff_id = user_admin('idvendor');
		}

	    $result = $this->model_notification->get_notification($offset, $limit, $user_reff, $user_reff_id);

	    if(count($result) > 0)
	    {
	   		echo "<li class='header'>Notifications</li>";
	   	}
	   	
	    foreach($result as $row)
	    {
	    	?>
            <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu <?= $row->status <> 'open' ? 'notif-active' : ''; ?>">
                        <li>
                            <a href="<?= site_url('admin/notification/detail/'.$row->uniqid); ?>">
                            <small class="pull-right" style='padding:3px'><i class="fa fa-clock-o text-warning"></i> <?= time_ago($row->datetime, ' '); ?></small>
                            	
                                <i class="fa  fa-info-circle info"></i> 
                                <?= $row->message; ?></a>
                        </li>
                    </ul>
                </li>
            <?php
	    }

	    if(count($result) <= 0)
	    {
	    	echo '<center style="padding:20px;">
			        <div class="fa fa-bell fa-5x " style="color:#ccc"></div>
			        <div class="text-mute" style="color:#ccc">No Notification</div>
			    </center>';
	    }
	    else
	    {
	     	echo "<li class='footer'><a href='".site_url('admin/notification')."'>View All</a></li>";
	    }

	}
}


/* End of file Notification.php */
/* Location: ./application/controllers/admin/Notification.php */