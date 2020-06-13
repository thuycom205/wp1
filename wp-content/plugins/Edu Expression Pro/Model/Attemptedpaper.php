<?php
$dir = plugin_dir_path(__FILE__);
class Attemptedpaper extends ExamApps
{
    public function examCount($id,$globalCondition)
    {
        $SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
        INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`) ".$globalCondition." AND `ExamResult`.`exam_id`=".$id;
        $this->autoInsert->iFetchCount($SQL,$examCount);
        return$examCount;
    }
    public function obtainedMarks($id=null)
    {
        $this->autoInsert->iSum($obtainedMarks,$this->wpdb->prefix."emp_exam_stats",'marks_obtained',array('exam_result_id'=>$id));
        return$obtainedMarks;
    }
}
?>