<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mailer
{
    protected $CI;

    const STATUS_MAILER_SENT        = "sent";
    const STATUS_MAILER_NOT_SENT    = "not sent";
    const STATUS_MAILER_NEW         = "new";

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load("mailer");

    }   

    function save($data = array())
    {
        $insert = array(
                        'mailer_module'     => (isset($data['module']))?$data['module']:'',
                        'mailer_from'       => (isset($data['from']))?$data['from']:'',
                        'mailer_to'         => (isset($data['to']))?$data['to']:'',
                        'mailer_cc'         => (isset($data['cc']))?$data['cc']:'',
                        'mailer_bcc'        => (isset($data['bcc']))?$data['bcc']:'',
                        'mailer_subject'    => (isset($data['subject']))?$data['subject']:'',
                        'mailer_message'    => (isset($data['message']))?$data['message']:'',
                        'mailer_status'     => self::STATUS_MAILER_NEW,
                        'mailer_created'    => (isset($data['created']))?$data['created']:date('Y-m-d H:i:s')
                        );
        $this->CI->db->insert('mailer',$insert); 
        return $this->CI->db->insert_id();
    }
    
    function update($id = NULL, $data = array())
    {
        $this->CI->db->where('mailer_id', $id);
        $this->CI->db->update('mailer', $data);
        return $this->CI->db->affected_rows();
    }
    
    function get_emails($limit=5)
    {

        $this->Ci->db->where("mailer_status != 'sent'");
        $this->CI->db->limit($limit);
        $this->Ci->db->order_by('mailer_created', 'DESC');
        
        $query = $this->CI->db->get('mailer');
        
        if($query->num_rows() > 0){
            return $query->result(); 
        }
    }
    
    function get_single_email($mailer_id = NULL)
    {
        $sql = "select * from mailer where mailer_id=".$mailer_id."";
        
        $query = $this->CI->db->query($sql);
        if($query->num_rows() > 0){
            return $query->row(); 
        }
    }
    
    function send_mail($mailer_id = NULL)
    {
        if($mailer_id != NULL){            
            $row = $this->get_single_email($mailer_id);
            if(!empty($row)){
                $config = $this->_mail_win();
                $this->CI->load->library('email', $config);                
                $this->CI->email->set_newline("\r\n");
                $this->CI->email->from($row->mailer_from, APP_NAME);
                $this->CI->email->to($row->mailer_to);                
                $this->CI->email->subject($row->mailer_subject);
                $this->CI->email->message($row->mailer_message);
                $send = $this->CI->email->send();

                $status = (!$send)?self::STATUS_MAILER_NOT_SENT:self::STATUS_MAILER_SENT;
                $this->update($row->mailer_id,array('mailer_status' => $status));
            }
        }
    }
    
    function _mail_win()
    {
        $config = Array(
            'protocol'              => $this->CI->config->item('protocol'),
            'smtp_host'             => $this->CI->config->item('smtp_host'),
            'smtp_port'             => $this->CI->config->item('smtp_port'),
            'smtp_user'             => $this->CI->config->item('smtp_user'),
            'smtp_pass'             => $this->CI->config->item('smtp_pass'),
            'mailtype'              => $this->CI->config->item('mailtype'),
            'charset'               => $this->CI->config->item('charset'),
            'smtp_crypto'           => $this->CI->config->item('smtp_crypto'),
            'bcc_batch_mode'        => true,
            'bcc_batch_size'        => 5            
        );
        return $config;
    }

    function _mail_unix()
    {
        $config = array(
            'mailtype'              => $this->CI->config->item('mailtype'),
            'charset'               => $this->CI->config->item('charset'),
            'bcc_batch_mode'        => true,
            'bcc_batch_size'        => 5            
        );
        return $config;
    }
    
    function send_mandrill($from,$from_name,$to,$to_name,$subject,$message)
    {
         $params = array(
                    "key" => MANDRILL_KEY,
                    "message" => array(
                        "html" => $message,                        
                        "text"  => $message,
                        "to" => array(
                            array( // add more sub-arrays for additional recipients
                                        'email' => $to,
                                        'name' => $to_name, // optional
                                        'type' => 'to' //optional. Default is 'to'. Other options: cc & bcc
                                        )
                        ),
                        'from_email'    => $from,
                        'from_name'     => $from_name, //optional
                        "subject"       => $subject,
                        "track_opens"   => true,
                        "track_clicks"  => true
                    ),
                    "async" => false
                );
        
        $uri = 'https://mandrillapp.com/api/1.0/messages/send.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);
        return json_decode($result);
    }

    
    
}

/* End of file Mailer.php */
/* Location: ./application/libraries/Mailer.php */