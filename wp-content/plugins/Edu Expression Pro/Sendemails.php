<?php
include('ExamApps.php');
include('Model/Sendemail.php');
include_once("tinyMce.class.php");
class Sendemails extends Sendemail
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_emailsettings";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Sendemail = new Sendemail();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Sendemail';
		$this->url=admin_url('admin.php').'?page=examapp_Sendemail';
	}
	function index()
	{
		try
		{
			if(is_array($_POST['data']))
			{
				$type=$_POST['data']['type'];
				$emailTemplate=$_POST['data']['email_template'];
				$studentId=$_POST['data']['student_id'];
				$teacherId=$_POST['data']['teacher_id'];
				$anyEmail=$_POST['data']['any_email'];
				$subject=$_POST['data']['subject'];
				$message=$_POST['data']['message'];
				if($type==null)
				{
					echo $this->ExamApp->showMessage('Please select any type in the list','danger');
				}
				elseif($type=='Any' && $anyEmail==null)
				{
					echo $this->ExamApp->showMessage('Please type any email','danger');
				}
				else
				{
					$toEmailArr=null;
					if($type=="Student" && $studentId!=null)
					{
					    $toEmailArr=explode(",",$studentId);
					}
					if($type=="Student" && $studentId==null)
					{
					    $SQL = "SELECT `User`.`user_email` AS `email` FROM  `".$this->wpdb->prefix."emp_students` AS `Student` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`) where `Student`.`status`='Active'";
				        $this->autoInsert->iWhileFetch($SQL,$typeArr);
					    foreach($typeArr as $value)
					    $toEmailArr[]=$value['email'];
					    unset($value);
					}
					if($type=="Teacher" && $teacherId!=null)
					{
					    $toEmailArr=explode(",",$teacherId);
					    
					}
					if($type=="Teacher" && $teacherId==null)
					{
					    $SQL = "SELECT `User`.`user_email` AS `email` FROM `wp_users` AS `User` LEFT JOIN `wp_students` AS `Student` ON(`Student`.`student_id`=`User`.`ID`) WHERE `Student`.`id` IS NULL";
				            $this->autoInsert->iWhileFetch($SQL,$typeArr);
					    foreach($typeArr as $value)
					    $toEmailArr[]=$value['email'];
					    unset($value);                   
					}
					if($type=="Any")
					{
					    $toEmailArr=explode(",",$anyEmail);
					    
					}
					if($toEmailArr)
					{
					    foreach($toEmailArr as $toEmail)
                        {
							if($toEmail)
							{
								
								$userEmail=$toEmail;
								$subject=wp_specialchars_decode($_POST['data']['subject']);
								$message=stripcslashes($_POST['data']['message']);
								wp_mail($userEmail,$subject,$message);
								$flag=1;
							}
						}
						if($flag)
						echo $this->ExamApp->showMessage("E-Mail Send Successfully ",'success');
						else
						echo $this->ExamApp->showMessage('The email could not be sent.','danger');
					}
				}	
			}
			$this->ExamApp->getDropdownDb($_POST['email_template'],$emailTemplate,$this->wpdb->prefix."emp_emailtemplates","description","name","WHERE `type` IS NULL AND `status`='Published'");			
			include("View/Sendemails/index.php");
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}	
	}
	public function studentssearch()
	{
        $term = $_REQUEST['q'];
		$sql="SELECT `User`.`user_email` AS `email` FROM `".$this->wpdb->prefix."users` As `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student`  ON(`Student`.`Student_id`=`User`.`ID`) WHERE `User`.`user_email` LIKE '%".$term."%'";
		$this->autoInsert->iWhileFetch($sql,$users);
        $result = array();
        foreach($users as $key => $user)
        {
            $result[$key]['id'] = $user['email'];
            $result[$key]['text'] = $user['email'];
        }
        $users = $result;        
        echo json_encode($users);
    }
	public function teacherssearch()
	{
        $term = $_REQUEST['q'];
		$sql="SELECT `User`.`user_email` AS `email` FROM `".$this->wpdb->prefix."users` As `User` LEFT JOIN `".$this->wpdb->prefix."emp_students` AS `Student`  ON(`Student`.`Student_id`=`User`.`ID`) WHERE `Student`.`id` IS NULL AND `User`.`user_email` LIKE '%".$term."%'";
		$this->autoInsert->iWhileFetch($sql,$users);
        $result = array();
        foreach($users as $key => $user)
        {
            $result[$key]['id'] = $user['email'];
            $result[$key]['text'] = $user['email'];
        }
        $users = $result;        
        echo json_encode($users);
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Sendemails;
$obj->$info();
?>