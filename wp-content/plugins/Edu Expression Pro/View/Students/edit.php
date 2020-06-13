<script type='text/javascript' src='<?php echo plugin_dir_url(__FILE__);?>../../js/main.custom.min.js'></script>
<div <?php if(!$isError){?>class="container"<?php }?>>    

<div class="panel panel-custom mrg">
    <div class="panel-heading"><?php echo __('Edit Students');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
    <div class="panel-body">
    <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=edit" method="post">
    <?php foreach($resultArr as $k=>$post):$user_info=get_userdata($post['id']);?>
    <script type="text/javascript">
	    $(document).ready(function(){        
	    $('#renewal_date<?php echo$k;?>').datetimepicker({locale:'<?php echo$this->configuration['language'];?>',format:'<?php echo $this->ExamApp->datePickerFormat();?>'});
	    });
	    
</script>
    
    <div class="panel panel-default">
    <div class="panel-heading"><strong><small class="text-danger"><?php echo __('Form');?> <?php echo$k;?></small></strong></div>
	<div class="panel-body">
	<div class="form-group">
	    <label for="email" class="col-sm-2 control-label"><small><?php echo __('Email');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
                <input type="email" name="data[<?php echo$k;?>][email]" value="<?php echo $post['email'];?>" class='form-control' placeholder="<?php echo __('Email');?>" required/>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Password');?><span class="text-danger"> </span></small></label>
	    <div class="col-sm-4">
                <input type="password" name="data[<?php echo$k;?>][password]" value="<?php echo $_POST['password'];?>" class='form-control' placeholder="<?php echo __('Password');?>" />
	    </div>
	    
	</div>
	<div class="form-group">
	    <label for="email" class="col-sm-2 control-label"><small><?php echo __('First Name');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
                <input type="text" name="data[<?php echo$k;?>][first_name]" value="<?php echo $user_info->first_name;?>" class='form-control' placeholder="<?php echo __('First Name');?>" required/>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Last Name');?><span class="text-danger"> </span></small></label>
	    <div class="col-sm-4">
                <input type="text" name="data[<?php echo$k;?>][last_name]" value="<?php echo $user_info->last_name;?>" class='form-control' placeholder="<?php echo __('Last Name');?>" />
	    </div>
	</div>
	<div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Group');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <select name="data[<?php echo$k;?>][group_name][]" class="form-control multiselectgrp" multiple="true">
		<?php echo$groupNameEditArr[$k];?>
	     </select>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Enrollment Number');?></small></label>
	    <div class="col-sm-4">
            <input type="text" name="data[<?php echo$k;?>][enroll]" value="<?php echo $user_info->examapp_enroll;?>" class='form-control' placeholder="<?php echo __('Enrollment Number');?>" />	    
	    </div>
	</div>
        <div class="form-group">	    
            <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Phone');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <input type="text" name="data[<?php echo$k;?>][phone]" value="<?php echo $user_info->examapp_phone;?>" class='form-control' placeholder="<?php echo __('Phone');?>" required/>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Alternate Number');?></small></label>
	    <div class="col-sm-4">
            <input type="text" name="data[<?php echo$k;?>][alternate_number]" value="<?php echo $user_info->examapp_alternate_number;?>" class='form-control' placeholder="<?php echo __('Alternate Number');?>" />
	    </div>
	</div>
	<div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Address');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <textarea name="data[<?php echo$k;?>][address]" class='form-control' placeholder="<?php echo __('Address');?>" required="required" cols="15" rows="5"><?php echo $user_info->examapp_address;?></textarea>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Username');?></small></label>
	    <div class="col-sm-4">
            <input type="text" name="data[<?php echo$k;?>][username]" value="<?php echo $post['user_login'];?>" readonly="readonly" class='form-control' placeholder="<?php echo __('Username');?>" />
	    </div>
	</div>
	    <?php if($this->configuration['student_expiry']){?>
	    <div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Expiry Days');?></small></label>
	    <div class="col-sm-4">
            <input type="number" name="data[<?php echo$k;?>][expiry_days]" value="<?php echo $user_info->examapp_expiry_days;?>" class='form-control' placeholder="<?php echo __('0 for Unlimited');?>" />
	    </div>
	     <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Renewal Date');?></small></label>
                        <div class="col-sm-4">
						<div class="input-group date" id="renewal_date<?php echo$k;?>">
						<input type="text" name="data[<?php echo$k;?>][renewal_date]" class="form-control" value="<?php echo $this->ExamApp->datePickerValueFormat($user_info->examapp_renewal_date);?>" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i>
					     </div>
						</div>
					    
			      </div>
			      </div>			      
	    <?php }?>
	    <input type="hidden" name="data[<?php echo$k;?>][id]" value="<?php echo $post['id'];?>">
	<?php endforeach;unset($post,$k);?>
	<div class="form-group text-left">
	    <div class="col-sm-offset-2 col-sm-8">
		<button type="submit" class="btn btn-success" name="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Update');?></button>
		<?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
		<a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
		</div>
	</div>
	</form>
    </div>
</div>
</div>