<?php 
    /*
    Plugin Name: Exam Application |  VestaThemes.com
    Plugin URI: http://www.eduexpression.com
    Description: Plugin for exam application
    Author: Edu Expression
    Version: 1.0
    Author URI: http://www.silversyclops.net
    */
global $examapp_db_version;
$examapp_db_version = '1.0';
final class examapp
{
    function __construct()
    {
        // Actions
        register_activation_hook(__FILE__,array($this,'examapp_install'));
        register_uninstall_hook(__FILE__,array($this,'examapp_uninstall'));
        add_action('wp_login',array($this,'user_login'),10,2);
        add_action('register_form',array($this,'examapp_user_register_form'));
        add_action('user_register',array($this,'examapp_user_register_save'));
        add_action('profile_personal_options',array($this,'examapp_use_profile_field'));
        add_action('personal_options_update',array($this,'examapp_user_profile_field_save'));
        // Filters
        add_filter('login_message',array($this,'user_login_message'));
        add_filter('registration_errors',array($this,'examapp_registration_errors'),10,3);
        add_action('admin_bar_menu',array($this,'wallet_balance'));
        add_action('admin_head',array($this,'wallet_balance_css'));
        add_action('admin_menu',array($this,'remove_menus'));
        add_action('wp_head',array($this,'register_multiselect'));
        add_action('login_enqueue_scripts', array($this,'themeslug_enqueue_style'),1);
        add_action('login_enqueue_scripts', array($this,'themeslug_enqueue_script'),1);
        
        add_action('admin_menu',array($this,'register_exam_menu_page'));
        add_action('wp_ajax_examapp_Dashboard',array($this,'examapp_dasboard'));
        add_action('wp_ajax_examapp_Usergroup',array($this,'examapp_Usergroup'));
        add_action('wp_ajax_examapp_Group',array($this,'examapp_Group'));
        add_action('wp_ajax_examapp_Subject',array($this,'examapp_Subject'));
        add_action('wp_ajax_examapp_Question',array($this,'examapp_Question'));
        add_action('wp_ajax_examapp_Exam',array($this,'examapp_Exam'));
        add_action('wp_ajax_examapp_Attemptedpaper',array($this,'examapp_Attemptedpaper'));
        add_action('wp_ajax_examapp_Addquestion',array($this,'examapp_Addquestion'));
        add_action('wp_ajax_examapp_UserExam',array($this,'examapp_UserExam'));
        add_action('wp_ajax_examapp_Result',array($this,'examapp_Result'));
        add_action('wp_ajax_examapp_Student',array($this,'examapp_Student'));
        add_action('wp_ajax_examapp_Currency',array($this,'examapp_Currency'));
        add_action('wp_ajax_examapp_Diff',array($this,'examapp_Diff'));
        add_action('wp_ajax_examapp_Qtype',array($this,'examapp_Qtype'));
        add_action('wp_ajax_examapp_Emailtemplate',array($this,'examapp_Emailtemplate'));
        add_action('wp_ajax_examapp_HelpContent',array($this,'examapp_HelpContent'));
        add_action('wp_ajax_examapp_Sendemail',array($this,'examapp_Sendemail'));
        add_action('wp_ajax_examapp_Smstemplate',array($this,'examapp_Smstemplate'));
        add_action('wp_ajax_examapp_Sendsms',array($this,'examapp_Sendsms'));
        add_action('wp_ajax_examapp_UserResult',array($this,'examapp_UserResult'));
        add_action('wp_ajax_examapp_ExamStart',array($this,'examapp_ExamStart'));
        add_action('wp_ajax_examapp_Payment',array($this,'examapp_Payment'));
        add_action('wp_ajax_examapp_UserTransaction',array($this,'examapp_UserTransaction'));
        add_action('wp_ajax_examapp_Iequestion',array($this,'examapp_Iequestion'));
        add_action('wp_ajax_examapp_Iestudent',array($this,'examapp_Iestudent'));
        
    }    
    public function register_exam_menu_page()
    {
        /* Admin Menu */
        add_menu_page('Exam Application','Exam Application','level_1','examapp_Dashboard','','dashicons-money',2);
        add_submenu_page('examapp_Dashboard','Dashboard','Dashboard','level_1','examapp_Dashboard',array($this,'examapp_Dashboard'));
        add_submenu_page('examapp_Dashboard','Groups','Groups','manage_options','examapp_Group',array($this,'examapp_Group'));
        add_submenu_page('examapp_Dashboard','User Groups','User Groups','manage_options','examapp_Usergroup',array($this,'examapp_Usergroup'));    
        add_submenu_page('examapp_Dashboard','Subjects','Subjects','level_1','examapp_Subject',array($this,'examapp_Subject'));
        add_submenu_page('examapp_Dashboard','Questions','Questions','level_1','examapp_Question',array($this,'examapp_Question'));
        add_submenu_page('examapp_Dashboard','Exams','Exams','level_1','examapp_Exam',array($this,'examapp_Exam'));
        add_submenu_page(null,'Attemptedpapers','Attemptedpapers','level_1','examapp_Attemptedpaper',array($this,'examapp_Attemptedpaper'));
        add_submenu_page(null,'Addquestions','Addquestions','level_1','examapp_Addquestion',array($this,'examapp_Addquestion'));
        add_submenu_page(null,'Iequestions','Iequestions','level_1','examapp_Iequestion',array($this,'examapp_Iequestion'));
        add_submenu_page(null,'Iestudents','Iestudents','level_1','examapp_Iestudent',array($this,'examapp_Iestudent'));
        add_submenu_page('examapp_Dashboard','Results','Results','level_1','examapp_Result',array($this,'examapp_Result'));
        add_submenu_page('examapp_Dashboard','Students','Students','level_1','examapp_Student',array($this,'examapp_Student'));
        add_menu_page('Exam Configuration','Exam Configuration','manage_options','examapp_Configuration','','dashicons-admin-generic',3);
        add_submenu_page('examapp_Configuration','Configurations','Configurations','manage_options','examapp_Configuration',array($this,'examapp_Configuration'));
        add_submenu_page('examapp_Configuration','Paypal','Paypal','manage_options','examapp_Paypal',array($this,'examapp_Paypal'));
        add_submenu_page('examapp_Configuration','Currency','Currency','manage_options','examapp_Currency',array($this,'examapp_Currency'));
        add_submenu_page('examapp_Configuration','Certificate Signature','Certificate Signature','manage_options','examapp_Signature',array($this,'examapp_Signature'));
        add_submenu_page('examapp_Configuration','Diffculty Level','Diffculty Level','manage_options','examapp_Diff',array($this,'examapp_Diff'));
        add_submenu_page('examapp_Configuration','Question Type','Question Type','manage_options','examapp_Qtype',array($this,'examapp_Qtype'));    
        add_submenu_page('examapp_Configuration','E-Mail Templates','E-Mail Templates','manage_options','examapp_Emailtemplate',array($this,'examapp_Emailtemplate'));
        add_submenu_page('examapp_Configuration','Send E-Mail','Send E-Mail','manage_options','examapp_Sendemail',array($this,'examapp_Sendemail'));
        add_submenu_page('examapp_Configuration','Sms Setting','Sms Setting','manage_options','examapp_Smssetting',array($this,'examapp_Smssetting'));
        add_submenu_page('examapp_Configuration','Sms Templates','Sms Templates','manage_options','examapp_Smstemplate',array($this,'examapp_Smstemplate'));
        add_submenu_page('examapp_Configuration','Send Sms','Send Sms','manage_options','examapp_Sendsms',array($this,'examapp_Sendsms'));
        add_submenu_page('examapp_Configuration','Help Contents','Help Contents','manage_options','examapp_HelpContent',array($this,'examapp_HelpContent'));
        /* End Admin Menu */
        
        /*Subscriber Menu */
        if(current_user_can('subscriber')|| true)
        {
           add_menu_page('Dashboard','Dashboard','level_0','examapp_UserDashboard',array($this,'examapp_UserDashboard'),'dashicons-align-left',2);
           add_menu_page('Leader Board','Leader Board','level_0','examapp_LeaderBoard',array($this,'examapp_LeaderBoard'),'dashicons-chart-bar',3);
           add_menu_page('My Exams','My Exams','level_0','examapp_UserExam',array($this,'examapp_UserExam'),'dashicons-align-left',4);
           add_menu_page('My Results','My Results','level_0','examapp_UserResult',array($this,'examapp_UserResult'),'dashicons-awards',5);
           add_menu_page('Group Perfomance','Group Perfomance','level_0','examapp_GroupPerfomance',array($this,'examapp_GroupPerfomance'),'dashicons-chart-line',6);
           add_menu_page('Payment','Payment','level_0','examapp_Payment',array($this,'examapp_Payment'),'dashicons-cart',7);
           add_menu_page('Transaction History','Transaction History','level_0','examapp_UserTransaction',array($this,'examapp_UserTransaction'),'dashicons-nametag',8);
           add_menu_page('Help','Help','level_0','examapp_Help',array($this,'examapp_Help'),'dashicons-nametag',8);
        }
        /* End Subscriber Menu */
        
        wp_register_style('exam1','http://fonts.googleapis.com/css?family=Arimo:400,700,400italic');
        wp_register_style('exam2', plugin_dir_url(__FILE__) . 'css/linecons.css');
        wp_register_style('exam3', plugin_dir_url(__FILE__) . 'css/font-awesome.min.css');
        if(current_user_can('subscriber'))
        wp_register_style('exam4', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
        else
        wp_register_style('exam4', plugin_dir_url(__FILE__) . 'css/bootstrap.css');
        if(current_user_can('subscriber'))
        wp_register_style('exam5', plugin_dir_url(__FILE__) . 'css/core.min.css');
        else
        wp_register_style('exam5', plugin_dir_url(__FILE__) . 'css/core.css');
        wp_register_style('exam6', plugin_dir_url(__FILE__) . 'css/forms.css');
        wp_register_style('exam7', plugin_dir_url(__FILE__) . 'css/components.css');
        wp_register_style('exam8', plugin_dir_url(__FILE__) . 'css/skins.css');
        wp_register_style('exam9', plugin_dir_url(__FILE__) . 'css/custom.css');
        wp_register_style('exam10', plugin_dir_url(__FILE__) . 'css/bootstrap-multiselect.css');
        wp_register_style('exam11', plugin_dir_url(__FILE__) . 'css/bootstrap-datetimepicker.min.css');
        wp_register_style('exam12', plugin_dir_url(__FILE__) . 'css/validationEngine.jquery.css');
        wp_register_style('exam13', plugin_dir_url(__FILE__) . 'css/style.css');
        wp_register_style('exam14', plugin_dir_url(__FILE__) . 'css/select2/select2.css');
    
        wp_enqueue_style('exam1');wp_enqueue_style('exam2');wp_enqueue_style('exam3');wp_enqueue_style('exam4');
        wp_enqueue_style('exam5');wp_enqueue_style('exam6');wp_enqueue_style('exam7');wp_enqueue_style('exam8');
        wp_enqueue_style('exam9');wp_enqueue_style('exam10');wp_enqueue_style('exam11');wp_enqueue_style('exam12');
        wp_enqueue_style('exam13');wp_enqueue_style('exam14');
    
        wp_register_script('exam1', plugin_dir_url(__FILE__) . 'js/jquery-1.11.1.min.js', array('jquery'));
        wp_register_script('exam2', plugin_dir_url(__FILE__) . 'js/html5shiv.js', array('jquery'));
        wp_register_script('exam3', plugin_dir_url(__FILE__) . 'js/respond.min.js', array('jquery'));
        wp_register_script('exam4', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'));
        wp_register_script('exam5', plugin_dir_url(__FILE__) . 'js/TweenMax.min.js', array('jquery'));
        wp_register_script('exam6', plugin_dir_url(__FILE__) . 'js/resizeable.js', array('jquery'));
        wp_register_script('exam7', plugin_dir_url(__FILE__) . 'js/joinable.js', array('jquery'));
        wp_register_script('exam8', plugin_dir_url(__FILE__) . 'js/api.js', array('jquery'));
        wp_register_script('exam9', plugin_dir_url(__FILE__) . 'js/toggles.js', array('jquery'));
        wp_register_script('exam10', plugin_dir_url(__FILE__) . 'js/widgets.js', array('jquery'));
        wp_register_script('exam11', plugin_dir_url(__FILE__) . 'js/globalize.min.js', array('jquery'));
        wp_register_script('exam12', plugin_dir_url(__FILE__) . 'js/toastr.min.js', array('jquery'));
        wp_register_script('exam13', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'));
        wp_register_script('exam14', plugin_dir_url(__FILE__) . 'js/bootstrap-multiselect.js', array('jquery'));
        wp_register_script('exam15', plugin_dir_url(__FILE__) . 'js/waiting-dialog.min.js', array('jquery'));
        wp_register_script('exam16', plugin_dir_url(__FILE__) . 'js/moment-with-locales.js', array('jquery'));
        wp_register_script('exam17', plugin_dir_url(__FILE__) . 'js/bootstrap-datetimepicker.min.js', array('jquery'));
        wp_register_script('exam18', plugin_dir_url(__FILE__) . 'js/jquery.validationEngine-en.js', array('jquery'));
        wp_register_script('exam19', plugin_dir_url(__FILE__) . 'js/jquery.validationEngine.js', array('jquery'));
        wp_register_script('exam20', plugin_dir_url(__FILE__) . 'js/highcharts/highcharts.js', array('jquery'));
        wp_register_script('exam21', plugin_dir_url(__FILE__) . 'js/highcharts/themes/sand-signika.js', array('jquery'));
        wp_register_script('exam22', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'));
        wp_register_script('exam24', plugin_dir_url(__FILE__) . 'js/main.custom.min.js', array('jquery'));        
        wp_enqueue_script('exam1');wp_enqueue_script('exam2');wp_enqueue_script('exam3');wp_enqueue_script('exam4');
        wp_enqueue_script('exam5');wp_enqueue_script('exam6');wp_enqueue_script('exam7');wp_enqueue_script('exam8');
        wp_enqueue_script('exam9');wp_enqueue_script('exam10');wp_enqueue_script('exam11');wp_enqueue_script('exam12');
        wp_enqueue_script('exam13');wp_enqueue_script('exam14');wp_enqueue_script('exam15');wp_enqueue_script('exam16');
        wp_enqueue_script('exam17');wp_enqueue_script('exam18');wp_enqueue_script('exam19');wp_enqueue_script('exam20');
        wp_enqueue_script('exam21');wp_enqueue_script('exam22');wp_enqueue_script('exam23');wp_enqueue_script('exam24');
    }
    public function examapp_Dashboard()
    {
        include "Dashboards.php";
        die();
    }    
    public function examapp_Usergroup()
    {
        include "Usergroups.php";
        die();
    }    
    public function examapp_Group()
    {
        include "Groups.php";
        die();
    }
    public function examapp_Subject()
    {
        include "Subjects.php";
        die();
    }
    public function examapp_Question()
    {
        include "Questions.php";
        die();
    }
    public function examapp_Exam()
    {
        include "Exams.php";
        die();
    }
    public function examapp_Attemptedpaper()
    {
        include "Attemptedpapers.php";
        die();
    }    
    public function examapp_Addquestion()
    {
        include "Addquestions.php";
        die();
    }    
    public function examapp_Result()
    {
        include "Results.php";
        die();
    }    
    public function examapp_Student()
    {
        include "Students.php";
        die();
    }
    public function examapp_Configuration()
    {
        include "Configurations.php";
        die();
    }
    public function examapp_Paypal()
    {
        include "Paypals.php";
        die();
    }    
    public function examapp_Currency()
    {
        include "Currencies.php";
        die();
    }
    public function examapp_Signature()
    {
        include "Signatures.php";
        die();
    }
    
    public function examapp_Diff()
    {
        include "Diffs.php";
        die();
    }    
    public function examapp_Qtype()
    {
        include "Qtypes.php";
        die();
    }    
        
    public function examapp_Emailtemplate()
    {
        include "Emailtemplates.php";
        die();
    }
    public function examapp_HelpContent()
    {
        include "HelpContents.php";
        die();
    }
    public function examapp_Sendemail()
    {
        include "Sendemails.php";
        die();
    }
    public function examapp_Smssetting()
    {
        include "Smssettings.php";
        die();
    }
    public function examapp_Smstemplate()
    {
        include "Smstemplates.php";
        die();
    }
    public function examapp_Sendsms()
    {
        include "Sendsmses.php";
        die();
    }
    public function examapp_UserDashboard()
    {
        include "UserDashboards.php";
        die();
    }
    public function examapp_UserExam()
    {
        include "UserExams.php";
        die();
    }
    public function examapp_UserResult()
    {
        include "UserResults.php";
        die();
    }
    public function examapp_ExamStart()
    {
        include "ExamStarts.php";
        die();
    }
    public function examapp_Help()
    {
        include "Helps.php";
        die();
    }
    public function examapp_UserTransaction()
    {
        include "UserTransactions.php";
        die();
    }
    public function examapp_LeaderBoard()
    {
        include "LeaderBoards.php";
        die();
    }
    public function examapp_GroupPerfomance()
    {
        include "GroupPerfomances.php";
        die();
    }
    public function examapp_Payment()
    {
        include "Payments.php";
        die();
    }
    public function examapp_Iequestion()
    {
        include "Iequestions.php";
        die();
    }
    public function examapp_Iestudent()
    {
        include "Iestudents.php";
        die();
    }
    
    public function wallet_balance($wp_admin_bar)
    {
        if(!current_user_can('subscriber'))
        return;
        include_once("ExamApps.php");
        $ExamApp=new ExamApps();
        $configuration=$ExamApp->configuration();
        if($configuration['paid_exam'])
        {
            $balance=$ExamApp->WalletBalance($ExamApp->getCurrentUserId());
            $currency=$ExamApp->getCurrency();
            $balanceText=array('id'=>'wbalance','title'=>"<p id='wbalance'>Wallet Balance: ".$currency.$balance."</p>");
            $wp_admin_bar->add_node( $balanceText );
        }
    }
    public function remove_menus()
    {
        if(!current_user_can('subscriber'))
        return;
        remove_menu_page( 'index.php' );//Dashboard
    }
    function wallet_balance_css()
    {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';
	echo "<style type='text/css'>#wbalance {float: $x;font-weight:bold;}</style>";
    }
    public function register_multiselect()
    {
        echo "<style type='text/css'>#wbalance4543 {font-weight:bold;}</style>";
    }
    public function user_login($user_login,$user=null)
    {
        if(!$user)
        {
            $user=get_user_by('login',$user_login);
        }
        if(!$user)
        {
            return;
	}
        include_once("ExamApps.php");
        $ExamApp=new ExamApps();
        $autoInsert=new autoInsert();
        global $wpdb;
        $disabled=get_user_meta($user->ID,'default_password_nag',true);
        $autoInsert->iFetch("SELECT `status` FROM `".$wpdb->prefix."emp_students` AS `Student` WHERE `Student`.`student_id`=".$user->ID,$record);
        $studentStatus=$record['status'];
        $configArr=$ExamApp->configuration();
        if($disabled==null && $studentStatus=="Unverified")
        {
            $autoInsert->iUpdateArray($wpdb->prefix."emp_students",array('status'=>'Pending'),array('`student_id`'=>$user->ID));
            $studentStatus="Pending";
        }
        if($disabled==null && $studentStatus=="Pending" && $configArr['manual_verification'])
        {
            wp_clear_auth_cookie();
            $login_url=site_url('wp-login.php','login');
            $login_url=add_query_arg('sstatus','1',$login_url);
            wp_redirect( $login_url );
	    exit;
        }
        if($disabled==null && $studentStatus=="Pending")
        {
            $autoInsert->iUpdateArray($wpdb->prefix."emp_students",array('status'=>'Active'),array('`student_id`'=>$user->ID));
            $studentStatus="Active";
        }
        if($disabled==null && $studentStatus=="Pending")
        {
            wp_clear_auth_cookie();
            $login_url=site_url('wp-login.php','login');
            $login_url=add_query_arg('sstatus','1',$login_url);
            wp_redirect( $login_url );
	    exit;
        }
        $status=get_user_meta($user->ID,'examapp_status_user',true);
        if($status=='1')
        {
            wp_clear_auth_cookie();
            $login_url=site_url('wp-login.php','login');
            $login_url=add_query_arg('status','1',$login_url);
            wp_redirect($login_url);
	    exit;
	}
        $expiryDays=get_user_meta($user->ID,'examapp_expiry_days',true);
        if($expiryDays>0)
        {
            $renewalDate=get_user_meta($user->ID,'examapp_renewal_date',true);
            $expiryDate=date('Y-m-d',strtotime($renewalDate."+$expiryDays days"));
            if($ExamApp->currentDate()>$expiryDate)
            {
                wp_clear_auth_cookie();
                $login_url=site_url('wp-login.php','login');
                $login_url=add_query_arg('estatus','1',$login_url);
                wp_redirect($login_url);
                exit;
            }
        }
        update_user_meta($user->ID,'examapp_last_login',$ExamApp->currentDateTime());
        $user=get_user_by('login',$user_login);
        if($user->roles[0]=='subscriber')
        {   
            $login_url=site_url('wp-admin/admin.php').'?page=examapp_UserDashboard';
            $login_url=add_query_arg('info','index',$login_url);
            $login_url=add_query_arg('msg','1',$login_url);
            wp_redirect($login_url);
	    exit;
        }
    }
    public function user_login_message($message)
    {
        if(isset($_GET['status']) && $_GET['status']==1)
        $message='<div id="login_error">'.apply_filters('examapp_status_users_notice', __( 'Account suspended. Please contact admin.','exampp_status_users')).'</div>';
        if(isset($_GET['sstatus']) && $_GET['sstatus']==1)
        $message='<div id="login_error">'.apply_filters('examapp_status_users_notice', __( 'Account pending. Please wait for admin approval.','exampp_status_users')).'</div>';
        if(isset($_GET['estatus']) && $_GET['estatus']==1)
        $message='<div id="login_error">'.apply_filters('examapp_status_users_notice', __( 'Account expired. Please contact admin.','exampp_status_users')).'</div>';
        return $message;
    }
    public function examapp_user_register_form()
    {
        include_once("ExamApps.php");
        $ExamApp=new ExamApps();
        $autoInsert=new autoInsert();
        global $wpdb;
        $groupName=$ExamApp->getMultipleDropdownDb($_POST['group_name'],$wpdb->prefix."emp_groups","id","group_name");
        $first_name=(!empty($_POST['first_name'])) ? trim($_POST['first_name']) : '';
        $last_name=(!empty($_POST['last_name'])) ? trim($_POST['last_name']) : '';
        ?>
        <p>
            <label for="user_group"><?php echo __('Group');?><br>
            <select name="group_name[]" class="form-control multiselectgrp" multiple="true">
            <?php echo$groupName;?>
            </select>
	</p>
        <p>
            <label for="first_name"><?php echo __('First Name');?><br>
            <input name="first_name" id="first_name" class="input" type="text" value="<?php echo esc_attr(wp_unslash($first_name));?>">
	</p>
         <p>
            <label for="last_name"><?php echo __('Last Name');?><br>
            <input name="last_name" id="last_name" class="input" type="text" value="<?php echo esc_attr(wp_unslash($last_name));?>">
	</p>
	<?php
    }
    public function examapp_user_register_save($user_id)
    {
        include_once("ExamApps.php");
        $ExamApp=new ExamApps();
        if($ExamApp->getUserRole($user_id)=="subscriber")
        {
            $autoInsert=new autoInsert();
            global $wpdb;
            $configArr=$ExamApp->configuration();
            if(!$configArr['manual_verification'])
            $status='Pending';
            else
            $status='Unverified';
            $autoInsert->iUpdateArray($wpdb->prefix.'users',array('display_name'=>$_POST['first_name'].' '.$_POST['last_name']),array('`ID`'=>$user_id));
            $userMeta=array('first_name'=>$_POST['first_name'],'last_name'=>$_POST['last_name'],'examapp_expiry_days'=>'0','examapp_renewal_date'=>$ExamApp->currentDate());
            foreach($userMeta as $metaKey=>$metaValue)
            {
                update_user_meta($user_id,$metaKey,$metaValue);
            }
            $studentArr=array('student_id'=>$user_id,'status'=>$status);
            $autoInsert->iInsert($wpdb->prefix.'emp_students',$studentArr);
            foreach($_POST['group_name'] as $value)
            {
                $autoInsert->iInsert($wpdb->prefix."emp_student_groups",array('group_id'=>$value,'student_id'=>$user_id));
            }
        }
    }
    public function examapp_use_profile_field($user)
    {
        // Only show this option to subscriber
        if(!current_user_can('subscriber'))
        return;
        ?>
        <h2><?php echo __('Personal Information');?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label for="examapp_enroll"><?php echo __('Enrolment Number','examapp_enroll'); ?></label>
                    </th>
                    <td>
                        <input name="examapp_enroll" id="examapp_enroll" value="<?php echo get_the_author_meta('examapp_enroll',$user->ID);?>" class="regular-text" type="text">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="examapp_address"><?php echo __('Address','examapp_address'); ?></label>
                    </th>
                    <td>
                        <input name="examapp_address" id="examapp_address" value="<?php echo get_the_author_meta('examapp_address',$user->ID);?>" class="regular-text" type="text">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="examapp_phone"><?php echo __('Phone','examapp_phone'); ?></label>
                    </th>
                    <td>
                        <input name="examapp_phone" id="examapp_phone" value="<?php echo get_the_author_meta('examapp_phone',$user->ID);?>" class="regular-text" type="text">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="examapp_alternate_number"><?php echo __('Alternate Number','examapp_alternate_number'); ?></label>
                    </th>
                    <td>
                        <input name="examapp_alternate_number" id="examapp_alternate_number" value="<?php echo get_the_author_meta('examapp_alternate_number',$user->ID);?>" class="regular-text" type="text">
                    </td>
                </tr>
            <tbody>
        </table>
        <?php
    }
    public function examapp_user_profile_field_save($user_id)
    {
        // Only update to subscriber
        if(!current_user_can('subscriber'))
        return;
        $userMeta=array('examapp_enroll'=>$_POST['examapp_enroll'],'examapp_address'=>$_POST['examapp_address'],'examapp_phone'=>$_POST['examapp_phone'],'examapp_alternate_number'=>$_POST['examapp_alternate_number']);
        foreach($userMeta as $metaKey=>$metaValue)
        {
            update_user_meta($user_id,$metaKey,$metaValue);
        }
    }
    public function examapp_registration_errors($errors,$sanitized_user_login,$user_email)
    {
        if (empty($_POST['first_name']) || ! empty($_POST['first_name']) && trim($_POST['first_name'])=='')
        {
            $errors->add('first_name_error', __( '<strong>ERROR</strong>: You must include a first name.','examapp'));
        }
        if(!is_array($_POST['group_name']))
        {
            $errors->add('group_name_error', __( '<strong>ERROR</strong>: Please select any group.','examapp'));
        }
        return $errors;
    }
    public function themeslug_enqueue_style()
    {
        wp_enqueue_style('bootstrap-core',plugin_dir_url(__FILE__) . 'css/bootstrap-multiselect-core.css',false);
        wp_enqueue_style('multiselect-core',plugin_dir_url(__FILE__) . 'css/bootstrap-multiselect.css',false);
    }
    public function themeslug_enqueue_script()
    {
        wp_enqueue_script('core-js',plugin_dir_url(__FILE__) . 'js/jquery-1.11.1.min.js',false);
        wp_enqueue_script('bootstrap-core-js',plugin_dir_url(__FILE__) . 'js/bootstrap.min.js',false);
        wp_enqueue_script('multiselect-core',plugin_dir_url(__FILE__) . 'js/bootstrap-multiselect.js',false);
        wp_enqueue_script('multiselect-core-add',plugin_dir_url(__FILE__) . 'js/custom.min.js',false);
    }
    function examapp_install()
    {
        global $wpdb;
        global $examapp_db_version;
        require_once('installsql.php');        
        add_option('examapp_db_version',$examapp_db_version);
    }
    function examapp_uninstall()
    {
        global $wpdb;
        global $examapp_db_version;
        require_once('uninstall.php');
    }
}
new examapp();