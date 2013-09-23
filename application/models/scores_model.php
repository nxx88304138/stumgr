<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The model is for the stumgr_scores table in the database.
 *
 * The structure of stumgr_scores:
 *     school_year      -- INT(4)       -- NOT NULL
 *
 * @author  Xie Haozhe <zjhzxhz@gmail.com>
 */
class Scores_model extends CI_Model {
    /**
     * The constructor of the class
     */
    public function __construct() 
    {
        parent::__construct(); 
        $this->load->database();
    }

    /**
     * Insert a record to the scores table.
     * @param  Array $record - an array contains a score record to insert
     * @return true if the query is successful
     */
    public function insert($record)
    {
    	return $this->db->insert($this->db->dbprefix('scores'), $record);
    }
}

/* End of file scores_model.php */
/* Location: ./application/models/scores_model.php */