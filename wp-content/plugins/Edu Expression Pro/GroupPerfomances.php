<?php
include_once('ExamApps.php');
include_once('Model/GroupPerfomance.php');
class GroupPerfomances extends GroupPerfomance
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->GroupPerfomance = new GroupPerfomance();
		$this->studentId=get_current_user_id();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_GroupPerfomance';
		$this->url=admin_url('admin.php').'?page=examapp_GroupPerfomance';
	}
	function index()
	{
		$testName=$this->GroupPerfomance->userGroupTestName($this->studentId);
        $performanceData=$this->GroupPerfomance->userPerformance($this->studentId);
        $myPerformanceChartData=$performanceData[0];
        $GroupPerformanceChartData=$performanceData[1];
        $xAxisCategories=array();
        if(is_array($testName))
        {
            foreach($testName as $textValue)
            {
                $xAxisCategories[]=$textValue['name'];
            }
        }
		$groupxAxis=json_encode($xAxisCategories);
        $groupSeries=json_encode(array(array('name'=>__('My Performance'),'data'=>$myPerformanceChartData),
		array('name'=>__('My Group performance'),'data'=>$GroupPerformanceChartData)));	
		include("View/GroupPerfomances/index.php");
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new GroupPerfomances;
$obj->$info();
?>