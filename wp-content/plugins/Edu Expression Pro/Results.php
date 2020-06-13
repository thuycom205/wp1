<?php
include('ExamApps.php');
include('Model/Result.php');
use Dompdf\Dompdf;

class Results extends Result
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Result = new Result();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Result';
		$this->url=admin_url('admin.php').'?page=examapp_Result';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->globalCondition="LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`ExamResult`.`exam_id`=`ExamGroup`.`exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`ExamGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ";
	}
	function index()
	{
		try
		{
			if($_REQUEST['id'])
			{
				$studentId=$_REQUEST['id'];	
			}
			else
			{
				$studentId=null;	
			}
			$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
			$this->ExamApp->getDropdownDb($_POST['exam_id'],$examName,$this->wpdb->prefix."emp_exams","id","name","LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`PrimaryTable`.`id`=`ExamGroup`.`exam_id`) WHERE 1 ".$this->ExamApp->userGroupWiseIn("`ExamGroup`.`group_id`")." AND `PrimaryTable`.`status`<>'Inactive' GROUP BY `PrimaryTable`.`id` ORDER BY `PrimaryTable`.`name` ASC");
			$name=null;$studentGroup=null;$status=null;$isExam=false;$isStudent=false;
			if(isset($_POST['exam_id']) || isset($_POST['exam_id']))
			{
				$name=$_POST['exam_id'];
				$isExamSearch=true;				
			}
			if(isset($_POST['name']) && strlen($_POST['name'])>0)
			{
				$name=$_POST['name'];
				$isStudentSearch=true;
			}
			if(isset($_POST['group_name']) && is_array($_POST['group_name']))
			{
				$isSearch=true;
				$group=$_POST['group_name'];
			}
			if(isset($_POST['status']) && strlen($_POST['status'])>0)
			{
				$status=$_POST['status'];
				$isExamSearch=true;
			}
			if(isset($_POST['examWise']))
			{
				$examDetails=$this->Result->examWise($name,$group,$this->globalCondition,$status);
				$isExam=true;
			}
			if(isset($_POST['studentWise']))
			{
				$studentDetails=$this->Result->studentWise($name,$group,$this->globalCondition);
				$isStudent=true;
			}
			if($studentId!=null)
			{
				$examDetails=$this->Result->studentExamWise($studentId,$group,$this->globalCondition);
				$studentId=$studentId;
				
			}
			include("View/Results/index.php");
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}	
	}
	public function view()
	{
		try
		{
			$id=$_REQUEST['id'];
			$SQL = "SELECT count(id) as id FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` WHERE `ExamResult`.`id`=".$id;
			$this->autoInsert->iFetch($SQL,$totalExam);
			if($totalExam['id']=='0')
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}        
			$sql="SELECT * from `".$this->wpdb->prefix."emp_diffs` AS `Diff` ";
			$this->autoInsert->iWhileFetch($sql,$diffValue);
			$easy=$this->Result->difficultyWiseQuestion($id,'E');
			$normal=$this->Result->difficultyWiseQuestion($id,'M');
			$difficult=$this->Result->difficultyWiseQuestion($id,'D');        
			$diffLevelSeries=json_encode(array(array('name'=>'Total Question','data'=>array(array($diffValue[0]['diff_level'],$easy),array($diffValue[1]['diff_level'],$normal),array($diffValue[2]['diff_level'],$difficult)))));
			$diffLevelTitle=__('Question Difficulty Level');
			
			$studentDetail=$this->Result->studentDetail($id);
			$studentName=$studentDetail['name']." ".__('Performance');
			$performanceTitle=$studentName;
			$studentId=$studentDetail['id'];
			$performanceChartData=array();
			$currentMonth=date('m');
			for($i=1;$i<=12;$i++)
			{
			  if($i>$currentMonth)
			  break;
			  $examData=$this->Result->performanceCount($studentId,$i);
			  $performanceChartData[]=(float) $examData;
			}
			$performanceSeries=json_encode(array(array('name'=>'Exam','data'=>$performanceChartData)));
			$xAxisCategories=json_encode(array(__('Jan'),__('Feb'),__('Mar'),__('Apr'),__('May'),__('Jun'),__('Jul'),__('Aug'),__('Sep'),__('Oct'),__('Nov'),__('Dec')));
			include("View/Results/view.php");
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
    }
    public function result()
    {
        try
        {
			$mathEditor=$this->configuration['math_editor'];
			$id=$_REQUEST['id'];
			$SQL = "SELECT COUNT(`ExamResult`.`id`) as `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ".$this->globalCondition." AND `ExamResult`.`id`=".$id." AND `ExamResult`.`user_id` > 0";
			$this->autoInsert->iFetchCount($SQL,$studentCount);
			if($studentCount==0)
			{
				?><script>window.location='<?php echo $this->url;?>&info=index&msg=invalid';</script><?php
			}
			$sql="SELECT (TIMESTAMPDIFF(SECOND,`ExamResult`.`start_time`,`ExamResult`.`end_time`)) AS `time_taken`,`Exam`.`name`,`User`.`ID` AS `ID`,`User`.`user_email`,`ExamResult`.`percent`,`ExamResult`.`obtained_marks`,`ExamResult`.`total_marks`,`Exam`.`passing_percent`,`Exam`.`duration`,
			`ExamResult`.`result`,`ExamResult`.`start_time`,`ExamResult`.`end_time`,`Exam`.`declare_result`,`ExamFeedback`.`comments` FROM  `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
			INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`ExamResult`.`student_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`User`.`ID`=`StudentGroup`.`student_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
			LEFT JOIN  `".$this->wpdb->prefix."emp_exam_feedbacks` AS `ExamFeedback`  ON(`ExamResult`.`id`=`ExamFeedback`.`exam_result_id`)
			WHERE `ExamResult`.`id`=".$id." ".$this->ExamApp->userGroupWiseIn('`StudentGroup`.`group_id`')." AND `ExamResult`.`user_id` > 0 ";
			$this->autoInsert->iFetch($sql,$examDetails);
			$userSubject=$this->Result->userSubject($id);
			$userMarksheet=$this->Result->userMarksheet($id);
			$SQL = "SELECT count(`id`) as `count` FROM `".$this->wpdb->prefix."emp_exam_warns` AS `ExamWarn` WHERE `ExamWarn`.`exam_result_id`=".$id;
			$this->autoInsert->iFetch($SQL,$examWarning1);
			$examWarning=$examWarning1['count'];
			foreach($userSubject as $subjectValue)
			{
				$xAxisCategories[]=$subjectValue['subject_name'];
			}
			foreach($userMarksheet as $k=>$userMarkValue)
			{
				if(strlen($k)!=5)
				{
					if($userMarkValue['Subject']['percent']<=33)
					$color='rgb(235, 29, 29)';
					elseif($userMarkValue['Subject']['percent']>=34 && $userMarkValue['Subject']['percent']<=59)
					$color='rgb(247, 147, 39)';
					else
					$color='rgb(57, 174, 57)';
					$chartData[]=array('y'=>(float) $userMarkValue['Subject']['obtained_marks'],'color'=>$color);
					$chartData1[]=(float) $userMarkValue['Subject']['total_marks'];
				}
			}
			$chartRerData=array();$chartRerData1=array();
			$chartRerData=array(array((int)$examDetails['obtained_marks'],$this->ExamApp->secondsToHourMinute(strtotime($examDetails['end_time'])-strtotime($examDetails['start_time']))));
			$chartRerData1=array(array((int)$examDetails['total_marks'],$this->ExamApp->secondsToHourMinute($examDetails['duration']*60)));
			
			$timeSeries=json_encode(array(array('name'=>__('Candidate Marks Distribution'),'data'=>$chartRerData),
										  array('name'=>__('Max Marks Distribution'),'data'=>$chartRerData1)));
			
			$subjectxAxis=json_encode($xAxisCategories);
			$subjectSeries=json_encode(array(array('name'=>__('Marks Scored'),'data'=>$chartData),
											 array('name'=>__('Max Marks'),'data'=>$chartData1)));
			
			$SQL="SELECT `ExamStat`.*,(TIMESTAMPDIFF(SECOND,`ExamStat`.`attempt_time`,`ExamStat`.`modified`)) AS `time_taken`,`Question`.*,`Subject`.*,`Qtype`.*  FROM `".$this->wpdb->prefix."emp_questions` AS `Question`
			INNER JOIN `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` ON(`Question`.`id`=`ExamStat`.`question_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Subject`.`id`=`Question`.`subject_id`)
			WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`student_id`=".$examDetails['ID']." ORDER BY `ExamStat`.`ques_no` ASC";
			$this->autoInsert->iWhileFetch($SQL,$post);
			$SQL="select * from `".$this->wpdb->prefix."emp_exam_warns` AS `ExamWarn` where `ExamWarn`.`exam_result_id`=".$id;
			$this->autoInsert->iwhileFetch($SQL,$examWarnArr);
			$configuration=$this->configuration;
			include("View/Results/result.php");
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
    }
    public function stdexamresult()
    {
        try
        {
			$mathEditor=$this->configuration['math_editor'];
			$id=$_REQUEST['id'];
			$SQL = "SELECT COUNT(`ExamResult`.`id`) as `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult` ".$this->globalCondition." AND `ExamResult`.`id`=".$id." AND `ExamResult`.`user_id` > 0";
			$this->autoInsert->iFetchCount($SQL,$studentCount);
			if($studentCount==0)
			{
				?><script>window.location='<?php echo $this->url;?>&info=index&msg=invalid';</script><?php
			}
			$sql="SELECT (TIMESTAMPDIFF(SECOND,`ExamResult`.`start_time`,`ExamResult`.`end_time`)) AS `time_taken`,`Exam`.`name`,`User`.`ID` AS `ID`,`User`.`user_email`,`ExamResult`.`percent`,`ExamResult`.`obtained_marks`,`ExamResult`.`total_marks`,`Exam`.`passing_percent`,`Exam`.`duration`,
			`ExamResult`.`result`,`ExamResult`.`start_time`,`ExamResult`.`end_time`,`Exam`.`declare_result`,`ExamFeedback`.`comments` FROM  `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`
			INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`ExamResult`.`student_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`User`.`ID`=`StudentGroup`.`student_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`ExamResult`.`exam_id`)
			LEFT JOIN  `".$this->wpdb->prefix."emp_exam_feedbacks` AS `ExamFeedback`  ON(`ExamResult`.`id`=`ExamFeedback`.`exam_result_id`)
			WHERE `ExamResult`.`id`=".$id." ".$this->ExamApp->userGroupWiseIn('`StudentGroup`.`group_id`')." AND `ExamResult`.`user_id` > 0 ";
			$this->autoInsert->iFetch($sql,$examDetails);
			$userSubject=$this->Result->userSubject($id);
			$userMarksheet=$this->Result->userMarksheet($id);
			$SQL = "SELECT count(`id`) as `count` FROM `".$this->wpdb->prefix."emp_exam_warns` AS `ExamWarn` WHERE `ExamWarn`.`exam_result_id`=".$id;
			$this->autoInsert->iFetch($SQL,$examWarning1);
			$examWarning=$examWarning1['count'];
			foreach($userSubject as $subjectValue)
			{
				$xAxisCategories[]=$subjectValue['subject_name'];
			}
			foreach($userMarksheet as $k=>$userMarkValue)
			{
				if(strlen($k)!=5)
				{
					if($userMarkValue['Subject']['percent']<=33)
					$color='rgb(235, 29, 29)';
					elseif($userMarkValue['Subject']['percent']>=34 && $userMarkValue['Subject']['percent']<=59)
					$color='rgb(247, 147, 39)';
					else
					$color='rgb(57, 174, 57)';
					$chartData[]=array('y'=>(float) $userMarkValue['Subject']['obtained_marks'],'color'=>$color);
					$chartData1[]=(float) $userMarkValue['Subject']['total_marks'];
				}
			}
			$chartRerData=array();$chartRerData1=array();
			$chartRerData=array(array((int)$examDetails['obtained_marks'],$this->ExamApp->secondsToHourMinute(strtotime($examDetails['end_time'])-strtotime($examDetails['start_time']))));
			$chartRerData1=array(array((int)$examDetails['total_marks'],$this->ExamApp->secondsToHourMinute($examDetails['duration']*60)));
			
			$timeSeries=json_encode(array(array('name'=>__('Candidate Marks Distribution'),'data'=>$chartRerData),
										  array('name'=>__('Max Marks Distribution'),'data'=>$chartRerData1)));
			
			$subjectxAxis=json_encode($xAxisCategories);
			$subjectSeries=json_encode(array(array('name'=>__('Marks Scored'),'data'=>$chartData),
											 array('name'=>__('Max Marks'),'data'=>$chartData1)));
			
			$SQL="SELECT `ExamStat`.*,(TIMESTAMPDIFF(SECOND,`ExamStat`.`attempt_time`,`ExamStat`.`modified`)) AS `time_taken`,`Question`.*,`Subject`.*,`Qtype`.*  FROM `".$this->wpdb->prefix."emp_questions` AS `Question`
			INNER JOIN `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` ON(`Question`.`id`=`ExamStat`.`question_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)
			INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON(`Subject`.`id`=`Question`.`subject_id`)
			WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`student_id`=".$examDetails['ID']." ORDER BY `ExamStat`.`ques_no` ASC";
			$this->autoInsert->iWhileFetch($SQL,$post);
			$SQL="select * from `".$this->wpdb->prefix."emp_exam_warns` AS `ExamWarn` where `ExamWarn`.`exam_result_id`=".$id;
			$this->autoInsert->iwhileFetch($SQL,$examWarnArr);
			$configuration=$this->configuration;
			include("View/Layouts/script_header.php");
			include("View/Results/stdexamresult.php");
			include("View/Layouts/script_footer.php");
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
    }
    public function downloadresult()
    {
        try
        {
            $name=null;$studentGroup=null;$status=null;
            if(strlen($_REQUEST['examId'])>0)
            {
                $name=$_REQUEST['examId'];
            }
            if(strlen($_REQUEST['status'])>0)
            {
                $status=$_REQUEST['status'];
            }
            if(strlen($_REQUEST['stuentGroup'])>0)
            {
                $studentGroupArr=explode(",",$_REQUEST['stuentGroup']);
                foreach($studentGroupArr as $value)
                {
                    $studentGroup[]=$value;
                }           
            }
            $examDetails=$this->Result->examWise($name,$studentGroup,$this->userGroupWiseId,$status);
			$examResult=$examDetails;
			require_once 'dompdf/autoload.inc.php';
			// instantiate and use the dompdf class
			ob_start();
			include_once("View/Layouts/pdf_header.php");
			include_once("View/Results/downloadresult.php");
			include_once("View/Layouts/pdf_footer.php");
			$dompdf = new Dompdf();
			$dompdf->loadHtml(ob_get_clean());
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'landscape');
			// Render the HTML as PDF
			$dompdf->render();
			$dompdf->stream('Result-' . rand());            
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
    }
    public function dwstdresult()
    {
        try
        {
		$studentId=$_REQUEST['studentId'];	
		$examDetails=$this->Result->studentExamWise($studentId,null,$this->userGroupWiseId);
		$examResult=$examDetails;
		 require_once 'dompdf/autoload.inc.php';
		// instantiate and use the dompdf class
		ob_start();
		include_once("View/Layouts/pdf_header.php");
		include_once("View/Results/dwstdresult.php");
		include_once("View/Layouts/pdf_footer.php");
		$dompdf = new Dompdf();
		$dompdf->loadHtml(ob_get_clean());
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// Render the HTML as PDF
		$dompdf->render();
		$dompdf->stream('Result-' . rand());
		//$this->pdfConfig = array('filename' => 'Student-Wise-Result-' . rand().'.pdf');
	}
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Results;
$obj->$info();
?>