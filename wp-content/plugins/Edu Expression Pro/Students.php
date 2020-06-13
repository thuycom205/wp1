<?php
include('ExamApps.php');
include('Model/Student.php');
class Students extends Student
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableNameUser = $wpdb->prefix."users";
		$this->tableNameStudent=$wpdb->prefix."emp_students";
		$this->tableNameUserMeta=$wpdb->prefix."usermeta";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Student = new Student();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Student';
		$this->url=admin_url('admin.php').'?page=examapp_Student';
		$this->Iestudent=admin_url('admin.php').'?page=examapp_Iestudent';
		$this->userGroupWise=$this->ExamApp->userGroupWise();
		$this->globalCondition="LEFT JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON (`Student`.`student_id`=`StudentGroup`.`student_id`) LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`StudentGroup`.`group_id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->userGroupWise." ";
	}
	function index()
	{
		include("View/Students/index.php");
		if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
			$pageNumber=$paginateSetArr['pageNumber'];
			$itemPerPage=$paginateSetArr['itemPerPage'];
			$this->ExamApp->getAdvancedSearch($searchArr,'display_name',$_POST['keyword'],'LIKE');
			$condition=$searchArr['condition'];
			$orderBy=$this->ExamApp->sortedQuery($_POST);
			$SQL = "SELECT `User`.`user_login` AS `userName`,`User`.`Id` AS `id`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`User`.`user_registered` AS `register`,`Student`.`status` AS `status` FROM  `".$this->wpdb->prefix."emp_students` AS `Student` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`)  ".$this->globalCondition.$condition." GROUP BY `User`.`id` ". $orderBy;
			$SQLc = "SELECT COUNT(DISTINCT(`User`.`Id`)) as `count` FROM  `".$this->wpdb->prefix."emp_students` AS `Student` INNER JOIN `".$this->wpdb->prefix."users` AS `User` ON(`Student`.`student_id`=`User`.`ID`)  ".$this->globalCondition.$condition;
			$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`User`.`Id`',$SQLc);
			$result=$resultArr['result'];
			$getTotalRows=$resultArr['getTotalRows'];
			$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
			$paginate=$paginateArr[0];
			$mainSerial=$paginateArr[2];
			include('View/Students/show.php');
			die();
		}
	}
	function add()
	{
		if(isset($_POST['submit']))
		{
			if(isset($_POST['group_name']) && is_array($_POST['group_name']))
			{
				$validatedDataArr=$this->Student->validate($_POST);
				if($validatedDataArr['validatedData'] === false)
				{
					echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
				}
				else 
				{
					$registerDate=$this->ExamApp->currentDateTime();
					if($this->configuration['student_expiry'])
					{
						$renewalDate=$this->ExamApp->currentDate();
						if($_POST['expiry_days']=='')
						$expiryDays=0;
						else
						$expiryDays=$_POST['expiry_days'];
					}
					$password=wp_hash_password($_POST['password']);
					$userArr=array('user_login'=>$_POST['username'],'user_pass'=>$password,'user_login'=>$_POST['username'],'user_nicename'=>$_POST['username'],'user_email'=>$_POST['email'],'user_registered'=>$registerDate,'display_name'=>$_POST['first_name'].' '.$_POST['last_name']);
					$this->autoInsert->iInsert($this->tableNameUser,$userArr);
					$studentId=$this->autoInsert->iLastID();
					$studentArr=array('status'=>'Active','student_id'=>$studentId);
					$this->autoInsert->iInsert($this->tableNameStudent,$studentArr);
					foreach($_POST['group_name'] as $value)
					{
						$this->autoInsert->iInsert($this->wpdb->prefix."emp_student_groups",array('group_id'=>$value,'student_id'=>$studentId));
					}
					$userMeta=array('examapp_phone'=>$_POST['phone'],'examapp_address'=>$_POST['address'],'examapp_alternate_number'=>$_POST['alternate_number'],'examapp_enroll'=>$_POST['enroll'],'examapp_renewal_date'=>$renewalDate,'examapp_expiry_days'=>$expiryDays,'nickname'=>$_POST['username'],
							'first_name'=>$_POST['first_name'],'last_name'=>$_POST['last_name'],'description'=>'','rich_editing'=>'true','comment_shortcuts'=>'false','admin_color'=>'fresh','use_ssl'=>'0','show_admin_bar_front'=>'true','wp_capabilities'=>'','wp_user_level'=>'0','examapp_status_user'=>0);
					foreach($userMeta as $metaKey=>$metaValue)
					{
						if($metaKey=='wp_capabilities')
						{
							$user = new WP_User( $studentId );
							$user->set_role('subscriber');
						}
						else
						{
							update_user_meta($studentId,$metaKey,$metaValue);
						}
					}
					$email=$_POST['email'];$studentName=$_POST['first_name'].' '.$_POST['last_name'];$password=$_POST['password'];
                    $mobileNo=$_POST['phone'];$siteName=get_bloginfo();$userName=$_POST['username'];
					if($this->configuration['email_notification'])
					{                          
						$url=site_url('wp-login.php','login');   
						$sql="SELECT `Emailtemplate`.`name` AS `name`,`Emailtemplate`.`status` AS `status`,`Emailtemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_emailtemplates` AS `Emailtemplate` where `Emailtemplate`.`type`='SLC'";   
						$this->autoInsert->iFetch($sql,$emailSettingArr);
						if($emailSettingArr['status']=="Published")
						{
							$userEmail=$_POST['email'];
							$subject=wp_specialchars_decode($emailSettingArr['name']);
							$message=eval('return "' . addslashes($emailSettingArr['description']). '";');
							add_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
							wp_mail($userEmail,$subject,$message);
							remove_filter('wp_mail_content_type',array($this->ExamApp,'wpdocs_set_html_mail_content_type'));
						}
					}				
					if($this->configuration['sms_notification'])
					{
						$url=site_url();
						$sql="SELECT `Smstemplate`.`name` AS `name`,`Smstemplate`.`status` AS `status`,`Smstemplate`.`description` AS `description` From `".$this->wpdb->prefix."emp_smstemplates` AS `Smstemplate` where `Smstemplate`.`type`='SLC'";   
						$this->autoInsert->iFetch($sql,$smsSettingArr);
						if($smsSettingArr['status']=="Published")
						{
							$mobileNo=$_POST['phone'];
							$message=eval('return "' . addslashes($smsSettingArr['description']). '";');							
							$this->ExamApp->sendSms($mobileNo,$message);
						}
					}
					echo $this->ExamApp->showMessage('Student Added Successfully','success');
					$_POST=array();
				}
			}
			else
			{
				echo $this->ExamApp->showMessage('Please select any group','danger');
			}
		}
		$groupName=$this->ExamApp->getMultipleDropdownDb($_POST['group_name'],$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		include("View/Students/add.php");
	}
	function edit()
	{
		$isError=false;
		if(isset($_POST['submit']))
		{			
			if(is_array($_POST['data']))
			{
				foreach($_POST['data'] as $post)
				{
					if(isset($post['group_name']) && is_array($post['group_name']))
					{
						$id=$post['id'];
						$renewalDate=$post['renewal_date'];
						if($this->configuration['student_expiry'])
						{
							if($post['expiry_days']=='')
							$expiryDays=0;
							else
							$expiryDays=$post['expiry_days'];
						}
						$userArr=array('user_email'=>$post['email'],'display_name'=>$post['first_name'].' '.$post['last_name']);
						$validatedDataArr=$this->Student->validate($post);
						if($validatedDataArr['validatedData'] === false)
						{
							echo $this->ExamApp->showMessage($validatedDataArr['error'],'danger');
							$this->index();
							die(0);
						}
						else
						{
							if($this->autoInsert->iUpdate($this->tableNameUser,$userArr,array('`ID`'=>$id)))
							{
								$isError=false;								
							}
							if(strlen($post['password'])>0)
							wp_set_password($post['password'],$id);
							$userMeta=array('examapp_phone'=>$post['phone'],'examapp_address'=>$post['address'],'examapp_alternate_number'=>$post['alternate_number'],'examapp_enroll'=>$post['enroll'],'examapp_renewal_date'=>$renewalDate,'examapp_expiry_days'=>$expiryDays,'first_name'=>$post['first_name'],'last_name'=>$post['last_name']);
							foreach($userMeta as $metaKey=>$metaValue)
							{
								update_user_meta($id,$metaKey,$metaValue);
							}
							$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_student_groups` WHERE `student_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);					
							foreach($post['group_name'] as $value)
							{
								$this->autoInsert->iInsert($this->wpdb->prefix."emp_student_groups",array('group_id'=>$value,'student_id'=>$id));
							}
						}
					}
					else
					{
						$isError=true;
						echo $this->ExamApp->showMessage('Please select any group','danger');						
					}
				}
			}
			if($isError==false)
			{
				echo $this->ExamApp->showMessage('Student Updated Successfully','success');
				$this->index();
				die(0);
			}
		}
		if (!isset($_REQUEST['id']))
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
			$SQL = "SELECT `User`.`Id` AS `id`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`User`.`user_registered` AS `register`,`User`.`user_login` AS `user_login`,`Student`.`status` AS `status` FROM `".$this->wpdb->prefix."users` AS `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`) WHERE   `User`.`ID`=".$id;
			$this->autoInsert->iFetch($SQL,$record);
			$resultArr[$k]=$record;
			$groupName=$this->ExamApp->getGroupName("emp_student_groups","emp_groups","student_id",$id);
			$groupNameArr=array();
			foreach($groupName as $grp)
			{
				$groupNameArr[]=$grp['id'];
			}
			$groupNameEditArr[$k]=$this->ExamApp->getMultipleDropdownDb($groupNameArr,$this->wpdb->prefix."emp_groups","id","group_name","LEFT JOIN `".$this->wpdb->prefix."emp_user_groups` AS `UserGroup` ON (`PrimaryTable`.`id`=`UserGroup`.`group_id`) WHERE 1=1 ".$this->ExamApp->userGroupWiseIn("`UserGroup`.`group_id`")." GROUP BY `PrimaryTable`.`id`");
		}		
		include("View/Students/edit.php");
	}
	function view()
	{
		try
		{
			$id=$_REQUEST['id'];
			if (!isset($_REQUEST['id']))
			{
			    echo $this->ExamApp->showMessage('Invalid Post','danger');
				$this->index();
				die(0);
			}
			$SQL = "SELECT `Student`.`status` AS `status`,`User`.`user_registered` AS `date`,`User`.`user_login` AS `userName`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`User`.`user_registered` AS `register` FROM `".$this->wpdb->prefix."users` AS `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`) WHERE `User`.`ID`=".$id;
			$this->autoInsert->iFetch($SQL,$post);
			include("View/Students/view.php");
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
					$this->autoInsert->iQuery("DELETE FROM `".$this->wpdb->prefix."emp_student_groups` WHERE `student_id`=".$id.$this->ExamApp->userGroupWiseIn('`group_id`'),$rs);
				}
				if($this->autoInsert->iQuery("DELETE `User` FROM `".$this->tableNameUser."` AS `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`) LEFT JOIN `".$this->wpdb->prefix."emp_student_groups` AS `StudentGroup` ON `User`.`ID` = `StudentGroup`.`student_id` WHERE `StudentGroup`.`id` IS NULL",$rs))
				$this->autoInsert->iDelete($this->tableNameUserMeta,array('`user_id`'=>$id));
				echo $this->ExamApp->showMessage('Student has been deleted','danger');
				$_REQUEST['info']='index';
				$this->index();
			}
		}
		catch (Exception $e)
		{
			echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
	}
	function studentstatus()
	{
		try
		{
			$id=$_GET['id'];
			$SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_students` AS `Student` ".$this->globalCondition." AND `Student`.`student_id`=".$id;
			$this->autoInsert->iFetchCount($SQL,$studentCount);
			if($studentCount==0)
			{
				$redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalid',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
			}			
			$msg="Student has been sucessfully ".$_GET['value'];
			if($_REQUEST['value']=='Active')
			{
				$studentStatus="Active";
				$status=0;
			}
			else
			{
				$studentStatus="Suspend";
				$status=1;
			}
			$recordArr=array('status'=>$studentStatus);
			$this->autoInsert->iUpdateArray($this->tableNameStudent,$recordArr,array('`student_id`'=>$id));
			update_user_meta($id,'examapp_status_user',$status);
			echo $this->ExamApp->showMessage($msg,'success');
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','sstatus',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;		
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}		
	}
	public function wallet()
	{
		try
		{
			$isError=false;
			if(isset($_POST['submit']))
			{
				if(is_array($_POST['data']))
				{ 					 
					foreach($_POST['data'] as $value)
					{
						if($this->ExamApp->WalletInsert($value['id'],$value['amount'],$value['action'],$this->ExamApp->currentDateTime(),"AD",$value['remarks'],get_current_user_id()))
						{
							$isError=false;							
						}
						else
						{
							$isError=true;
							echo $this->ExamApp->showMessage("Invalid Amount",'danger');
						}
					} 
				}
				if($isError==false)
				{
					echo $this->ExamApp->showMessage("Student Wallet has been updated",'success');
					$this->index();
					die(0);
				}
			}
			$id=$_REQUEST['id'];
			$ids=explode(",",$id);
			$resultArr=array();
			foreach($ids as $k=>$id)
			{
				$k++;
				$SQL = "SELECT `User`.`Id` AS `id`,`User`.`display_name` AS `name`,`User`.`user_email` AS `email`,`Wallet`.`balance` AS `balance` FROM `".$this->wpdb->prefix."users` AS `User` INNER JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`) LEFT JOIN `".$this->wpdb->prefix."emp_wallets` AS `Wallet` ON(`User`.`ID`=`Wallet`.`student_id`) WHERE `User`.`ID`=".$id." ORDER BY  `Wallet`.`id` DESC ";
				$this->autoInsert->iFetch($SQL,$record);
				$resultArr[$k]=$record;
			}
			include("View/Students/wallet.php");
		}
		catch (Exception $e)
		{
		    echo $this->ExamApp->showMessage($e->getMessage(),'danger');
		}
    }
    public function trnhistory()
    {
        try
        {
			include("View/Students/trnhistory.php");
			if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			{
				$paginateSetArr=$this->ExamApp->getPaginateSetting($_POST,$this->configuration);
				$pageNumber=$paginateSetArr['pageNumber'];
				$itemPerPage=$paginateSetArr['itemPerPage'];
				$this->ExamApp->getAdvancedSearch($searchArr,'user_email',$_POST['keyword'],'LIKE');
				$condition=$searchArr['condition'];
				$orderBy=$this->ExamApp->sortedQuery($_POST,"Wallet.id");
				$SQL = "SELECT `User`.`user_email`, `Wallet`.`in_amount`, `Wallet`.`out_amount`, `Wallet`.`balance`, `Wallet`.`date`, `Wallet`.`type`, `Wallet`.`remarks` FROM
				`".$this->wpdb->prefix."users` AS `User` Inner JOIN `".$this->wpdb->prefix."emp_wallets` AS `Wallet` ON (`User`.`ID`=`Wallet`.`student_id`)
				Inner JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON (`User`.`ID`=`Student`.`student_id`) ".$this->globalCondition.$condition." GROUP BY `Wallet`.`id` ".$orderBy;
				$SQLc="SELECT COUNT(DISTINCT(`Wallet`.`id`)) as `count` FROM `".$this->wpdb->prefix."users` AS `User`
				Inner JOIN `".$this->wpdb->prefix."emp_wallets` AS `Wallet` ON (`User`.`ID`=`Wallet`.`student_id`)
				Inner JOIN `".$this->wpdb->prefix."emp_students` AS `Student` ON (`User`.`ID`=`Student`.`student_id`) ".$this->globalCondition.$condition;
				$resultArr=$this->ExamApp->getRecordSet($SQL,$itemPerPage,$pageNumber,'`Wallet`.`id`',$SQLc);
				$result=$resultArr['result'];
				$getTotalRows=$resultArr['getTotalRows'];
				$this->ExamApp->paginateFunction($paginateArr,$getTotalRows,$itemPerPage,$pageNumber);
				$paginate=$paginateArr[0];
				$mainSerial=$paginateArr[2];
				$paymentTypeArr=array("AD"=>__('Administrator'),"PG"=>__('Payment Gateway'),"EM"=>__('Pay Exam'));
				include("View/Students/trnhistoryshow.php");
				die();
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
$obj = new Students;
$obj->$info();
?>