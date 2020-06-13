<?php
include('ExamApps.php');
include('Model/Smstemplate.php');
class Smstemplates extends Smstemplate
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_smstemplates";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Smstemplate = new Smstemplate();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Smstemplate';
		$this->url=admin_url('admin.php').'?page=examapp_Smstemplate';
	}
	function index()
	{
		include("View/Smstemplates/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'id');
			$SQL = "SELECT * FROM ".$this->tableName." WHERE 1 ".$condition." $orderBy";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Smstemplates/show.php');
			die();
		}
	}
	function add()
	{
		if(isset($_POST['submit']))
		{
			$validatedDataArr=$this->Smstemplate->validate($_POST);
			if($validatedDataArr['validatedData'] === false)
			{
				echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
			}
			else
			{
				
				$this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']);
				echo $this->ExamApp->showMessage('Sms Template Added','success');
				$_POST=array();
			}
		}
		include("View/Smstemplates/add.php");
	}
	function edit()
	{
		if(isset($_POST['submit']))
		{
			if(is_array($_POST['data']))
			{
				foreach($_POST['data'] as $post)
				{
					$id=$post['id'];
					$validatedDataArr=$this->Smstemplate->validate($post);
					if($validatedDataArr['validatedData'] === false)
					{
						echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
						$this->index();
						die(0);
					}
					else
					{
						$this->autoInsert->iUpdate($this->tableName,$validatedDataArr['validatedData'],array('`id`'=>$id));
					}
				}
			}
			echo $this->ExamApp->showMessage('Sms Template Updated','success');
			$this->index();
			die(0);
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
		include("View/Smstemplates/edit.php");
	}
	function deleteall()
	{
		try
		{
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
						$this->autoInsert->iQuery("DELETE FROM ".$this->tableName."  WHERE `id`=".$id." and `type` is NULL",$rs);
					}
					echo $this->ExamApp->showMessage('Sms Template has been deleted','danger');
					$_REQUEST['info']='index';
					$this->index();
				}
		}
		catch (Exception $e)
        {
            echo $this->ExamApp->showMessage(__('Delete Record first'),'danger');
        }
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Smstemplates;
$obj->$info();
?>