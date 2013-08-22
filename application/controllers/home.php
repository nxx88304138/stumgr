<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * It is the CORE class of the system. It provides services
 * for students.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Home extends CI_Controller {
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
        if ( !($session['is_logged_in'] && !$session['is_administrator']) ) {
            redirect(base_url(). 'accounts');
        }

        $this->get_profile($session['username']);
        $this->get_options();
    }

    /**
     * Get the profile of the student.
     * @param  String  $student_id - the student id of the student
     */
    private function get_profile($student_id)
    {
        $this->profile = $this->lib_accounts->get_profile($student_id);
        $this->profile += array(
                'username'          => $student_id,
                'display_name'      => $this->profile['student_name'],
                'is_administrator'  => false
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
     * Load the index page for the Home class.
     *
     * The index.php is a frame and doesn't contain any
     * information.
     */
    public function index()
    {
        $navigator_item = array(
            '欢迎'            => base_url().'home#welcome',
            '账户'            => base_url().'home#profile'
        );
        $data = array( 
            'profile'           => $this->profile, 
            'navigator_item'    => $navigator_item 
        );
        $this->load->view('/home/index.php', $data);
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
            $this->load->view("/home/$page.php", $data);
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
        $data = array( 'profile' => $this->profile );
        return $data;
    }

    /**
     * Handle users' editing profile requests.
     * @return an array which contains the query flags
     */
    public function edit_profile()
    {
        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');

        $result = $this->lib_accounts->edit_profile($this->profile['student_id'], $mobile, $email);
        echo json_encode($result);
    }

    /**
     * Handle users' changing password requests.
     * @return an array which contains the query flags
     */
    public function change_password()
    {
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('password_again');

        $result = $this->lib_accounts->change_password($this->profile['student_id'], $old_password,
                                                       $new_password, $confirm_password);
        echo json_encode($result);
    }

    /**
     * Get data for the assessment.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_assessment()
    {
        $year = date('Y');
        $is_peer_assessment_active = $this->get_option('is_peer_assessment_active');
        $is_participated = $this->lib_evaluation->is_participated($year, $this->profile['student_id']);
        $students = array();
        if ( $is_peer_assessment_active && !$is_participated ) {
            $students = $this->lib_evaluation->get_students_list_by_class($this->profile['grade'], $this->profile['class']);
        }

        $extra = array(
                'is_participated'   =>  $is_participated,
                'student_id'        =>  $this->profile['student_id'],
                'student_name'      =>  $this->profile['student_name'],
                'grade'             =>  $this->profile['grade'],
                'class'             =>  $this->profile['class']
            );
        $data = array( 'options'    => $this->options, 'students' => $students,
                       'extra'      => $extra );
        return $data;
    }

    /**
     * Handle user's post peer assessment votes requests.
     * @return an array which contains the query flags
     */
    public function post_votes()
    {
        $year = date('Y');
        $is_peer_assessment_active = $this->get_option('is_peer_assessment_active');
        $is_participated = $this->lib_evaluation->is_participated($year, $this->profile['student_id']);
        $result = array(
                'is_successful'                 => boolval(($is_peer_assessment_active && !$is_participated)),
                'is_peer_assessment_active'     => boolval($is_peer_assessment_active),
                'is_participated'               => boolval($is_participated),
                'is_post_successful'            => false
            );

        if ( $result['is_successful'] ) {
            $year = date('Y');
            $students = $this->lib_evaluation->get_students_list_by_class($this->profile['grade'], $this->profile['class']);
            $posted_votes = array();
            foreach ( $students as $student ) {
                $student_id = $student['student_id'];
                if ( $student_id == $this->profile['student_id'] ) {
                    continue;
                }
                $posted_votes[$student_id]['moral'] = $this->input->post('moral-'.$student_id);
                $posted_votes[$student_id]['strength'] = $this->input->post('strength-'.$student_id);
                $posted_votes[$student_id]['ability'] = $this->input->post('ability-'.$student_id);
            }
            $result['is_post_successful'] = $this->lib_evaluation->post_votes($posted_votes, $students, 
                                                                              $this->profile['student_id'], $this->options);
            $result['is_successful'] &= $result['is_post_successful'];
        }

        echo json_encode($result);
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */