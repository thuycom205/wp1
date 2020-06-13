<?php
include_once('ExamApps.php');
include_once('Model/UserTransaction.php');
class UserTransactions extends UserTransaction
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName = $wpdb->prefix."emp_wallets";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->UserTransaction = new UserTransaction();
		$this->autoInsert=new autoInsert();
		$this->studentId=get_current_user_id();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_UserTransaction';
		$this->url=admin_url('admin.php').'?page=examapp_UserTransaction';
		
	}
	function index()
	{
		include("View/UserTransactions/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'remarks',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST);
			$SQL = "SELECT * FROM ".$this->tableName." AS `Wallet` WHERE `Wallet`.`student_id`=".$this->studentId." ORDER BY `Wallet`.`id` DESC";
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Wallet`.`id`');
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber,"No","No");
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			$paymentTypeArr=array("AD"=>__('Administrator'),"PG"=>__('Payment Gateway'),"EM"=>__('Pay Exam'));
			include('View/UserTransactions/show.php');
			die();
		}
	}
	
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new UserTransactions;
$obj->$info();
?>