<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * It is the CORE class of the system. It provides services
 * for administrators.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Admin extends CI_Controller {
    /**
     * @var an array contains the student's information
     */
    private $profile;
    /**
     * @var an array contains all options.
     */
    private $options;
    /**
     * The contructor of the class.
     *
     * If the user hasn't logged in, it will redirect to
     * the Accounts controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_accounts');
        $this->load->library('lib_evaluation');

        $session = $this->session->all_userdata();
        if ( !($session['is_logged_in'] && $session['is_administrator']) ) {
            redirect(base_url(). 'accounts');
        }

        $this->get_profile($session['username']);
        $this->get_options();
    }

    /**
     * Get the profile of the student.
     * @param  String  $username - the username of the user
     */
    private function get_profile($username)
    {
        $this->profile = array(
                'username'          => $username,
                'display_name'      => $username,
                'is_administrator'  => true
            );
    }

    /**
     * Get the options of the system.
     */
    private function get_options()
    {
        $this->load->model('Options_model');
        $options = $this->Options_model->select();
        foreach ( $options as $option ) {
            $this->options[$option['option_name']] = $option['option_value'];
        }
    }

    /**
     * Get the value of a certain option.
     * @param  String $option_name - the name of the option
     * @return the value of the option
     */
    private function get_option($option_name)
    {
         return $this->options[$option_name];
    }
    
    /**
     * Load the index page for the admin class.
     *
     * The index.php is a frame and doesn't contain any
     * information.
     */
    public function index()
    {
        $navigator_item = array(
            '欢迎'            => base_url().'admin#welcome',
            '账户'            => base_url().'admin#profile'
        );
        $data = array( 
            'profile'           => $this->profile, 
            'navigator_item'    => $navigator_item 
        );
        $this->load->view('/admin/index.php', $data);
    }

    /**
     * The function will be invoked by an ajax request from 
     * index.php.
     *
     * The function will invoke another function to get the data 
     * which is needed by the page.
     * 
     * @param  String $page - the name of the page to load
     */
    public function load($page = '')
    {
        $function = 'get_data_for_'.$page;
        if ( method_exists($this, $function) ) {
            $data = $this->$function();
            $this->load->view("/admin/$page.php", $data);
        } else {
            return false;
        }
    }

    /**
     * Get data for the welcome.php page.
     * @return an array which contains data which the page needs.
     */
    public function get_data_for_welcome()
    {
        $session = $this->session->all_userdata();
        $welcome = array(
                'display_name'          => $this->profile['display_name'],
                'ip_address'            => $session['ip_address'],
                'last_time_signin'      => $session['last_time_signin'],
                'allow_auto_sign_in'    => $session['allow_auto_sign_in']
            );
        $data = array( 'welcome' => $welcome, 'profile' => $this->profile );
        return $data;
    }

    /**
     * Get data for the profile.php page.
     * @return an array which contains data which the page needs.
     */
    public function get_data_for_profile()
    {
        $account = $this->lib_accounts->get_last_time_change_password($this->profile['username']);
        $data = array( 'account' => $account, 'profile' => $this->profile );
        return $data;
    }

    /**
     * Handle users' changing password requests.
     * @return an array which contains the query flags
     */
    public function change_password()
    {
        $old_password       = $this->input->post('old_password');
        $new_password       = $this->input->post('new_password');
        $confirm_password   = $this->input->post('password_again');

        $result = $this->lib_accounts->change_password($this->profile['username'], $old_password,
                                                       $new_password, $confirm_password);
        echo json_encode($result);
    }

    /**
     * Get data for addusers.php page.
     *
     * IMPORTANT: No data is needed by the page. However, each page
     *            will invoke a function when the page is loaded.
     *            So, you CANNOT remove this function.
     */
    public function get_data_for_addusers() { }

    /**
     * Add a user from the form.
     * @return an array which contains the query flags
     */
    public function add_user() 
    {
        $user_information = array(
                'student_id'    => $this->input->post('student_id'),
                'student_name'  => $this->input->post('student_name'),
                'grade'         => $this->input->post('grade'),
                'class'         => $this->input->post('class'),
                'room'          => $this->input->post('room'),
                'password'      => $this->input->post('password')
            );

        $result = $this->lib_accounts->add_user($user_information);
        echo json_encode($result);
    }

    /**
     * Handle administrator's uploading excel files requests.
     * The function is mainly used for importing data to the database.
     * 
     * @return an array with a boolean flag which infers if the operation
     *         is successful, and an extra message.
     */
    private function upload_files()
    {
        $config['upload_path']      = './application/cache/';
        $config['allowed_types']    = 'xls|xlsx';
        $config['max_size']         = '1024';
        $this->load->library('lib_upload', $config);

        return $this->lib_upload->do_upload();
    }

    /**
     * Add users from an excel file.
     */
    public function add_users() 
    {
        $result = array(
                'is_successful'         => false,
                'is_upload_successful'  => false,   'is_query_successful'   => false,
                'success_message'       => '',      'error_message'         => ''
            );

        $upload_result = $this->upload_files();
        $result['is_upload_successful'] = $upload_result['is_successful'];
        if ( !$result['is_upload_successful'] ) {
            $result['error_message']        = $upload_result['extra_message'];
        } else {
            $result['is_query_successful']  = $this->lib_accounts->add_users($upload_result['extra_message'], $result);
            $result['is_successful']        = $result['is_query_successful'];
        }

        echo json_encode($result);
    }

    public function get_data_for_editusers() 
    { 

    }
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */