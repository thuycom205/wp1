<?php
include_once('ExamApps.php');
include_once('Model/LeaderBoard.php');
class LeaderBoards extends LeaderBoard
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->LeaderBoard = new LeaderBoard();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_LeaderBoard';
		$this->url=admin_url('admin.php').'?page=examapp_LeaderBoard';
	}
	function index()
	{
		$sql="SELECT `points`,`student_id`,`exam_given`,`name` FROM (SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `".$this->wpdb->prefix."emp_exam_results` WHERE `student_id` = `ExamResult`.`student_id`)),2) AS `points` ,`student_id`,(SELECT COUNT( `id` ) FROM `".$this->wpdb->prefix."emp_exam_results` WHERE `student_id` = `ExamResult`.`student_id`) AS `exam_given`, `Student`.`display_name` AS `name` FROM `".$this->wpdb->prefix."emp_exam_results` AS `ExamResult`INNER JOIN `".$this->wpdb->prefix."users` AS `Student` ON `ExamResult`.`student_id` = `Student`.`ID` WHERE `finalized_time` IS NOT NULL GROUP BY `student_id`) `Selection` ORDER BY `points` DESC LIMIT 10 ";
		$this->autoInsert->iWhileFetch($sql,$scoreboard);
		include("View/LeaderBoards/index.php");
	}
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new LeaderBoards;
$obj->$info();
?>