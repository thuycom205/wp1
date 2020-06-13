<?php
include('ExamApps.php');
include('Model/Attemptedpaper.php');
class Attemptedpapers extends Attemptedpaper
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_exams";
		$this->examStat= $wpdb->prefix."emp_exam_stats";
		$this->examResult = $wpdb->prefix."emp_exam_results";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Attemptedpaper = new Attemptedpaper();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Attemptedpaper';
		$this->url=admin_url('admin.php').'?page=examapp_Attemptedpaper';
		$this->urlexam=admin_url('admin.php').'?page=examapp_Exam';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->globalCondition="LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`ExamGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ";
	}
	function index()
	{
		$mathEditor=$this->configuration['math_editor'];
		$examId=$_REQUEST['id'];
		$id=$examId;
		$examCount=$this->Attemptedpaper->examCount($id,$this->globalCondition);
		if($id==null || $examCount==0)
		{
			?><script>window.location="<?php echo $this->urlexam;?>&info=index&msg=notattempted";</script><?php
		}
		include("View/Attemptedpapers/index.php");		
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{			
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST);
			$SQL = "SELECT *,ExamResult.id As ExamResultId,User.display_name As adminName,Student.display_name As studentName,Student.user_email As studentEmail,Exam.name as examName,`ExamResult`.user_id as `user_id` FROM ".$this->wpdb->prefix."emp_exam_results As ExamResult left JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`)LEFT JOIN `".$this->wpdb->prefix."users` AS `User` ON (`User`.`ID`=`ExamResult`.`user_id`)Inner JOIN `".$this->wpdb->prefix."users` AS `Student` ON (`Student`.`ID`=`ExamResult`.`student_id`)  WHERE ExamResult.end_time IS NOT NULL and ExamResult.exam_id=".$examId;
			$resultArr=$this->ExamApp->getRecordSet($SQL,1,$pageNumber,'`ExamResult`.`id`');
			$Attemptedpapers=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,1,$pageNumber,"NO","NO");
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			$sqlQtype ="SELECT * FROM ".$this->wpdb->prefix."emp_qtypes";	
			$this->autoInsert->iwhileFetch($sqlQtype,$Qtype);
			$sqlUser ="SELECT * FROM ".$this->wpdb->prefix."users";	
			$this->autoInsert->iwhileFetch($sqlUser,$UserArr);
			include('View/Attemptedpapers/show.php');
			die();
		}
	}
	public function marksupdate()
    {
		try
		{
			$userId = get_current_user_id();
			$marksObtained=$_POST['marks_obtained'];
			$id=$_POST['id'];
			$statId=$_POST['statId'];
			$page=$_POST['page'];
			$examCount=$this->Attemptedpaper->examCount($id);
			$SQL1 = "SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_exam_stats` WHERE `id`=".$statId;
			$this->autoInsert->iFetchCount($SQL1,$examStatCount);
			if($id==null || $statId==null || $examCount==0 || $examStatCount==0)
			{
				$redirectUrl=$this->urlexam;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$attemptArr=array('marks_obtained'=>$marksObtained,'user_id'=>$userId,'checking_time'=>'');
			$this->autoInsert->iUpdateArray($this->examStat,$attemptArr,array('`id`'=>$statId));
			if(isset($page) && $page>1)
			$page=$page;
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('id',$id,$redirectUrl);
			$redirectUrl=add_query_arg('pageName',$page,$redirectUrl);
			$redirectUrl=add_query_arg('msg','marksupdate',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		catch (Exception $e)
		{
		   echo $this->ExamApp->showMessage($e->getMessage(),'danger');	
		}
    }
	public function finalize()
	{
		$userId = get_current_user_id();		
		$id=$_REQUEST['id'];
		$pageId=$_REQUEST['pageId'];
		$examResultId=$_REQUEST['examResultId'];
		$sqlFinilize = "SELECT `ExamResult`.`total_marks`,`Exam`.`passing_percent` FROM `".$this->wpdb->prefix."emp_exam_results` As `ExamResult` LEFT JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`ExamResult`.`exam_id`=`Exam`.`id`) WHERE `ExamResult`.`id`=".$examResultId;
		$this->autoInsert->iFetch($sqlFinilize,$post);
		$obtainedMarks=$this->Attemptedpaper->obtainedMarks($examResultId);
		$percent=number_format(($obtainedMarks*100)/$post['total_marks'],2);
		if($percent>=$post['passing_percent'])
		$result="Pass";
		else
		$result="Fail";
		$examResultArr=array('user_id'=>$userId,'finalized_time'=>$this->ExamApp->currentDateTime(),'obtained_marks'=>$obtainedMarks,'percent'=>$percent,'result'=>$result);
		$this->autoInsert->iUpdateArray($this->examResult,$examResultArr,array('`id`'=>$examResultId));
		$page="";
		if(isset($pageId) && $pageId>1)
		$page=$pageId;
		$redirectUrl=$this->url;
		$redirectUrl=add_query_arg('info','index',$redirectUrl);
		$redirectUrl=add_query_arg('id',$id,$redirectUrl);
		$redirectUrl=add_query_arg('pageName',$page,$redirectUrl);
		$redirectUrl=add_query_arg('msg','success',$redirectUrl);
		wp_redirect($redirectUrl);
		exit;
	}
	public function closeexam()
	{
		$id=$_REQUEST['id'];
		$SQL = "SELECT COUNT(*) as `count` FROM ".$this->tableName." AS `Exam` ".$this->globalCondition." AND `Exam`.`id`=".$id;
		$this->autoInsert->iFetchCount($SQL,$examCount);
        if($id==null || $examCount==0)
        {
			$redirectUrl=$this->urlexam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		$sqlFinilize = "SELECT COUNT(*) as `count` FROM ".$this->wpdb->prefix."emp_exam_results WHERE `exam_id`=".$id." AND `user_id` IS NULL AND `finalized_time` IS NULL";
		$this->autoInsert->iFetchCount($sqlFinilize,$finalizedExam);
		if($finalizedExam>0)
        {
			$redirectUrl=$this->urlexam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','pfinalize',$redirectUrl);
			$redirectUrl=add_query_arg('c',$finalizedExam,$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		$userId = get_current_user_id();
		$finalizedTime=$this->ExamApp->currentDateTime();
		$recordArr=array('status'=>'Closed','user_id'=>$userId,'finalized_time'=>$finalizedTime);
		$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>$id));
		$redirectUrl=$this->ajaxUrl;
		$redirectUrl=add_query_arg('info','cenotif',$redirectUrl);
		$redirectUrl=add_query_arg('id',$id,$redirectUrl);
		$redirectUrl=add_query_arg('offset','0',$redirectUrl);
		wp_redirect($redirectUrl);
		exit;
	}
	public function cenotif()
    {
		$id=$_REQUEST['id'];
		$offset=$_REQUEST['offset'];
		if($this->configuration['email_notification'] || $this->configuration['sms_notification'])
		{
			$SQL = "SELECT count(id) as id FROM ".$this->tableName." WHERE status='Closed' and id=".$id;
			$this->autoInsert->iFetch($SQL,$examCount);
			if($id==null || $examCount['id']==0)
			{
				$redirectUrl=$this->urlexam;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$limit=10;
			$sql="SELECT count(`ExamResult`.`id`) AS `count`,`User`.`display_name` AS `studentnName` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`Student`.`student_id`=`ExamResult`.`student_id`)  INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`)   WHERE
			`Exam`.`status`='Closed'  AND `Exam`.`id`=".$id."  ORDER BY `ExamResult`.`percent` DESC ";
			$this->autoInsert->iFetch($sql,$numRows);
			$sql="SELECT `ExamResult`.`total_marks` AS `total_marks`,`ExamResult`.`obtained_marks` AS `obtained_marks`,`ExamResult`.`total_question` AS `total_question`,`ExamResult`.`total_answered` AS `total_answered`,`ExamResult`.`percent` AS `percent`,`ExamResult`.`result` AS `result`,`User`.`display_name` AS `studentnName`,`User`.`user_email` AS `email`,`Student`.`student_id` AS `id`,`Exam`.`name` AS `name`,`ExamResult`.`start_time` AS `start_time`,`ExamResult`.`end_time` AS `end_time`,`ExamResult`.`result` AS `result` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
			INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`Student`.`student_id`=`ExamResult`.`student_id`)  INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`)  WHERE  `Exam`.`status`='Closed'  AND `Exam`.`id`=".$id."  ORDER BY `ExamResult`.`percent` DESC  LIMIT ".$offset.",".$limit;
			$this->autoInsert->iWhileFetch($sql,$post);
            $rank=0;
            foreach($post as $value)
            {
                $rank=$offset+1+$rank;
                $siteName=$this->siteName;$siteEmailContact=$this->siteEmailContact;$url=$this->siteDomain;
                $email=$value['email'];$studentName=$value['studentnName'];$mobileNo=get_user_meta($value['id'],'examapp_phone',true);
                $examName=$value['name'];$result=$value['result'];$obtainedMarks=$value['obtained_marks'];
                $questionAttempt=$value['total_answered'];$timeTaken=$this->ExamApp->secondsToWords(strtotime($value['end_time'])- strtotime($value['start_time']));
                $percent=$value['percent'];$siteName=get_bloginfo();
                if($this->configuration['email_notification'])
                {
                    /* Send Email */
					$url=site_url('wp-login.php','login');	
					$sql="SELECT `Emailtemplate`.`name` AS `name`,`Emailtemplate`.`status` AS `status`,`Emailtemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_emailtemplates` AS `Emailtemplate` where `Emailtemplate`.`type`='EFD'";   
					$this->autoInsert->iFetch($sql,$emailTemplateArr);
					if($emailTemplateArr['status']=="Published")
					{
						$userEmail=$email;
						$subject=wp_specialchars_decode($emailTemplateArr['name']);
						$message=eval('return "' . addslashes($emailTemplateArr['description']). '";');
						add_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
						wp_mail($userEmail,$subject,$message);
						remove_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
					}
                }
                if($this->configuration['sms_notification'])
                {
                    /* Send Sms */
					$url=site_url();
					$sql="SELECT `Smstemplate`.`name` AS `name`,`Smstemplate`.`status` AS `status`,`Smstemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_smstemplates` AS `Smstemplate` where `Smstemplate`.`type`='EFD'";   
					$this->autoInsert->iFetch($sql,$smsTemplateArr);
					if($smsTemplateArr['status']=="Published")
					{  
						$message=eval('return "' . addslashes($smsTemplateArr['description']). '";');							
						$this->ExamApp->sendSms($mobileNo,$message);			
					}
				}				
			}
			$offset=$offset+$limit;
			if($numRows['count']>$offset)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','cenotif',$redirectUrl);
				$redirectUrl=add_query_arg('id',$id,$redirectUrl);
				$redirectUrl=add_query_arg('offset',$offset,$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			else
			{
				$redirectUrl=$this->urlexam;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','successclose',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
        }
		else
		{
			$redirectUrl=$this->urlexam;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','successclose',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
	}	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Attemptedpapers;
$obj->$info();
?>