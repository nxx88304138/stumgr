<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The class handles all requests about routine.
 * @author: Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lib_scores {
    /**
     * @var A instance of CodeIgniter.
     */
    private $__CI;
    
    /**
     * The contructor of the Routine Library class.
     */
    public function __construct() 
    {
        $this->__CI =& get_instance();
        $this->__CI->load->model('Students_model');

        $this->__CI->load->library('lib_utils');
    }
}

/* End of file lib_scores.php */
/* Location: ./application/libraries/lib_scores.php */