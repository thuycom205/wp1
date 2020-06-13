<?php
include('ExamApps.php');
include('Model/Configuration.php');
class Configurations extends Configuration
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_configurations";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Configuration = new Configuration();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Configuration';
		$this->url=admin_url('admin.php').'?page=examapp_Configuration';
	}
	function index()
	{
		try
		{
			$recordArr=array();
			if(is_array($_POST['data']))
			{
				$recordArr=array('manual_verification'=>$_POST['data']['manual_verification'],'student_expiry'=>$_POST['data']['student_expiry'],
								 'currency'=> $_POST['data']['currency'],'min_limit'=> $_POST['data']['min_limit'],'max_limit' =>$_POST['data']['max_limit'],
								 'sms_notification' => $_POST['data']['sms_notification'] ,'email_notification' => $_POST['data']['email_notification'],
								 'certificate' => $_POST['data']['certificate'],'paid_exam' => $_POST['data']['paid_exam'],'math_editor' => $_POST['data']['math_editor'],
								 'exam_expiry' => $_POST['data']['exam_expiry'],'exam_feedback' =>$_POST['data']['exam_feedback'], 'tolrance_count' =>$_POST['data']['tolrance_count']);
                $this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>1));
				echo $this->ExamApp->showMessage("Setting has been saved",'success');
			}
			$sql="select * from `".$this->wpdb->prefix."emp_configurations` AS `Configuration`";
			$this->autoInsert->iFetch($sql,$post);			
            $this->ExamApp->getDropdownDb($post['currency'],$currencyName,$this->wpdb->prefix."emp_currencies","id","name");
			include("View/Configurations/index.php");
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Configurations;
$obj->$info();
?>