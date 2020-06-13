<?php
include_once('ExamApps.php');
include_once('Model/Payment.php');
include_once('Paypal/Lib/Paypal.php');
class Payments extends Payment
{
	function __construct()
	{
		global $wpdb;
		$this->wpdb=$wpdb;
		$this->tableName=$wpdb->prefix."emp_configurations";
		$this->ExamApp = new ExamApps();
		$this->configuration=$this->ExamApp->configuration();
		$this->Payment = new Payment();
		$this->autoInsert=new autoInsert();
		$this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Payment';
		$this->url=admin_url('admin.php').'?page=examapp_Payment';
		$this->studentId=$this->ExamApp->getCurrentUserId();
		$this->Paypal=new Paypal();
		$SQL="SELECT * FROM `".$this->wpdb->prefix."emp_paypal_configs` AS `Paypal` WHERE `id`=1";
		$this->autoInsert->iFetch($SQL,$paySetting);
		if(strlen($paySetting['username'])==0 || strlen($paySetting['password'])==0 || strlen($paySetting['signature'])==0)
        {
			$redirectUrl=admin_url('admin.php').'?page=examapp_UserDashboard&info=index&msg=invalidpaypal';
			?><script>window.location='<?php echo$redirectUrl;?>';</script><?
			exit;
        }
		$this->currencyType=$this->ExamApp->getCurrencyCode();
        if($paySetting['sandbox_mode']==1)
        $sandboxMode=true;
        else
        $sandboxMode=false;        
        $this->Paypal = new Paypal(array(
                                         'sandboxMode' => $sandboxMode,
                                         'nvpUsername' => $paySetting['username'],
                                         'nvpPassword' => $paySetting['password'],
                                         'nvpSignature' => $paySetting['signature']
                                         ));
	}
	public function index()
	{
		$id=$_REQUEST['id'];
		if(isset($_REQUEST['token']))
		{
			echo $this->ExamApp->showMessage("Payment Cancel",'danger');
		}
		include("view/Payments/index.php");
	}
    public function checkout()
    {
        $description=$_POST['remarks'];
        $amount=$_POST['amount'];
        if($amount>0)
        {
            $returnUrl=$this->ajaxUrl.'&info=postpayment';
            $cancelUrl=$this->url.'&info=index';
            $order = array(
            'description' => $description,
            'currency' => $this->currencyType,
            'return' => $returnUrl,
            'cancel' => $cancelUrl,
            'items' => array(
                0 => array(
                    'name' =>__('Wallet Payment'),
                    'tax' => 0.00,
                    'shipping' => 0.00,
                    'description' => $description,
                    'subtotal' => $amount,
                ),
                )
            );
            try
            {
                $token=$this->Paypal->setExpressCheckout($order);
				wp_redirect($token);
				exit;
            }
            catch (Exception $e)
            {
                $redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
            }
            catch (Exception $e)
            {
                $redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
            }
            $redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
        else
        {
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalidamount',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
        }
    }
    public function postpayment($id=null)
    {
        if(isset($_REQUEST['token']) && isset($_REQUEST['PayerID']))
        {
            $token=$_REQUEST['token'];
            try
            {
                $detailsArr=$this->Paypal->getExpressCheckoutDetails($token);
                if(is_array($detailsArr))
                {
                    $amount=$detailsArr['AMT'];
                    $description=$detailsArr['DESC'];
                    $payerId=$_REQUEST['PayerID'];
                    if($detailsArr['ACK']=="Success")
                    {
                        $order = array(
                        'description' => $description,
                        'currency' => $this->currencyType,
                        'return' => $this->ajaxUrl.'&info=postpayment',
                        'cancel' => $this->url.'&info=index',
                        'items' => array(
                            0 => array(
                                'name' =>__('Wallet Payment'),
                                'tax' => 0.00,
                                'shipping' => 0.00,
                                'description' => $description,
                                'subtotal' => $amount,
                            ),
                            )
                        );
                        try
                        {
                            $paymentDetails=$this->Paypal->doExpressCheckoutPayment($order,$token,$payerId);
                            if(is_array($paymentDetails))
                            {
                                if($paymentDetails['PAYMENTINFO_0_PAYMENTSTATUS']=="Completed" && $paymentDetails['PAYMENTINFO_0_ACK']=="Success")
                                {
                                    $transactionId=$paymentDetails['PAYMENTINFO_0_TRANSACTIONID'];
									$SQL="SELECT COUNT(*) as `count` FROM `".$this->wpdb->prefix."emp_payments` AS `Payment` WHERE `Payment`.`transaction_id`=".$transactionId;
									$this->autoInsert->iFetchCount($SQL,$$total);
                                    if($total==0)
                                    {
                                        $recordArr=array('student_id'=>$this->studentId,'transaction_id'=>$transactionId,'amount'=>$amount,'remarks'=>$description);
                                        $this->autoInsert->iInsert($this->wpdb->prefix."emp_payments",$recordArr,'Yes');
										$this->ExamApp->WalletInsert($this->studentId,$amount,"Added",$this->currentDateTime(),"PG",$description);
                                        $redirectUrl=admin_url('admin.php').'?page=examapp_UserTransaction';;
										$redirectUrl=add_query_arg('info','index',$redirectUrl);
										$redirectUrl=add_query_arg('msg','paysucess',$redirectUrl);
										wp_redirect($redirectUrl);
										exit;
                                    }
                                    else
                                    {
										$redirectUrl=$this->url;
										$redirectUrl=add_query_arg('info','index',$redirectUrl);
										$redirectUrl=add_query_arg('msg','payadone',$redirectUrl);
										wp_redirect($redirectUrl);
										exit;
                                    }
                                }
                            }
                        }
                        catch(PaypalRedirectException $e)
                        {
                            $redirectUrl=$this->url;
							$redirectUrl=add_query_arg('info','index',$redirectUrl);
							$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
							wp_redirect($redirectUrl);
							exit;
                        }
                        catch (Exception $e)
                        {
                            $redirectUrl=$this->url;
							$redirectUrl=add_query_arg('info','index',$redirectUrl);
							$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
							wp_redirect($redirectUrl);
							exit;
                        }                        
                    }
                    else
                    {
						$redirectUrl=$this->url;
						$redirectUrl=add_query_arg('info','index',$redirectUrl);
						$redirectUrl=add_query_arg('msg','invalidpayment',$redirectUrl);
						wp_redirect($redirectUrl);
						exit;
                    }
                }                
            }
            catch (Exception $e)
            {
                $redirectUrl=$this->url;
				$redirectUrl=add_query_arg('info','index',$redirectUrl);
				$redirectUrl=add_query_arg('msg','invalidconnect',$redirectUrl);
				wp_redirect($redirectUrl);
				exit;
            }
        }
        $redirectUrl=$this->url;
		$redirectUrl=add_query_arg('info','index',$redirectUrl);
		wp_redirect($redirectUrl);
		exit;
    }
}
if($_REQUEST['info']==null)
$info="index";
else
$info=$_REQUEST['info'];
$obj = new Payments;
$obj->$info();
?>