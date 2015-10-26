<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct()
    {
        date_default_timezone_set("asia/jakarta");
        parent::__construct();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache"); 

    }

    public function print_pre($obj)
    {
       echo "<pre>";
       print_r($obj);
       echo "</pre>";
    }

    public function pagination($config = array())
    {
        $this->load->library('pagination');
        
        $config['suffix'] = isset($_GET)?"?".http_build_query($_GET):"";
        $config['base_url'] = site_url($config['base_url']);
        $config['total_rows'] = $config['total_rows'];
        $config['per_page'] = $config['per_page'];
        $config['uri_segment'] = $config['uri_segment'];
        $config['num_links'] = 3;
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        
        $this->pagination->initialize($config);
        
        return  "<center>".$this->pagination->create_links()."</center>";
    }

}

/**
* BASe controller
*/

class Admin extends MY_Controller
{
    function __construct()
    {
        parent::__construct();        
        $config = array(
            "redirect_url"          => "admin/login",
            "session_user_level"    => "session_admin",
            "session_user"          => "session_admin",
        );
        $this->auth->initialize($config);

    }

    public function render($view = '', $data = array(), $bool = FALSE)
    {
        $data['_content']   = $this->load->view($view, $data, TRUE);
        $data['_header']    = $this->load->view('layout/partial/admin_partial_header', $data, TRUE);
        $data['_nav']       = $this->load->view('layout/partial/admin_partial_nav', $data, TRUE);
        $data['_footer']    = $this->load->view('layout/partial/admin_partial_footer', $data, TRUE);
        $this->load->view('layout/admin_layout_default', $data, $bool);
    }
}

class Front extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $config = array(
            "redirect_url"          => "user/login",
            "session_user_level"    => "session_user",
            "session_user"          => "session_user",
        );
        $this->auth->initialize($config);

        $data_partial = array(
            'header' => 'front/partial/header_partial',
            'footer' => 'front/partial/footer_partial',
        );

        $this->template->set_partial($data_partial);

        $this->template->set_template('front/layout/front_default_layout');
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */