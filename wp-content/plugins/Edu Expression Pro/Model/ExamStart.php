<?php
$dir = plugin_dir_path(__FILE__);
include($dir.'../Model/Result.php');
include($dir.'../Model/Exam.php');
class ExamStart extends ExamApps
{
    function __construct()
    {
	global $wpdb;
	$this->wpdb=$wpdb;
        $this->tableResult = $wpdb->prefix."emp_exam_results";
        $this->tableStat = $wpdb->prefix."emp_exam_stats";
        $this->ExamApp = new ExamApps();
        $this->configuration=$this->ExamApp->configuration();
	$this->autoInsert=new autoInsert();
    }
    public function checkPost($id,$studentId)
    {
	$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup`
        INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`ExamGroup`.`group_id`=`StudentGroup`.`group_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamGroup`.`exam_id`=`Exam`.`id`)
        WHERE `ExamGroup`.`exam_id`=".$id."  AND `StudentGroup`.`student_id`=".$studentId." AND `Exam`.`status`='Active'  AND `Exam`.`user_id`=0 ";
	$this->autoInsert->iFetch($sql,$checkPost);
	return($checkPost['count']);
    }    
    public function userQuestion($id,$ques_random,$type)
    {
        $totalMarks=0;
        if($type=="Exam")
        {
            if($ques_random==1)
            {
                $sql="SELECT `ExamQuestion`.`exam_id`,`ExamQuestion`.`question_id`,`Question`.`marks`,`Question`.`answer` AS `Question.answer`,`Question`.`true_false` AS `Question.true_false`,`Question`.`fill_blank` AS `Question.fill_blank`,`Qtype`.`type` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion`
                INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamQuestion`.`question_id`=`Question`.`id`)
                INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
                INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)
                WHERE `ExamQuestion`.`exam_id`=".$id." ORDER BY `Subject`.`subject_name` ASC, rand()";
                $this->autoInsert->iWhileFetch($sql,$userQuestion);
            }
            else
            {
                $sql="SELECT `ExamQuestion`.`exam_id`,`ExamQuestion`.`question_id`,`Question`.`marks`,`Question`.`answer` AS `Question.answer`,`Question`.`true_false` AS `Question.true_false`,`Question`.`fill_blank` AS `Question.fill_blank`,`Qtype`.`type` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion`
                INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamQuestion`.`question_id`=`Question`.`id`)
                INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
                INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)
                WHERE `ExamQuestion`.`exam_id`=".$id." ORDER BY `Subject`.`subject_name`,`Question`.`id` ASC";
                $this->autoInsert->iWhileFetch($sql,$userQuestion);        
            }
        }
        else
        {
            $ExamPrepArr=Result::getPrepSubject($id);
            if($ExamPrepArr)
            {
                foreach($ExamPrepArr as $value)
                {
                    $type=$value['type'];
                    $level=$value['level'];
                    $sql="SELECT `Question`.`id`,`Question`.`marks`,`Question`.`answer` AS `Question.answer`,`Question`.`true_false` AS `Question.true_false`,`Question`.`fill_blank` AS `Question.fill_blank`,`Qtype`.`type` FROM `".$this->wpdb->prefix."emp_questions` AS `Question`
                    INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
                    WHERE `Question`.`subject_id`=".$value['subject_id']."  AND  `Question`.`qtype_id` IN($type) AND `Question`.`diff_id` IN($level) ORDER BY rand() limit ".$value['ques_no'];
                    $this->autoInsert->iWhileFetch($sql,$userQuestion);
                    $userQuestionArr[]=$userQuestion;
                }               
            }
            unset($value);
            $totalMarks=0;
            $userQuestion=array();
            foreach($userQuestionArr as $value)
            {
                foreach($value as $value1)
                {
                    $totalMarks=$totalMarks+$value1['marks'];
                    $userQuestion[]=array('marks'=>$value1['marks'],'question_id'=>$value1['id'],'answer'=>$value1['Question.answer'],'true_false'=>$value1['Question.true_false'],'fill_blank'=>$value1['Question.fill_blank'],'type'=>$value1['type']);
                }
            }
        }
        return array($userQuestion,$totalMarks);
    }
    function shuffleAssoc($isShuffle)
    {
        $array=array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6');
        if($isShuffle==1)
        {
            $keys = array_keys($array);
            shuffle($keys);
            foreach($keys as $key)
            {
                $new[$key] = $array[$key];
            }
            $array = $new;
        }
        return $array;
    }
    public function getOptionsStat($isShuffle)
    {
        $option=$this->shuffleAssoc($isShuffle);
        return implode(",",$option);
    }
    public function totalQuestion($id)
    {
        $sql="SELECT `ExamMaxquestion`.`subject_id`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion`  WHERE `ExamMaxquestion`.`exam_id`=".$id;
        $this->autoInsert->iWhileFetch($sql,$examMaxQuestionArr);
        $totalQuestion=0;
        if($examMaxQuestionArr)
        {
            foreach($examMaxQuestionArr as $value)
            {
                $quesNo=$value['max_question'];
                $subjectId=$value['subject_id'];
                if($quesNo==0)
                    {
                    $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamQuestion`.`question_id`) WHERE `ExamQuestion`.`exam_id`=".$id." AND `Question`.`subject_id`=".$subjectId;
                    $this->autoInsert->iFetchCount($sql,$totalQuestionCount);
                }
                else
                {
                    $totalQuestionCount=$quesNo;
                }
                $totalQuestion=$totalQuestion+$totalQuestionCount;
            }
        }
        else
        {
            $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion`
            LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamQuestion`.`question_id`)
            WHERE `ExamQuestion`.`exam_id`=".$id;
            $this->autoInsert->iFetchCount($sql,$totalQuestion);
        }    
        return$totalQuestion;
    } 
    public function totalPrepQuestions($id,$studentId=null)
    {
        if($studentId==null)
        {
            $sql="SELECT SUM(`ExamPrep`.`ques_no`) AS `total_question` FROM `".$this->wpdb->prefix."emp_exam_preps` AS `ExamPrep`
            WHERE `ExamPrep`.`exam_id`=".$id;
            $this->autoInsert->iFetchCount($sql,$totalQuestionArr);
            $totalQuestion=$totalQuestionArr['total_question'];
            return$totalQuestion;
        }
        else
        {
            $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$id." AND `ExamResult`.`student_id`=".$studentId." AND `ExamResult`.`end_time` IS NULL";
            $this->autoInsert->iFetch($sql,$ExamResultArr);
            $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_id`=".$id." AND `ExamStat`.`exam_result_id`=".$ExamResultArr['id']." AND `ExamStat`.`student_id`=".$studentId;
            $this->autoInsert->iFetchCount($sql,$totalQuestion);
            return$totalQuestion;
        }
    }
    public function userSubject($exam_id,$quesNo,$studentId)
    {
        if($quesNo==0)
        $quesNo=1;
        $sql="select `Subject`.`subject_name` FROM  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`)
        INNER JOIN  `".$this->wpdb->prefix."emp_subjects` AS `Subject`  ON(`Question`.`subject_id`=`Subject`.`id`)
        WHERE  `ExamStat`.`exam_id`=".$exam_id."  AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`closed`=0 ORDER BY `ExamStat`.`ques_no` ASC ";
        $this->autoInsert->iFetch($sql,$subjectName);
        return$subjectName['subject_name'];
    }
    public function userQuestionRead($exam_id,$quesNo,$studentId,$currentDateTime)
    {
        if($quesNo==0)
        $quesNo=1;
        $usrQues=array('opened'=>1,'attempt_time'=>$currentDateTime);
        $this->autoInsert->iUpdateArray($this->tableStat,$usrQues,array('exam_id'=>$exam_id,' AND student_id'=>$studentId,' AND ques_no'=>$quesNo,' AND closed'=>0,' AND opened'=>0));
    }
    public function totalPrepAttemptQuestion($id)
    {
        $sql="SELECT `ExamMaxquestion`.`subject_id`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion`  WHERE `ExamMaxquestion`.`exam_id`=".$id;
        $this->autoInsert->iWhileFetch($sql,$examMaxQuestionArr);$totalQuestion=0;
        if($examMaxQuestionArr)
        {
            foreach($examMaxQuestionArr as $value)
            {
                $quesNo=$value['max_question'];
                $subjectId=$value['subject_id'];
                if($quesNo==0)
                {
                    $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_preps` AS `ExamPrep` WHERE `ExamPrep`.`exam_id`=".$id." AND `ExamPrep`.`subject_id`=".$subjectId;
                    $this->autoInsert->iFetch($sql,$totalQuestionCountArr);
                    $totalQuestionCount=$totalQuestionCountArr['ques_no'];
                }
                else
                $totalQuestionCount=$quesNo;        
                $totalQuestion=$totalQuestion+$totalQuestionCount;
            }
        }
        else
        {
            $totalQuestion=0;
        }    
        return$totalQuestion;
    }
    public function userExamQuestion($exam_id,$studentId,$quesNo=0)
    {
        $SQL="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        WHERE `ExamStat`.`exam_id`=".$exam_id." AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`closed`=0";
        $this->autoInsert->iFetchCount($SQL,$examQuestionCount);
        if($examQuestionCount==0)
        $quesNo=1;
        $SQL="SELECT `ExamStat`.`id` as `ExamStat.id`,`ExamStat`.`correct_answer`,`ExamStat`.`exam_result_id`,`ExamStat`.`answered`,`ExamStat`.`ques_no`,`ExamStat`.`option_selected`,`ExamStat`.`true_false` AS `ExamStat.true_false`,`ExamStat`.`fill_blank` AS `ExamStat.fill_blank`,
        `ExamStat`.`marks`,`ExamStat`.`review`,`ExamStat`.`options`,`Exam`.`negative_marking`,`ExamStat`.`answer` AS `ExamStat.answer`,`Subject`.`subject_name`,`Question`.`id`,`Question`.`question`,`Question`.`option1`,`Question`.`option1`,`Question`.`option2`,`Question`.`option3`,`Question`.`option4`,`Question`.`option5`,`Question`.`option6`,`Question`.`negative_marks`,
        `Question`.`answer` AS `Question.answer`,`Question`.`true_false` AS `Question.true_false`,`Question`.`fill_blank` AS `Question.fill_blank`,`Question`.`hint`,`Qtype`.`type` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamStat`.`question_id`=`Question`.`id`)
        INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Question`.`subject_id`=`Subject`.`id`)
        INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
        INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamStat`.`exam_id`)
        WHERE `ExamStat`.`exam_id`=".$exam_id." AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`closed`=0";
        $this->autoInsert->iFetch($SQL,$userExamQuestion);
        return $userExamQuestion;
   }
    public function userSectionQuestion($exam_id,$type,$studentId)
    {
        $subjectName='';
        if($type=="Exam"){
            $result = new Result();
            $subjectName=$result->getSubject($exam_id);
        }

        else {
            $result = new Result();

            $subjectName=$result->getPrepSubject($exam_id);
        }

        foreach($subjectName as $value)
        {
            $sql="select `ExamStat`.`ques_no`,`ExamStat`.`opened`,`ExamStat`.`answered`,`ExamStat`.`review` FROM  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
            INNER JOIN  `".$this->wpdb->prefix."emp_questions` AS `Question`  ON(`ExamStat`.`question_id`=`Question`.`id`)
            WHERE  `ExamStat`.`exam_id`=".$exam_id."  AND `ExamStat`.`student_id`=".$studentId." AND `Question`.`subject_id`=".$value['id']." AND `ExamStat`.`closed`=0 ORDER BY `ExamStat`.`ques_no` ASC ";
            $this->autoInsert->iWhileFetch($sql,$subjectDetail);
            $userSectionQuestion[$value['subject_name']]=$subjectDetail;
        }
        return $userSectionQuestion;
   }
    public function userExamInsert($id,$ques_random,$type,$optionShuffle,$studentId,$currentDateTime)
    {
        $userQuestionArr=$this->userQuestion($id,$ques_random,$type);
        $userQuestion=$userQuestionArr[0];
        if($type=="Exam")
        {
            $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion`
            WHERE `ExamQuestion`.`exam_id`=".$id;
            $this->autoInsert->iFetchCount($sql,$totalQuestion);
            $totalAttemptQuestion=$this->totalQuestion($id);
            $exam = new Exam();
            $totalMarks=$exam->totalMarks($id);


//            $totalMarks=Exam::totalMarks($id);
        }
        else
        {
            $totalQuestion=$this->totalPrepQuestions($id);
            $totalAttemptQuestion=$this->totalPrepAttemptQuestion($id);
            $totalMarks=$userQuestionArr[1];
        }
        $ExamResultArr=array("exam_id"=>$id,"student_id"=>$studentId,"start_time"=>$currentDateTime,"total_question"=>$totalQuestion,'total_attempt'=>$totalAttemptQuestion,"total_marks"=>$totalMarks);
        if($this->autoInsert->iInsert($this->tableResult,$ExamResultArr))
        {
            $lastId=$this->autoInsert->iLastID();
            if($type=="Exam")
            {
                foreach($userQuestion as $ques_no=>$examQuestionArr)
                {
                    $ques_no++;
                    if($examQuestionArr['type']=="M")
                    $correct_answer=$examQuestionArr['Question.answer'];
                    elseif($examQuestionArr['type']=="T")
                    $correct_answer=$examQuestionArr['Question.true_false'];
                    elseif($examQuestionArr['type']=="F")
                    $correct_answer=$examQuestionArr['Question.fill_blank'];
                    else
                    $correct_answer=null;
                    $options=$this->getOptionsStat($optionShuffle);
                    $recordArr[]=array("exam_result_id"=>$lastId,"exam_id"=>$examQuestionArr['exam_id'],"student_id"=>$studentId,"ques_no"=>$ques_no,
                                       "question_id"=>$examQuestionArr['question_id'],'marks'=>$examQuestionArr['marks'],"correct_answer"=>$correct_answer,'options'=>$options,'created'=>$currentDateTime,'modified'=>$currentDateTime);
                }
                foreach($recordArr as $value)
                {
                    $this->autoInsert->iInsert($this->tableStat,$value);
                }
            }
            else
            {
                foreach($userQuestion as $ques_no=>$examQuestionArr)
                {
                    $ques_no++;
                    if($examQuestionArr['type']=="M")
                    $correct_answer=$examQuestionArr['Question.answer'];
                    elseif($examQuestionArr['type']=="T")
                    $correct_answer=$examQuestionArr['Question.true_false'];
                    elseif($examQuestionArr['type']=="F")
                    $correct_answer=$examQuestionArr['Question.fill_blank'];
                    else
                    $correct_answer=null;
                    $options=$this->getOptionsStat($examQuestionArr['option_shuffle']);
                    $recordArr[]=array("exam_result_id"=>$lastId,"exam_id"=>$id,"student_id"=>$studentId,"ques_no"=>$ques_no,
                                       "question_id"=>$examQuestionArr['question_id'],'marks'=>$examQuestionArr['marks'],"correct_answer"=>$correct_answer,'options'=>$options);
                }
                foreach($recordArr as $value)
                {
                    $this->autoInsert->iInsert($this->tableStat,$value);
                }
            }
        }
    }
    public function userSaveAnswer($exam_id,$quesNo,$studentId,$currentDateTime,$valueArr)
    {
        $sql="SELECT `Question`.`subject_id` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamStat`.`question_id`)
        LEFT JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamStat`.`exam_id`)
        WHERE `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`exam_id`=".$exam_id;
        $this->autoInsert->iFetch($sql,$subjectArr);    
        $subjectId=$subjectArr['subject_id'];
        $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` WHERE `ExamMaxquestion`.`exam_id`=".$exam_id." AND `ExamMaxquestion`.`subject_id`=".$subjectId;
        $this->autoInsert->iFetch($sql,$maxQuestionArr);
        if($maxQuestionArr)
        $maxQuestion=$maxQuestionArr['max_question'];
        else
        $maxQuestion=0;
        $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        LEFT JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamStat`.`question_id`)
        WHERE `ExamStat`.`exam_id`=".$exam_id." AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`closed`=0  AND `ExamStat`.`answered`=1 AND `Question`.`subject_id`=".$subjectId;
        $this->autoInsert->iFetchCount($sql,$maxAnswer);
        if($maxAnswer>=$maxQuestion && $maxQuestion!=0)
        {
          return false;
        }
        else
        {
            $userExamQuestion=$this->userExamQuestion($exam_id,$studentId,$quesNo);
            $id=$userExamQuestion['ExamStat.id'];    
            $marksObtained=0;$isAnswer=false;$isAnswered=false;
            if(isset($valueArr['option_selected']))
            {
                if(is_array($valueArr['option_selected']))
                {
                  $usrQues['option_selected']=implode(",",$valueArr['option_selected']);
                }
                else
                {
                  $usrQues['option_selected']=$valueArr['option_selected'];
                }
                if($usrQues['option_selected']==$userExamQuestion['Question.answer'])
                $isAnswer=true;
                if($valueArr['option_selected']!=NULL)
                $isAnswered=true;
            }
            if(isset($valueArr['true_false']))
            {
                $usrQues['true_false']=$valueArr['true_false'];
                if(strtolower($usrQues['true_false'])==strtolower($userExamQuestion['Question.true_false']))
                $isAnswer=true;
                if($valueArr['true_false']!=NULL)
                $isAnswered=true;
            }
            if(isset($valueArr['fill_blank']))
            {
                $usrQues['fill_blank']=$valueArr['fill_blank'];
                if(str_replace(" ","",strtolower($usrQues['fill_blank']))==str_replace(" ","",strtolower($userExamQuestion['Question.fill_blank'])))
                $isAnswer=true;
                if($valueArr['fill_blank']!=NULL)
                $isAnswered=true;
            }
            if(isset($valueArr['answer']))
            {
              if($valueArr['answer']!=NULL)
              $isAnswered=true;
            }
            $usrQues['ques_status']=null;
            $marksObtained=null;
            if($isAnswered==true)
            {
                $usrQues['answered']='1';
                $usrQues['review']='0';
                if($isAnswer==true)
                {
                  $marksObtained=$userExamQuestion['marks'];
                  $usrQues['ques_status']='R'; 
                }
                else
                {
                  if($userExamQuestion['negative_marking']=="Yes" && !isset($valueArr['answer']))
                  {
                      if($userExamQuestion['negative_marks']==0)
                      $marksObtained=0;
                      else
                      $marksObtained=-($userExamQuestion['negative_marks']);
                      
                  }
                  if(!isset($valueArr['answer']))
                  $usrQues['ques_status']='W';
                }
            }
            $usrQues['marks_obtained']=$marksObtained;
            if(isset($valueArr['answer']))
            {
                $usrQues['answer']=$valueArr['answer'];        
            } 
            $usrQues['modified']=$currentDateTime;
            $this->autoInsert->iUpdateArray($this->tableStat,$usrQues,array('`id`'=>$id));          
            return true;
        }    
    }
    public function userExamFinish($exam_id,$studentId,$currentDateTime)
    {
        $sql="SELECT `ExamResult`.`id` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$exam_id." AND `ExamResult`.`student_id`=".$studentId." AND `ExamResult`.`end_time` IS NULL ";
        $this->autoInsert->iFetch($sql,$ExamResultRecord);
        $id=$ExamResultRecord['id'];
        $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`answered`=1 ";
        $this->autoInsert->iFetchCount($sql,$total_answered);
        $userResult=array('end_time'=>$currentDateTime,'total_answered'=>$total_answered);
        $this->autoInsert->iUpdateArray($this->tableResult,$userResult,array('`id`'=>$id));
        $this->autoInsert->iUpdateArray($this->tableStat,array('closed'=>1),array('`exam_result_id`'=>$id));
        $sql="SELECT `Exam`.`finish_result` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$exam_id;
        $this->autoInsert->iFetch($sql,$ExamRecord);
        $finish_result=$ExamRecord['finish_result'];
        if($finish_result==1)
        {
            $examResultId=$id;
            $sql="SELECT `ExamResult`.`total_marks`,`Exam`.`passing_percent` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
            LEFT JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamResult`.`exam_id`=`Exam`.`id`)
            WHERE `ExamResult`.`id`=".$examResultId;
            $this->autoInsert->iFetch($sql,$post);
            $obtainedMarks=$this->obtainedMarks($examResultId);
            $percent=number_format(($obtainedMarks*100)/$post['total_marks'],2);
            if($percent>=$post['passing_percent'])
            $result="Pass";
            else
            $result="Fail";
            $examResultArr=array('user_id'=>1,'finalized_time'=>$currentDateTime,'obtained_marks'=>$obtainedMarks,'percent'=>$percent,'result'=>$result);
            $this->autoInsert->iUpdateArray($this->tableResult,$examResultArr,array('`id`'=>$examResultId));
        }
    }
    public function userResetAnswer($exam_id,$quesNo,$studentId)
    {
        $sql="SELECT `ExamStat`.`id` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        WHERE `ExamStat`.`exam_id`=".$exam_id." AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`closed`=0";
        $this->autoInsert->iFetch($sql,$currRecord);     
        $id=$currRecord['id'];    
        $usrQues=array('attempt_time'=>null,'answered'=>0,'option_selected'=>null,'answer'=>null,'true_false'=>null,'fill_blank'=>null,'marks_obtained'=>null,'ques_status'=>null);
        $this->autoInsert->iUpdateArray($this->tableStat,$usrQues,array('`id`'=>$id));
    }
    public function userReviewAnswer($exam_id,$quesNo,$studentId,$review)
    {
        $sql="SELECT `ExamStat`.`id` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat`
        WHERE `ExamStat`.`exam_id`=".$exam_id." AND `ExamStat`.`student_id`=".$studentId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`closed`=0";
        $this->autoInsert->iFetch($sql,$currRecord);   
        $id=$currRecord['id'];
        $usrQues=array('review'=>$review);
        $this->autoInsert->iUpdateArray($this->tableStat,$usrQues,array('`id`'=>$id));
    }
    public function obtainedMarks($id=null)
    {
        $sql="SELECT SUM(marks_obtained) AS `total_marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE  `ExamStat`.`exam_result_id`=".$id;echo$sql;
        $this->autoInsert->iFetch($sql,$ExamStatArr);
        $obtainedMarks=$ExamStatArr['total_marks'];
        return$obtainedMarks;
    }
}
?>