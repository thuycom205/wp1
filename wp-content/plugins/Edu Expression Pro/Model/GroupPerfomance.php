<?php
$dir = plugin_dir_path(__FILE__);
class GroupPerfomance extends ExamApps
{
    function __construct()
    {
	global $wpdb;
	$this->wpdb=$wpdb;
	$this->ExamApp = new ExamApps();
	$this->configuration=$this->ExamApp->configuration();
	$this->studentId=get_current_user_id();
	$this->autoInsert=new autoInsert();		
    }
    public function userGroupTestName($studentId)
    {
        $sql="SELECT `Exam`.`id`,`Exam`.`name` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam`
        INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)
        WHERE `Exam`.`status`='Closed' AND  `StudentGroup`.`student_id`=".$studentId." GROUP BY `Exam`.`id` ORDER BY `Exam`.`id` DESC LIMIT 20";
        $this->autoInsert->iWhileFetch($sql,$testName);
        return$testName;
    }
    public function userAveragePerformance($examId,$studentId)
    {
	$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId." AND `ExamResult`.`student_id`=".$studentId;
	$this->autoInsert->iFetchCount($sql,$totalAttempt);
	$sql="SELECT SUM(`ExamResult`.`percent`) AS `total_percent` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId." AND `ExamResult`.`student_id`=".$studentId;
	$this->autoInsert->iFetch($sql,$totalPercentArr);
	$totalPercent=$totalPercentArr['total_percent'];
	if($totalAttempt>0)
	{
	  $averagePercent=number_format($totalPercent/$totalAttempt,2);
	}
	else
	$averagePercent=0;
	return (float) $averagePercent;
    }
    public function userGroupAveragePerformance($examId)
    {
	$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId;
	$this->autoInsert->iFetchCount($sql,$totalAttempt);
	$sql="SELECT SUM(`ExamResult`.`percent`) AS `total_percent` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$examId;
	$this->autoInsert->iFetch($sql,$totalPercentArr);
	$totalPercent=$totalPercentArr['total_percent'];
	if($totalAttempt>0)
	{
	  $averagePercent=number_format($totalPercent/$totalAttempt,2);
	}
	else
	$averagePercent=0;
	return (float) $averagePercent;
    }
    public function userPerformance($studentId)
    {
	$testName=$this->userGroupTestName($studentId);
	$userPerformance=array();$userGroupPerformance=array();
	foreach($testName as $testValue)
	{
	  $userPerformance[]=$this->userAveragePerformance($testValue['id'],$studentId);
	  $userGroupPerformance[]=$this->userGroupAveragePerformance($testValue['id']);
	}
	return array($userPerformance,$userGroupPerformance);    
    }    
}
?>