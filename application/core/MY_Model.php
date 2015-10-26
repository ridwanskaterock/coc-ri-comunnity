<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    private $primary_key = "id";
    private $table_name = "table";

    public function __construct($config = array())
    {
        $this->load->database();

        parent::__construct();

        foreach ($config as $key => $val)
        {
            if(isset($this->$key))
                $this->$key = $val;
        }

        $this->db->close();

    }

    public function remove($id = NULL)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table_name);
    }

    public function soft_remove($id = NULL)
    {
        $this->db->where($this->primary_key, $id);
        
       $data = array(
            'om_deletion_flag' => '1', 
            'om_update_date' => date('Y-m-d H:i:s'),
            'om_update_by' => user_admin('iduser')
            );
        
        return $this->db->update($this->table_name, $data);
    }

    public function change($id = NULL, $data = array())
    {        
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table_name, $data);

        return $this->db->affected_rows();
    }


    public function find($id = NULL)
    {
        $this->db->where($this->primary_key, $id);
        $query = $this->db->get($this->table_name);

        if($query->num_rows()>0)
        {
            return $query->row();
        }
        else
        {
            return FALSE;
        }
    }

    public function real_find($id = NULL)
    {
        $this->db->where($this->primary_key, $id);
        $this->db->where('om_deletion_flag', '0');
        $query = $this->db->get($this->table_name);

        if($query->num_rows()>0)
        {
            return $query->row();
        }
        else
        {
            return FALSE;
        }
    }

    public function real_find_all()
    {
        $this->db->where('om_deletion_flag', '0');
        $this->db->order_by($this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }


    public function find_all()
    {
        $this->db->order_by($this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function store($data = array())
    {
        $this->db->insert($this->table_name, $data);
        return $this->db->insert_id();
    }

    //====================================================

    function count_data($tabel, $filter = NULL)
    {
        $fields = $this->db->list_fields($tabel);

        if ($this->session->userdata('filter'))
            $filter = $this->session->userdata('filter');

        $iterasi = 1;
        $num = count($fields);
        $where = "";
        foreach ($fields as $field) {
            if ($iterasi == 1) {
                $where .= "(" . $field . " LIKE '%" . $filter . "%' ";
            } else if ($iterasi == $num) {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%') ";
            } else {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%' ";
            }

            $iterasi++;
        }
        $this->db->where($where);

        $this->db->from($tabel);
        return $this->db->count_all_results();
    }

    function get_data($tabel, $limit = NULL, $offset = NULL, $filter = NULL)
    {
        $fields = $this->db->list_fields($tabel);
        if ($this->session->userdata('filter'))
            $filter = $this->session->userdata('filter');

        $iterasi = 1;
        $num = count($fields);
        $where = "";
        foreach ($fields as $field) {
            if ($iterasi == 1) {
                $where .= "(" . $field . " LIKE '%" . $filter . "%' ";
            } else if ($iterasi == $num) {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%') ";
            } else {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%' ";
            }

            $iterasi++;
        }
        $this->db->where($where);

        $this->db->limit($limit, $offset);
        $query = $this->db->get($tabel);
        $data = $query->result();

        return $data;
    }


    function get_all_data($tabel)
    {
        $query = $this->db->get($tabel);
        $data = $query->result();

        return $data;
    }

    function get_where($tabel, $where = array())
    {
        $query = $this->db->get_where($tabel, $where);
        $data = $query->result();

        return $data;
    }

    function get_all_data_tabel($tabel, $limit = 100, $offset = 0)
    {
        $sql = $this->db->query('select * from ' . $tabel . ' limit ' . (int) $offset . ',' . (int) $limit . ';');

        return $sql->result();
    }

    function get_all_data_order($tabel, $field, $order)
    {
        $this->db->order_by($field, $order);
        $query = $this->db->get($tabel);
        $data = $query->result();

        return $data;
    }

    function get_single($tabel,  $field, $id)
    {
        $data = array();

        $query = $this->db->get_where($tabel, array($field => $id));
        $data = $query->row();

        return $data;
    }

    function get_datas($tabel, $id, $field)
    {
        $data = array();

        $query = $this->db->get_where($tabel, array($field => $id));
        $data = $query->result();

        return $data;
    }

    function insert($tabel, $data)
    {
        $this->db->insert($tabel, $data);
        return $this->db->insert_id();
    }

    function update($tabel, $data, $field, $id)
    {
        $this->db->where($field, $id);
        $this->db->update($tabel, $data);

        return $this->db->affected_rows();
    }

    function join($table, $table2, $on, $on2, $where = array())
    {
        $this->db->join($table2, "{$table}.{$on} = {$table2}.{$on2}", "LEFT");
        $this->db->where($where);
        $query = $this->db->get($table);
        return $query->result();
    }

    function delete($tabel,  $field, $id)
    {
        $this->db->where($field, $id);
        $this->db->delete($tabel);

        return $this->db->affected_rows();
    }

    function security($str)
    {
        return htmlspecialchars(mysql_real_escape_string(addslashes($str)));
    }

    function print_pre($obj)
    {
       
    }

    public function __destruct()
    {
        $this->db->close();
    }

}



/* End of file MY_Model */
/* Location: ./application/core/MY_Model */