<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Auth 
* @author Muhamad Ridwan
* @since 2014
*/

$config['level'] = array('tt_protel_manager', 'pm_protel_manager', 'pm_protel_qc', 'pm_vendor_manager', 'pm_vendor_qc', 'tenant', 'tt_vendor_manager', 'all');

/*
level access di sesuaikan sesuai index level
jika resource di izinkan untuk semua level array  pada level access isikan '*'
#index level di mulai dari 0
 */
/*$config['access'] = array(
		array( 'resource' => 'reporting'			, 'level_access' => array('*')),
		array( 'resource' => 'dashboard'			, 'level_access' => array('*')),

		array( 'resource' => 'user'					, 'level_access' => array(0,3)),
		array( 'resource' => 'user_list' 			, 'level_access' => array(0,3)),
		array( 'resource' => 'user_add' 			, 'level_access' => array(0,3)),
		array( 'resource' => 'user_update' 			, 'level_access' => array(0,3)),
		array( 'resource' => 'user_delete' 			, 'level_access' => array(0,3)),
		array( 'resource' => 'user_profile' 		, 'level_access' => array('*')),
		array( 'resource' => 'user_group_right' 	, 'level_access' => array(0)),
		array( 'resource' => 'user_change_password' , 'level_access' => array('*')),
		//module pm
		array( 'resource' => 'pm' 					, 'level_access' => array()),
		array( 'resource' => 'pm_pdf_detail' 		, 'level_access' => array()),
		array( 'resource' => 'pm_approve' 			, 'level_access' => array()),
		array( 'resource' => 'pm_reject_remark' 	, 'level_access' => array()),
		array( 'resource' => 'pm_approve_remark' 	, 'level_access' => array()),
		array( 'resource' => 'pm_plan' 				, 'level_access' => array(1)),
		//module support
		array( 'resource' => 'support' 				, 'level_access' => array()),
		//module team
		array( 'resource' => 'team' 				, 'level_access' => array()),
		array( 'resource' => 'team_add' 			, 'level_access' => array()),
		array( 'resource' => 'team_update'			, 'level_access' => array()),
		array( 'resource' => 'team_delete' 			, 'level_access' => array()),
		//module media
		//modeule task
		array( 'resource' => 'task' 				, 'level_access' => array('*')),
		array( 'resource' => 'task_add' 			, 'level_access' => array('*')),
		array( 'resource' => 'task_privilage' 		, 'level_access' => array('*')),
		array( 'resource' => 'task_delete'	 		, 'level_access' => array(0,1,6)),
		array( 'resource' => 'task_history' 		, 'level_access' => array('*')),
		array( 'resource' => 'task_remark' 			, 'level_access' => array(2,3,4)),
		//modeule map
		array( 'resource' => 'map' 					, 'level_access' => array()),
		array( 'resource' => 'map_incident'			, 'level_access' => array(0,1,2,3,4,5)),
		//modeule support
		//modeule media
		array( 'resource' => 'media' 				, 'level_access' => array(0)),
		array( 'resource' => 'media_add' 			, 'level_access' => array(0)),
		array( 'resource' => 'media_update'			, 'level_access' => array(0)),
		//modeule task_privilage
		//module meta
		array( 'resource' => 'meta' 				, 'level_access' => array(0)),
		array( 'resource' => 'meta_list' 			, 'level_access' => array(0)),
		array( 'resource' => 'meta_add' 			, 'level_access' => array(0)),
		array( 'resource' => 'meta_update' 			, 'level_access' => array(0)),
		array( 'resource' => 'meta_delete' 			, 'level_access' => array(0)),
		//module site
		array( 'resource' => 'site' 				, 'level_access' => array(0,1,2,3,4,5)),
		array( 'resource' => 'site_delete' 			, 'level_access' => array(0,1)),
		array( 'resource' => 'site_detail' 			, 'level_access' => array(0,1,2,3,5)),
	);*/



/* End of file auth.php */
/* Location: ./application/config/auth.php */


