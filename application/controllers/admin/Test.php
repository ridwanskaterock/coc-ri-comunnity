<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class Test extends admin
{
	
	function __construct()
	{
		parent::__construct();	

		$this->load->library('time_zone');
	}

	public function index()
	{
		$lat = $this->input->get('lat');
		$lng= $this->input->get('lng');
		$zone_result = $this->time_zone->get_zone_by_latlong($lat, $lng);
		$zone = $zone_result->timeZoneId;


		$time_result = $this->time_zone->compare_time('2015-06-09 16:59:00', $zone);

		echo $time_result;
	}
}

/* End of file Test.php */
/* Location: ./application/controllers/admin/Test.php */