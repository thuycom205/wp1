<?php
include_once('ExamApps.php');
include_once('Model/ExamStart.php');
class ExamStarts extends ExamStart
{
	public function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_exam_results";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->ExamStart = new ExamStart();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_ExamStart';
		$this->url=admin_url('admin.php').'?page=examapp_ExamStart';
		$this->urlUserExam=admin_url('admin.php').'?page=examapp_UserExam';
		$this->studentId=$this->ExamApp->getCurrentUserId();
		$id=$_REQUEST['id'];
		if(!$id)
		{
			$this->error();
		    die();
		}
		$checkPost=$this->ExamStart->checkPost($id,$this->studentId);
		if($checkPost==0)
		{
		    $this->error();
		    die();
		}
	}
	public function servertimes()
	{
		$serverDateTime=$this->ExamApp->getStringDateFormat('M j, Y H:i:s');
		include("View/ExamStarts/servertimes.php");
	}
	public function index()
	{
		$id=$_REQUEST['id'];
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$id;
		$this->autoInsert->iFetch($sql,$post);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`end_time` IS NULL AND `ExamResult`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetchCount($sql,$currentExamResult);
		if($currentExamResult==0)
		{
		    $paidexam=$post['paid_exam'];
		    $sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$id." AND `ExamResult`.`student_id`=".$this->studentId;
		    $this->autoInsert->iFetchCount($sql,$totalExam);
		    $attempt_count=$post['attempt_count'];
		    if($paidexam==1)
		    {
				if(!$this->checkPaidStatus($id,$this->studentId))
                {
					$redirectUrl=$this->ajaxUrl;
					$redirectUrl=add_query_arg('info','paid',$redirectUrl);
					$redirectUrl=add_query_arg('id',$id,$redirectUrl);
					$redirectUrl=add_query_arg('P','P',$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
				}                
		    }
		    else
		    {
				if($attempt_count<=$totalExam && $attempt_count>0)
				{	
					$redirectUrl=$this->urlUserExam;
					$redirectUrl=add_query_arg('info','error',$redirectUrl);
					$redirectUrl=add_query_arg('msg','maximumexam',$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
				}
		    }
		    $this->ExamStart->userExamInsert($id,$post['ques_random'],$post['type'],$post['option_shuffle'],$this->studentId,$this->ExamApp->currentDateTime());
			$redirectUrl=$this->ajaxUrl;
			$redirectUrl=add_query_arg('info','start',$redirectUrl);
			$redirectUrl=add_query_arg('id',$id,$redirectUrl);
			$redirectUrl=add_query_arg('ques','1',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}		
		if($currentExamResult==1)
        {
			$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`student_id`=".$this->studentId." AND `ExamResult`.`end_time` IS NULL ";
			$this->autoInsert->iFetch($sql,$examWise);
            $examWiseId=$examWise['exam_id'];
            $endTime=$this->ExamApp->currentDateTime(strtotime($examWise['start_time'])+($post['duration']*60));
			if($this->ExamApp->currentDateTime()>=$endTime && $post['duration']>0)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','finish',$redirectUrl);
				$redirectUrl=add_query_arg('id',$examWiseId,$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
            if($examWiseId!=$id)
			{
				$sql="SELECT `ExamStat`.`ques_no` FROM `".$this->wpdb->prefix."emp_exam_stats' AS `ExamStat` WHERE `exam_result_id`=".$examWise['id']." AND `attempt_time` IS NULL";
				$this->autoInsert->iFetch($sql,$examStat);
				if($examStat && $examStat['ques_no']!=1)
                $quesNo=$examStat['ques_no']-1;
                else
                $quesNo=1;
            }
            else
            $quesNo=1;
			$redirectUrl=$this->ajaxUrl;
			$redirectUrl=add_query_arg('info','start',$redirectUrl);
			$redirectUrl=add_query_arg('id',$id,$redirectUrl);
			$redirectUrl=add_query_arg('ques',$quesNo,$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
	}
	public function start()
	{
		$id=$_REQUEST['id'];
		$sql="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$id." AND `ExamResult`.`student_id`=".$this->studentId." AND `ExamResult`.`end_time` IS NULL";
        $this->autoInsert->iFetchCount($sql,$examCount);
		if($examCount==0)
		{
			$this->error();
		    die();
		}
		include('View/Layouts/exam_header.php');
		global$quesNo;global$id;
		$id=$_REQUEST['id'];
		$quesNo=$_REQUEST['ques'];
		$ajaxView="No";
		$this->ajaxcontentview($ajaxView);
		include('View/Layouts/exam_footer.php');
		die();
	}
	public function ajaxcontentview($preAjaxView=null)
	{
		global$quesNo;global$id;global$msg;global$ajaxView;
		if(isset($_REQUEST['id']))
		$id=$_REQUEST['id'];
		if(isset($_REQUEST['ques']))
		$quesNo=$_REQUEST['ques'];
		if($preAjaxView!=null)
		$ajaxView=$preAjaxView;
		else
		$ajaxView="Yes";
		$mathEditor=$this->configuration['math_editor'];
		include("View/ExamStarts/ajaxcontentview.php");
		include("View/ExamStarts/start.php");
	}
	public function error()
	{
		echo "<h1 style=\"color:#ff0000;\">".__('Please Closed Tab')."</h1>";
	}
	public function save()
    {
		global$quesNo;global$id;global$msg;
		$id=$_REQUEST['id'];
		$quesNo=$_REQUEST['ques'];
        $dataArr=$_REQUEST;
        if($this->ExamStart->userSaveAnswer($id,$quesNo,$this->studentId,$this->ExamApp->currentDateTime(),$dataArr))
        {
            if($_REQUEST['saveNext']=="Yes")
            $quesNo++;
        }
        else
        {
            $msg=$this->ExamApp->showMessage('You have attempted maximum number of questions in this subject','danger');
        }
		$_REQUEST=array();
		$this->ajaxcontentview();
    }
    public function resetAnswer()
    {
		global$quesNo;global$id;
		$id=$_REQUEST['id'];
		$quesNo=$_REQUEST['ques'];
        $this->ExamStart->userResetAnswer($id,$quesNo,$this->studentId);
		$_REQUEST=array();
        $this->ajaxcontentview();
    }
    public function reviewAnswer()
    {
		global$quesNo;global$id;
		$id=$_REQUEST['id'];
		$quesNo=$_REQUEST['ques'];
        $this->ExamStart->userReviewAnswer($id,$quesNo,$this->studentId,1);
        $quesNo++;
		$_REQUEST=array();
        $this->ajaxcontentview();
    }
    public function unreviewAnswer()
    {
		global$quesNo;global$id;
		$id=$_REQUEST['id'];
		$quesNo=$_REQUEST['ques'];
        $this->ExamStart->userReviewAnswer($id,$quesNo,$this->studentId,0);
        $quesNo++;
		$_REQUEST=array();
        $this->ajaxcontentview();
    }
	public function submit()
    {
        $examId=$_REQUEST['id'];
		$examResultId=$_REQUEST['examResultId'];
        $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$examId;
		$this->autoInsert->iFetch($sql,$post);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`opened`=1 AND `ExamStat`.`exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($sql,$attempted);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`opened`=0 AND `ExamStat`.`exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($sql,$notAttempted);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`answered`=1 AND `ExamStat`.`exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($sql,$answered);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`answered`=0 AND `ExamStat`.`exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($sql,$notAnswered);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`review`=1 AND `ExamStat`.`exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($sql,$review);
		include("View/ExamStarts/submit.php");
    }
	public function examwarning()
    {
		$examResultId=$_REQUEST['examResultId'];
        $SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_warns` AS `ExamWarn` WHERE `exam_result_id`=".$examResultId;
		$this->autoInsert->iFetchCount($SQL,$navigateCount);
        $navigateCount++;
        if($navigateCount>$this->configuration['tolrance_count'])
        {
            print "Yes";
            exit(0);
        }
        else
        {
			$this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_warns",array('exam_result_id'=>$examResultId,'created'=>$this->ExamApp->currentDateTime()));            
        }
		include("View/ExamStarts/examwarning.php");
    }
    public function examclose()
    {
        $examResultId=$_REQUEST['examResultId'];
		$id=$_REQUEST['id'];
        $SQL="SELECT `Exam`.`finish_result` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `id`=".$id;
		$this->autoInsert->iFetch($SQL,$examArr);
		if($examArr['finish_result'])
        {
			$msg=$this->ExamApp->showMessage("You can find your result here.",'success');
            $controller='examapp_UserResult';
            $action='view';
        }
        else
        {
			$msg=$this->ExamApp->showMessage("Thanks for given the exam.",'success');
            $controller='examapp_UserExam';
            $action='index';
        }
		include('View/Layouts/exam_header.php');
		include("View/ExamStarts/examclose.php");
		include('View/Layouts/exam_footer.php');
    }
    public function finish()
    {
		global$quesNo;global$id;
		$id=$_REQUEST['id'];
		$warn=$_REQUEST['warn'];
        if($id==null)
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','error',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
        $sql="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`exam_id`=".$id." AND `ExamResult`.`student_id`=".$this->studentId." AND `ExamResult`.`end_time` IS NULL ";
        $this->autoInsert->iFetch($sql,$currentExamResult);
		if($currentExamResult)
        {
            $this->ExamStart->userExamFinish($id,$this->studentId,$this->ExamApp->currentDateTime());
            if($warn==null)
            {
				$sql="select * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$id;
				$this->autoInsert->iFetch($sql,$examArr);
                if($this->configuration['exam_feedback'])
                {
                    if($examArr['finish_result'])
                    $this->resultEmailSms($currentExamResult,$examArr);
					$redirectUrl=$this->ajaxUrl;
					$redirectUrl=add_query_arg('info','feedbacks',$redirectUrl);
					$redirectUrl=add_query_arg('id',$id,$redirectUrl);
					$redirectUrl=add_query_arg('examResultId',$currentExamResult['id'],$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
                }
                else
                {                   
                    if($examArr['finish_result'])
                    {
                        $this->resultEmailSms($currentExamResult,$examArr);
						$redirectUrl=admin_url('admin.php').'?page=examapp_UserResult';
						$redirectUrl=add_query_arg('info','view',$redirectUrl);
						$redirectUrl=add_query_arg('id',$id,$redirectUrl);
						$redirectUrl=add_query_arg('examResultId',$currentExamResult['id'],$redirectUrl);
						$redirectUrl=add_query_arg('msg','result',$redirectUrl);
						wp_redirect($redirectUrl);
						exit;
                    }
                    else
                    {
						$redirectUrl=$this->urlUserExam;
						$redirectUrl=add_query_arg('info','index',$redirectUrl);
						$redirectUrl=add_query_arg('msg','thanks',$redirectUrl);
						wp_redirect($redirectUrl);
						exit;
                    }
                }
            }
            else
            {
				$this->resultEmailSms($currentExamResult,$examArr);
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','examclose',$redirectUrl);
				$redirectUrl=add_query_arg('id',$id,$redirectUrl);
				$redirectUrl=add_query_arg('examResultId',$currentExamResult['id'],$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
            }
        }
        else
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','error',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
    }
    public function  resultEmailSms($currentExamResult,$examArr)
    {
		try
        {
            if($this->configuration['email_notification'] || $this->configuration['sms_notification'])
            {
				$SQL="SELECT * FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `id`=".$currentExamResult['id'];
				$this->autoInsert->iFetch($SQL,$valueArr);
				$user_info = get_userdata($this->studentId);
                $siteName=get_bloginfo();$url=admin_url('admin.php').'?page=examapp_UserResult&info=index';
                $email=$user_info->user_email;$studentName=$user_info->display_name;$mobileNo=get_user_meta($this->studentId,'examapp_phone',true);
                $examName=$examArr['name'];$result=$valueArr['result'];$obtainedMarks=$valueArr['obtained_marks'];
                $questionAttempt=$valueArr['total_answered'];$timeTaken=$this->ExamApp->secondsToWords(strtotime($valueArr['end_time'])-strtotime($valueArr['start_time']));
                $percent=$valueArr['percent'];
                if($this->configuration['email_notification'])
                {
                    /* Send Email */
                    $sql="SELECT `Emailtemplate`.`name` AS `name`,`Emailtemplate`.`status` AS `status`,`Emailtemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_emailtemplates` AS `Emailtemplate` where `Emailtemplate`.`type`='ERT'";
					$this->autoInsert->iFetch($sql,$emailSettingArr);
					if($emailSettingArr['status']=="Published")
					{
                        $subject=wp_specialchars_decode($emailSettingArr['name']);
						$message=eval('return "' . addslashes($emailSettingArr['description']). '";');
						add_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
						wp_mail($email,$subject,$message);
						remove_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
                        /* End Email */
                    }
                }
                if($this->configuration['sms_notification'])
                {
                    /* Send Sms */
                    $url=site_url();
					$sql="SELECT `Smstemplate`.`name` AS `name`,`Smstemplate`.`status` AS `status`,`Smstemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_smstemplates` AS `Smstemplate` where `Smstemplate`.`type`='ERT'";
					$this->autoInsert->iFetch($sql,$smsSettingArr);
					if($smsSettingArr['status']=="Published")
					{
						$message=eval('return "' . addslashes($smsSettingArr['description']). '";');							
						$this->ExamApp->sendSms($mobileNo,$message);
					}
                    /* End Sms */
                }
            }
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),"danger");
        }            
    }
    public function paid()
    {
		$id=$_REQUEST['id'];
		$type=$_REQUEST['P'];
        if($id==null)
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','error',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
        else
        {
            if($this->checkPaidStatus($id,$this->studentId))
            {
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('id',$id,$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
            }
            else
            {
                if($this->paidAmount($id))
                {
                    $redirectUrl=$this->ajaxUrl;
					$redirectUrl=add_query_arg('info','index',$redirectUrl);
					$redirectUrl=add_query_arg('id',$id,$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
                }
            }
        }
        $redirectUrl=$this->urlUserExam;
		$redirectUrl=add_query_arg('info','index',$redirectUrl);
		wp_redirect($redirectUrl);
		exit;
    }
    public function paidAmount($id)
    {
		$SQL="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `id`=".$id." AND `paid_exam`='1'";
		$this->autoInsert->iFetch($SQL,$exampost);
		$amount=$exampost['amount'];
        $balance=$this->ExamApp->WalletBalance($this->studentId);
        if($balance>=$amount)
        {
            if($this->ExamApp->WalletInsert($this->studentId,$amount,"Deducted",$this->ExamApp->currentDateTime(),"EM",__("$amount Deducted for paying exam")))
            {
                $expiryDays=$exampost['expiry'];
                $expiryDate=date('Y-m-d',strtotime($this->ExamApp->currentDate()."+$expiryDays days"));
                $this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_orders",array("student_id"=>$this->studentId,"exam_id"=>$id,'date'=>$this->ExamApp->currentDate(),'expiry_date'=>$expiryDate));
				return true;
            }
        }
        else
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','insufficient',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
        return false;
    }
    public function renewexam()
    {
		$id=$_REQUEST['id'];
        if($this->paidAmount($id))
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
    }    
	public function feedbacks()
    {
        $id=$_REQUEST['id'];
		$examResultId=$_REQUEST['examResultId'];
		$SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `id`='".$examResultId."'";
		$this->autoInsert->iFetchCount($SQL,$count);
        if (!$examResultId || $count==0)
        {
            $redirectUrl=$this->urlUserExam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
        $isClose='No';
        if($_POST)
        {
            try
            {
				$SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_feedbacks` AS `ExamFeedback` WHERE `exam_result_id`='".$examResultId."'";
				$this->autoInsert->iFetchCount($SQL,$count);
				if($count==0)
				{
					$comments=__("1. The test instructions were ").$_POST['test_instruction']."<br><br>".
					__("2. Language of question was ").$_POST['question_language']."<br><br>".
					__("3. Overall test experience was ").$_POST['test_experience']."<br><br>".
					__("Any other feedback suggestion: ").$_POST['comments'];
					$recordArr=array('exam_result_id'=>$examResultId,'comments'=>$comments,'created'=>$this->ExamApp->currentDateTime());
					$this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_feedbacks",$recordArr,'No');
					$msg=$this->ExamApp->showMessage("Feedback has submitted successfully",'success');
					$isClose='Yes';
				}
				else
				{
					$msg=$this->ExamApp->showMessage("Feedback already submitted.",'danger');
					$isClose='Yes';
				}
			}
            catch (Exception $e)
            {
				$msg=$this->ExamApp->showMessage("Feedback already submitted.",'danger');
                $isClose='Yes';
            }            
        }
		include("View/Layouts/exam_header.php");
		include("View/ExamStarts/feedbacks.php");
		include("View/Layouts/exam_footer.php");
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new ExamStarts;
$obj->$info();
?>