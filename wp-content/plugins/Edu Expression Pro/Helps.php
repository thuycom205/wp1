<?php
include_once('ExamApps.php');
include_once('Model/Help.php');
class Helps extends Help
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Help = new Help();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Help';
		$this->url=admin_url('admin.php').'?page=examapp_Help';
	}
	function index()
	{
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_helpcontents` WHERE `status`='Published' ORDER BY id ASC";
		$this->autoInsert->iWhileFetch($sql,$helpPost);
		include("View/Helps/index.php");
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Helps;
$obj->$info();
?>