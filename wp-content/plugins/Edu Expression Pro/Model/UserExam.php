<?php
$dir = plugin_dir_path(__FILE__);
include($dir.'../Model/Result.php');
include($dir.'../Model/Exam.php');
class UserExam extends ExamApps
{
    function __construct()
    {
	global $wpdb;
	$this->wpdb=$wpdb;
	$this->tableExamResults = $wpdb->prefix."emp_exam_results";
	$this->tableExam=$wpdb->prefix."emp_exams";
	$this->examQuestion=$wpdb->prefix."emp_exam_questions";
	$this->studentGroup=$wpdb->prefix."emp_student_groups";
	$this->examGroup=$wpdb->prefix."emp_exam_groups";
	$this->question=$wpdb->prefix."emp_questions";
	$this->examPreps=$wpdb->prefix."emp_exam_preps";
	$this->examOrder=$wpdb->prefix."emp_exam_orders";
	$this->examResult=$wpdb->prefix."emp_exam_results";
	$this->ExamApp = new ExamApps();
	$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_UserExam';
	$this->url=admin_url('admin.php').'?page=examapp_UserExam';
	$this->configuration=$this->ExamApp->configuration();
	$this->autoInsert=new autoInsert();	    
    }
    public function getUserExam($type,$studentId,$currentDateTime,$limit=0)
    {
	$start_date=null;$end_date=null;$examLimit=null;
	if($type=="today")
	{
	    $start_date=" AND `Exam`.`start_date` <='".$currentDateTime."'";
	    $end_date=" AND `Exam`.`end_date` >'".$currentDateTime."'";
	}
	if($type=="upcoming")
	{
	    $start_date=" AND `Exam`.`start_date` >'".$currentDateTime."'";
	}    
	if($limit>0)
	$examLimit=" LIMIT ".$limit;
	$SQL="SELECT (SELECT COUNT(ExamResult.id) FROM `".$this->examResult."` AS `ExamResult` WHERE `ExamResult`.`exam_id`=`Exam`.`id` AND `ExamResult`.`student_id`=".$studentId.") AS `attempt`,
	(SELECT COUNT(ExamOrder.id) FROM ".$this->examOrder." AS `ExamOrder` WHERE `ExamOrder`.`exam_id`=`Exam`.`id` AND `ExamOrder`.`student_id`=".$studentId.") AS `attempt_order`,
	`Exam`.`id`, `Exam`.`type`, `Exam`.`name`, `Exam`.`start_date`, `Exam`.`end_date`, `Exam`.`paid_exam`, `Exam`.`amount`, `Exam`.`attempt_count`, `Exam`.`expiry`, `ExamOrder`.`expiry_date`
	FROM `".$this->tableExam."` AS `Exam`
	LEFT JOIN ".$this->examQuestion." AS `ExamQuestion` ON (`Exam`.`id`=`ExamQuestion`.`Exam_id`)
	Inner JOIN ".$this->examGroup." AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`)
	Inner JOIN ".$this->studentGroup." AS `StudentGroup` ON (`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)
	LEFT JOIN ".$this->question." AS `Question` ON (`ExamQuestion`.`question_id`=`Question`.`id`)
	LEFT JOIN ".$this->examPreps." AS `ExamPrep` ON (`Exam`.`id`=`ExamPrep`.`exam_id`)
	LEFT JOIN ".$this->examOrder." AS `ExamOrder` ON (`Exam`.`id`=`ExamOrder`.`exam_id` AND `StudentGroup`.`student_id`=`ExamOrder`.`student_id`)
	WHERE 1=1 ".$start_date.$end_date." AND `StudentGroup`.`student_id`=".$studentId." AND `Exam`.`status` = 'Active' AND `Exam`.`user_id` = 0
	GROUP BY `Exam`.`id`
	ORDER BY `Exam`.`start_date` asc ".$examLimit;
	$this->autoInsert->iWhileFetch($SQL,$examList);
	return$examList;
    }
    public function getPurchasedExam($type,$studentId,$currentDateTime,$limit=0)
    {
	$start_date=null;$end_date=null;$examLimit=null;$expiredDate=null;
	if($type=="today")
	{
		$start_date=" AND `Exam`.`start_date` <='".$currentDateTime."'";
		$end_date=" AND `Exam`.`end_date` >'".$currentDateTime."'";
	}
	if($type=="upcoming")
	{
		$start_date=" AND `Exam`.`start_date` >'".$currentDateTime."'";
	}    
	if($type=="expired")
	{
		$start_date=" AND `Exam`.`start_date` <='".$currentDateTime."'";
		$end_date=" AND `Exam`.`end_date` >'".$currentDateTime."'";
		$expiredDate="HAVING Max(`ExamOrder`.`expiry_date`) <'".$currentDateTime."'";
	}
	if($limit>0)
	$examLimit=$limit;
	$SQL="SELECT (SELECT COUNT(ExamResult.id) FROM `".$this->examResult."` AS `ExamResult` WHERE `ExamResult`.`exam_id`=`Exam`.`id` AND `ExamResult`.`student_id`=".$studentId.") AS `attempt`,
	(SELECT COUNT(ExamOrder.id) FROM ".$this->examOrder." AS `ExamOrder` WHERE `ExamOrder`.`exam_id`=`Exam`.`id` AND `ExamOrder`.`student_id`=".$studentId.") AS `attempt_order`,
	(SELECT MAX(ExamOrder.expiry_date) FROM ".$this->examOrder." AS `ExamOrder` WHERE `ExamOrder`.`exam_id`=`Exam`.`id` AND `ExamOrder`.`student_id`=".$studentId.") AS `fexpiry_date`,
	`Exam`.`id`, `Exam`.`type`, `Exam`.`name`, `Exam`.`start_date`, `Exam`.`end_date`, `Exam`.`paid_exam`, `Exam`.`amount`, `Exam`.`attempt_count`, `Exam`.`expiry`, `ExamOrder`.`expiry_date`
	FROM `".$this->tableExam."` AS `Exam`
	LEFT JOIN ".$this->examQuestion." AS `ExamQuestion` ON (`Exam`.`id`=`ExamQuestion`.`Exam_id`)
	INNER JOIN ".$this->examGroup." AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`)
	INNER JOIN ".$this->studentGroup." AS `StudentGroup` ON (`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)
	LEFT JOIN ".$this->question." AS `Question` ON (`ExamQuestion`.`question_id`=`Question`.`id`)
	LEFT JOIN ".$this->examPreps." AS `ExamPrep` ON (`Exam`.`id`=`ExamPrep`.`exam_id`)
	LEFT JOIN ".$this->examOrder." AS `ExamOrder` ON (`Exam`.`id`=`ExamOrder`.`exam_id` AND `StudentGroup`.`student_id`=`ExamOrder`.`student_id`)
	WHERE 1=1 ".$start_date.$end_date." AND `StudentGroup`.`student_id`=".$studentId." AND `Exam`.`status` = 'Active' AND `Exam`.`user_id` = 0 AND `ExamOrder`.`student_id`=".$studentId."
	GROUP BY `Exam`.`id` ".$expiredDate."
	ORDER BY `Exam`.`start_date` asc ".$examLimit;
	$this->autoInsert->iWhileFetch($SQL,$examList);
	return ($examList);
    }
    public function showExamList($showType,$exam)
    {
	$currency=$this->ExamApp->getCurrency();
	$frontExamPaid=$this->configuration['paid_exam'];$examExpiry=$this->configuration['exam_expiry'];
	$examList="";$attempt="";$serialNo=0;$ppendingAttempt="";$amountHeading=null;$amount=null;$expireHeading=null;$expireValue=null;
        if($showType=='today' || $showType=='purchased'){$ppendingAttempt="<th>".__('Attempts')."<br>".__('Remaining')."</th>";}
        if($frontExamPaid==true)$amountHeading="<th>".__('Amount')."</th>";
        if($showType=='today')$dateHeading="<th>".__('End Date')."</th>";
        elseif($showType=='purchased')$dateHeading="<th>".__('Expiry Date')."</th>";
        elseif($showType=='upcoming')$dateHeading="<th>".__('Start Date')."</th>";
        elseif($showType=='expired')$dateHeading="<th>".__('Expired Date')."</th>";
        else$dateHeading="<th>Date</th>";
        if(($showType=='today'|| $showType=='upcoming') && $examExpiry)$expireHeading="<th>".__('Expiry')."</th>";
        $examList="<tr>
        <th>".__('#')."</th>
        <th>".__('Name')."</th>
        <th>".__('Type')."</th>
        $dateHeading
        $expireHeading
        $ppendingAttempt
        $amountHeading
        <th>".__('Action')."</th>
        </tr>";
        foreach($exam as $post)
        {
            $serialNo++;
            $id=$post['id'];
	    $viewUrl=$this->ajaxUrl."&info=view&showType=".$showType."&id=".$id;
            if($frontExamPaid==true){if($post['paid_exam']=="1"){$amount="<td>".$currency.$post['amount']."</td>";}else{$amount="<td>".__('Free')."</td>";}}
            if($post['attempt_count']==0){$pendingAttempt=__('Unlimited');}else{ if($post['paid_exam']==1 && !$post['expiry_date']){$pendingAttempt=__('Not Purchased');}else{if($post['paid_exam']==1)$pendingAttempt=($post['attempt_order']*$post['attempt_count']-$post['attempt']);else$pendingAttempt=$post['attempt_count']-$post['attempt'];}}
            if($showType=='today' || $showType=='purchased'){$attempt='<a href="'.$this->ajaxUrl.'&info=guidelines&id='.$id.'" data-toggle="tooltip" title="'.__('Attempt Now').'" class="btn btn-success" target="_blank" class="btn btn-info"><span class="fa fa-sign-in"></span>&nbsp;</a>';
            $ppendingAttempt="<td>". $pendingAttempt."</td>";}
            if($showType=='today')$dateHeading=$dateValue=$this->ExamApp->dateTimeFormat($post['end_date']);
            elseif($showType=='purchased'){if($post['expiry']==0)$dateValue="Unlimited";else$dateValue=$this->ExamApp->dateFormat($post['fexpiry_date']);}
            elseif($showType=='upcoming')$dateValue=$this->ExamApp->dateFormat($post['start_date']);
            elseif($showType=='expired')$dateValue=$this->ExamApp->dateFormat($post['fexpiry_date']);
            else$dateHeading=$dateValue=$this->ExamApp->dateFormat($post['start_date']);
            if($showType!='expired' && $showType!='purchased' && $examExpiry){if($post['expiry']==0)$expireValue='<td>'.__('Unlimited').'</td>';else$expireValue='<td>'.$post['expiry'].' '.__('Days').'</td>';}
            $examList.="
                        <td>". $serialNo."</td>
                        <td>". $this->ExamApp->h($post['name'])."</td>
                        <td>". __($post['type'])."</td>
                        <td>".$dateValue."</td>".
                        $expireValue.
                        $ppendingAttempt.
                        $amount."
                        <td>".' '.'<a href="javascript:void(0);" data-toggle="tooltip" title="'.__('View Details').'" onclick="show_modal(\''.$viewUrl.'\');" class="btn btn-info"><span class="fa fa-arrows-alt"></span></a> '.
                        $attempt."</td>
                    </tr>";
        }
        unset($post);unset($id);
        return($examList);
    }
    public function checkPost($id,$studentId)
    {
	$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`ExamGroup`.`group_id`=`StudentGroup`.`group_id`)  INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamGroup`.`exam_id`=`Exam`.`id`)  WHERE `ExamGroup`.`exam_id`=".$id."  AND `StudentGroup`.`student_id`=".$studentId." AND `Exam`.`status`='Active'  AND `Exam`.`user_id`=0 ";
	$this->autoInsert->iFetch($sql,$checkPost);
	return($checkPost['count']);
    }
    public function subjectWiseQuestion($examId,$subjectId,$type='Prep')
    {
	if($type=="Exam")
	{
	  $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`ExamQuestion`.`question_id`=`Question`.`id`) WHERE `ExamQuestion`.`exam_Id`=".$examId."  AND `Question`.`subject_id`=".$subjectId;
	  $this->autoInsert->iFetchCount($sql,$totalQuestion);
	  $sql="select `ExamMaxquestion`.`subject_id`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion`  WHERE `ExamMaxquestion`.`exam_id`=".$examId." AND `ExamMaxquestion`.`subject_id`=".$subjectId;
	  $this->autoInsert->iFetch($sql,$examMaxQuestionArr);
	  if($examMaxQuestionArr && $examMaxQuestionArr['max_question']!=0)
	  $totalAttemptQuestion=$examMaxQuestionArr['max_question'];
	  else
	  $totalAttemptQuestion=$totalQuestion;
	}
	else
	{
	  $sql="select * FROM `".$this->wpdb->prefix."emp_exam_preps` AS `ExamPrep`  WHERE `ExamPrep`.`exam_id`=".$examId." AND `ExamPrep`.`subject_id`=".$subjectId;
	  $this->autoInsert->iFetch($sql,$ExamPrepArr);
	  $totalQuestion=$ExamPrepArr['ques_no'];
	  $sql="select `ExamMaxquestion`.`subject_id`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion`  WHERE `ExamMaxquestion`.`exam_id`=".$examId." AND `ExamMaxquestion`.`subject_id`=".$subjectId;
	  $this->autoInsert->iFetch($sql,$examMaxQuestionArr);
	  if($examMaxQuestionArr && $examMaxQuestionArr['max_question']!=0)
	  $totalAttemptQuestion=$examMaxQuestionArr['max_question'];
	  else
	  $totalAttemptQuestion=$totalQuestion;
	}
	$questionArr=array('total_question'=>$totalQuestion,'total_attempt_question'=>$totalAttemptQuestion);
	return$questionArr;
    }	
}
?>