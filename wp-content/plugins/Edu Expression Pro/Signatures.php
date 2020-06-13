<?php
include('ExamApps.php');
include('Model/Signature.php');
class Signatures extends Signature
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_configurations";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Signature = new Signature();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Signature';
		$this->url=admin_url('admin.php').'?page=examapp_Signature';
	}
	function index()
	{
		if(isset($_POST['submit']))
		{					
			$fileArr=$this->ExamApp->uploadFile('img','photo',rand(),array('.jpg','.jpeg','.gif','.png','.bmp'),array('image/jpeg','image/png','image/bmp','image/gif'),array('maxWidth'=>200,'maxHeight'=>75,'maxSize'=>200));
			if(strlen($fileArr['errorMsg'])==0)
			{
				$recordArr=array('signature'=>$fileArr['uploadFileName']);
				$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('id'=>1));
				echo $this->ExamApp->showMessage('Signature Added','success');
				$_POST=array();
			}
			else
			{
				echo $this->ExamApp->showMessage($fileArr['errorMsg'],'danger');
			}
			
		}
		include("View/Signatures/index.php");
	}	
	function deleteall()
	{
		try
		{
			$sql="SELECT * FROM ".$this->tableName;
			$this->autoInsert->iFetch($sql,$record);
			$fileName=$record['signature'];			
			if(!isset($fileName))
			{
			    echo $this->ExamApp->showMessage('No image found !','danger');
					$this->index();
					die(0);
			}
			if(isset($fileName))
			{
				$recordArr=array('signature'=>NULL);
				$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>1));
				$this->ExamApp->deleteUpload('img',$fileName);
				echo $this->ExamApp->showMessage('Signature has been deleted','danger');
				$_REQUEST['info']='index';
				$this->index();
			}
		}
		catch (Exception $e)
		{
		 echo $this->ExamApp->showMessage('Something Wrong','danger');
		}
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Signatures;
$obj->$info();
?>