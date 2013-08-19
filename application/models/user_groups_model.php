<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The model is for the stumgr_assessment table in the database.
 *
 * The structure of stumgr_options:
 *     group_id		-- INT(4)		-- NOT NULL --	[PRIMARY][AUTO_INCREMENTAL]
 *     group_name	-- VARCHAR(64)	-- NOT NULL --	[UNIQUE]
 *     display_name	-- VARCHAR(64)	-- NOT NULL
 *
 * @author	Xie Haozhe <zjhzxhz@gmail.com>
 */
class User_groups_model extends CI_Model {
	/**
	 * The constructor of the class.
	 */
	public function __construct() 
	{
		parent::__construct(); 
		$this->load->database();
	}
	
	/**
	 * Get information of a certain user group.
	 * @param  int $group_id - the id of the user group
	 * @return the information of the user group
	 */
	public function select($group_id)
	{
		$this->db->where('group_id', $group_id);
		$query = $this->db->get( $this->db->dbprefix('user_groups') );
		if ( $query->num_rows() > 0 ) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	/**
	 * Get the name of a certain group.
	 * @param  int $group_id - the id of the user group
	 * @return the name of the user group
	 */
	public function get_group_name($group_id)
	{
		$query = $this->select($group_id);
		if ( $query ) {
			return $query['group_name'];
		}
		return $query;
	}
}

/* End of file user_groups_model.php */
/* Location: ./application/models/user_groups_model.php */