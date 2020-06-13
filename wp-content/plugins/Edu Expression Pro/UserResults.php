<?php
include_once('ExamApps.php');
include_once('Model/UserResult.php');
// reference the Dompdf namespace
use Dompdf\Dompdf;
class UserResults extends UserResult
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_exam_results";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->UserResult = new UserResult();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_UserResult';
		$this->url=admin_url('admin.php').'?page=examapp_UserResult';
		$this->studentId=$this->ExamApp->getCurrentUserId();
	}
	function index()
	{
		include("View/UserResults/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$studentId=$this->studentId;
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST,'Result.id');
			$SQL = "SELECT *,`Result`.`id` as `Result.id` FROM ".$this->tableName." AS `Result` INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`Result`.`exam_id`) WHERE  `Result`.`student_id`=".$studentId."  AND `Result`.`user_id` > 0 ".$condition." $orderBy";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Result`.`id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber,"NO","NO");
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/UserResults/show.php');
			die();
		}
	}
	public function view()
	{
		$mathEditor=$this->configuration['math_editor'];
		$id=$_REQUEST['id'];
		$studentId=$this->studentId;
		$result=new Result();
		$sql="select `Exam`.`id` AS `Exam.id`,`Exam`.`name` AS `Exam.name`,`Exam`.`type` AS `Exam.type`,`User`.`display_name` AS `User.name`,`Result`.`id` AS `Result.id`,`Result`.`percent` AS `Result.percent`,`Result`.`obtained_marks` AS `Result.obtained_marks`,`Result`.`total_marks` AS `Result.total_marks`,`Result`.`total_question` AS `Result.total_question`,`Result`.`total_attempt` AS `Result.total_attempt`,`Exam`.`passing_percent` AS `Exam.passing_percent`,`Exam`.`duration` AS `Exam.duration`,`Result`.`result` AS `Result.result`,`Result`.`start_time` AS `Result.start_time`,`Result`.`end_time` AS `Result.end_time`,`Exam`.`declare_result` AS `Exam.declare_result` from  `".$this->wpdb->prefix."emp_exam_results` AS `Result` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Result`.`student_id`)    INNER JOIN `".$this->wpdb->prefix."emp_exams` AS `Exam` ON(`Exam`.`id`=`Result`.`exam_id`)  where `Result`.`id`=".$id." AND  `Result`.`student_id`=".$studentId."  AND `Result`.`user_id` > 0 ";
		$this->autoInsert->iFetch($sql,$examDetails);
		if(!$examDetails)
		{
			echo $this->ExamApp->showMessage("Invalid Post",'danger');
			$this->index();
			die();
		}
		$userSubject=$result->userSubject($id);
		$userMarksheet=$result->userMarksheet($id);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `Result` WHERE `Result`.`exam_id`=".$examDetails['Exam.id']." AND `Result`.`user_id` > 0 ";
		$this->autoInsert->iFetchCount($sql,$totalStudentCount);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='R' ";
		$this->autoInsert->iFetchCount($sql,$correctQuestion);
		$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='W' ";
		$this->autoInsert->iFetchCount($sql,$incorrectQuestion);
		$sql="SELECT SUM(`ExamStat`.`marks_obtained`) AS `total_marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='R' ";
		$this->autoInsert->iFetch($sql,$rightMarksArr1);
		$rightMarksArr=$rightMarksArr1['total_marks'];
		$sql="SELECT SUM(`ExamStat`.`marks_obtained`) AS `total_marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='W' ";
		$this->autoInsert->iFetch($sql,$negativeMarksArr1);
		$negativeMarksArr=$negativeMarksArr1['total_marks'];
		$leftQuestion=$examDetails['Result.total_attempt']-$correctQuestion-$incorrectQuestion;
		$sql="SELECT (SELECT SUM(`marks`) FROM (SELECT `Question`.`marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` Inner JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`ExamStat`.`question_id`=`Question`.`id`) WHERE `ExamStat`.`exam_result_id`=".$id." LIMIT ".$leftQuestion.") subquery)  AS `left_marks` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE 1=1 LIMIT 1";
		$this->autoInsert->iFetch($sql,$leftQuestionArr);
		$sql="SET @i=0;";
		$this->autoInsert->iMainQuery($sql,$setI);
		$sql="SELECT `id`,`student_id`,`percent`, @i:=@i+1 AS `rank` FROM `".$this->wpdb->prefix."emp_exam_results` WHERE `exam_id`=".$examDetails['Exam.id']." ORDER BY `percent` DESC";
		$this->autoInsert->iWhileFetch($sql,$rankArr);
		foreach($rankArr as $rnk)
		{
		    if($rnk['id']==$id)
		    {
				$rank=$rnk['rank'];
				break;
		    }
		}
		$sql="SET @i=0;";
		$this->autoInsert->iMainQuery($sql,$setI);
		$sql="SELECT `id`,`student_id`,`percent`, @i:=@i+1 AS `rank` FROM `".$this->wpdb->prefix."emp_exam_results` WHERE `exam_id`=".$examDetails['Exam.id']." AND  `user_id` IS NOT NULL ORDER BY `percent` DESC";
		$this->autoInsert->iWhileFetch($sql,$topRankArr);
		$userSectionQuestion=$result->userSectionQuestion($examDetails['Result.id'],$examDetails['Exam.id'],$examDetails['Exam.type'],$studentId);
		$rightMarks=$rightMarksArr;
		if($rightMarks=="")
		$rightMarks=0;
		$negativeMarks=$negativeMarksArr;
		if($negativeMarks=="")
		$negativeMarks=0;
		$leftQuestionMarks=$leftQuestionArr['left_marks'];
		if($leftQuestionMarks=="")
		$leftQuestionMarks=0;
		$attemptedQuestion=$correctQuestion+$incorrectQuestion;
		$rank=$rank;
		$mainRank=$rank;
		$myRank=$this->ExamApp->showRank($rank);
		$percent=$examDetails['Result.percent'];
		$percentile=round((($totalStudentCount-$rank)/$totalStudentCount)*100,2);
		$sql="select  `ExamStat`.*,TIMESTAMPDIFF(SECOND,`ExamStat`.`attempt_time`,`ExamStat`.`modified`) AS `time_taken`,`Question`.*,`Diff`.*,`Qtype`.*  from  `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamStat`.`question_id`)    INNER JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)  INNER JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON(`Diff`.`id`=`Question`.`diff_id`)  where `ExamStat`.`exam_result_id`=".$id." AND `ExamStat`.`student_id`=".$studentId."  ORDER BY `ExamStat`.`ques_no`  ASC";
		$this->autoInsert->iWhileFetch($sql,$post);
		foreach($userSubject as $subjectValue)
		{
			$xAxisCategories[]=$subjectValue['subject_name'];
		}
		foreach($userMarksheet as $k=>$userMarkValue)
		{
			if(strlen($k)!=5)
			{
				$chartData[]=(float) $userMarkValue['Subject']['total_marks'];
				$chartData1[]=(float) $userMarkValue['Subject']['obtained_marks'];                
				$timeTaken=$this->ExamApp->secondsToWords($userMarkValue['Subject']['time_taken'],'-');
				$chartRerData[]=array('name'=>$userMarkValue['Subject']['name'],'y'=>($userMarkValue['Subject']['time_taken']/60),'mylabel'=>$timeTaken);
			}
		}
		$chartDataTotal[]=(float) $examDetails['Result.total_marks'];
		$chartDataTotal1[]=(float) $examDetails['Result.obtained_marks'];
		$chartQuestionRerData=array(array(__('Correct Question'),(int)$correctQuestion),
										array(__('Incorrect Question'),(int)$incorrectQuestion),
										array(__('Right Marks'),(int)$rightMarks),
										array(__('Negative Marks'),(int) str_replace("-","",$negativeMarks)));
		
		$performanceSeries=json_encode(array(array('name'=>__('Marks Scored'),'data'=>$chartDataTotal),
											 (array('name'=>__('Max Marks'),'data'=>$chartDataTotal1))));
		$qmReportSeries=json_encode(array(array('name'=>__('Question & Marks Wise'),'data'=>$chartQuestionRerData)));
		$grxAxis=json_encode($xAxisCategories);
		$grReportSeries=json_encode(array(array('name'=>__('Max Marks'),'data'=>$chartData),
										  array('name'=>__('Marks Scored'),'data'=>$chartData1)));
		$tmReportTitle=__('Subject Wise Time Taken');
		$tmReportSeries=json_encode(array(array('name'=>$tmReportTitle,'data'=>$chartRerData)));
	
		$xAxisCategories=array();$compareArr=array();
		$isYou=false;
		foreach($topRankArr as $k=>$postArr)
		{
			$studentId=$postArr['student_id'];
			$UserRank=$postArr['rank'];
			$resultId=$postArr['id'];
			$sql="select `User`.`display_name` AS `Student.name`,`Result`.`exam_id` AS `Result.exam_id`,`Result`.`percent` AS `Result.percent`,`Result`.`obtained_marks` AS `Result.obtained_marks`,`Result`.`total_marks` AS `Result.total_marks`,`Result`.`total_question` AS `Result.total_question`,`Result`.`total_attempt` AS `Result.total_attempt`,`Result`.`result` AS `Result.result`,`Result`.`start_time` AS `Result.start_time`,`Result`.`end_time` AS `Result.end_time` from  `".$this->wpdb->prefix."emp_exam_results` AS `Result` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Result`.`student_id`)    where `Result`.`id`=".$resultId." AND  `Result`.`student_id`=".$studentId."  AND `Result`.`user_id` > 0 ";
					$this->autoInsert->iFetch($sql,$examDetailCompare);
			$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$resultId." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='R' ";
			$this->autoInsert->iFetchCount($sql,$correctQuestion);
			$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$resultId." AND `ExamStat`.`answered`=1  AND `ExamStat`.`ques_status`='W' ";
			$this->autoInsert->iFetchCount($sql,$incorrectQuestion);
			$leftQuestion=$examDetailCompare['Result.total_attempt']-$correctQuestion-$incorrectQuestion;
			$attemptedQuestion=$correctQuestion+$incorrectQuestion;
			$rank=$this->showRank($UserRank);
			$sql="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_exam_results` AS `Result` WHERE `Result`.`exam_id`=".$examDetailCompare['Result.exam_id']." AND `Result`.`user_id` > 0 ";
					$this->autoInsert->iFetchCount($sql,$totalStudentCount);
			$percentile=round((($totalStudentCount-$rank)/$totalStudentCount)*100,2);
			if($id!=$resultId)
			$compareArr[]=array($examDetailCompare,'correct_question'=>$correctQuestion,'incorrect_question'=>$incorrectQuestion,'left_question'=>$leftQuestion,'attempted_question'=>$attemptedQuestion,'student_id'=>$studentId,'rank'=>$rank,'percentile'=>$percentile);
			$topperData[]=(float) $examDetailCompare['Result.percent'];
			if($id==$resultId)
			{
				$isYou=true;
				$xAxisCategories[]=array("You $UserRank");
			}
			else
			$xAxisCategories[]=array("Topper $UserRank");
			if($k==9)
			break;
		}
		if($isYou==false)
		{
			$xAxisCategories[]=array("You $mainRank");
			$topperData[]=(float) $percent;
		}
		$compareCount=count($compareArr)-1;
		$crReportSeries=json_encode(array(array('name'=>'Percentage(%)','data'=>$topperData)));
		$crxAxis=json_encode($xAxisCategories);
		$cryAxis=__('Percentage(%) in Exam');
		############## End Report     #############
		include('View/UserResults/view.php');
	}
	public function bookmark()
    {
		$studentId=$this->studentId;
		$examResultId=$_REQUEST['examResultId'];
		$quesNo=$_REQUEST['id'];
		$sql="SELECT `ExamStat`.`id` AS `ExamStat.id`,`ExamStat`.`bookmark` AS `ExamStat.bookmark` FROM `".$this->wpdb->prefix."emp_exam_stats` AS `ExamStat` WHERE `ExamStat`.`exam_result_id`=".$examResultId." AND `ExamStat`.`ques_no`=".$quesNo." AND `ExamStat`.`student_id`=".$studentId;
		$this->autoInsert->iFetch($sql,$examStatArr);
		if($examStatArr)
		{
		    $examStatId=$examStatArr['ExamStat.id'];
		    $boomark=$examStatArr['ExamStat.bookmark'];
		    if($boomark=='Y')
		    $boomarkSave=NULL;
		    else
		    $boomarkSave='Y';
		    $this->autoInsert->iUpdateArray($this->wpdb->prefix."emp_exam_stats",array('bookmark'=>$boomarkSave),array('id'=>$examStatId));
		    print$boomarkSave;
		}
    }
	public function certificate()
	{
		$id=$_REQUEST['id'];
		if(!$id)
		{
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		$SQL="SELECT * FROM `".$this->tableName."` AS `ExamResult` WHERE `id`=".$id." AND `student_id`=".$this->studentId;
		$this->autoInsert->iFetch($SQL,$post);
		if(!is_array($post) || !$this->configuration['certificate'])
		{
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		$signature=$this->configuration['signature'];
		require_once 'dompdf/autoload.inc.php';
		// instantiate and use the dompdf class
		ob_start();
		include_once("View/Layouts/pdf_header.php");
		include_once("View/UserResults/certificate.php");
		include_once("View/Layouts/pdf_footer.php");
		$dompdf = new Dompdf();
		$dompdf->loadHtml(ob_get_clean());
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// Render the HTML as PDF
		$dompdf->render();
		$dompdf->stream('Certificate-' . rand());		
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new UserResults;
$obj->$info();
?>