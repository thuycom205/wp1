<?php
include_once('ExamApps.php');
include_once('Model/UserDashboard.php');
class UserDashboards extends UserDashboard
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_exam_results";
		$this->ExamApp = new ExamApps();
		$this->UserExam=new UserExam();
		$this->configuration=$this->ExamApp->configuration();
		$this->UserDashboard = new UserDashboard();
		$this->autoInsert=new autoInsert();
		$this->studentId=$this->ExamApp->getCurrentUserId();
		$this->limit=5;
	}
	function index()
	{
		$todayExam=$this->UserExam->getUserExam("today",$this->studentId,$this->ExamApp->currentDateTime(),$this->limit);
		$upcomingExam=$this->UserExam->getUserExam("upcoming",$this->studentId,$this->ExamApp->currentDateTime(),$this->limit);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->tableName."` AS `Dashboard` WHERE `Dashboard`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetchCount($sql,$totalExamGiven);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->tableName."` AS `Dashboard` WHERE `Dashboard`.`result`='Fail' AND `Dashboard`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetchCount($sql,$failedExam);
		$userTotalAbsent=$this->UserDashboard->userTotalAbsent($this->studentId);
		if($userTotalAbsent<0)
		$userTotalAbsent=0;
		$bestScoreArr=$this->UserDashboard->userBestExam($this->studentId);
		$bestScore="";
		$bestScoreDate="";
		if(isset($bestScoreArr['name']))
		{
		    $bestScore=$bestScoreArr['name'];
		    $bestScoreDate=$this->ExamApp->dateFormat($bestScoreArr['start_time']);
		}
		$limit=$this->limit;
		$performanceChartData=array();
		$currentMonth=$this->ExamApp->getStringDateFormat('m');
		for($i=1;$i<=12;$i++)
		{
		  if($i>$currentMonth)
		  break;
		  $examData=$this->UserDashboard->performanceCount($this->studentId,$i);
		  $monthPerformanceChartData[]=(float) $examData;
		}
		$xAxisCategories=array(__('Jan'),__('Feb'),__('Mar'),__('Apr'),__('May'),__('Jun'),__('Jul'),__('Aug'),__('Sep'),__('Oct'),__('Nov'),__('Dec'));
		$monthxAxis=json_encode($xAxisCategories);
		$monthSeries=json_encode(array(array('name'=>__('Month'),'data'=>$monthPerformanceChartData)));
		$monthyAxisTitleText=__('Percentage');
		
		$sql="SELECT `Exam`.`name`,`ExamResult`.`percent` FROM `".$this->tableName."` AS `ExamResult`
		INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamResult`.`exam_id`=`Exam`.`id`)
		WHERE `ExamResult`.`student_id`=".$this->studentId." ORDER BY `ExamResult`.`id` DESC LIMIT 10";
		$this->autoInsert->iWhileFetch($sql,$examResultArr);
		$sql="SELECT SUM(`ExamResult`.`percent`) AS `total_percent` FROM `".$this->tableName."` AS `ExamResult`
		WHERE `ExamResult`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetch($sql,$totalPercentArr);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->tableName."` AS `ExamResult` WHERE `ExamResult`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetchCount($sql,$totalExamAttempt);
		$totalPercent=$totalPercentArr['total_percent'];
		if($totalExamAttempt>0)
		$averagePercent=round($totalPercent/$totalExamAttempt,2);
		else
		$averagePercent=0;
		$performanceChartData=array();$xAxisCategories=array();
		foreach($examResultArr as $post)
		{
		   $xAxisCategories[]=array($post['name']);
		   $performanceChartData[]=array((float) $post['percent']);
		}
		$examyAxisTitleText=__('Percentage');
		$examxAxis=json_encode($xAxisCategories);
		$examSeries=json_encode(array(array('name'=>__('Exams'),'data'=>$performanceChartData)));
		
		$rank=0;
		$sql="SELECT `percent`,`student_id`, FIND_IN_SET(`percent`,(SELECT GROUP_CONCAT(`percent` ORDER BY `percent` DESC) FROM `".$this->wpdb->prefix."emp_exam_results`)) AS `rank` FROM `".$this->wpdb->prefix."emp_exam_results`  WHERE `student_id`=$this->studentId HAVING `rank` IS NOT NULL ORDER BY `percent` DESC LIMIT 1";
		$this->autoInsert->iFetch($sql,$rankPost);
		if($rankPost)
		$rank=$rankPost['rank'];
		include("View/UserDashboards/index.php");		
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new UserDashboards;
$obj->$info();
?>