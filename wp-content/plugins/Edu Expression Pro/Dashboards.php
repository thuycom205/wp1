<?php
include('ExamApps.php');
include('Model/Dashboard.php');
class Dashboards extends Dashboard
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_groups";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Dashboard = new Dashboard();
		$this->autoInsert=new autoInsert();
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Result';
		$this->url=admin_url('admin.php').'?page=examapp_Result';
	}
	function index()
	{
		// Upcoming Exam Statistic
		
		$currentDateTime=$this->ExamApp->currentDateTime();
		$SQL="SELECT `Exam`.`id`, `Exam`.`name`, `Exam`.`start_date`, `Exam`.`duration`, (SUM(`Question`.`marks`)) AS `total_marks` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` Inner JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` ON (`Exam`.`id`=`ExamQuestion`.`Exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`ExamQuestion`.`question_id`=`Question`.`id`) WHERE `Exam`.`status` = 'Active' ".$this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`")." GROUP BY `Exam`.`id` ORDER BY `Exam`.`start_date` asc LIMIT 7";
		$this->autoInsert->iWhileFetch($SQL,$examStat);
		
		$SQL="SELECT COUNT(DISTINCT(`Exam`.`id`)) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) WHERE `Exam`.`start_date` <= '".$currentDateTime."' AND `Exam`.`end_date` > '".$currentDateTime."' AND `Exam`.`status` = 'Active' ".$this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`")." GROUP BY `Exam`.`id`";
		$this->autoInsert->iFetch($SQL,$totalInprogressExamArr);
		$totalInprogressExam=$totalInprogressExamArr['count'];
		
		$SQL="SELECT COUNT(DISTINCT(`Exam`.`id`)) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) WHERE `Exam`.`start_date` > '".$currentDateTime."'  AND `Exam`.`status` = 'Active' ".$this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`");
		$this->autoInsert->iFetch($SQL,$totalUpcomingExamArr);
		$totalUpcomingExam=$totalUpcomingExamArr['count'];
		
		$SQL="SELECT COUNT(DISTINCT(`Exam`.`id`)) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) WHERE `Exam`.`status` = 'Closed' ".$this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`");
		$this->autoInsert->iFetch($SQL,$totalCompletedExamArr);
		$totalCompletedExam=$totalCompletedExamArr['count'];
		
		$SQL="SELECT COUNT(DISTINCT(`Student`.`id`)) AS `count` FROM `".$this->wpdb->prefix."emp_students` AS `Student` LEFT JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON (`StudentGroup`.`student_id`=`Student`.`student_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`StudentGroup`.`group_id`");
		$this->autoInsert->iFetch($SQL,$totalStudentsArr);
		$totalStudents=$totalStudentsArr['count'];
		
		// End Exam Statistic
		
		// Start Student Statistic
		$studentStatitics=$this->Dashboard->studentStatitics($this->ExamApp->userGroupWiseIn("`Group`.`id`"));
		$totalStudentSort=array();
		foreach($studentStatitics as $key => $row)
		{
		    $totalStudentSort[$key] = $row['GroupName']['total_student'];
		}
		unset($key,$row);
		array_multisort($totalStudentSort,SORT_DESC,$studentStatitics);
		unset($totalStudentSort);
		$studentDetailxaxis=array();
		$activeData=array();$pendingData=array();$suspendData=array();
		$temp=0;
		foreach($studentStatitics as $studentValue)
		{
		    $temp++;
		    $studentDetailXaxis[]=$studentValue['GroupName']['name'];
		    $activeData[]=(int)$studentValue['GroupName']['active'];
		    $pendingData[]=(int)$studentValue['GroupName']['pending'];
		    $suspendData[]=(int)$studentValue['GroupName']['suspend'];
		    if($temp==10)
		    break;
		}
		unset($temp);
		$studentDetailTitle=__('Student Details');
		$studentDetailSeries=json_encode(array(array('name'=>__('Active'),'data'=>$activeData),
						 array('name'=>__('Pending'),'data'=>$pendingData),
						 array('name'=>__('Suspend'),'data'=>$suspendData)));
		$studentDetailXaxis=json_encode($studentDetailXaxis);
		
		// Start Recent Exam Result Result
		$recentExamResult=$this->Dashboard->recentExamResult($this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`"));
		
		// Start Question Bank
		$this->autoInsert->iWhileFetch("SELECT `Diff`.`id`, `Diff`.`diff_level`, `Diff`.`type` FROM `".$this->wpdb->prefix."emp_diffs` AS `Diff` WHERE 1 = 1",$DiffLevel);
		$SQL="SELECT `Subject`.`id`, `Subject`.`subject_name` FROM `".$this->wpdb->prefix."emp_subjects` AS `Subject` LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`Subject`.`id`=`SubjectGroup`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`Subject`.`id`=`Question`.`subject_id`) WHERE 1 ".$this->userGroupWiseIn("`SubjectGroup`.`group_id`")." GROUP BY `Subject`.`id` ORDER BY (Count(DISTINCT(`Question`.`id`))) desc";
		$this->autoInsert->iWhileFetch($SQL,$Subject);
		$totalQuestionArr=array();$chartData=array();
		foreach($Subject as $value)
		{
		    $subjectId=$value['id'];
		    $subjectName=$value['subject_name'];
		    $easy=$this->Dashboard->viewDiffType($subjectId,'E',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $medium=$this->Dashboard->viewDiffType($subjectId,'M',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $difficult=$this->Dashboard->viewDiffType($subjectId,'D',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $DifficultyDetail[$subjectName][]=$easy;
		    $DifficultyDetail[$subjectName][]=$medium;
		    $DifficultyDetail[$subjectName][]=$difficult;
		    $totalQuestion=$easy+$medium+$difficult;
		    $DifficultyDetail[$subjectName]['total_question']=$totalQuestion;
		    $chartData[]=array($subjectName,$totalQuestion,$subjectId);
		    $questionSubjectxAxis[]=$subjectName;
		    $totalQuestionArr[]=$totalQuestion;
		}
		$questionSubjectxAxis=json_encode($questionSubjectxAxis);
		array_multisort($totalQuestionArr,SORT_DESC,$chartData);
		$chartData=array_slice($chartData,0,10);
		$questionCountTitle=__('Question Count');
		$questionCountSeries=json_encode(array('name'=>__('Total Question'),'data'=>$chartData));
		foreach($chartData as $value)
		{
		    $subjectId=$value[2];
		    $easy=$this->Dashboard->viewdifftype($subjectId,'E',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $medium=$this->Dashboard->viewdifftype($subjectId,'M',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $difficult=$this->Dashboard->viewdifftype($subjectId,'D',$this->userGroupWiseIn("`QuestionGroup`.`group_id`"));
		    $easyData[]=(int)$easy;
		    $mediumData[]=(int)$medium;
		    $difficultData[]=(int)$difficult;
		}
		$questionSubjectTile=__('Question Bank Difficulty Wise');
		$questionSubjectSeries=json_encode(array(array('name'=>$this->ExamApp->getDiffLevel('E'),'data'=>$easyData),
						 array('name'=>$this->ExamApp->getDiffLevel('M'),'data'=>$mediumData),
						 array('name'=>$this->ExamApp->getDiffLevel('D'),'data'=>$difficultData)));
		// End Question Bank
		include("View/Dashboards/index.php");		
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Dashboards;
$obj->$info();
?>