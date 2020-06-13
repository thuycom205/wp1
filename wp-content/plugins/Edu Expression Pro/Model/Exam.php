<?php
$dir = plugin_dir_path(__FILE__);
class Exam extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'name'    => 'required|alphaNumericCustom',
                'passing_percent'    => 'required|numeric',
                'duration'    => 'required|numeric',
                'attempt_count'    => 'required|numeric',
                'start_date'    => 'required|date',
                'end_date'    => 'required|date'
                
                ));
        $gump->filter_rules(array(
                'name' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("name", "Group Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }    
    public function examStats($id)
    {
        $sql="SELECT `Exam`.`id`,`Exam`.`name`,`Exam`.`start_date`,`Exam`.`end_date`,`Exam`.`passing_percent` from `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) WHERE `Exam`.`status`='Closed' and `Exam`.`id`=".$id;
        $this->autoInsert->iFetch($sql,$examvalue);
        $examStats=array();
        $examStats['Exam']['id']=$examvalue['id'];
        $examStats['Exam']['name']=$examvalue['name'];
        $examStats['Exam']['start_date']=$examvalue['start_date'];
        $examStats['Exam']['end_date']=$examvalue['end_date'];
        $examStats['OverallResult']['passing']=(float) $examvalue['passing_percent'];
        $examStats['OverallResult']['average']=(float) $this->studentAverageResult($examvalue['id']);
        $examStats['StudentStat']['pass']=$this->studentStat($examvalue['id'],'Pass');
        $examStats['StudentStat']['fail']=$this->studentStat($examvalue['id'],'Fail');
        $examStats['StudentStat']['absent']=$this->examTotalAbsent($examvalue['id']);
        return$examStats;
    }
    public function examAttendance($id,$type)
    {
        $examStats=array();
        $examStats=$this->studentStat($id,$type,'all');
        return$examStats;
    }
    public function examAbsent($id)
    {
      $examStats=array();
      $examStats=$this->examTotalAbsent($id,'all');
      return$examStats;
    }
    public function totalMarks($id)
    {
        global $wpdb;

        $limit=0;
        $sql="SELECT `ExamMaxquestion`.`subject_id`,`ExamMaxquestion`.`max_question` from `".$wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` where `ExamMaxquestion`.`exam_id`=".$id;
        $this->autoInsert->iWhileFetch($sql,$examMaxQuestionArr);
        $totalMarks=0;
        if($examMaxQuestionArr)
        {
          foreach($examMaxQuestionArr as $value)
          {
            $quesNo=$value['max_question'];
            $subjectId=$value['subject_id'];
            if($quesNo==0)
            $limit=" ";
            else
            $limit=' LIMIT '.$quesNo;
            $sqlExamQuestion="select sum(`marks`) AS `total_marks` from (select `Question`.`marks` FROM `".$wpdb->prefix."emp_exam_questions` AS `ExamQuestion` Inner JOIN `".$wpdb->prefix."emp_questions` AS `Question` ON (`ExamQuestion`.`question_id`=`Question`.`id`) WHERE `ExamQuestion`.`exam_id`=".$id." AND `Question`.`subject_id`=".$subjectId.$limit.") AS `ExamQuestion`";
            $this->autoInsert->iFetch($sqlExamQuestion,$totalMarksArr);
            $totalMarks=$totalMarks+$totalMarksArr['total_marks'];
        }
    }
    else
    {
        $sql="SELECT SUM(`Question`.`marks`) AS `total_marks` from `".$wpdb->prefix."emp_exam_questions` AS `ExamQuestion` Inner JOIN `".$wpdb->prefix."emp_questions` AS `Question` ON (`Question`.`id`=`ExamQuestion`.`question_id`) where `ExamQuestion`.`exam_id`=".$id;
        $this->autoInsert->iFetch($sql,$totalMarksArr);
        $totalMarks=$totalMarksArr['total_marks'];
    }    
    return$totalMarks;
  }    
}
?>