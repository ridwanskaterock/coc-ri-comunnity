<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

session_start();
//menampilkan jika data pada database tidak ada
function no_data(){
	return "<tr><th style='text-align:center; padding:5px' colspan='10'>Tidak ada data</th></tr>";
}


//fungsi untuk module settings
function get_setting($name=NULL){
	$ci =& get_instance();
	$query = $ci->db->query("SELECT * FROM `setting`");
	$row =  $query->row();
	$jml =  $query->num_rows();
	if($jml>0){
		return $row->$name;
	}else{
		return FALSE;
	}
}

function update_setting($name="",$value=""){
	$ci =& get_instance();
	$query = $ci->db->query("UPDATE `setting` SET $name = '$value' ");
	if($query){
		return TRUE;
	}else{
		return FAlSE;
	}
}


function alert($msg = "", $type = "success"){
	return  '<div class="alert alert-' . $type . '">
	<button type="button" class="close" data-dismiss="alert">&times;</button>  
	' . $msg .'                   
</div>';
}

function alert_form($msg = "", $type = "danger")
{
	return "<div class='alert alert-danger' style='margin-bottom: -10!important;'>
	<i class='fa fa-info'>
	</i>
	<b>Error : </b>
	{$msg}
</div>";
}

function alert_login($msg = "", $type = "danger"){
	return  '<div class="alert alert-' . $type . '">

	' . $msg .'                   
</div>';
}


function  user_admin($colom="username"){
	$ci =& get_instance();
	if (userdata("session_admin")) {	
		$user = userdata('session_admin');
		if(isset($user->$colom))
		{
			return $user->$colom;
		}
		else
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
}


function  user_member($colom="username"){
	$ci =& get_instance();
	if (userdata("session_user")) {	
		$user = userdata('session_user');
		if(isset($user->$colom))
		{
			return $user->$colom;
		}
		else
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
}

function notif($msg="",$class=""){
	$ci =& get_instance();
	echo "<div style='position:absolute;z-index:99999;left:25%;padding:5px;margin-top:5px;width:50%' class='alert alert-info fade in $class'>
	<button class='close' data-dismiss='alert' type='button'>×</button>
	$msg
</div>";
}

//fungsi untuk mengamankan password user
function enkripsi($pass){
	return md5("!@#$%^&*()".$pass);
}

//menampilkan str singkat
function  str_singkat($str="",$count=100){
	$kata = $str;
	$cari = array("<br>",'\n');
	$katas = str_replace($cari, " ", $kata);		
	$kata = substr($katas,0, $count);		
	$kata =  htmlentities(stripslashes(strip_tags($kata)),ENT_QUOTES);
	$akhir = explode(" ", $kata);
	return $kata.(strlen($katas) > $count ?"...":"");

}

function count_data($table = NULL)
{
	$ci =& get_instance();

	return $ci->db->get($table)->num_rows();
}

//fungsi nama hari
function hari(){
	$nama_hari = array("minggu","senin","selasa","rabu","kamis","jum'at","sabtu");
	$hari = date("w");
	$hari = $nama_hari[$hari];
	return ai($hari);
}

//fungsi bulan
function bulan($bulan){		
	switch ($bulan) {
		case '01':
		$bulan = "January";
		break;
		case '02':
		$bulan = "February";
		break;
		case '03':
		$bulan = "Maret";
		break;
		case '04':
		$bulan = "April";
		break;
		case '05':
		$bulan = "Mei";
		break;
		case '06':
		$bulan = "Juni";
		break;
		case '07':
		$bulan = "Juli";
		break;
		case '08':
		$bulan = "Agustus";
		break;
		case '09':
		$bulan = "September";
		break;
		case '10':
		$bulan = "Oktober";
		break;
		case '11':
		$bulan = "November";
		break;
		case '12':
		$bulan = "Desember";
		break;			
		default:
		$bulan = "January";
		break;
	}
	return $bulan;
}

	//fungsi tanggal
function tanggal($tgl="",$type="",$aktif_bln="y",$pemisah="-"){		
	switch ($type) {
		case 'indo-eng':
		$tahun 		= substr($tgl,6,4);
		$bulan 		= substr($tgl,3,2);
		$tanggal 	= substr($tgl,0,2);
		return $tahun.$pemisah.$bulan.$pemisah.$tanggal;
		break;
		case 'eng-indo':
		$tahun 		= substr($tgl,0,4);
		$bulan 		= substr($tgl,5,2);
		$tanggal 	= substr($tgl,8,2);
		$bl 		= ($aktif_bln=="y")?bulan($bulan):$bulan;
		return $tanggal.$pemisah.$bl.$pemisah.$tahun;
		break;
		default:
		$tanggal = date("Y-m-d");
		return $tanggal;
		break;
	}

}

	//fungsi waktu
function waktu(){
	return date("H:i:s");
}

function ket_waktu($date)
{
	return tanggal($date, "eng-indo");
}


function uri_segment($uri){
	$ci =& get_instance();
	return $ci->uri->segment($uri);
}

function generate_code($leng = 10)
{
	return substr((str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890")), 0, $leng);
}

function jumlah($bil, $akhiran = "k")
{
	if($bil >= 1000){
		$bil =  $bil/1000;
	}else{
		$bil = $bil;
	}
	return $bil . $akhiran;
}

function clean_title($title)
{
	$title = str_replace("-", " ", $title);
	return ucwords($title);
}

function time_ago($time, $akhiran = " ago")
{
	$datetime1 = date_create(date("Y-m-d H:i:s"));
	$datetime2 = date_create($time);
	$interval = date_diff($datetime1, $datetime2);
	$keterangan = "a few seconds " . $akhiran;
	if($interval->y != 0){
		$keterangan = $interval->y . " year " . $akhiran;
	}elseif($interval->m != 0){
		$keterangan = $interval->m . " month " . $akhiran;
	}elseif($interval->d != 0){
		$keterangan = $interval->d . " days " . $akhiran;
	}elseif($interval->h != 0){
		$keterangan = $interval->h . " hours " . $akhiran;
	}elseif($interval->i != 0){
		$keterangan = $interval->i . " minutes " . $akhiran;
	}elseif($interval->s != 0){
		$keterangan = $interval->s . " seconds " . $akhiran;
	}

	return $keterangan;

}

function alert_js($str = "")
{
	echo "<script> " . $str . "</script>";
}


function crop_gambar($file_name)
{
	$ci =& get_instance();
	$ci->load->library('image_lib');
	$url_logo = './asset/user-image/'.$file_name ;

	$config['image_library'] = 'GD2';
	$config['source_image'] = $url_logo;
	$config['new_image'] = './asset/user-image/thumb';
	$config['height'] = '400';
	$config['width'] = '640';
	$config['maintain_ratio'] = FALSE;

	$y_axis = 0;
	$x_axis = round(($config['width']/2) - ($resize_width/2));


	$source_img01 = $config['new_image'];

	$CI->image_lib->clear();
	$CI->image_lib->initialize($config);
	$CI->image_lib->resize();

	$config['image_library'] = 'gd2';
	$config['source_image'] = $source_img01;
	$config['create_thumb'] = false;
	$config['maintain_ratio'] = false;
	$config['width'] = 640;
	$config['height'] = 400;
	$config['y_axis'] = $y_axis ;
	$config['x_axis'] = $x_axis ;

	$ci->image_lib->initialize($config);

	if ( ! $ci->image_lib->crop())
	{
		return $ci->image_lib->display_errors();
	}else
	{
		return true;
	}
}


function print_pre($data = array())
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}


function curl($url = NULL, $data = array(), $post_method = TRUE)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, $post_method);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$tmp = curl_exec($ch);

	return $tmp;
}


function ms_time($datetime = NULL)
{
	return date("Y-m-d H:i:s", strtotime($datetime));
}


function mss_time($datetime = '', $suffix = ' WIB')
{
	if($datetime)
	{
		return date_format(date_create($datetime), 'Y-m-d H:i:s').$suffix;
	}
	else
	{
		return false;
	}
}
function get_web_page( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}

function redirect_back($url = '')
{
    if(isset($_SERVER['HTTP_REFERER']))
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
    else
    {
        redirect($url);
    }
    exit;
}



/* Generate PHPtumb */
 function phpthumb($url="",$w="200",$h="150",$crop="1",$extra="")
{
	if($url != BASE_ASSET.'user-image/' OR $url !=  BASE_ASSET.'group-image/'){
		$img = base_url()."includes/phpThumb/phpThumb.php?src=".$url."&h=$h&w=$w&zc=$crop&q=100&fltr[]=usm|80|0.5|3".$extra;
	}else{
		$img = base_url()."includes/phpThumb/phpThumb.php?src=".base_url('asset/user-image/default.jpg')."&h=$h&w=$w&zc=$crop&q=100&fltr[]=usm|80|0.5|3".$extra;
	}

	return $img;
}

function set_jnotif($title = NULL, $message = NULL, $type = 'info', $delay = 6000)
{
	set_flashdata('jnotif', array('title' => $title, 'message' => $message, 'type' => $type, 'delay' => $delay));
}

function get_jnotif()
{
	return flashdata('jnotif');
}

function now()
{
	return date('Y-m-d H:i:s');
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}


function space_text($count = 30)
{
	$text = NULL;
	for($i=0; $i<$count; $i++)
	{
		$text .= '&nbsp;ssss';
	}

}


function member_profile($photo = NULL, $type = NULL, $width = 50, $height = 50)
{
	if($type == 'facebook')
	{
		$type = $width > 50 ? 'type=large' : '';
		return "https://graph.facebook.com/".$photo.'/picture?'.$type;
	}
	elseif($type == 'twitter')
	{
		$type = $width > 50 ? '' : ''.'.jpeg';
		return $photo.$type;
	}
	else
	{
		$photo = empty($photo) ? 'default.jpg' : $photo ;
		return phpThumb(BASE_ASSET.'user-image/'.$photo, $width, $height);
	}
}

function build_query($request = array(), $unset = array())
{
	$query = $request;
	foreach($unset as $unset)
	{
		unset($query[$unset]);
	}

	return http_build_query($query);
}

function asset($location = NULL) {
	return BASE_ASSET . $location;
}