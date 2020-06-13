<?php 
if($_GET['msg']=='invalidamount'){$msg=$this->ExamApp->showMessage("Invalid Amount",'danger');}
if($_GET['msg']=='invalidcurrency'){$msg=$this->ExamApp->showMessage("You must provide a currency code",'danger');}
if($_GET['msg']=='invalidorder'){$msg=$this->ExamApp->showMessage("You must pass a valid order array",'danger');}
if($_GET['msg']=='invalidcancel'){$msg=$this->ExamApp->showMessage('Valid "return" and "cancel" urls must be provided','danger');}
if($_GET['msg']=='invalidconnect'){$msg=$this->ExamApp->showMessage('Try again! Can not connect to paypal','danger');}
if($_GET['msg']=='invalidpayment'){$msg=$this->ExamApp->showMessage('Payment not done','danger');}
if($_GET['msg']=='payadone'){$msg=$this->ExamApp->showMessage('Payment already done','danger');}
if($_GET['msg']=='paysucess'){$msg=$this->ExamApp->showMessage('Payment successfully! Amount added in your wallet ','success');}
if(isset($_GET['fmsg'])){$msg=$this->ExamApp->showMessage($_GET['fmsg'],'danger');}
?>
<?php echo$msg;?>
<div class="page-title"><div class="title-env"> <h1 class="title"><?php echo __('Payment From Pay Pal');?></h1></div></div>
<div class="panel">    
    <div class="panel-body">
	<form class="form-horizontal" name="post_req" id="post_req" action="<?php echo$this->ajaxUrl;?>&info=checkout" method="post" accept-charset="utf-8">
	<div class="form-group">
		<label for="group_name" class="col-sm-3 control-label"><?php echo __('Amount');?></label>
		<div class="col-sm-9">
		 <input type="number" name="amount" class="form-control" placeholder="<?php echo __('Amount');?>" type="number" autocomplete="off" value="<?php echo $_POST['amount'];?>" required/>
		</div>
	    </div>
	    <div class="form-group">
		<label for="group_name" class="col-sm-3 control-label"><?php echo __('Remarks');?></label>
		<div class="col-sm-9">
		<input type="text" name="remarks" class="form-control" placeholder="<?php echo __('Remarks');?>" type="number" value="<?php echo $_POST['remarks'];?>" />
            </div>
	    </div>                    
	    <div class="form-group text-left">
		<div class="col-sm-offset-3 col-sm-7">
		    <button type="submit" class="btn btn-success"><span class="fa fa-paypal"></span> <?php echo __('Pay From Pay Pal');?></button>
		</div>
	    </div>
	</form> 
    </div>
</div>