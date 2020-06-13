<?php
include('ExamApps.php');
include('Model/Sendsms.php');
class Sendsmses extends Sendsms
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_smssettings";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Sendsms = new Sendsms();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Sendsms';
		$this->url=admin_url('admin.php').'?page=examapp_Sendsms';
	}
	function index()
	{
		try
		{			
			if(is_array($_POST['data']))
			{
				$type=$_POST['data']['type'];
				$smsTemplate=$_POST['data']['sms_template'];
				$studentId=$_POST['data']['student_id'];
				$teacherId=$_POST['data']['teacher_id'];
				$anySms=$_POST['data']['any_sms'];
				$subject=$_POST['data']['subject'];
				$message=$_POST['data']['message'];
				if($type==null)
				{
					echo $this->ExamApp->showMessage('Please select any type in the list','danger');
				}
				elseif($type=='Any' && $anySms==null)
				{
					echo $this->ExamApp->showMessage('Please type any name','danger');
				}
				else
				{
					$toSmsArr=null;
					if($type=="Student" && $studentId!=null)
					{
					    $toSmsArr=explode(",",$studentId);
					}
					if($type=="Student" && $studentId==null)
					{
					    $sql="SELECT `User`.`Id` AS `id` FROM `".$this->wpdb->prefix."users` As `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student`  ON(`Student`.`Student_id`=`User`.`ID`) WHERE `Student`.`status`='Active'";
					    $this->autoInsert->iWhileFetch($sql,$typeArr);
					    foreach($typeArr as $value)
					    $toSmsArr[]=get_user_meta($value['id'],'examapp_phone',true);
					    unset($value);
					}
					if($type=="Teacher" && $teacherId!=null)
					{
					    $toSmsArr=explode(",",$teacherId);
					    
					}
					if($type=="Teacher" && $teacherId==null)
					{
					    $SQL = "SELECT `User`.`Id` AS `id` FROM `wp_users` AS `User` LEFT JOIN `wp_students` AS `Student` ON(`Student`.`student_id`=`User`.`ID`) WHERE `Student`.`id` IS NULL";
				        $this->autoInsert->iWhileFetch($SQL,$typeArr);
					    foreach($typeArr as $value)
					    $toSmsArr[]=get_user_meta($value['id'],'examapp_phone',true);
					    unset($value);                   
					}
					if($type=="Any")
					{
					    $toSmsArr=explode(",",$anyEmail);
					    
					}
					if($toSmsArr)
					{
					    foreach($toSmsArr as $toSms)
                        {
							if($toSms)
							{
								$this->ExamApp->sendSms($toSms,$message);
								$flag=1;
							}
						}
						if($flag)
						echo $this->ExamApp->showMessage("Sms Send Successfully ",'success');
						else
						echo $this->ExamApp->showMessage('The Sms could not be sent.','danger');
					}
				}	
			}
			$this->ExamApp->getDropdownDb($_POST['sms_template'],$smsTemplate,$this->wpdb->prefix."emp_smstemplates","description","name","WHERE `type` IS NULL AND `status`='Published'");			
			include("View/Sendsmses/index.php");
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}	
	}
	public function studentssearch()
	{
        $term = $_REQUEST['q'];
		$sql="SELECT `User`.`Id` AS `id`,`User`.`display_name` AS `name` FROM `".$this->wpdb->prefix."users` As `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student`  ON(`Student`.`Student_id`=`User`.`ID`) WHERE `User`.`display_name` LIKE '%".$term."%'";
		$this->autoInsert->iWhileFetch($sql,$users);
        $result = array();
        foreach($users as $key => $user)
        {
            $result[$key]['id'] = get_user_meta($user['id'],'examapp_phone',true);
            $result[$key]['text'] = $user['name'];
        }
        $users = $result;        
        echo json_encode($users);
    }
	public function teacherssearch()
	{
        $term = $_REQUEST['q'];
		$sql="SELECT `User`.`Id` AS `id`,`User`.`display_name` AS `name` FROM `".$this->wpdb->prefix."users` As `User` LEFT JOIN `".$this->wpdb->prefix."emp_students` AS `Student`  ON(`Student`.`Student_id`=`User`.`ID`) WHERE `Student`.`id` IS NULL AND `User`.`display_name` LIKE '%".$term."%'";
		$this->autoInsert->iWhileFetch($sql,$users);
        $result = array();
        foreach($users as $key => $user)
        {
            $result[$key]['id'] = get_user_meta($user['id'],'examapp_phone',true);
            $result[$key]['text'] = $user['name'];
        }
        $users = $result;        
        echo json_encode($users);
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Sendsmses;
$obj->$info();
?>