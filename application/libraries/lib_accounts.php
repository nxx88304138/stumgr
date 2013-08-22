<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The class handles all requests about accounts.
 * @author: Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lib_accounts {
    /**
     * @var A instance of CodeIgniter.
     */
    private $__CI;
    
    /**
     * The contructor of the Account Library class.
     */
    public function __construct() 
    {
        $this->__CI =& get_instance();
        $this->__CI->load->model('Users_model');
        $this->__CI->load->model('User_groups_model');
        $this->__CI->load->model('Sessions_model');
        $this->__CI->load->model('Students_model');
    }

    /**
     * Handle users' signing in request.
     * @param  String $username the user's username
     * @param  String $password the user's password
     * @return an array which contains the query flags
     */
    public function sign_in($username, $password)
    {
        $passport = array( 'username' => $username, 'password' => $password );
        $result   = array(
                        'is_passed'         => false,   'is_username_empty'     => true,
                        'is_password_empty' => true,    'is_password_correct'   => false,
                        'is_administrator'  => false,   'last_time_signin'      => '0000-00-00 00:00:00'
                    );

        return $this->verify_user($passport, $result);
    }

    /**
     * Verify if the username and password is correct.
     * @param  String $username the user's username
     * @param  String $password the user's password
     * @return an array which contains the query flags
     */
    private function verify_user($passport, &$result)
    {
        $result['is_username_empty'] = empty( $passport['username'] );
        $result['is_password_empty'] = empty( $passport['password'] );
        $result['is_passed'] = (!$result['is_username_empty'] && !$result['is_password_empty']);

        if ( $result['is_passed'] ) {
            $result['is_password_correct'] = $this->is_password_correct($passport, $result);
            $result['is_passed'] &= $result['is_password_correct'];
        }

        return $result;
    }

    /**
     * Verify if the username and password is correct.
     * @param  Array  $passport contains two strings: username and password
     * @param  Array  $result is an array contains query flags.
     * @return true if the username and password is correct
     */
    private function is_password_correct($passport, &$result)
    {
        $username = $passport['username'];
        $password = $passport['password'];

        if ( $query = $this->__CI->Users_model->signin($username, md5($password)) ) {
            $result['is_administrator'] = $query['is_administrator'];
            $result['last_time_signin'] = $query['last_time_signin'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Handle users' signing in automatically request.
     * @param  Array $session - the session data
     * @return true if allow user sign in automatically
     */
    public function auto_sign_in($session)
    {
        if ( !(isset($session['is_logged_in']) && $session['allow_auto_sign_in']) ) {
            return false;
        }
        if ( !($session['is_logged_in'] && $session['allow_auto_sign_in']) ) {
            return false;
        }
        if ( !$this->__CI->Sessions_model->is_valid($session['session_id']) ) {
            return false;
        }

        return true;
    }

    /**
     * Set userdata in session.
     * @param boolean $is_administrator - if the user is administrator
     * @param boolean $username - the username of the user
     * @param boolean $allow_auto_sign_in - if the user allows auto sign in next time
     * @param String  $last_time_signin - the time when the user signed in
     */
    public function set_user_session($is_administrator, $username, $allow_auto_sign_in, $last_time_signin)
    {
        $session = array(
                        'username'              => $username,
                        'is_logged_in'          => true,
                        'allow_auto_sign_in'    => $allow_auto_sign_in,
                        'is_administrator'      => $is_administrator,
                        'last_time_signin'      => $last_time_signin
                    );
        $this->__CI->session->set_userdata($session);
    }

    /**
     * Get redirect address by user's group.
     * @param  boolean $is_administrator - if the user is administrator
     * @return the address to redirect
     */
    public function get_redirect_url($is_administrator)
    {
        if ( $is_administrator ) {
            return base_url().'admin';
        } else {
            return base_url().'home';
        }
    }

    /**
     * Return the time when the user change password last time.
     * @param  String $username - the username of the user
     * @return an array which contains the time when the user change 
     *         password the last time
     */
    public function get_last_time_change_password($username)
    {
        $account_info = $this->__CI->Users_model->select($username);
        $return_value = array( 'last_time_change_password' => $account_info['last_time_change_password'] );
        return $return_value;
    }

    /**
     * Get the profile for a certain student.
     * @param  String $student_id - the id of the student
     * @return the information of the student if the query is successful, 
     *         or return false if the query is failed
     */
    public function get_profile($student_id)
    {
        $profile = $this->__CI->Students_model->select($student_id);
        $account = $this->__CI->Users_model->select($student_id);

        return array(
                'student_id'                => $profile['student_id'],
                'student_name'              => $profile['student_name'],
                'grade'                     => $profile['grade'],
                'class'                     => $profile['class'],
                'room'                      => $profile['room'],
                'mobile'                    => $profile['mobile'],
                'email'                     => $profile['email'],
                'user_group'                => $this->get_user_groups_name($profile['user_groups_id']),
                'last_time_signin'          => $account['last_time_signin'],
                'last_time_change_password' => $account['last_time_change_password']
            );
    }

    /**
     * Get available grades to select from existing data.
     * @return an array contains all available grades
     */
    public function get_available_grades()
    {
        return $this->__CI->Students_model->get_available_grades();
    }

    public function get_user_groups_list()
    {
        return $this->__CI->User_groups_model->get_user_groups_list();
    }

    /**
     * Get students' profile in a certain grade.
     * @return an array of students' profile list
     */
    public function get_students_profile_list($grade)
    {
        $students = $this->__CI->Students_model->get_students_list_by_grade($grade);
        foreach ( $students as &$student ) {
            $student['user_group'] = $this->get_user_groups_name($student['user_groups_id']);
        }

        return $students;
    }

    /**
     * Get the name of a certain group.
     * @param  int $group_id - the id of the user group
     * @return the name of the user group
     */
    private function get_user_groups_name($user_groups_id)
    {
        return $this->__CI->User_groups_model->get_group_name($user_groups_id);
    }

    /**
     * Handle administrator's adding user requests.
     * @param Array $user_information - the essential information of the 
     *        user
     * @return an array which contains the query flags
     */
    public function add_user($user_information)
    {
        $result = array(
                'is_successful'         => false,
                'is_required_empty'     => true,    'is_information_legal'  => false,
                'is_user_exists'        => true,    'is_query_successful'   => false
            );

        $result['is_successful'] = $this->verify_user_information($user_information, $result);
        if ( $result['is_successful'] ) {
            $result['is_user_exists'] = ( $this->__CI->Users_model->select($user_information['student_id']) ||
                                          $this->__CI->Students_model->select($user_information['student_id']) );
            if ( !$result['is_user_exists'] ) {
                $result['is_query_successful'] = $this->create_user_in_database($user_information);
            }
        }

        $result['is_successful'] &= (!$result['is_user_exists'] && $result['is_query_successful']);
        return $result;
    }

    /**
     * Verify if the information posted is legal.
     * @param  Array $user_information - the essential information of the 
     *         user
     * @param  Array $result - an array which contains the query flags
     * @return true if the information is legal
     */
    private function verify_user_information($user_information, &$result) 
    {
        $result['is_required_empty'] = empty($user_information['student_id']);

        $result['is_information_legal'] = (strlen($user_information['student_name']) <= 24) &&
                                          (strlen($user_information['password']) <= 16) &&
                                          (strlen($user_information['room']) < 8) &&
                                          preg_match('/^[0-9]{8,10}$/', $user_information['student_id']) &&
                                          preg_match('/^[0-9]{4}$/', $user_information['grade']) &&
                                          preg_match('/^[0-9]{1,2}$/', $user_information['class']);

        $result['is_successful'] = (!$result['is_required_empty'] && $result['is_information_legal']);
        return $result['is_successful'];
    }

    /**
     * Insert the information of the user into database.
     * @param  Array $user_information - the essential information of the 
     *         user
     * @return true if the query is successful
     */
    private function create_user_in_database($user_information)
    {
        $security_information = array(
                'username'      =>  $user_information['student_id'],
                'password'      =>  $user_information['password']
            );
        $basic_information    = array(
                'student_id'    =>  $user_information['student_id'],
                'student_name'  =>  $user_information['student_name'],
                'grade'         =>  $user_information['grade'],
                'class'         =>  $user_information['class'],
                'room'          =>  $user_information['room']
            );
        return ($this->__CI->Users_model->insert($security_information) &&
                $this->__CI->Students_model->insert($basic_information));
    }

    /**
     * Add users by importing data from an excel file.
     * @param String $file_path - the file path of the excel file
     * @param Array  $result - an array which contains the query flags
     */
    public function add_users($file_path, &$result)
    {
        $this->__CI->load->library('lib_excel');
        $data = $this->__CI->lib_excel->get_data_from_excel($file_path);

        $number_of_records = count($data);
        $result['is_query_successful'] = true;
        for ( $i = 1; $i < $number_of_records; ++ $i ) {
            $user_information = $this->get_user_information_array($data[$i]);
            $query_result = $this->add_user($user_information);
            $result['is_query_successful']  &= $query_result['is_successful'];
            if ( $query_result['is_successful'] ) {
                $result['success_message']  .= $user_information['student_name'].'的信息已成功导入.<br />';
            } else {
                $result['error_message']    .= $user_information['student_name'].'的信息未能成功导入.<br />';
            }
        }
        return $result['is_query_successful'];
    }

    /**
     * Get information of the user which read from an excel file to 
     * a row array.
     * @param  Array $record - an array contains user's information
     * @return an array which contains the information of the user
     */
    private function get_user_information_array(&$record)
    {
        $user_information = array(
                'student_id'    => (string)$record[0],
                'student_name'  => (string)$record[1],
                'grade'         => (int)$record[3],
                'class'         => (int)$record[4],
                'room'          => (string)$record[5],
                'password'      => (string)$record[2]
            );
        return $user_information;
    }

    /**
     * Handle users' editing profile requests.
     * @param  String $student_id - the id of the student
     * @param  String $mobile - the mobile phone number of the student
     * @param  String $email - the email address of the student
     * @return an array which contains the query flags
     */
    public function edit_profile($student_id, $mobile, $email)
    {
        $result = array(
                'is_successful'     => false,  'is_query_successful'    => false,
                'is_mobile_empty'   => true,   'is_mobile_legal'        => false,
                'is_email_empty'    => true,   'is_email_legal'         => false
            );

        $result['is_successful'] = $this->verify_profile_information($mobile, $email, $result);
        if ( $result['is_successful'] ) {
            $contact_information = array(
                    'student_id'    => $student_id,
                    'mobile'        => $mobile,
                    'email'         => $email
                );
            $result['is_query_successful'] = $this->__CI->Students_model->update($contact_information);
        }
        $result['is_successful'] &= $result['is_query_successful'];

        return $result;
    }

    /**
     * Verify if all fields are legal.
     * @param  String $mobile - the mobile phone number of the student
     * @param  String $email - the email address of the student
     * @param  Array  $result - an array which contains the query flags 
     * @return an array which contains the query flags
     */
    private function verify_profile_information($mobile, $email, &$result)
    {
        $result['is_mobile_empty'] = empty($mobile);
        $result['is_mobile_legal'] = preg_match('/^[1-9][0-9]{10}$/', $mobile);
        $result['is_email_empty']  = empty($email);
        $result['is_email_legal']  = preg_match('/^[A-Z0-9._%-]{4,18}@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);

        $result['is_successful']   = (!$result['is_mobile_empty'] && !$result['is_email_empty'] &&
                                       $result['is_mobile_legal'] && $result['is_email_legal']);

        return $result['is_successful'];
    }

    /**
     * Handle users' changing password requests.
     * @param  String  $username - the username of the user
     * @param  String  $old_password - the password the user used before
     * @param  String  $new_password - the new password the user will use
     * @param  String  $confirm_password - confirm the new password to use
     * @return an array which contains the query flags
     */
    public function change_password($username, $old_password, $new_password, $confirm_password)
    {
        $result = array(
                'is_successful'                 =>  false,  'is_old_password_empty'     =>  true,
                'is_old_password_correct'       =>  false,  'is_new_password_empty'     =>  true,
                'is_new_password_length_legal'  =>  false,  'is_password_again_empty'   =>  true,
                'is_password_again_matched'     =>  false
            );

        $result['is_successful'] = $this->verify_passwords($old_password, $new_password, $confirm_password, $result);
        if ( $result['is_successful'] ) {
            $result['is_old_password_correct']  = ( strlen($old_password) >= 6 && strlen($old_password) <= 16 );
            $result['is_old_password_correct'] &= $this->__CI->Users_model->change_password($username, $old_password, $new_password);          
        }

        $result['is_successful'] &= $result['is_old_password_correct'];
        return $result;
    }

    /**
     * Verify if all fields are legal.
     * @param  String  $old_password - the password the user used before
     * @param  String  $new_password - the new password the user will use
     * @param  String  $confirm_password - confirm the new password to use
     * @param  Array   $result - an array which contains the query flags
     * @return an array which contains the query flags
     */
    private function verify_passwords($old_password, $new_password, $confirm_password, &$result)
    {
        $result['is_old_password_empty']        = empty($old_password);
        $result['is_new_password_empty']        = empty($new_password);
        $result['is_password_again_empty']      = empty($confirm_password);
        $result['is_new_password_length_legal'] = ( strlen($new_password) >= 6 && strlen($new_password) <= 16 );
        $result['is_password_again_matched']    = ( $new_password == $confirm_password );
        $result['is_successful'] = !( $result['is_old_password_empty'] || $result['is_new_password_empty'] ||
                                      $result['is_password_again_empty'] || !$result['is_new_password_length_legal'] ||
                                     !$result['is_password_again_matched'] );

        return $result['is_successful'];
    }

    /**
     * Delete user's account.
     * @param  String $username [description]
     * @return [type]           [description]
     */
    public function delete_account($username)
    {

    }
}

/* End of file lib_accounts.php */
/* Location: ./application/libraries/lib_accounts.php */