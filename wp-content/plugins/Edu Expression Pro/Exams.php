<?php
ini_set('max_execution_time', 3000);
include('ExamApps.php');
include('Model/Exam.php');
include_once("tinyMce.class.php");
// reference the Dompdf namespace
use Dompdf\Dompdf;
class Exams extends Exam
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_exams";
		$this->tableExamPrep=$wpdb->prefix."emp_exam_preps";
		$this->tableMaxQuestion=$wpdb->prefix."emp_exam_maxquestions";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Exam = new Exam();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Exam';
		$this->url=admin_url('admin.php').'?page=examapp_Exam';
		$this->urlResult=admin_url('admin.php').'?page=examapp_Result';
		$this->urlAttemptedpapers=admin_url('admin.php').'?page=examapp_Attemptedpaper';
		$this->urlAquestions=admin_url('admin.php').'?page=examapp_Addquestion';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->globalCondition="LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON (`Exam`.`id`=`ExamGroup`.`exam_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`ExamGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ";
	}
	function index()
	{
		try
		{
			include("View/Exams/index.php");			
			if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			{
				$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
				$pageNumber=$paginateSetArr['pageNumber'];
				$itemPerPage=$paginateSetArr['itemPerPage'];
				$this->ExamApp->getAdvancedSearch($searchArr,'name',$_POST['keyword'],'LIKE');
				$condition=$searchArr['condition'];
				$orderBy=$this->ExamApp->sortedQuery($_POST);
				$SQL = "SELECT `Exam`.`id` as `id`,`Exam`.`name` as `name`,`Exam`.`start_date` as `start_date`,`Exam`.`end_date` as `end_date`,`Exam`.`type` as `type`,`Exam`.`status` as `status` FROM `".$this->tableName."` AS `Exam` ".$this->globalCondition.$condition." GROUP BY `Exam`.`id` ".$orderBy;
				$SQLc = "SELECT COUNT(DISTINCT(`Exam`.`id`)) as `count` FROM `".$this->tableName."` AS `Exam` ".$this->globalCondition.$condition;
				$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Exam`.`id`',$SQLc);
				$result=$resultArr['result'];
				$getTotalRows=$resultArr['getTotalRows'];
				$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
				$paginate=$paginateArr[0];
				$mainSerial=$paginateArr[2];
				include('View/Exams/show.php');
				die();
			}
		}
		catch (Exception $e)

		{
		    var_dump($e->getMessage());
		   // echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	function add()
	{
		try
		{
			if(isset($_POST['submit']))
			{
				if(strtotime($_POST['end_date'])<strtotime($_POST['start_date']))
				{
					echo $this->ExamApp->showMessage('End Date is not less than Start date','danger');
				}
				elseif(!is_array($_POST['group_name']))
				{
					echo $this->ExamApp->showMessage('Please Select any group','danger');
				}
				elseif($_POST['type']=="Prepration" && !isset($_POST['data']['ExamPrep']))
				{
					echo $this->ExamApp->showMessage('Please Add Subject To Exam','danger');
				}
				else
				{
					if($_POST['start_date']!=null)
					$_POST['start_date']=$this->ExamApp->dateTimeFormatBeforeSave($_POST['start_date']);
					if($_POST['end_date']!=null)
					$_POST['end_date']=$this->ExamApp->dateTimeFormatBeforeSave($_POST['end_date']);
					$validatedDataArr=$this->Exam->validate($_POST);
					if($validatedDataArr['validatedData'] === false)
					{
						echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
					}
					else
					{
						if($this->configuration['exam_expiry']==1 && $_POST['expiry']==NULL)
						$_POST['expiry']=0;
						$this->autoInsert->iInsert($this->tableName,$validatedDataArr['validatedData']);
						$examId=$this->autoInsert->iLastID();
						foreach($_POST['group_name'] as $value)
						{
							$this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_groups",array('group_id'=>$value,'exam_id'=>$examId));
						}
						if(is_array($_POST['data']['ExamPrep']))
						{
							foreach($_POST['data']['ExamPrep'] as $value)
							{	
								$examPrep=array();
								$maxQuestion=array();
								
								$examPrep=array('exam_id'=>$examId,'subject_id'=>$value['subject_id'],'ques_no'=>$value['ques_no'],'type'=>$value['type'],'level'=>$value['level']);
								$this->autoInsert->iInsert($this->tableExamPrep,$examPrep);
								if($value['max_question'])
								{
									$maxQuestion=array('exam_id'=>$examId,'subject_id'=>$value['subject_id'],'max_question'=>$value['max_question']);
								}
								else
								{
									$maxQuestion=array('exam_id'=>$examId,'subject_id'=>$value['subject_id']);
								}
								$this->autoInsert->iInsert($this->tableMaxQuestion,$maxQuestion);								
							}
							echo $this->ExamApp->showMessage('Exam Added Successfully','success');
							$_POST=array();
						}
						else
						{							
							?>
						<script>
						location.href="<?php echo $this->urlAquestions;?>&examId=<?php echo$examId;?>&msg=success";
						</script>	
						<?php }
						
					}	
				}
			}
			$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
			$this->ExamApp->getDropdownDb($_POST['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name","LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`PrimaryTable`.`id`=`SubjectGroup`.`subject_id`) WHERE 1 ".$this->ExamApp->userGroupWiseIn("`SubjectGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
			$this->ExamApp->getDropdownDb($_POST['level'],$diffName,$this->wpdb->prefix."emp_diffs","id","diff_level");
			$this->ExamApp->getDropdownDb($_POST['type'],$qtypeName,$this->wpdb->prefix."emp_qtypes","id","question_type");
			$mathEditor=$this->configuration['math_editor'];
			include("View/Exams/add.php");
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	function edit()
	{
		try
		{
			$isError=false;
			if(isset($_POST['submit']))
			{
				if(is_array($_POST['data']))
				{
					foreach($_POST['data'] as $post)
					{
						if($post['start_date']!=null)
						$post['start_date']=$this->ExamApp->dateTimeFormatBeforeSave($post['start_date']);
						if($post['end_date']!=null)
						$post['end_date']=$this->ExamApp->dateTimeFormatBeforeSave($post['end_date']);
						if(strtotime($post['end_date'])<strtotime($post['start_date']))
						{
							$isError=true;
							echo $this->ExamApp->showMessage('End Date is not less than Start date','danger');
						}
						elseif(!isset($post['group_name']) || !is_array($post['group_name']))
						{
							$isError=true;
							echo $this->ExamApp->showMessage('Please select any group','danger');
						}
						else
						{
							$id=$post['id'];
							$validatedDataArr=$this->Exam->validate($post);
							if($validatedDataArr['validatedData'] === false)
							{
								echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
								$isError=true;
							}
							else
							{
								if($this->configuration['exam_expiry']==1 && $post['expiry']==NULL)
								$post['expiry']=0;
								if($this->autoInsert->iUpdate($this->tableName,$validatedDataArr['validatedData'],array('`id`'=>$id)))
								{
									$isError=false;
								}
								$examId=$id;
								$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_exam_groups` WHERE `exam_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
								foreach($post['group_name'] as $value)
								{
									$this->autoInsert->iInsert($this->wpdb->prefix."emp_exam_groups",array('group_id'=>$value,'exam_id'=>$examId));
								}								
							}
						}
					}					
				}
				if($isError==false)
				{
					echo $this->ExamApp->showMessage('Exam Updated Successfully','success');
					$this->index();
					die(0);
				}
			}
			if(!isset($_REQUEST['id']))
			{
				echo $this->ExamApp->showMessage('Invalid Post','danger');
				$this->index();
				die(0);
			}
			$ids=explode(",",$_REQUEST['id']);
			$resultArr=array();
			foreach($ids as $k=>$id)
			{
				$k++;
				$SQL = "select *,`Exam`.`id` as `id` from `".$this->tableName."` AS `Exam` ".$this->globalCondition." AND `Exam`.`id`=".$id;
				$this->autoInsert->iFetch($SQL,$record);
				$resultArr[$k]=$record;
				$groupName=$this->ExamApp->getGroupName("emp_exam_groups","emp_groups","exam_id",$id);
				$groupNameArr=array();
				foreach($groupName as $grp)
				{
					$groupNameArr[]=$grp['id'];
				}
				$groupNameEditArr[$k]=$this->ExamApp->getMultipleDropdownDb($groupNameArr,$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
			}
			$mathEditor=$this->configuration['math_editor'];
			include("View/Exams/edit.php");
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	function deleteall()
	{
		try
		{
			if (!isset($_POST['id']))
			{
				echo $this->ExamApp->showMessage('Invalid Post','danger');
				$this->index();
				die(0);
			}
			if(is_array($_POST['id']))
			{
				foreach($_POST['id'] as $id)
				{
					$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_exam_groups` WHERE `exam_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
				}
				$this->autoInsert->iQuery("DELETE `Exam` FROM `".$this->tableName."` AS `Exam` LEFT JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON `Exam`.`id` = `ExamGroup`.`exam_id` WHERE `ExamGroup`.`id` IS NULL",$rs);
				echo $this->ExamApp->showMessage('Exam has been deleted','danger');
				$_REQUEST['info']='index';
				$this->index();
			}
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	function view()
	{
		try
		{
			$id=$_GET['id'];
			$resultArr=array();
			$SQL = "SELECT *,`Exam`.`id` as `id` FROM ".$this->tableName." AS `Exam` ".$this->globalCondition." AND `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($SQL,$resultArr);
			if(!$resultArr)
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$SubjectDetail="";$DiffLevel="";
			$sqlExamCount = "SELECT count(Exam.id) AS `count` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` ON (`Exam`.`id`=`ExamMaxquestion`.`exam_id`) WHERE  `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($sqlExamCount,$examCount);
			$totalMarks=500;
			$totalMarks=$this->Exam->totalMarks($id);
			if($resultArr['type']=="Exam")
			{
				$SubjectDetail=array();
				$chartData=array();
				$SQLTotalQuestion = "SELECT count(id) as id FROM ".$this->wpdb->prefix."emp_exam_questions WHERE exam_id=".$id;
				$this->autoInsert->iFetch($SQLTotalQuestion,$TotalQuestion1);
				$TotalQuestion=$TotalQuestion1['id'];
				$sqlSubjectDetail = "SELECT `Question`.`subject_id`,`subject`.`subject_name`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` INNER JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`Question`.`id`=`ExamQuestion`.`question_id`) INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Subject`.`id`=`Question`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` ON (`ExamQuestion`.`exam_id`=`ExamMaxquestion`.`exam_id` and `Subject`.`id`=`ExamMaxquestion`.`subject_id`) WHERE  `ExamQuestion`.`exam_id`=".$id."  GROUP BY `Question`.`subject_id`";
				$this->autoInsert->iWhileFetch($sqlSubjectDetail,$SubjectDetail);
				$SQLDiffLevel = "SELECT * FROM ".$this->wpdb->prefix."emp_diffs";
				$this->autoInsert->iWhileFetch($SQLDiffLevel,$DiffLevel1);
				$DiffLevel=$DiffLevel1;
				foreach($SubjectDetail as $value)
				{
					$subject_id=$value['subject_id'];
					$subject_name=$value['subject_name'];
					$QuestionDetail[$subject_name][]=$this->viewquestiontype($id,$subject_id,'S');
					$QuestionDetail[$subject_name][]=$this->viewquestiontype($id,$subject_id,'M');
					$QuestionDetail[$subject_name][]=$this->viewquestiontype($id,$subject_id,'T');
					$QuestionDetail[$subject_name][]=$this->viewquestiontype($id,$subject_id,'F');
					$DifficultyDetail[$subject_name][]=$this->viewdifftype($id,$subject_id,'E');
					$DifficultyDetail[$subject_name][]=$this->viewdifftype($id,$subject_id,'M');
					$DifficultyDetail[$subject_name][]=$this->viewdifftype($id,$subject_id,'D');
					$j=0;
					foreach($DiffLevel as $diff)
					{
						$tot_ques=(float) $DifficultyDetail[$subject_name][$j];
						$chartData[]=array($diff['diff_level'],$tot_ques);
						$j++;
					}	
				}				
			}
			else
			{
				$sqlQuestionArr="SELECT sum(`ExamPrep`.`ques_no`) As `total` from `".$this->wpdb->prefix."emp_exam_preps` As `ExamPrep` where `ExamPrep`.`exam_id`=".$id;
				$this->autoInsert->iFetch($sqlQuestionArr,$TotalQuestionArr);
				$sqlSubjectPrepAll = "SELECT `Subject`.`subject_name`,`ExamPrep`.`ques_no`,`ExamPrep`.`type`,`ExamPrep`.`level`,`ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_preps` AS `ExamPrep` INNER JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Subject`.`id`=`ExamPrep`.`subject_id`) LEFT JOIN `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` ON (`ExamPrep`.`exam_id`=`ExamMaxquestion`.`exam_id` and `ExamPrep`.`subject_id`=`ExamMaxquestion`.`subject_id`) WHERE  `ExamPrep`.`exam_id`=".$id;
				$this->autoInsert->iWhileFetch($sqlSubjectPrepAll,$subjectPrepAll);
				$SubjectDetail=array();
				$chartData=array();
				foreach($subjectPrepAll as $value)
				{
					$subjectName=$value['subject_name'];
					$totalQuestion=(int) $value['ques_no'];
					$chartData[]=array($subjectName,$totalQuestion);
					foreach(explode(",",$value['type']) as $examType)
					{
						$sqlqtype="select  `Qtype`.`question_type` As `question_type` from `".$this->wpdb->prefix."emp_qtypes` As `Qtype` where `Qtype`.`id`=".$examType;
						$this->autoInsert->iFetch($sqlqtype,$qtypeArr);
						$qtype[]=$qtypeArr['question_type'];
					}
					$questionType=implode(" | ",$qtype);
					unset($examType,$qtype);
					foreach(explode(",",$value['level']) as $examType)
					{
						$sqldtype="select  `Diff`.`diff_level` As `diff_level` from `".$this->wpdb->prefix."emp_diffs` As `Diff` where `Diff`.`id`=".$examType;
						$this->autoInsert->iFetch($sqldtype,$qtypeArr);
						$qtype[]=$qtypeArr['diff_level'];
					}
					$levelType=implode(" | ",$qtype);
					unset($examType,$qtype);
					$SubjectDetail[]=array('Subject'=>$subjectName,'Type'=>$questionType,'Level'=>$levelType,'QuesNo'=>$value['ques_no'],'MaxQuestion'=>$value['max_question']);
					unset($questionType,$levelType);
				}
				$TotalQuestion=$TotalQuestionArr['total'];
				$prepTitle=__('Subject Wise Question Count');
				$prepSeries=json_encode(array(array('name'=>__('Total Question'),'data'=>$chartData)));				
			}
		}
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
		include("View/Exams/view.php");
	}
	function activateexam()
	{
		try
		{
			$id=$_GET['id'];
			$resultArr=array();
			$SQL = "SELECT * FROM ".$this->tableName." AS `Exam` ".$this->globalCondition." AND `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($SQL,$resultArr);
			if(!$resultArr)
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			if($_GET['value']=="Inactive")
			{
				$recordArr=array('status'=>'Inactive');		
				$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>$id));	
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','inactive',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			else
			{
				$recordArr=array('status'=>'Active','user_id'=>0,'finalized_time'=>NULL);		
				$this->autoInsert->iUpdateArray($this->tableName,$recordArr,array('`id`'=>$id));
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','aenotif',$redirectUrl);
				$redirectUrl=add_query_arg('id',$id,$redirectUrl);
				$redirectUrl=add_query_arg('offset','0',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}	
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}		
	}
	private function viewquestiontype($id,$subject_id,$type)
        {
		try
		{		
			$SQL="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_questions` AS `Question` Inner JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON (`Question`.`qtype_id`=`Qtype`.`id`) Inner JOIN `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` ON (`Question`.`id`=`ExamQuestion`.`question_id`) WHERE `ExamQuestion`.`exam_id` = $id AND `Question`.`subject_id` = $subject_id AND `Qtype`.`type` = '$type'";
			$this->autoInsert->iFetch($SQL,$record);
			return($record['count']);
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	private function viewdifftype($id,$subject_id,$type)
	{
		try
		{
			$SQL="SELECT COUNT(*) AS `count` FROM `".$this->wpdb->prefix."emp_questions` AS `Question` Inner JOIN `".$this->wpdb->prefix."emp_diffs` AS `Diff` ON (`Question`.`diff_id`=`Diff`.`id`) Inner JOIN `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` ON (`Question`.`id`=`ExamQuestion`.`question_id`) WHERE `ExamQuestion`.`exam_id` = $id AND `Question`.`subject_id` = $subject_id AND `Diff`.`type` = '$type'";
			$this->autoInsert->iFetch($SQL,$record);
			return($record['count']);
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	public function  maxquestion()
	{
		try
		{
			$id=$_REQUEST['id'];
			$examId=$_REQUEST['id'];
			$SQL = "SELECT `Subject`.`id` AS subject_id, `Subject`.`subject_name`, `ExamMaxquestion`.`id`, `ExamMaxquestion`.`max_question` FROM `".$this->wpdb->prefix."emp_exam_questions` AS `ExamQuestion` Inner JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON (`Question`.`id`=`ExamQuestion`.`question_id`) Inner JOIN `".$this->wpdb->prefix."emp_subjects` AS `Subject` ON (`Subject`.`id`=`Question`.`subject_id`) Left JOIN `".$this->wpdb->prefix."emp_exam_maxquestions` AS `ExamMaxquestion` ON (`ExamQuestion`.`exam_id`=`ExamMaxquestion`.`exam_id` AND `Subject`.`id`=`ExamMaxquestion`.`subject_id`) WHERE `ExamQuestion`.`exam_id` =".$id." GROUP BY `Question`.`subject_id`";	
			$this->autoInsert->iwhileFetch($SQL,$record);
			$post=$record;
			if(!$post){$msg=$this->ExamApp->showMessage('There are no question added !','danger');}
			if(is_array($_POST['data']))
			{
				foreach($_POST['data'] as $value)
				{
					$examMaxQuestionId=$value['id'];
					if($examMaxQuestionId>0)
					{
						$this->autoInsert->iUpdate($this->tableMaxQuestion,$value,array('id'=>$examMaxQuestionId));
					}
					else
					{
						$this->autoInsert->iInsert($this->tableMaxQuestion,$value);
					}
				}
				echo $this->ExamApp->showMessage('Maximum attempt question has been saved','success');
				$_REQUEST['info']='index';
				$this->index();
				die(0);
			}	
			include("View/Exams/maxquestion.php");
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
        }
	public function stats()
        {
               try
		{
			$id=$_GET['id'];
			$sql="SELECT *,`Exam`.`id` AS `id` from `".$this->tableName."` AS `Exam` ".$this->globalCondition." AND `Status`='Closed' AND `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($sql,$post);
			if(!$post)
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$examStats=$this->Exam->examStats($id);
			include("View/Exams/stats.php");
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
        }
       public function downloadlist()
       {
               try
		{        
			$id=$_REQUEST['id'];
			$type=$_REQUEST['type'];
			if (!$id)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$sql="select * from `".$this->wpdb->prefix."emp_exams` AS `Exam` where `Exam`.`status`='Closed' and `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($sql,$post);
			if (!$post)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$examResult=$this->Exam->examAttendance($id,$type);
			require_once 'dompdf/autoload.inc.php';
			// instantiate and use the dompdf class
			ob_start();
			include_once("View/Layouts/pdf_header.php");
			include_once("View/Exams/downloadlist.php");
			include_once("View/Layouts/pdf_footer.php");
			$dompdf = new Dompdf();
			$dompdf->loadHtml(ob_get_clean());
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'landscape');
			// Render the HTML as PDF
			$dompdf->render();
			$dompdf->stream('Student-' . rand());
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
    }
    public function downloadabsentlist()
    {
        try
		{
			$id=$_REQUEST['id'];
			$type=$_REQUEST['type'];
			if (!$id)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$sql="select * from `".$this->wpdb->prefix."emp_exams` AS `Exam` where `Exam`.`status`='Closed' and `Exam`.`id`=".$id;
			$this->autoInsert->iFetch($sql,$post);
			if (!$post)
			{
				$redirectUrl=$this->ajaxUrl;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
			$examResult=$this->Exam->examAbsent($id,$type);
			require_once 'dompdf/autoload.inc.php';
			// instantiate and use the dompdf class
			ob_start();
			include_once("View/Layouts/pdf_header.php");
			include_once("View/Exams/downloadabsentlist.php");
			include_once("View/Layouts/pdf_footer.php");
			$dompdf = new Dompdf();
			$dompdf->loadHtml(ob_get_clean());
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'landscape');
			// Render the HTML as PDF
			$dompdf->render();
			$dompdf->stream('Absent-Student-' . rand());
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),'danger');
        }
    }
    public function aenotif()
    {
        try
		{
			$id=$_REQUEST['id'];
			$offset=$_REQUEST['offset'];
			if($this->configuration['email_notification'] || $this->configuration['sms_notification'])
			{
				$SQL = "SELECT count(id) as id FROM ".$this->wpdb->prefix."emp_exams WHERE status='Active' AND id=".$id;
				$this->autoInsert->iFetch($SQL,$examCount);
				if($examCount['id']=='0')
				{
					$redirectUrl=$this->url;
					$redirectUrl=add_query_arg('info','index',$redirectUrl);
					$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
				}
				$limit=10;
				$sql="SELECT count(`Exam`.`id`) AS `count`,`Exam`.`name` AS `name`,`Exam`.`start_date` AS `start_date`,`Exam`.`end_date` AS `end_date`,`Exam`.`type` AS `type` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`)
				INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)  INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`Student`.`student_id`=`StudentGroup`.`student_id`)   WHERE
				`Exam`.`status`='Active'  AND `Exam`.`id`=".$id." AND  `Student`.`status`='Active' GROUP BY `StudentGroup`.`student_id` ORDER BY `Student`.`student_id` ASC ";
				$this->autoInsert->iFetch($sql,$numRows);
				
				$sql="SELECT `User`.`display_name` AS `student_name`,`User`.`user_email` AS `email`,`Student`.`student_id` AS `id`,`Exam`.`name` AS `name`,`Exam`.`start_date` AS `start_date`,`Exam`.`end_date` AS `end_date`,`Exam`.`type` AS `type` FROM `".$this->wpdb->prefix."emp_exams` AS `Exam` INNER JOIN `".$this->wpdb->prefix."emp_exam_groups` AS `ExamGroup` ON(`Exam`.`id`=`ExamGroup`.`exam_id`)
				INNER JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON(`StudentGroup`.`group_id`=`ExamGroup`.`group_id`)  INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`Student`.`student_id`=`StudentGroup`.`student_id`)  INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Student`.`student_id`) WHERE
				`Exam`.`status`='Active'  AND `Exam`.`id`=".$id." AND  `Student`.`status`='Active' GROUP BY `StudentGroup`.`student_id` ORDER BY `Student`.`student_id` ASC LIMIT ".$offset.",".$limit;
				$this->autoInsert->iWhileFetch($sql,$post);
				foreach($post as $value)
				{
					$email=$value['email'];$studentName=$value['student_name'];$mobileNo=get_user_meta($value['id'],'examapp_phone',true);
					$startDate=$this->ExamApp->dateFormat($value['start_date']);
					$endDate=$this->ExamApp->dateFormat($value['end_date']);
					$examName=$value['name'];$type=$value['type'];$siteName=get_bloginfo();
					if($this->configuration['email_notification'])
					{
						$url=site_url('wp-login.php','login');	
						$sql="SELECT `Emailtemplate`.`name` AS `name`,`Emailtemplate`.`status` AS `status`,`Emailtemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_emailtemplates` AS `Emailtemplate` where `Emailtemplate`.`type`='EAN'";   
						$this->autoInsert->iFetch($sql,$emailSettingArr);										
						$userEmail=$email;
						$subject=wp_specialchars_decode($emailSettingArr['name']);
						$message=eval('return "' . addslashes($emailSettingArr['description']). '";');
						add_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
						wp_mail($userEmail,$subject,$message);
						remove_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
					}
					if($this->configuration['sms_notification'])
					{
						$url=site_url();
						$sql="SELECT `Smstemplate`.`name` AS `name`,`Smstemplate`.`status` AS `status`,`Smstemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_smstemplates` AS `Smstemplate` where `Smstemplate`.`type`='EAN'";   
						$this->autoInsert->iFetch($sql,$smsSettingArr);
						$mobileNo=$_POST['phone'];
						$message=eval('return "' . addslashes($smsSettingArr['description']). '";');							
						$this->ExamApp->sendSms($mobileNo,$message);
					}
				}
				$offset=$offset+$limit;
				if($numRows['count']>$offset)
				{
					$redirectUrl=$this->ajaxUrl;
					$redirectUrl=add_query_arg('info','aenotif',$redirectUrl);
					$redirectUrl=add_query_arg('id',$id,$redirectUrl);
					$redirectUrl=add_query_arg('offset',$offset,$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
				}
				else
				{
					$redirectUrl=$this->url;
					$redirectUrl=add_query_arg('info','index',$redirectUrl);
					$redirectUrl=add_query_arg('msg','active',$redirectUrl);
					wp_redirect($redirectUrl);
					exit;
				}
			}
			else
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','active',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}
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
$obj = new Exams;
$obj->$info();
?>