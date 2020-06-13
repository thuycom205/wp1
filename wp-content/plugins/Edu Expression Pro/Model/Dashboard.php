<?php
$dir = plugin_dir_path(__FILE__);
class Dashboard extends ExamApps
{
    function __construct()
    {
        global $wpdb;
        $this->autoInsert=new autoInsert();
        $this->tableNameConfig = $wpdb->prefix."emp_configurations";
        $this->wpdb=$wpdb;
    }
    public function viewDiffType($subjectId,$type,$userGroupWiseId)
    {
        $SQL="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_questions` AS `Question` Inner JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON (`Diff`.`id`=`Question`.`diff_id`) LEFT JOIN `".$this->wpdb->prefix."emp_question_groups` AS `QuestionGroup` ON (`Question`.`id`=`QuestionGroup`.`question_id`) WHERE `Question`.`subject_id` = ".$subjectId." AND `Diff`.`type` = '".$type."'".$userGroupWiseId." GROUP BY `Question`.`subject_id`";
        $this->autoInsert->iFetch($SQL,$record);
        if(is_array($record))
        $quesCount=$record['count'];
        else
        $quesCount=0;
        return $quesCount;
    }
    public function studentGroups($userGroupWiseId)
    {
        $SQL="SELECT * FROM `".$this->wpdb->prefix."emp_groups` AS `Group` WHERE 1 $userGroupWiseId";
        $this->autoInsert->iWhileFetch($SQL,$record);
        return$record;
    }
    public function studentGroupCount($groupId,$status=null)
    {
        if($status==null)
        $statusCond=null;
        else
        $statusCond=" AND `Student`.`status`='".$status."'";
        $SQL="SELECT COUNT(DISTINCT(`Student`.`id`)) AS `count` FROM `".$this->wpdb->prefix."emp_students` AS `Student` LEFT JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`Student`.`student_id`=`StudentGroup`.`student_id`) WHERE 1=1 AND `StudentGroup`.`group_id`=".$groupId.$statusCond;
        $this->autoInsert->iFetch($SQL,$record);
        $count=$record['count'];
        return$count;
    }
    public function studentStatitics($userGroupWiseId)
    {
        $studentGroup=$this->studentGroups($userGroupWiseId);
        $studentStatitics=array();
        foreach($studentGroup as $k=>$groupValue)
        {
            $studentStatitics[$k]['GroupName']['name']=$groupValue['group_name'];
            $studentStatitics[$k]['GroupName']['total_student']=$this->studentGroupCount($groupValue['id']);
            $studentStatitics[$k]['GroupName']['active']=$this->studentGroupCount($groupValue['id'],'Active');
            $studentStatitics[$k]['GroupName']['pending']=$this->studentGroupCount($groupValue['id'],'Pending');
            $studentStatitics[$k]['GroupName']['suspend']=$this->studentGroupCount($groupValue['id'],'Suspend');
        }
        return$studentStatitics;
    }
    public function recentExamResult($userGroupWiseId)
    {
        $SQL="SELECT `Exam`.`id`,`Exam`.`name`,`Exam`.`start_date`,`Exam`.`end_date`,`Exam`.`passing_percent` FROM `".$this->wpdb->prefix."emp_exams` as `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` as `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`)
        WHERE 1=1 AND `Exam`.`status`='Closed' ".$userGroupWiseId." GROUP BY `Exam`.`id` LIMIT 3";
        $this->autoInsert->iWhileFetch($SQL,$examList);
        $recentExamResult=array();
        foreach($examList as $k=>$examvalue)
        {
            $recentExamResult[$k]['RecentExam']['id']=$examvalue['id'];
            $recentExamResult[$k]['RecentExam']['name']=$examvalue['name'];
            $recentExamResult[$k]['RecentExam']['start_date']=$examvalue['start_date'];
            $recentExamResult[$k]['RecentExam']['end_date']=$examvalue['end_date'];
            $recentExamResult[$k]['RecentExam']['OverallResult']['passing']=(float) $examvalue['passing_percent'];
            $recentExamResult[$k]['RecentExam']['OverallResult']['average']=(float) $this->studentAverageResult($examvalue['id']);
            $recentExamResult[$k]['RecentExam']['StudentStat']['pass']=$this->studentStat($examvalue['id'],'Pass');
            $recentExamResult[$k]['RecentExam']['StudentStat']['fail']=$this->studentStat($examvalue['id'],'Fail');
            $recentExamResult[$k]['RecentExam']['StudentStat']['absent']=$this->examTotalAbsent($examvalue['id']);
        }
        return$recentExamResult;
    }
}
?>