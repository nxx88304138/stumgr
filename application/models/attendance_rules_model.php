<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The model is for the stumgr_attendance_rules table in the database.
 *
 * The structure of stumgr_attendance_rules:
 *     rules_id             -- INT(4)       -- NOT NULL --  [PRIMARY][AUTO_INCREMENT]
 *     rules_group          -- VARCHAR(16)  -- NOT NULL
 *     rules_name           -- VARCHAR(32)  -- NOT NULL --  [UNIQUE]
 *     description          -- VARCHAR(64)  -- NOT NULL
 *     additional_points    -- FLOAT        -- NOT NULL
 *
 * @author  Xie Haozhe <zjhzxhz@gmail.com>
 */
class Attendance_rules_model extends CI_Model {
    /**
     * The constructor of the class
     */
    public function __construct() 
    {
        parent::__construct(); 
        $this->load->database();
    }

    /**
     * Get attendance rules list for different user group.
     * @param  String $rules_group - the group of the rules
     * @return an array contains attendance rules
     */
    public function select($rules_group = '')
    {
        if ( !empty($rules_group) ) {
            $this->db->where('rules_group', $rules_group);
        }
        $query = $this->db->get( $this->db->dbprefix('attendance_rules') );
        if ( $query->num_rows() > 0 ) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    /**
     * Insert a record to the attendance rules table.
     * @param  Array $record - an array contains an attendance rule to
     *         insert
     * @return true if the query is successful
     */
    public function insert($record)
    {
        return $this->db->insert($this->db->dbprefix('attendance_rules'), $record);
    }

    /**
     * Update a record in the attendance rules table.
     * @param  Array $record - an array contains an attendance rule to
     *         insert
     * @return true if the query is successful
     */
    public function update($record)
    {
        $this->db->where('rules_name', $record['rules_name']);
        return $this->db->update($this->db->dbprefix('attendance_rules'), $record);
    }

    /**
     * Delete a record from the attendance rules table.
     * @param  String $rules_name - the name of the rule
     * @return true if the query is successful
     */
    public function delete($rules_name)
    {
        $this->db->where('rules_name', $rules_name);
        return $this->db->delete($this->db->dbprefix('attendance_rules')); 
    }

    /**
     * Get the rules id of a certain rule.
     * @param  String $rules_name - the name of the rule
     * @return the id of the rule
     */
    public function get_rules_id($rules_name)
    {
        $this->db->where('rules_name', $rules_name);
        $query = $this->db->get( $this->db->dbprefix('attendance_rules') );
        if ( $query->num_rows() > 0 ) {
            $attendance_rule = $query->row_array();
            return $attendance_rule['rules_id'];
        } else {
            return false;
        }
    }
}

/* End of file attendance_rules_model.php */
/* Location: ./application/models/attendance_rules_model.php */