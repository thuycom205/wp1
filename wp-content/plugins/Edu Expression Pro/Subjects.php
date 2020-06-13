<?php
include('ExamApps.php');
include('Model/Subject.php');
class Subjects extends Subject
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_subjects";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Subject = new Subject();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Subject';
		$this->url=admin_url('admin.php').'?page=examapp_Subject';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
	}
	function index()
	{
		include("View/Subjects/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'subject_name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'Subject.id');
			$SQL="SELECT `Subject`.`id`, `Subject`.`subject_name`, (Count(DISTINCT(`Question`.`id`))) AS `qbank_count` FROM `".$this->tableName."` AS `Subject` LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`Subject`.`id`=`Question`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`Subject`.`id`=`SubjectGroup`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_question_groups` AS `QuestionGroup` ON (`Question`.`id`=`QuestionGroup`.`question_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`SubjectGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ".$condition." GROUP BY `Subject`.`id` ".$orderBy;
			$SQLc="SELECT Count(DISTINCT(`Subject`.`id`)) AS `count` FROM `".$this->tableName."` AS `Subject` LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`Subject`.`id`=`SubjectGroup`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`SubjectGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ".$condition;
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Subject`.`id`',$SQLc);
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Subjects/show.php');
			die();
		}
	}
	function add()
	{
		if(isset($_POST['submit']))
		{
			if(isset($_POST['group_name']) && is_array($_POST['group_name']))
			{
				$validatedDataArr=$this->Subject->validate($_POST);
				if($validatedDataArr['validatedData'] === false)
				{
					echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
				}
				else
				{
					$this->autoInsert->iFetch("SELECT * FROM `".$this->wpdb->prefix."emp_subjects` WHERE `subject_name`='".$validatedDataArr['validatedData']['subject_name']."'",$subjectArr);
					if($subjectArr)
					{
						foreach($_POST['group_name'] as $value)
						{
							$this->autoInsert->iFetchCount("SELECT COUNT(`id`) as `count` FROM `".$this->wpdb->prefix."emp_subject_groups` WHERE `subject_id`=".$subjectArr['id']." AND `group_id`=".$value,$totalRecord);
							if($totalRecord==0)
							{
								$isFlag=true;
								$this->autoInsert->iInsert($this->wpdb->prefix."emp_subject_groups",array('group_id'=>$value,'subject_id'=>$subjectArr['id']));
							}
							else
							{
								$isFlag=false;
							}
						}
						if($isFlag==true)
						{
							echo $this->ExamApp->showMessage('Subject Added Successfully','success');
							$_POST=array();
						}
						else
						{
							echo $this->ExamApp->showMessage('Subject already exist','danger');
						}
					}
					else
					{
						$this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']);
						$subjectId=$this->autoInsert->iLastID();
						foreach($_POST['group_name'] as $value)
						{
							$this->autoInsert->iInsert($this->wpdb->prefix."emp_subject_groups",array('group_id'=>$value,'subject_id'=>$subjectId));
						}
						$_POST=array();
						echo $this->ExamApp->showMessage('Subject Added Successfully','success');
					}					
				}
			}
			else
			{
				echo $this->ExamApp->showMessage('Please select any group','danger');
			}
		}
		$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		include("View/Subjects/add.php");
	}
	function edit()
	{
		$isError=false;
		if(isset($_POST['submit']))
		{
			if(is_array($_POST['data']))
			{
				foreach($_POST['data'] as $post)
				{
					if(isset($post['group_name']) && is_array($post['group_name']))
					{
						$id=$post['id'];
						$validatedDataArr=$this->Subject->validate($post);
						if($validatedDataArr['validatedData'] === false)
						{
							echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
							$isError=true;
							$isMsg=true;
							break;
						}
						else
						{
							if($this->autoInsert->iUpdate($this->tableName,$validatedDataArr['validatedData'],array('`id`'=>$id)))
							{
								$isError=false;								
							}
							$subjectId=$id;
							$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_subject_groups` WHERE `subject_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
							foreach($post['group_name'] as $value)
							{
								$this->autoInsert->iInsert($this->wpdb->prefix."emp_subject_groups",array('group_id'=>$value,'subject_id'=>$subjectId));
							}
						}
					}
					else
					{
						$isError=true;
						break;
					}
				}
			}
			if($isError==false)
			{
				echo $this->ExamApp->showMessage('Subject Updated Successfully','success');
				$this->index();
				die(0);
			}
			if($isError==true)
			{
				if($isMsg==false)
				echo $this->ExamApp->showMessage('Please select any group','danger');
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
			$groupName=$this->ExamApp->getGroupName("emp_subject_groups","emp_groups","subject_id",$id);
			$groupNameArr=array();
			foreach($groupName as $grp)
			{
				$groupNameArr[]=$grp['id'];
			}
			$groupNameEditArr[$k]=$this->ExamApp->getMultipleDropdownDb($groupNameArr,$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		}		
		include("View/Subjects/edit.php");
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
				$this->autoInsert->iQuery('START TRANSACTION',$rs);
				foreach($_POST['id'] as $id)
				{
					$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_subject_groups` WHERE `subject_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
				}
				if($this->autoInsert->iQuery("DELETE `Subject` FROM `".$this->tableName."` AS `Subject` LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON `Subject`.`id` = `SubjectGroup`.`subject_id` WHERE `SubjectGroup`.`id` IS NULL",$rs))
				{
					$this->autoInsert->iQuery('COMMIT',$rs);
					echo $this->ExamApp->showMessage('Subject has been deleted','danger');
				}
				else
				{
					$this->autoInsert->iQuery('ROLLBACK',$rs);
					echo $this->ExamApp->showMessage('Delete exam first!','danger');
				}
				$_REQUEST['info']='index';
				$this->index();
			}			
		}
		catch (Exception $e)
        {
            echo $this->ExamApp->showMessage('Delete exam first!','danger');
        }
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Subjects;
$obj->$info();
?>