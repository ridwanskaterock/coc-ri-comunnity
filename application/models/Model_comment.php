<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Model_comment extends MY_Model {

	private $primary_key 	= "idcomment";
	private $table_name 	= "comment";
	private $field_search 	= array('comment_text');

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
        $this->db->where("comment_status != 'delete'");

		$query = $this->db->get($this->table_name);
		return $query->num_rows();
	}

	public function get_comment($q, $limit, $offset)
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
        $this->db->where("comment_status != 'delete'");
        $this->db->join('user', 'comment.comment_created_by = user.iduser', 'LEFT');
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

	public function find_comment_by_idcomment($idcomment)
	{
		$this->db->join('user', 'comment.comment_created_by = user.iduser', 'LEFT');
		$this->db->where($this->primary_key, $idcomment);
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

	public function find_comment_by_idbase($idbase)
	{
		$this->db->join('user', 'comment.comment_created_by = user.iduser', 'LEFT');
		$this->db->where('comment_table_reff', 'base');
		$this->db->where('comment_table_reff_id', $idbase);
		$this->db->order_by($this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
	}

	public function get_all_comment()
	{
        $this->db->where("comment_status != 'delete'");
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);
		return $query->result();
	}

    public function find_comment_user_base_by_idbase($idbase, $iduser)
    {
        $this->db->join('user', 'comment.comment_created_by = user.iduser', 'LEFT');
        $this->db->where('comment_table_reff', 'base');
        $this->db->where('comment_table_reff_id', $idbase);
        $this->db->where('comment_created_by', $iduser);
        $this->db->order_by($this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

}

/* End of file Model_comment.php */
/* Location: ./application/models/Model_comment.php */