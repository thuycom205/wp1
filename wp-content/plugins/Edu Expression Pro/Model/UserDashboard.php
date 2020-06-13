<?php
$dir = plugin_dir_path(__FILE__);
include($dir.'../Model/UserExam.php');
class UserDashboard extends ExamApps
{ 
    public function performanceCount($studentId,$month)
    {      
        $conditions=" AND `ExamResult`.`student_id`=".$studentId." AND MONTH(`ExamResult`.`start_time`)=".$month." AND `ExamResult`.`user_id` >0";
        $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE 1=1 ".$conditions;
        $this->autoInsert->iFetchCount($sql,$examCount);
        $sql="SELECT SUM(`ExamResult`.`percent`) AS `total` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE 1=1".$conditions;
        $this->autoInsert->iFetch($sql,$exampercent);
        $percent=$exampercent['total'];
        if($examCount>0)
        $averagePercent=number_format($percent/$examCount,2);
        else
        $averagePercent=0;
        return$averagePercent;
    }
    public function userTotalAbsent($studentId)
    {
        $sql="SELECT Count(*) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam`
        INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`)
        LEFT JOIN `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ON(`ExamResult`.`exam_id`=`Exam`.`id`)
        INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)
        WHERE `Exam`.`status`='Closed'  AND `StudentGroup`.`student_id`=".$studentId." GROUP BY `Exam`.`id` ORDER BY `Exam`.`start_date` ASC ";
        $this->autoInsert->iFetchCount($sql,$userTotalExam);
        $sql="SELECT Count(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
        WHERE `ExamResult`.`student_id`=".$studentId." GROUP BY `ExamResult`.`exam_id`";
        $this->autoInsert->iFetchCount($sql,$userAttemptExam);
        $userTotalAbsent=$userTotalExam-$userAttemptExam;
        return$userTotalAbsent;
    }
    public function userBestExam($studentId)
    {
        $bestExam=array();
        $sql="SELECT `Exam`.`name`,`ExamResult`.`start_time` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
        INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamResult`.`exam_id`=`Exam`.`id`)
        WHERE `ExamResult`.`student_id`=".$studentId." ORDER BY `ExamResult`.`percent` DESC ";
        $this->autoInsert->iFetch($sql,$bestExam);
        return$bestExam;
    }
    
}
?>