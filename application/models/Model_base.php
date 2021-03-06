<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Model_base extends MY_Model {

	private $primary_key 	= "idbase";
	private $table_name 	= "base";
	private $field_search 	= array('base_name', 'base_desc');

	public function __construct()
	{
		$config = array(
			"primary_key" 	=> $this->primary_key,
		 	"table_name" 	=> $this->table_name
		 	);

		parent::__construct($config);
	}

	public function count_all($q = NULL)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        foreach ($this->field_search as $field) {
            if ($iterasi == 1) {
                $where .= "(" . $field . " LIKE '%" . $q . "%' ";
            } else if ($iterasi == $num) {
                $where .= "OR " . $field . " LIKE '%" . $q . "%') ";
            } else {
                $where .= "OR " . $field . " LIKE '%" . $q . "%' ";
            }
            $iterasi++;
        }

        $this->db->where($where);
        $this->db->where("base_status != 'delete'");

		$query = $this->db->get($this->table_name);
		return $query->num_rows();
	}

	public function get_base($q, $limit, $offset)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        foreach ($this->field_search as $field) {
            if ($iterasi == 1) {
                $where .= "(" . $field . " LIKE '%" . $q . "%' ";
            } else if ($iterasi == $num) {
                $where .= "OR " . $field . " LIKE '%" . $q . "%') ";
            } else {
                $where .= "OR " . $field . " LIKE '%" . $q . "%' ";
            }
            $iterasi++;
        }

        $this->db->where($where);
        $this->db->where("base_status != 'delete'");
        $this->db->join('user', 'base.base_created_by = user.iduser', 'LEFT');
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

	public function find_base_by_idbase($idbase)
	{
		$this->db->join('user', 'base.base_created_by = user.iduser', 'LEFT');
		$this->db->where($this->primary_key, $idbase);
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

	public function get_all_base()
	{
        $this->db->where("base_status != 'delete'");
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);
		return $query->result();
	}

	

}


/* End of 

/* End of file Model_base.php */
/* Location: ./application/models/Model_base.php */