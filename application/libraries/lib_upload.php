<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The class handles all requests on uploading files.
 * It uses XMLHttpRequest to transfer files.
 * 
 * @author: Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lib_upload extends CI_Upload {
	/**
	 * [__construct description]
	 * @param array $config [description]
	 */
    public function __construct($config = array())
    {
    	parent::__construct($config);
    }  
}

/* End of file lib_upload.php */
/* Location: ./application/libraries/lib_upload.php */