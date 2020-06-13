<?php
include_once('ExamApps.php');
include_once('Model/UserExam.php');
class UserExams extends UserExam
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->UserExam = new UserExam();
		$this->Result = new Result();
		$this->Exam = new Exam();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_UserExam';
		$this->url=admin_url('admin.php').'?page=examapp_UserExam';
		$this->studentId=$this->ExamApp->getCurrentUserId();
	}
	function index()
	{
		$todayExam=$this->UserExam->getUserExam("today",$this->studentId,$this->currentDateTime());
		include('View/UserExams/index.php');
	}
	public function purchased()
	{
		$purchasedExam=$this->UserExam->getPurchasedExam("today",$this->studentId,$this->currentDateTime());
		include('View/UserExams/purchased.php');
	}
	public function upcoming()
	{
		$upcomingExam=$this->UserExam->getUserExam("upcoming",$this->studentId,$this->currentDateTime());
		include('View/UserExams/upcoming.php');
	}
	public function expired()
	{
		$expiredExam=$this->UserExam->getPurchasedExam("expired",$this->studentId,$this->currentDateTime());
		include('View/UserExams/expired.php');
	}
	function view()
	{
		$id=$_REQUEST['id'];
		$showType=$_REQUEST['showType'];
		$checkPost=$this->UserExam->checkPost($id,$this->studentId);
		if($checkPost==0)
		{
			echo $this->ExamApp->showMessage("Invalid Post","danger");
			$this->index();
			die(0);
		}
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` LEFT JOIN `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` ON(`Exam`.`id`=`ExamMaxquestion`.`exam_id`) WHERE `Exam`.`id`=".$id;
		$this->autoInsert->iFetch($sql,$post);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` WHERE `Exam`.`id`=".$id." AND `Exam`.`status`='Active' ";
		$this->autoInsert->iFetchCount($sql,$examCount);
		
		if($post['type']=="Exam")
		{
			$subjectDetailArr=$this->Result->getSubject($id);
			foreach($subjectDetailArr as $value)
			{
				$subjectId=$value['id'];
				$subjectName=$value['subject_name'];
				$totalQuestionArr=$this->UserExam->subjectWiseQuestion($id,$subjectId,'Exam');
				$subjectDetail[$subjectName]=$totalQuestionArr;
			}
			$totalMarks=$this->Exam->totalMarks($id);
		}
		else
		{
			
			$subjectDetailArr=$this->Result->getPrepSubject($id);
			foreach($subjectDetailArr as $value)
			{
				$subjectId=$value['id'];
				$subjectName=$value['subject_name'];
				$totalQuestionArr=$this->UserExam->subjectWiseQuestion($id,$subjectId);
				$subjectDetail[$subjectName]=$totalQuestionArr;
			}
			$totalMarks=0;
		}	 
		include('View/UserExams/view.php');
	}
	public function guidelines()
    {
        $id=$_REQUEST['id'];
		$checkPost=$this->UserExam->checkPost($id,$this->studentId);
        if($checkPost==0)
        {
            $this->error();
			die(0);
        }
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam`  WHERE `Exam`.`id`=".$id." AND `Exam`.`status`='Active'";
		$this->autoInsert->iFetch($sql,$post);
        if (!$post)
        {
            $this->error();
			die(0);
        }
		$sql="SELECT `ExamResult`.`exam_id` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`end_time` IS NULL AND `ExamResult`.`student_id`=".$this->studentId;
		$this->autoInsert->iFetch($sql,$currentExamResult);
		if($currentExamResult)
		{
			$redirectUrl=$this->ajaxUrl;
			$redirectUrl=add_query_arg('info','instruction',$redirectUrl);
			$redirectUrl=add_query_arg('id',$currentExamResult['exam_id'],$redirectUrl);
			$redirectUrl=add_query_arg('msg','pending',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		include('View/Layouts/exam_header.php');
		include('View/UserExams/guidelines.php');
		include('View/Layouts/exam_footer.php');		
    }
	public function instruction()
    {
        $id=$_REQUEST['id'];
		$checkPost=$this->UserExam->checkPost($id,$this->studentId);
        if($checkPost==0)
        {
            $this->error();
			die(0);
        }
		$sql="SELECT * FROM `".$this->wpdb->prefix."emp_exams` AS `Exam`  WHERE `Exam`.`id`=".$id." AND `Exam`.`status`='Active'";
		$this->autoInsert->iFetch($sql,$post);
        $ispaid=$this->ExamApp->checkPaidStatus($id,$this->studentId);
		include('View/Layouts/exam_header.php');
        include('View/UserExams/instruction.php');
		include('View/Layouts/exam_footer.php');
    }	
    public function error()
    {
        include('View/UserExams/error.php');
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new UserExams;
$obj->$info();
?>