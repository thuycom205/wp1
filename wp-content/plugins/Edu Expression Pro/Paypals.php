<?php
include('ExamApps.php');
include('Model/Paypal.php');
class Paypals extends Paypal
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_paypal_configs";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Paypal = new Paypal();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Paypal';
		$this->url=admin_url('admin.php').'?page=examapp_Paypal';
	}
	function index()
	{
		try
		{
			$recordArr=array();
			if(is_array($_POST['data']))
			{
				if($_POST['data']['sandbox_mode'])
				$sandbox=$_POST['data']['sandbox_mode'];
				else
				$sandbox=0;
				$recordArr=array('username'=> $_POST['data']['username'],'password' =>$_POST['data']['password'], 'signature' => $_POST['data']['signature'] ,'sandbox_mode' => $sandbox);
                               	$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>1));
				echo $this->ExamApp->showMessage("PayPal Setting has been saved",'success');
			}
			$sql="select * from `".$this->wpdb->prefix."emp_paypal_configs` AS `PaypalConfig`";
			$this->autoInsert->iFetch($sql,$post);
			include("View/Paypals/index.php");
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
$obj = new Paypals;
$obj->$info();
?>