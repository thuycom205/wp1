<?php
include('ExamApps.php');
include('Model/Diff.php');
class Diffs extends Diff
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_diffs";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Diff = new Diff();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Diff';
		$this->url=admin_url('admin.php').'?page=examapp_Diff';
	}
	function index()
	{
		include("View/Diffs/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'diff_level',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'id');
			$SQL = "SELECT * FROM ".$this->tableName." WHERE 1 ".$condition." $orderBy";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'id');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Diffs/show.php');
			die();
		}
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
					$validatedDataArr=$this->Diff->validate($post);
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
			echo $this->ExamApp->showMessage('Diffculty Level Updated','success');
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
		include("View/Diffs/edit.php");
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Diffs;
$obj->$info();
?>