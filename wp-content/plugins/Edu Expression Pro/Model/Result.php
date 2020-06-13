<?php
$dir = plugin_dir_path(__FILE__);
class Result extends ExamApps
{
    public function userSubjectScore($id,$subjectId,$quesStatus)
    {
        $sql="select SUM(`ExamStat`.`marks_obtained`) AS `total_marks` from `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamStat`.`question_id`=`Question`.`id`) where `ExamStat`.`exam_result_id`=".$id." AND `Question`.`subject_id`=".$subjectId." AND `ExamStat`.`ques_status`='".$quesStatus."'";
        $this->autoInsert->iFetch($sql,$userSubject);
        $userSubjectMarks=$userSubject['total_marks'];
        return$userSubjectMarks;
    }
    public function examOptions($serGroupWiseId)
    {
        $sql="select `Exam`.`id`,`Exam`.`name` from `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`) where `Exam`.`status`='Closed' And `ExamGroup`.`group_id` IN($serGroupWiseId) Order by `Exam`.`name` ASC ";
        $this->autoInsert->iWhileFetch($sql,$examOptions);
        return($examOptions);
    }
    public function difficultyWiseQuestion($id,$type)
    {
        $sql="SELECT COUNT(`ExamStat`.`id`) As `count` from `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamStat`.`question_id`=`Question`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON(`Question`.`diff_id`=`Diff`.`id`) where `ExamStat`.`exam_result_id`=".$id." And `Diff`.`type`='".$type."' AND `ExamStat`.`answered`= 1 ";
        $this->autoInsert->iFetchCount($sql,$quesCount);
        return(int)$quesCount;
    }
    public function studentDetail($id)
    {
        $sql="SELECT `User`.`ID` AS `id`,`User`.`display_name` AS `name` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`ExamResult`.`student_id`) WHERE `ExamResult`.`id`=".$id;
        $this->autoInsert->iFetch($sql,$studentDetail);
        return($studentDetail);
    }
    public function performanceCount($studentId,$month)
    {
        $conditions.=" AND `ExamResult`.`student_id`=$studentId  AND  MONTH(`ExamResult`.`start_time`)='$month' AND  `ExamResult`.`user_id` > 0";
        $sql="SELECT COUNT(`ExamResult`.`id`) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE 1=1 ".$conditions;
        $this->autoInsert->iFetchCount($sql,$examCount);
        $sql="select SUM(`ExamResult`.`percent`) AS `total` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE 1=1 ".$conditions;
        $this->autoInsert->iFetch($sql,$exampercent);
        $percent=$exampercent['total'];
        if($examCount>0)
        $averagePercent=number_format($percent/$examCount,2);
        else
        $averagePercent=0;
        return (float)$averagePercent;
    }
    public function studentWise($name=null,$studentGroup=null,$userGroupWiseId)
    {    
        $conditions=null;
        if($name!=null)
        $conditions.=" AND `User`.`display_name` LIKE '%$name%' ";
        if($studentGroup!=null)
        {
            $studentGroup=implode(",",$studentGroup);
            $conditions.="AND `StudentGroup`.`group_id` IN(".$studentGroup.")";
        }
        $sql="SELECT DISTINCT(`User`.`ID`),`User`.`display_name`,`User`.`user_email` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
        INNER JOIN  `".$this->wpdb->prefix."users` AS `User`  ON(`User`.`ID`=`ExamResult`.`student_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`User`.`ID`=`StudentGroup`.`student_id`) ".$userGroupWiseId.$conditions;
        $this->autoInsert->iWhileFetch($sql,$studentDetail);
        return$studentDetail;
    }
    public function examWise($name=null,$group=null,$userGroupWiseId,$status=null)
    {
        $conditions=null;
        if($name!=null)
        $conditions.=" AND `Exam`.`id`=$name";
        if(count($group)>0)
        {
            $examGroup=implode(",",$group);
            if($examGroup)
            $conditions.=" AND `ExamGroup`.`group_id` IN(".$examGroup.")";
        }
        if($status!=null)
        $conditions.=" AND `ExamResult`.`result`='$status'";
        $SQL="SELECT `User`.`display_name` AS `Student.name`,`User`.`user_email` AS `email`,`User`.*,`Exam`.`name` AS `Exam.name`,`Exam`.*,`ExamResult`.* from  `".$this->wpdb->prefix."emp_exams` AS `Exam`
        INNER JOIN `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
        INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`ExamResult`.`student_id`)
        ".$userGroupWiseId." AND `ExamResult`.`user_id` > 0 ".$conditions."
        Group BY `ExamResult`.`id`
        ORDER BY `ExamResult`.`percent` desc";
        $this->autoInsert->iWhileFetch($SQL,$examDetail);
        return$examDetail;
    }
    public function studentExamWise($name=null,$examGroup=null,$userGroupWiseId)
    {    
        $conditions=null;
        if($name!=null)
        $conditions.=" AND `User`.`ID`=$name";
        if($examGroup!=null)
        {
            $examGroup=implode(",",$examGroup);
            $conditions.=" AND `ExamGroup`.`group_id` IN(".$examGroup.")";
        }
        $sql="SELECT `User`.`display_name` AS `Student.name`,`User`.`user_email` AS `email`,`Student`.*,`User`.*,`Exam`.`name` AS `Exam.name`,`Exam`.*,`ExamResult`.* from  `".$this->wpdb->prefix."emp_exams` AS `Exam`
        INNER JOIN  `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
        INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`ExamResult`.`student_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_Students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`) WHERE
        `ExamResult`.`user_id` > 0 ".$conditions."  Group BY `ExamResult`.`id` ORDER BY `ExamResult`.`percent` desc";
        $this->autoInsert->iWhileFetch($sql,$examDetail);
        return($examDetail);
    }
    public function userSubject($id)
    {
        $sql="select DISTINCT(`Subject`.`id`),`Subject`.`subject_name` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)   where  `ExamStat`.`exam_result_id`=".$id;
        $this->autoInsert->iWhileFetch($sql,$userSubject);
        return($userSubject);
    }
    public function userSubjectMarks($id,$subjectId,$sumField)
    {
        $sql="select sum(`ExamStat`.$sumField) AS `total_marks` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)   where  `ExamStat`.`exam_result_id`=".$id." AND `Subject`.`id`=".$subjectId;
        $this->autoInsert->iFetch($sql,$userSubject);
        $userSubjectMarks=$userSubject['total_marks'];
        return($userSubjectMarks);
    }
    public function userSubjectQuestion($id,$subjectId)
    {
        $sql="select count(`ExamStat`.`id`) AS `count` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)   where  `ExamStat`.`exam_result_id`=".$id." AND `Subject`.`id`=".$subjectId;
        $this->autoInsert->iFetch($sql,$userSubjectQuestion);
        return$userSubjectQuestion['count'];
    }
    public function userMarks($id)
    {
        $sql="select sum(`ExamStat`.`marks`) AS `total_marks` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`   where  `ExamStat`.`exam_result_id`=".$id;
        $this->autoInsert->iFetch($sql,$userSubject);
        $userSubjectMarks=$userSubject['total_marks'];
        return$userSubjectMarks;
    }
    public function userSubjectTime($id,$subjectId)
    {
        $sql="select SUM(TIMESTAMPDIFF(SECOND,`ExamStat`.`attempt_time`,`ExamStat`.`modified`)) AS `time_taken` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`) INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)   where  `ExamStat`.`exam_result_id`=".$id." AND `Subject`.`id`=".$subjectId." AND `ExamStat`.`answered`=1";
        $this->autoInsert->iFetch($sql,$userSubject);
        $userSubjectTime=$userSubject['time_taken'];
        return$userSubjectTime;
    }
    public function userSectionQuestion($id,$exam_id,$type,$studentId)
    {
        if($type=="Exam")
        $subjectName=$this->getSubject($exam_id);
        else
        $subjectName=$this->getPrepSubject($exam_id);
        foreach($subjectName as $value)
        {
            $sql="select `ExamStat`.`ques_no`,`ExamStat`.`opened`,`ExamStat`.`answered`,`ExamStat`.`review`,`ExamStat`.`bookmark` from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`)  where  `ExamStat`.`exam_result_id`=".$id."  AND `ExamStat`.`student_id`=".$studentId." AND `Question`.`subject_id`=".$value['id']." AND `ExamStat`.`closed`=1 ORDER BY `ExamStat`.`ques_no` ASC ";
            $this->autoInsert->iWhileFetch($sql,$subjectDetail);
            $userSectionQuestion[$value['subject_name']]=$subjectDetail;
        }
      return $userSectionQuestion;
    }
    public function getSubject($id)
    {      
        $sql="select DISTINCT(`Subject`.`id`),`Subject`.`subject_name` from  `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion`  INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`Question`.`id`=`ExamQuestion`.`question_id`)   INNER JOIN  `".$this->wpdb->prefix."emp_subjects` AS `Subject`  ON(`Subject`.`id`=`Question`.`subject_id`)   where  `ExamQuestion`.`exam_id`=".$id." ORDER BY `Subject`.`subject_name` ASC ";
        $this->autoInsert->iWhileFetch($sql,$subjectDetail);
        return$subjectDetail;
    }
    public function getPrepSubject($id)
    {
        $sql="select `Subject`.`id`,`Subject`.`subject_name`,`ExamPrep`.`subject_id`,`ExamPrep`.`ques_no`,`ExamPrep`.`type`,`ExamPrep`.`level` from  `".$this->wpdb->prefix."emp_exam_preps` AS `ExamPrep`  INNER JOIN  `".$this->wpdb->prefix."emp_subjects` AS `Subject`  ON(`Subject`.`id`=`ExamPrep`.`subject_id`)  where  `ExamPrep`.`exam_id`=".$id." ORDER BY `Subject`.`subject_name` ASC ";
        $this->autoInsert->iWhileFetch($sql,$subjectDetail);
        return$subjectDetail;
    }
    public function userSubjectStatusQuestion($id,$subjectId,$quesStatus)
    {      
        $sql="select Count(*) AS `count` from `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamStat`.`question_id`=`Question`.`id`) where `ExamStat`.`exam_result_id`=".$id." AND `Question`.`subject_id`=".$subjectId." AND `ExamStat`.`ques_status`='".$quesStatus."'";
        $this->autoInsert->iFetch($sql,$userSubject);
        $userSubjectQuestion=$userSubject['count'];
        return$userSubjectQuestion;
    }
    public function userSubjectUnattemptedMarks($id,$subjectId,$leftQuestion)
    {      
        $sql="SELECT (SELECT SUM(`marks`) FROM (SELECT `Question`.`marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` Inner JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`ExamStat`.`question_id`=`Question`.`id`) WHERE `ExamStat`.`exam_result_id`=".$id.' AND `Question`.`subject_id`='.$subjectId." LIMIT ".$leftQuestion.") subquery)  AS `left_marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE 1=1 LIMIT 1";
        $this->autoInsert->iFetch($sql,$leftQuestionArr);
        $unattemptedMarks=$leftQuestionArr['left_marks'];
        if($unattemptedMarks==NULL)
        $unattemptedMarks="0";
        return$unattemptedMarks;
    }
    public function userMarksheet($id)
    {
        $userSubject=$this->userSubject($id);
        $userMarksheet=array();
        $grandTotalMarks=0;$grandObtainedMarks=0;$grandTotalQuestion=0;$grandTimeTaken=0;$totalCorrectQuestion=0;$totalIncorrectQuestion=0;$totalMarksScored=0;
        $totalNegativeMarks=0;$totalUnattemptedQuestion=0;$totalUnattemptedQuestionMarks=0;
        foreach($userSubject as $k=>$subjectValue)
        {
            $totalMarks=$this->userSubjectMarks($id,$subjectValue['id'],'marks',$examId);
            $obtainedMarks=$this->userSubjectMarks($id,$subjectValue['id'],'marks_obtained',$examId);
            $totalQuestion=$this->userSubjectQuestion($id,$subjectValue['id'],$examId);
            $allMarks=$this->userMarks($id);
            $marksScored=$this->userSubjectScore($id,$subjectValue['id'],'R');
            if($marksScored=="")
            $marksScored=0;
            $negativeMarks=str_replace("-","",$this->userSubjectScore($id,$subjectValue['id'],'W'));
            if($negativeMarks=="")
            $negativeMarks=0;
            $correctQuestion=$this->userSubjectStatusQuestion($id,$subjectValue['id'],'R');
            $incorrectQuestion=$this->userSubjectStatusQuestion($id,$subjectValue['id'],'W');
            $unattemptedQuestion=$totalQuestion-($correctQuestion+$incorrectQuestion);
            $unattemptedQuestionMarks=$this->userSubjectUnattemptedMarks($id,$subjectValue['id'],$unattemptedQuestion);
            if($unattemptedQuestionMarks=="")
            $unattemptedQuestionMarks=0;
            $timeTaken=$this->userSubjectTime($id,$subjectValue['id']);
            $marksWeightage=number_format(($totalMarks*100)/$allMarks,2);
            $grandTotalMarks=number_format($grandTotalMarks+$totalMarks,2);
            $grandObtainedMarks=number_format($grandObtainedMarks+$obtainedMarks,2);
            $grandTotalQuestion=$grandTotalQuestion+$totalQuestion;
            $grandTimeTaken=$grandTimeTaken+$timeTaken;
            $percent=number_format(($obtainedMarks*100)/$totalMarks,2);
            
            $userMarksheet[$k]['Subject']['name']=$subjectValue['subject_name'];
            $userMarksheet[$k]['Subject']['total_marks']=$totalMarks;
            $userMarksheet[$k]['Subject']['obtained_marks']=$obtainedMarks;
            $userMarksheet[$k]['Subject']['marks_scored']=$marksScored;
            $userMarksheet[$k]['Subject']['negative_marks']=$negativeMarks;
            $userMarksheet[$k]['Subject']['percent']=$percent;
            $userMarksheet[$k]['Subject']['total_question']=$totalQuestion;
            $userMarksheet[$k]['Subject']['marks_weightage']=$marksWeightage;
            $userMarksheet[$k]['Subject']['time_taken']=$timeTaken;
            $userMarksheet[$k]['Subject']['correct_question']=$correctQuestion;
            $userMarksheet[$k]['Subject']['incorrect_question']=$incorrectQuestion;        
            $userMarksheet[$k]['Subject']['unattempted_question']=$unattemptedQuestion;
            $userMarksheet[$k]['Subject']['unattempted_question_marks']=$unattemptedQuestionMarks;
            $totalUnattemptedQuestionMarks=$totalUnattemptedQuestionMarks+$unattemptedQuestionMarks;
            $totalCorrectQuestion=$totalCorrectQuestion+$correctQuestion;
            $totalIncorrectQuestion=$totalIncorrectQuestion+$incorrectQuestion;
            $totalMarksScored=$totalMarksScored+$marksScored;
            $totalNegativeMarks=$totalNegativeMarks+$negativeMarks;
            $totalUnattemptedQuestion=$totalUnattemptedQuestion+$unattemptedQuestion;
            
        }
        if($grandTotalMarks==0)
        $grandPercent=0;
        else
        $grandPercent=$percent=number_format(($grandObtainedMarks*100)/$grandTotalMarks,2);
        $userMarksheet['total']['Subject']=array('name'=>'Grand Total','total_marks'=>$grandTotalMarks,'obtained_marks'=>$grandObtainedMarks,
                                                 'percent'=>$grandPercent,'total_question'=>$grandTotalQuestion,'marks_weightage'=>100,'time_taken'=>$grandTimeTaken,
                                                 'correct_question'=>$totalCorrectQuestion,'incorrect_question'=>$totalIncorrectQuestion,'marks_scored'=>$totalMarksScored,
                                                 'negative_marks'=>$totalNegativeMarks,'unattempted_question'=>$totalUnattemptedQuestion,'unattempted_question_marks'=>$totalUnattemptedQuestionMarks);
        return$userMarksheet;
    }  
}
?>