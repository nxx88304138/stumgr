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
        $this->load->library('lib_routine');
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
     * Get data for the attendance.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_attendance()
    {
        $available_years = $this->lib_routine->get_available_years_for_attendance($this->profile['student_id']);
        $extra = array(
                'current_school_year'   => $this->lib_routine->get_current_school_year(),
                'current_semester'      => $this->lib_routine->get_current_semester(),
                'user_groups'           => $this->profile['user_group']['group_name']
            );
        $data = array( 
                'available_years'   => $available_years,
                'extra'             => $extra
            );
        return $data;
    }

    /**
     * Handle students' getting attendance records requests.
     * @param  int    $school_year - the year to query
     * @param  String $time  - the value is one of ( 'a-week', 'two-weeks'
     *         'a-month', 'all' ), stands for which record to query
     * @param  String $range - the value is one of ( 'myself', 'all' ), 
     *         stands for which record to query
     * @return an array contains attendance records with query flags
     */
    public function get_attendance_records($year, $time, $range)
    {
        $student_id         = $this->profile['student_id'];
        $attendance_records = array();
        if ( $range == 'all' && $this->profile['user_group']['group_name'] == 'Study-Monitors' ) {
            $attendance_records = $this->lib_routine->get_attendance_records_by_class($year, $time, 
                                                                                      $this->profile['grade'], $this->profile['class'], 'Study');
        } else if ( $range == 'all' && $this->profile['user_group']['group_name'] == 'Sports-Monitors' ) {
            $attendance_records = $this->lib_routine->get_attendance_records_by_class($year, $time, 
                                                                                      $this->profile['grade'], $this->profile['class'], 'Sports');
        } else {
            $attendance_records = $this->lib_routine->get_attendance_records_by_students($year, $student_id, 
                                                                                         $this->profile['student_name'], $time);
        }
        $result = array(
                'is_successful' => ($attendance_records != false),
                'records'       => $attendance_records
            );
        echo json_encode($result);
    }

    /**
     * Get extra data (students list and attendance rules) for study monitors
     * and sports monitors.
     * @return an array contains students list and rules list
     */
    public function get_extra_attendance_data_for_administration()
    {
        $extra = array(
                'students'  => $this->lib_routine->get_students_list_by_class($this->profile['grade'], 
                                                                              $this->profile['class']),
                'rules'     => $this->lib_routine->get_rules_list($this->profile['user_group']['group_name'])
            );
        echo json_encode($extra);
    }

    /**
     * Handle study monitors and sports monitors posting attendance data
     * requests.
     * @return an array which contains the query flags
     */
    public function add_attendance_record()
    {
        if ( $this->profile['user_group']['group_name'] != 'Study-Monitors' &&
             $this->profile['user_group']['group_name'] != 'Sports-Monitors' ) {
            return;
        }
        $attendance_record = array(
                'student_id'    => $this->input->post('student_id'),
                'datetime'      => $this->input->post('datetime'),
                'reason'        => $this->input->post('reason')
            );

        $result = $this->lib_routine->add_attendance_record($attendance_record);
        echo json_encode($result);
    }

    /**
     * Get data for the hygiene.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_hygiene()
    {
        $available_years = $this->lib_routine->get_available_years_for_hygiene($this->profile['student_id']);
        $extra = array(
                'current_school_year'   => $this->lib_routine->get_current_school_year(),
                'current_semester'      => $this->lib_routine->get_current_semester(),
                'user_groups'           => $this->profile['user_group']['group_name']
            );
        $data = array( 
                'available_years'   => $available_years,
                'extra'             => $extra
            );
        return $data;
    }

    public function get_hygiene_records($year, $semester)
    {
        $student_id         = $this->profile['student_id'];
        $hygiene_records    = array();

        $hygiene_records    = $this->lib_routine->get_hygiene_records_by_students($year, $semester, $student_id);

        $result = array(
                'is_successful' => ($hygiene_records != false),
                'records'       => $hygiene_records
            );
        echo json_encode($result);
    }

    public function get_extra_hygiene_data_for_administration()
    {
        $extra = array(
                'rooms'     => $this->lib_routine->get_rooms_list($this->profile['grade']),
                'weeks'     => $this->lib_routine->get_available_weeks($this->profile['grade'])
            );
        echo json_encode($extra);
    }

    /**
     * Get data for the transcripts.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_transcripts()
    {
        
    }

    /**
     * Get data for the gpa.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_gpa()
    {

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

    /**
     * Get data for the rewards.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_rewards()
    {

    }

    /**
     * Get data for the result.php page.
     * @return an array which contains data which the page needs
     */
    public function get_data_for_result()
    {

    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */