<?php
include('ExamApps.php');
include('Model/Smssetting.php');
class Smssettings extends Smssetting
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_smssettings";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Smssetting = new Smssetting();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Smssetting';
		$this->url=admin_url('admin.php').'?page=examapp_Smssetting';
	}
	function index()
	{
		try
		{
			$recordArr=array();
			if(is_array($_POST['data']))
			{
				$recordArr=array('api'=> $_POST['data']['api'],'senderid'=>$_POST['data']['senderid'],'username'=>$_POST['data']['username'],'password' =>$_POST['data']['password'],'husername' => $_POST['data']['husername'], 'hpassword' => $_POST['data']['hpassword'] ,'hmobile' => $_POST['data']['hmobile'],'hmessage' => $_POST['data']['hmessage'],'hsenderid' => $_POST['data']['hsenderid']);
				$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>1));
				echo $this->ExamApp->showMessage("Sms Setting has been saved",'success');
			}
			$sql="select * from ".$this->tableName;
			$this->autoInsert->iFetch($sql,$post);
	    include("View/Smssettings/index.php");
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
$obj = new Smssettings;
$obj->$info();
?>