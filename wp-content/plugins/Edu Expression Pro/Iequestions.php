<?php
ini_set('max_execution_time', 300);
include_once('ExamApps.php');
include_once('Model/Iequestion.php');
include_once('excel/PHPExcel.php');
class Iequestions extends Iequestion
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Iequestion = new Iequestion();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Iequestion';
		$this->url=admin_url('admin.php').'?page=examapp_Iequestion';
		$this->question=admin_url('admin.php').'?page=examapp_Question';
	}
	function index()
	{
		
		$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		$this->ExamApp->getDropdownDb($_POST['subject_id'],$subjectName,$this->wpdb->prefix."emp_subjects","id","subject_name","LEFT JOIN `".$this->wpdb->prefix."emp_subject_groups` AS `SubjectGroup` ON (`PrimaryTable`.`id`=`SubjectGroup`.`subject_id`) WHERE 1 ".$this->ExamApp->userGroupWiseIn("`SubjectGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		include("View/Iequestions/index.php");
	}
	public function import()
    {
        try
        {
			if(isset($_POST['submit']))
			{
				if(is_array($_POST['group_name']))
				{
					if(strlen($_POST['subject_id'])>0)
					{
						$fixed = array('Iequestion' => array('subject_id' =>$_POST['subject_id']));
						$groupName=$_POST['group_name'];
						$filename = null;$extension=null;
						$extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
						if($extension=="xls")
						{
							if (!empty($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
							{
								$filename = basename($_FILES['file']['name']);
								$tmpPath=plugin_dir_path( __FILE__ ).'tmp/'.$filename;
								move_uploaded_file($_FILES['file']['tmp_name'],$tmpPath);
								$inputFileType = 'Excel5';
								$inputFileName = $tmpPath;
								try
								{
									/**  Create a new Reader of the type defined in $inputFileType  **/
									$objReader = PHPExcel_IOFactory::createReader($inputFileType);
									/**  Advise the Reader that we only want to load cell data  **/
									$objReader->setReadDataOnly(true);
									/**  Load $inputFileName to a PHPExcel Object  **/
									$objPHPExcel = $objReader->load($inputFileName);									
								}
								catch(Exception $e)
								{
									die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
								}
								//  Get worksheet dimensions
								$sheet = $objPHPExcel->getSheet(0); 
								$highestRow = $sheet->getHighestRow(); 
								$highestColumn = $sheet->getHighestColumn();
								
								//  Loop through each row of the worksheet in turn
								for($row = 1; $row <= $highestRow; $row++)
								{
									//  Read a row of data into an array
									if($row>1)
									$rowData[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);				
								}
								if($this->Iequestion->importInsert($rowData,$_POST['group_name'],$_POST['subject_id']))
								echo $this->ExamApp->showMessage("Questions imported successfully","success");
								else
								echo $this->ExamApp->showMessage("File not uploaded","danger");
								$_POST=array();
								if(file_exists($tmpPath))
								unlink($tmpPath);
								$this->index();
					            die();
							}
							else
							{
								echo $this->ExamApp->showMessage("File not uploaded","danger");
								$this->index();
					            die();
							}
						}
						else
						{
							echo $this->ExamApp->showMessage("Only Excel File supported","danger");
							$this->index();
					        die();							
						}
					}
					else
					{
						echo $this->ExamApp->showMessage("Please Select Subject","danger");
						$this->index();
						die();
					}
				}
				else
				{
					echo $this->ExamApp->showMessage("Please Select any Group","danger");
					$this->index();
					die();
				}
			}
			else
			{
				$this->index();
				die();
			}
		}
        catch (Exception $e)
        {
			echo $this->ExamApp->showMessage($e->getMessage(),"danger");
        }
    }	
    public function export()
    {
        try
        {
			$dataArray=$this->Iequestion->exportData($this->ExamApp->userGroupWise());
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->fromArray($dataArray, null, 'A1');
			$objPHPExcel->getProperties()->setCreator(get_bloginfo());
			$objPHPExcel->getProperties()->setLastModifiedBy(get_bloginfo());
			$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Question");
			$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Question");
			$objPHPExcel->getProperties()->setDescription("Question for Office 2007 XLSX, generated using PHP classes.");
			$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
			$objPHPExcel->getProperties()->setCategory("Question file");
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="question.xls"');
			header('Cache-Control: max-age=0');
			// Do your stuff here
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			$objWriter->save('php://output');
        }
        catch (Exception $e)
        {
            echo $this->ExamApp->showMessage($e->getMessage(),"danger");
        }
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Iequestions;
$obj->$info();
?>