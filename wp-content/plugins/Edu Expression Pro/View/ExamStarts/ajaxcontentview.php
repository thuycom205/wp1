<?php
global$quesNo;global$id;global$msg;
		if(!is_numeric($quesNo) || $quesNo<1  || !($id))
		{
			$redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','error',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
		}
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$id;
		$this->autoInsert->iFetch($sql,$post);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`end_time` IS NULL AND `ExamResult`.`student_id`=".get_current_user_id();
		$this->autoInsert->iFetchCount($sql,$currentExamResult);
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`student_id`=".get_current_user_id()." AND `ExamResult`.`end_time` IS NULL ";
		$this->autoInsert->iFetch($sql,$examWise);
        $userExamQuestion=$this->ExamStart->userExamQuestion($id,get_current_user_id(),$quesNo);
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$id." AND `ExamResult`.`end_time` IS NULL AND `ExamResult`.`student_id`=".get_current_user_id();
		$this->autoInsert->iFetch($sql,$examResult);
        $userSectionQuestion=$this->ExamStart->userSectionQuestion($id,$post['type'],get_current_user_id());
        if($post['type']=="Exam")
		{
			$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` WHERE `ExamQuestion`.`exam_id`=".$id;
			$this->autoInsert->iFetchCount($sql,$totalQuestion);
		}
        else
		{
			$totalQuestion=$this->ExamStart->totalPrepQuestions($id,get_current_user_id());
		}
        $nquesNo=$quesNo;
        $pquesNo=$quesNo;
        if($totalQuestion<$quesNo)
        $quesNo=1;
	    $currSubjectName=$this->ExamStart->userSubject($id,$quesNo,get_current_user_id());
        $this->ExamStart->userQuestionRead($id,$quesNo,get_current_user_id(),$this->ExamApp->currentDateTime());
        $oquesNo=$quesNo;
        if($totalQuestion==$quesNo)
        $quesNo=0;
        if($totalQuestion<$quesNo)
        $pquesNo=2;
        if($quesNo==1)
        $pquesNo=2;
		$nquesNo=$quesNo+1;
		$pquesNo=$pquesNo-1;
		$oquesNo=$oquesNo;
		$examId=$id;
        $totalQuestion=$totalQuestion;
		$examResultId=$userExamQuestion['exam_result_id'];
                ?>