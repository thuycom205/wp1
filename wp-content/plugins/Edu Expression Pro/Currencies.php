<?php
include('ExamApps.php');
include('Model/Currency.php');
class Currencies extends Currency
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_currencies";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Currency = new Currency();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Currency';
		$this->url=admin_url('admin.php').'?page=examapp_Currency';
	}
	function index()
	{
		include("View/Currencies/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'id');
			$SQL = "SELECT * FROM ".$this->tableName." WHERE 1 ".$condition." $orderBy";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'id');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Currencies/show.php');
			die();
		}
	}
	function add()
	{
		if(isset($_POST['submit']))
		{
			$validatedDataArr=$this->Currency->validate($_POST);
			if($validatedDataArr['validatedData'] === false)
			{
				echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
			}
			else
			{
				
				$fileArr=$this->ExamApp->uploadFile('img/currencies','photo',rand(),array('.jpg','.jpeg','.gif','.png','.bmp'),array('image/jpeg','image/png','image/bmp','image/gif'),array('maxWidth'=>50,'maxHeight'=>50,'maxSize'=>200));
				if(strlen($fileArr['errorMsg'])==0)
				{
					$validatedDataArr['validatedData']['photo']=$fileArr['uploadFileName'];
					$this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']);
					echo $this->ExamApp->showMessage('Currency Added','success');
					$_POST=array();
				}
				else
				{
					echo $this->ExamApp->showMessage($fileArr['errorMsg'],'danger');
				}
			}
		}
		include("View/Currencies/add.php");
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
						$sql="SELECT `Currency`.`photo` FROM `".$this->tableName."` AS `Currency` WHERE  `Currency`.`id`=".$id;
						$this->autoInsert->iFetch($sql,$record);
						$fileName=$record['photo'];
						$this->autoInsert->iDelete($this->tableName,array('`id`'=>$id));
						$this->ExamApp->deleteUpload('img/currencies',$fileName);
					}
					echo $this->ExamApp->showMessage('Currency has been deleted','danger');
					$_REQUEST['info']='index';
					$this->index();
				}
		}
		catch (Exception $e)
		{
		 echo $this->ExamApp->showMessage('Delete Records first','danger');
		}
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Currencies;
$obj->$info();
?>