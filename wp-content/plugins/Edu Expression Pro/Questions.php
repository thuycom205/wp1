<?php
include('ExamApps.php');
include('Model/Question.php');
include_once("tinyMce.class.php");
class Questions extends Question
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_questions";
		$this->tableQuestionGroup = $wpdb->prefix."emp_question_groups";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Question = new Question();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Question';
		$this->url=admin_url('admin.php').'?page=examapp_Question';
		$this->Iequestions=admin_url('admin.php').'?page=examapp_Iequestion';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
	}
	function index()
	{
		$mathEditor=$this->configuration['math_editor'];
		$this->ExamApp->getDropdownDb($_POST['qtype_id'],$qtypeName,$this->wpdb->prefix."emp_qtypes","id","question_type");
		$this->ExamApp->getDropdownDb($_POST['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name");
		$this->ExamApp->getDropdownDb($_POST['diff_id'],$diffName,$this->wpdb->prefix."emp_diffs","id","diff_level");
		include("View/Questions/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'Question.subject_id',$_POST['subject_id'],'=');
			$condition.=$searchArr['condition'];
			$this->ExamApp->getAdvancedSearch($searchArr,'Question.qtype_id',$_POST['qtype_id'],'=');
			$condition.=$searchArr['condition'];
			$this->ExamApp->getAdvancedSearch($searchArr,'Question.diff_id',$_POST['diff_id'],'=');
			$condition.=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'Question.id');
			$SQL = "SELECT *,`Diff`.`diff_level` AS `diffLevel`,`Qtype`.`question_type` AS `qtypeName`,`Question`.`id` as `id` FROM `".$this->tableName."` AS `Question` LEFT JOIN `".$this->wpdb->prefix."emp_question_groups` AS `QuestionGroup` ON (`Question`.`id`=`QuestionGroup`.`question_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`QuestionGroup`.`group_id`=`UserGroup`.`group_id`) LEFT JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Question`.`subject_id`=`Subject`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`) INNER JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON(`Diff`.`id`=`Question`.`diff_id`) WHERE  1=1 ".$this->userGroupWise." ".$condition." GROUP BY `Question`.`id` ".$orderBy;
			$SQLc="SELECT COUNT(DISTINCT(`Question`.`id`)) as `count` FROM `".$this->tableName."` AS `Question` LEFT JOIN `".$this->wpdb->prefix."emp_question_groups` AS `QuestionGroup` ON (`Question`.`id`=`QuestionGroup`.`question_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`QuestionGroup`.`group_id`=`UserGroup`.`group_id`) LEFT JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Question`.`subject_id`=`Subject`.`id`) WHERE  1=1 ".$this->userGroupWise." ".$condition;
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Question`.`id`',$SQLc);
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber,"NO");
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Questions/show.php');
			die();
		}
	}
	function add()
	{
		if(isset($_POST['submit']))
		{
			if($_POST['qtype_id']==1 && $_POST['answer1']==null)
			{
			    echo $this->ExamApp->showMessage('Please Choose the Correct answer','danger');
			}
			elseif(($_POST['qtype_id']==2) && !isset($_POST['true_false']))
			{
			    echo $this->ExamApp->showMessage('Please select true or false','danger');
			}
			elseif(($_POST['qtype_id']==3) && $_POST['fill_blank']==null)
			{
			    echo $this->ExamApp->showMessage('Please fill blank space','danger');
			}
			elseif(!is_array($_POST['group_name']))
			{
				echo $this->ExamApp->showMessage('Please Select any group','danger');
			}
			else
			{
				$validatedDataArr=$this->Question->validate($_POST);
				if($validatedDataArr['validatedData'] == false)
				{
					echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
				}
				else
				{
					if($_POST['answer1'])
					{
						$selected=implode(",",$_POST['answer1']);
						$validatedDataArr['validatedData']['answer']=$selected;
					}					
					$this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']);
					$questionId=$this->autoInsert->iLastID();
					foreach($_POST['group_name'] as $value)
					{
						$this->autoInsert->iInsert($this->wpdb->prefix."emp_question_groups",array('group_id'=>$value,'question_id'=>$questionId));
					}
					echo $this->ExamApp->showMessage('Question Added Successfully','success');
					$_POST=array();
				}
			}
			
		}
		$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		$this->ExamApp->getDropdownDb($_POST['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name","LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`PrimaryTable`.`id`=`SubjectGroup`.`subject_id`) WHERE 1 ".$this->ExamApp->userGroupWiseIn("`SubjectGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		$this->ExamApp->getDropdownDb($_POST['diff_id'],$diffName,$this->wpdb->prefix."emp_diffs","id","diff_level");
		$sqlQtype="select * from ".$this->wpdb->prefix."emp_qtypes";
		$this->autoInsert->iWhileFetch($sqlQtype,$records);
		$mathEditor=$this->configuration['math_editor'];
		include("View/Questions/add.php");
	}
	function edit()
	{
		$isError=false;
		$sqlQtype="select * from ".$this->wpdb->prefix."emp_qtypes";
        $this->autoInsert->iWhileFetch($sqlQtype,$records);		
		if(isset($_POST['submit']))
		{
			if(is_array($_POST['data']))
			{
				if($_POST['data']['qtype_id']==1 && $_POST['data']['answer1']==null)
				{
					$isError=true;
					echo $this->ExamApp->showMessage('Please Choose the Correct answer','danger');
				}
				elseif(($_POST['data']['qtype_id']==2) && !isset($_POST['data']['true_false']))
				{
					$isError=true;
				    echo $this->ExamApp->showMessage('Please select true or false','danger');
				}
				elseif(($_POST['data']['qtype_id']==3) && $_POST['data']['fill_blank']==null)
				{
					$isError=true;
				    echo $this->ExamApp->showMessage('Please fill blank space','danger');
				}
				elseif(!is_array($_POST['data']['group_name']))
				{
					$isError=true;
					echo $this->ExamApp->showMessage('Please Select any group','danger');
				}
				else
				{
					if($_POST['data']['qtype_id']==1)
					{
					    $_POST['data']['true_false']='';
					    $_POST['data']['fill_blank']='';
					}
					elseif($_POST['data']['qtype_id']==2)
					{
					    $_POST['data']['answer1']='';
					    $_POST['data']['fill_blank']='';
					    $_POST['data']['option1']='';
					    $_POST['data']['option2']='';
					    $_POST['data']['option3']='';
					    $_POST['data']['option4']='';
					    $_POST['data']['option5']='';
					    $_POST['data']['option6']='';
					}
					elseif($_POST['data']['qtype_id']==3)
					{
					    $_POST['data']['answer1']='';
					    $_POST['data']['true_false']='';
					    $_POST['data']['option1']='';
					    $_POST['data']['option2']='';
					    $_POST['data']['option3']='';
					    $_POST['data']['option4']='';
					    $_POST['data']['option5']='';
					    $_POST['data']['option6']='';
					}
					else
					{
					    $_POST['data']['answer1']='';
					    $_POST['data']['true_false']='';
					    $_POST['data']['fill_blank']='';
					    $_POST['data']['option1']='';
					    $_POST['data']['option2']='';
					    $_POST['data']['option3']='';
					    $_POST['data']['option4']='';
					    $_POST['data']['option5']='';
					    $_POST['data']['option6']='';
					}
					$id=$_POST['data']['id'];
					$validatedDataArr=$this->Question->validate($_POST['data']);
					if($validatedDataArr['validatedData'] === false)
					{
						echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
						$this->index();
						die(0);
					}
					else
					{
						if($_POST['data']['answer1'])
						{
							$selected=implode(",",$_POST['data']['answer1']);
							$_POST['data']['answer']=$selected;
						}
						if($this->autoInsert->iUpdate($this->tableName,$validatedDataArr['validatedData'],array('`id`'=>$id)))
						{
							$isError=false;				
						}
						$this->autoInsert->iQuery("DELETE FROM `".$this->tableQuestionGroup."` WHERE `question_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
						foreach($_POST['data']['group_name'] as $value)
						{
							$this->autoInsert->iInsert($this->wpdb->prefix."emp_question_groups",array('group_id'=>$value,'question_id'=>$id));
						}						
					}
				}
			}
			if($isError==false)
			{
				echo $this->ExamApp->showMessage('Question Updated Successfully','success');
				$this->index();
				die(0);
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
			$groupName=$this->ExamApp->getGroupName("emp_question_groups","emp_groups","question_id",$id);
			$groupNameArr=array();
			foreach($groupName as $grp)
			{
				$groupNameArr[]=$grp['id'];
			}
			$groupNameEditArr=$this->ExamApp->getMultipleDropdownDb($groupNameArr,$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		}
		$this->ExamApp->getDropdownDb($resultArr[1]['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name","LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`PrimaryTable`.`id`=`SubjectGroup`.`subject_id`) WHERE 1 ".$this->ExamApp->userGroupWiseIn("`SubjectGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		$this->ExamApp->getDropdownDb($resultArr[1]['diff_id'],$diffName,$this->wpdb->prefix."emp_diffs","id","diff_level");
		$mathEditor=$this->configuration['math_editor'];
		include("View/Questions/edit.php");
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
				$this->autoInsert->iQuery('START TRANSACTION',$rs);
				foreach($_POST['id'] as $id)
				{
					$this->autoInsert->iQuery("DELETE FROM `".$this->tableQuestionGroup."` WHERE `question_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
				}
				if($this->autoInsert->iQuery("DELETE `Question` FROM `".$this->tableName."` AS `Question` LEFT JOIN `".$this->tableQuestionGroup."` AS `QuestionGroup` ON `Question`.`id` = `QuestionGroup`.`question_id` WHERE `QuestionGroup`.`id` IS NULL",$rs))
				{
					$this->autoInsert->iQuery('COMMIT',$rs);
					echo $this->ExamApp->showMessage('Question has been deleted','danger');
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
            echo $this->ExamApp->showMessage(__($e->getMessage()),'danger');
        }
	}
	function viewquestion()
	{
		$id=$_REQUEST['id'];
		include("View/Questions/viewquestion.php");
	}
	function view()
	{
		$mathEditor=$this->configuration['math_editor'];
		$id=$_REQUEST['id'];
		$resultArr=array();
		$SQL = "SELECT *,`Qtype`.`question_type` as `qtypename`,`Diff`.`diff_level` as `diffname`,`Subject`.`subject_name` as `subjectname`,`Question`.`id` as `id` FROM `".$this->tableName."` AS `Question` LEFT JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Question`.`subject_id`=`Subject`.`id`)LEFT JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON (`Question`.`diff_id`=`Diff`.`id`)LEFT JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON (`Question`.`qtype_id`=`Qtype`.`id`)WHERE Question.`id`=".$id;
		$this->autoInsert->iFetch($SQL,$record);
		$resultArr=$record;
		include("View/Layouts/script_header.php");
		include("View/Questions/view.php");
		include("View/Layouts/script_footer.php");
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Questions;
$obj->$info();
?>