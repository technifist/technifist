<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'events';
    }
	
	function get_all($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE type > '0'
        ";

        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
            {
                $value = $this->db->escape('%' . $value . '%');
                $sql .= " AND {$key} LIKE {$value}";
            }
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
        {
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $results['results'] = $query->result_array();
        }
        else
        {
            $results['results'] = NULL;
        }

        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;

        return $results;
    }
	
	function clear_log() {
		$this->db->truncate('events');
	}
	
	function get_log($id = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE id = " . $this->db->escape($id) . "
                    AND deleted = '0'
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
  
  function register_event($data) 
	{
		$this->db->insert("events", $data);
		return $this->db->insert_id();
	}
	
	function get_list_user_events($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE user = '{$user}'
        ";

        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
            {
                $value = $this->db->escape('%' . $value . '%');
                $sql .= " AND {$key} LIKE {$value}";
            }
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
        {
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $results['results'] = $query->result_array();
        }
        else
        {
            $results['results'] = NULL;
        }

        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;

        return $results;
    }
	
		function get_events_user_admin($user) 
		{
			return $this->db->where("user", $user)->order_by('id', 'DESC')->limit(20)->get("events");
		}
}