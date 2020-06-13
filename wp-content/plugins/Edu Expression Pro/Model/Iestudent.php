<?php
$dir = plugin_dir_path(__FILE__);
class Iestudent extends ExamApps
{
    function __construct()
    {
        global $wpdb;
        $this->wpdb=$wpdb;
        $this->ExamApp = new ExamApps();
        $this->configuration=$this->ExamApp->configuration();
        $this->autoInsert=new autoInsert();
    }
    public function importInsert($rowData,$groupArr,$currentDateTime)
    {
        foreach($rowData as $dataValue)
        {
            $dataValue=array_shift($dataValue);
            $registerDate=$this->ExamApp->currentDate();
            $password=wp_hash_password($dataValue[4]);
            $recordArr=array('user_login'=>$dataValue[0],'user_nicename'=>$dataValue[0],'user_pass'=>$password,'user_email'=>$dataValue[1],
                             'user_registered'=>$registerDate,'display_name'=>$dataValue[2].' '.$dataValue[3]);            
            if($this->autoInsert->iInsert($this->wpdb->prefix."users",$recordArr))
            {
                $studentId=$this->autoInsert->iLastID();
                $studentArr=array('status'=>'Active','student_id'=>$studentId);
                $this->autoInsert->iInsert($this->wpdb->prefix."emp_students",$studentArr);
                foreach($_POST['group_name'] as $value)
                {
                    $this->autoInsert->iInsert($this->wpdb->prefix."emp_student_groups",array('group_id'=>$value,'student_id'=>$studentId));
                }
                if($dataValue[9]=='')
                $dataValue[9]=0;
                $userMeta=array('examapp_enroll'=>$dataValue[5],'examapp_address'=>$dataValue[6],'examapp_phone'=>$dataValue[7],'examapp_alternate_number'=>$dataValue[8],'examapp_expiry_days'=>$dataValue[9],'examapp_renewal_date'=>$renewalDate,
                                'nickname'=>$dataValue[0],'first_name'=>$dataValue[2],'last_name'=>$dataValue[3],'description'=>'','rich_editing'=>'true','comment_shortcuts'=>'false','admin_color'=>'fresh','use_ssl'=>'0','show_admin_bar_front'=>'true',
                                'wp_capabilities'=>'','wp_user_level'=>'0','examapp_status_user'=>0);
                foreach($userMeta as $metaKey=>$metaValue)
                {
                    if($metaKey=='wp_capabilities')
                    {
                        $user = new WP_User( $studentId );
                        $user->set_role('subscriber');
                    }
                    else
                    {
                        update_user_meta($studentId,$metaKey,$metaValue);
                    }
                }
                $email=$dataValue[1];$studentName=$dataValue[2].' '.$dataValue[3];$password=$dataValue[4];
                $mobileNo=$dataValue[7];$siteName=get_bloginfo();$userName=$dataValue[0];
                if($this->configuration['email_notification'])
                {                          
                    $url=site_url('wp-login.php','login');   
                    $sql="SELECT `Emailtemplate`.`name` AS `name`,`Emailtemplate`.`status` AS `status`,`Emailtemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_emailtemplates` AS `Emailtemplate` where `Emailtemplate`.`type`='SLC'";   
                    $this->autoInsert->iFetch($sql,$emailSettingArr);
                    if($emailSettingArr['status']=="Published")
                    {
                        $subject=wp_specialchars_decode($emailSettingArr['name']);
                        $message=eval('return "' . addslashes($emailSettingArr['description']). '";');
                        wp_mail($email,$subject,$message);
                    }
                }				
                if($this->configuration['sms_notification'])
                {
                    $url=site_url();
                    $sql="SELECT `Smstemplate`.`name` AS `name`,`Smstemplate`.`status` AS `status`,`Smstemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_smstemplates` AS `Smstemplate` where `Smstemplate`.`type`='SLC'";   
                    $this->autoInsert->iFetch($sql,$smsSettingArr);
                    if($smsSettingArr['status']=="Published")
                    {
                        $message=eval('return "' . addslashes($smsSettingArr['description']). '";');							
                        $this->ExamApp->sendSms($mobileNo,$message);
                    }
                }
            }
            else
            {
                return false;
            }
        }
        return true;
    }
    public function exportData($userGroupWise)
    {
        try
        {
            $SQL = "SELECT `User`.`user_login` AS `userName`,`User`.`Id` AS `id`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`User`.`user_registered` AS `register`,`Student`.`status` AS `status` FROM  `".$this->wpdb->prefix."emp_students` AS `Student`
            INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`)  
            LEFT JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON (`Student`.`student_id`=`StudentGroup`.`student_id`)
            LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`StudentGroup`.`group_id`=`UserGroup`.`group_id`)
            WHERE 1=1 ".$userGroupWise." GROUP BY `User`.`id`";
            $this->autoInsert->iWhileFetch($SQL,$post);
            $data=$this->showExportData($post);
            return $data;
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),"danger");
        }    
    }
    public function showExportData($post)
    {
        $ExamApp=new ExamApps();
        $showData=array(array('Groups','Username','Email','First Name','Last Name','Enrolment Number','Address','Phone','Alternate Number','Expiry Days','Registration Date','Status'));
        foreach($post as $value)
        {
            $user_info=get_userdata($value['id']);
            if($user_info->examapp_expiry_days==0)
            $expiryDays="Unlimited";
            else
            $expiryDays=$user_info->examapp_expiry_days;
            $showData[]=array('groups'=>$ExamApp->showGroupName("emp_student_groups","emp_groups","student_id",$value['id']),
                              'username'=>$value['userName'],'email'=>$value['email'],'first_name'=>$user_info->first_name,'last_name'=>$user_info->last_name,
                              'enroll'=>$user_info->examapp_enroll,'address'=>$user_info->examapp_address,'phone'=>$user_info->examapp_phone,
                              'alternate_number'=>$user_info->examapp_alternate_number,'expiry_days'=>$expiryDays,
                              'register_date'=>$this->ExamApp->dateFormat($value['register']),'status'=>$value['status']);
        }
        return$showData;
    }
}
?>