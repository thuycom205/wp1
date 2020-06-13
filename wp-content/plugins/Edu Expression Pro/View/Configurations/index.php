<div class="page-title"><div class="title-env"> <h1 class="title"><?php echo __('Configuration Options');?></h1></div></div>
<div class="panel">    
    <div class="panel-body">
	<form class="form-horizontal" name="post_req" id="post_req" action="<?php echo$this->url;?>&info=index" method="post" accept-charset="utf-8">
	    <div class="form-group">
		<label for="site_name" class="col-sm-2 control-label"><?php echo __('Currency');?></label>
		<div class="col-sm-4">
		<select name="data[currency]" class="form-control">
		<?php echo$currencyName;?>
		</select>
		</div>
		<label for="site_name" class="col-sm-2 control-label"><?php echo __('Tolrance Count');?></label>
		<div class="col-sm-4">
		<input name="data[tolrance_count]" class="form-control" type="number" value="<?php echo $post['tolrance_count'];?>" <?php if($post['tolrance_count']){echo 'checked';}?> id="ConfigurationTolranceCount"/>
	       </div>	
	    </div>
	    <div class="form-group">
		<label for="site_name" class="col-sm-2 control-label"><?php echo __('Display records per page');?></label>
		<div class="col-sm-4">
		<input name="data[min_limit]" class="form-control" placeholder="<?php echo __('Display records per page');?>" type="number" value="<?php echo $post['min_limit'];?>" id="ConfigurationMinLimit"/>
		</div>
		<label for="site_name" class="col-sm-2 control-label"><?php echo __('Max records per page');?></label>
		<div class="col-sm-4">
		   <input name="data[max_limit]" class="form-control" placeholder="<?php echo __('Max records per page');?>" type="number" value="<?php echo $post['max_limit'];?>" id="ConfigurationMaxLimit"/>	
		</div>
	    </div>	    
	    <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('SMS Notification');?></label>
		<div class="col-sm-1">
		   <input type="checkbox" name="data[sms_notification]"  class="form-control" value="1" <?php if($post['sms_notification']){echo 'checked';}?> id="ConfigurationSmsNotification"/>
		</div>
		 <label for="site_name" class="col-sm-3 control-label"><?php echo __('Email Notification');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[email_notification]"  class="form-control" value="1" <?php if($post['email_notification']){echo 'checked';}?> id="ConfigurationEmailNotification"/>
		</div>
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Manual Verification');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[manual_verification]"  class="form-control" value="1" <?php if($post['manual_verification']){echo 'checked';}?> id="ConfigurationStudent_expiry" />
		</div>
	    </div>
	    <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Exam Expiry');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[exam_expiry]"  class="form-control" value="1" <?php if($post['exam_expiry']){echo 'checked';}?> id="ConfigurationExam_expiry" />
		</div>
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Exam Feedback');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[exam_feedback]"  class="form-control" value="1" <?php if($post['exam_feedback']){echo 'checked';}?> id="Configurationexam_feedback" />
		</div>
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Paid Exam');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[paid_exam]"  class="form-control" value="1" <?php if($post['paid_exam']){echo 'checked';}?> id="ConfigurationPaid_exam" />
		</div>
	    </div>                
	    <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Student Expiry');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[student_expiry]"  class="form-control" value="1" <?php if($post['student_expiry']){echo 'checked';}?> id="ConfigurationStudent_expiry" />
		</div>
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Certificate');?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[certificate]"  class="form-control" value="1" <?php if($post['certificate']==1){echo 'checked';}?> id="ConfigurationCertificate" />
		</div>
		<label for="site_name" class="col-sm-3 control-label"><?php echo __('Math Editor')?></label>
		<div class="col-sm-1">
		<input type="checkbox" name="data[math_editor]"  class="form-control" value="1" <?php if($post['math_editor']){echo 'checked';}?> id="ConfigurationMath_editor" />
		</div>		
	    </div>
	    <div class="form-group text-left">
		<div class="col-sm-offset-2 col-sm-7">
		<button class="btn btn-success" type="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Save Settings');?></button>
		</div>
	    </div>
	</form>
    </div>
</div>