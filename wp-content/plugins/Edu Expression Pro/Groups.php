<?php
include('ExamApps.php');
include('Model/Group.php');
class Groups extends Group
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_groups";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Group = new Group();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Group';
		$this->url=admin_url('admin.php').'?page=examapp_Group';
	}
	function index()
	{
		include("View/Groups/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'group_name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST);
			$SQL = "SELECT * FROM ".$this->tableName." AS `Group` WHERE 1 ".$condition." $orderBy";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Group`.`id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Groups/show.php');
			die();
		}
	}
	function add()
	{
		try
		{
			if(isset($_POST['submit']))
			{
				$validatedDataArr=$this->Group->validate($_POST);
				if($validatedDataArr['validatedData'] === false)
				{
					echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
				}
				else
				{
					$this->autoInsert->iFetchCount("SELECT COUNT(`id`) as `count` FROM `".$this->wpdb->prefix."groups` WHERE `group_name`='".$validatedDataArr['validatedData']['group_name']."'",$totalRecord);
					if($totalRecord==0)
					{
						if($this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']))
						{
							echo $this->ExamApp->showMessage('Group Added','success');
							$_POST=array();
						}						
					}
					else
					{
						echo $this->ExamApp->showMessage('Group already exist','danger');
					}
				}
			}
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
		include("View/Groups/add.php");
	}
	function edit()
	{
		if(isset($_POST['submit']))
		{
			$isError=false;
			if(is_array($_POST['data']))
			{
				foreach($_POST['data'] as $post)
				{
					$id=$post['id'];
					$validatedDataArr=$this->Group->validate($post);
					if($validatedDataArr['validatedData'] === false)
					{
						echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
						$isError=true;
						$isMsg=true;
						break;
					}
					else
					{
						$this->autoInsert->iFetchCount("SELECT COUNT(`id`) as `count` FROM `".$this->wpdb->prefix."groups` WHERE `id`<>".$validatedDataArr['validatedData']['id']." AND `group_name`='".$validatedDataArr['validatedData']['group_name']."'",$totalRecord);
						if($totalRecord==0)
						{
							$isError=false;
							$this->autoInsert->iUpdate($this->tableName,$validatedDataArr['validatedData'],array('`id`'=>$id));
						}
						else
						{
							$isError=true;
							break;
						}
					}
				}
			}
			if($isError==false)
			{
				echo $this->ExamApp->showMessage('Group Updated','success');
				$this->index();
				die(0);
			}
			if($isError==true)
			{
				if($isMsg==false)
				echo $this->ExamApp->showMessage('Group already exist','danger');
			}			
		}
		if (!isset($_REQUEST['id']))
        {
            echo $this->ExamApp->showMessage('Invalid Post','danger');
			$this->index();
			die(0);
        }
		$ids=explode(",",$_REQUEST['id']);
        $resultArr=array();
        foreach($ids as $k=>$id)
        {
			$k++;
			$SQL = "select * from ".$this->tableName." WHERE `id`=".$id;
			$this->autoInsert->iFetch($SQL,$record);
			$resultArr[$k]=$record;
		}
		include("View/Groups/edit.php");
	}
	function deleteall()
	{
		try
		{
			$isError=false;
			if (!isset($_POST['id']))
			{
			    echo $this->ExamApp->showMessage('Invalid Post','danger');
				$this->index();
				die(0);
			}
				if(is_array($_POST['id']))
				{
					foreach($_POST['id'] as $id)
					{
						if(!$this->autoInsert->iDelete($this->tableName,array('`id`'=>$id)))
						{
							$isError=true;
						}
					}
					if($isError==false)
					echo $this->ExamApp->showMessage('Group has been deleted','danger');
					else
					echo $this->ExamApp->showMessage(__('Delete subject first'),'danger');
					$_REQUEST['info']='index';
					$this->index();
				}
		}
		catch (Exception $e)
        {
            echo $this->ExamApp->showMessage(__('Delete subject first'),'danger');
        }
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Groups;
$obj->$info();
?>