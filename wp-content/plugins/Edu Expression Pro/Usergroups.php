<?php
include('ExamApps.php');
include('Model/Usergroup.php');
class Usergroups extends Usergroup
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Usergroup = new Usergroup();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Usergroup';
		$this->url=admin_url('admin.php').'?page=examapp_Usergroup';
	}
	function index()
	{
		include("View/Usergroups/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'display_name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST);
			$SQL = "SELECT `User`.`Id` AS `id`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`User`.`user_login` AS `username` FROM `".$this->wpdb->prefix."users` AS `User` INNER JOIN `".$this->wpdb->prefix."usermeta` AS `UserMeta` ON(`User`.`ID`=`UserMeta`.`user_id`) WHERE `UserMeta`.`meta_value` like '%contributor%' ".$condition." $orderBy ";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`User`.`Id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Usergroups/show.php');
			die();
		}
	}
	function addusergroup()
	{
		$isError=false;
		$userId=$_REQUEST['id'];
		$sql="select count(id) AS `count` from `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` where `UserGroup`.`user_id`=".$userId;
		$this->autoInsert->iFetchCount($sql,$count);
		if(isset($_POST['submit']))
		{
			if(isset($_POST['group_name']) && is_array($_POST['group_name']))
			{
				if($count >0)
				{
					$this->autoInsert->iDelete($this->wpdb->prefix."emp_user_groups",array('`user_id`'=>$userId));
					   foreach($_POST['group_name'] as $value)
					{
						
						$this->autoInsert->iInsert($this->wpdb->prefix."emp_user_groups",array('group_id'=>$value,'user_id'=>$userId));
					}	
				}
				else
				{
					foreach($_POST['group_name'] as $value)
					{
						
						$this->autoInsert->iInsert($this->wpdb->prefix."emp_user_groups",array('group_id'=>$value,'user_id'=>$userId));
					}
				}	
				echo $this->ExamApp->showMessage('User Group Added Successfully','success');
				$this->index();
				die(0);
			}
			else
			{
				echo $this->ExamApp->showMessage('Please select any group','danger');
				$isError=true;
			}
		}
		if($count >0)
		{
			$groupName1=$this->ExamApp->getGroupName("emp_user_groups","emp_groups","user_id",$userId);
			$groupNameArr=array();
			foreach($groupName1 as $grp)
			{
				$groupNameArr[]=$grp['id'];
			}
			$groupName=$this->ExamApp->getMultipleDropdownDb($groupNameArr,$this->wpdb->prefix."emp_groups","id","group_name");
		}
		else
		{
			$this->ExamApp->getDropdownDb($_POST['group_name'],$groupName,$this->wpdb->prefix."emp_groups","id","group_name");
		}
		include("View/Usergroups/addusergroup.php");
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Usergroups;
$obj->$info();
?>