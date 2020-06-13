<?php if($_GET['msg']=='pending'){$msg=$this->ExamApp->showMessage("Your previous exam is pending!",'danger');}?>
<style>
    .radio input[type="radio"],
.radio-inline input[type="radio"],
.checkbox input[type="checkbox"],
.checkbox-inline input[type="checkbox"] {
  margin-left: 0px;
}
</style>
<script>
$(document).ready(function(){$("#checkme").click(function() {$("#submitaccept").attr("disabled", !this.checked);});});
</script>
<div class="col-md-9 col-sm-offset-2">
    <?php echo$msg;?>
    <div class="panel panel-default">
	<div class="panel-heading"><strong><?php echo __('Instructions').' '.__('of').' '.$post['name'];?></strong></div>
	<div class="panel-body">
	    <strong><?php echo str_replace("<script","",$post['instruction']);?></strong>
	    <div class="input checkbox"><input type="checkbox" id="checkme" name="accept"><label for="checkme"><?php echo __('I am ready to begin');?></label></div>
	    <?php
	    if($post['paid_exam']==1)
	    {
		if($ispaid==true)
		{?>
		<p><a href="javascript:void(0);" id="submitaccept" disabled="disabled" class="btn btn-success" onclick="document.post_56bacb3f453b0556431157.submit(); event.returnValue = false; return false;"><?php echo __('Exam Start');?></a></p>
		<?php }	else{?>
		<p><a href="javascript:void(0);" id="submitaccept" disabled="disabled" class="btn btn-danger" onclick="if(confirm('<?php echo __('This Exam is paid. Amount should be deducted on your wallet automatically. After starting the exam timer will not stop. Do you want to pay & start?');?>')) { document.post_56bacb3f453b0556431157.submit(); } event.returnValue = false; return false;"><?php echo __('Exam Start');?></a></p>
		<?php }
	    }
	    else
	    {?>
	    <p><a href="javascript:void(0);" id="submitaccept" disabled="disabled" class="btn btn-success" onclick="document.post_56bacb3f453b0556431157.submit(); event.returnValue = false; return false;"><?php echo __('Exam Start');?></a></p>
	    <?php }?>
	</div>
    </div>
</div>
<form action="<?php echo admin_url('admin-ajax.php');?>?action=examapp_ExamStart&info=index&id=<?php echo$id;?>" name="post_56bacb3f453b0556431157" id="post_56bacb3f453b0556431157" style="display:none;" method="post"><input type="hidden" name="_method" value="POST"/></form>