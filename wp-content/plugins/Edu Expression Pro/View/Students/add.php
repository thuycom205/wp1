<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Add Students');?></div>
    <div class="panel-body">
    <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
	<div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('User Name');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
                <input type="text" name="username" value="<?php echo $_POST['username'];?>" class='form-control' placeholder="<?php echo __('User Name');?>" required/>
	    </div>
	    <label for="email" class="col-sm-2 control-label"><small><?php echo __('Email');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
                <input type="email" name="email" value="<?php echo $_POST['email'];?>" class='form-control' placeholder="<?php echo __('Email');?>" required/>
	    </div>	    
	</div>
        <div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('First Name');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
                <input type="text" name="first_name" value="<?php echo $_POST['first_name'];?>" class='form-control' placeholder="<?php echo __('First Name');?>" required/>
	    </div>
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Last Name');?><span class="text-danger"> </span></small></label>
	    <div class="col-sm-4">
            <input type="text" name="last_name" value="<?php echo $_POST['last_name'];?>" class='form-control' placeholder="<?php echo __('Last Name');?>" /></div>
	</div>
        <div class="form-group">
	   <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Password');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <input type="password" name="password" value="<?php echo $_POST['password'];?>" class='form-control' placeholder="<?php echo __('Password');?>" maxlength='15' minlength='4' required/>
	    </div>            
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Enrollment Number');?></small></label>
	    <div class="col-sm-4">
            <input type="text" name="enroll" value="<?php echo $_POST['enroll'];?>" class='form-control' placeholder="<?php echo __('Enrollment Number');?>" />
	    </div>
	</div>
        <div class="form-group">
	    <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Group');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <select name="group_name[]" class="form-control multiselectgrp" multiple="true">
            <?php echo$groupName;?>
            </select>
	    </div>
            <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Address');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <textarea name="address" class='form-control' placeholder="<?php echo __('Address');?>" required="required" cols="15" rows="5"><?php echo $_POST['address'];?></textarea>
	    </div>
            
	</div>
	<div class="form-group">
	     <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Phone');?><span class="text-danger"> *</span></small></label>
	    <div class="col-sm-4">
            <input type="text" name="phone" value="<?php echo $_POST['phone'];?>" class='form-control' placeholder="<?php echo __('Phone');?>" required/>
	    </div>
            <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Alternate Number');?></small></label>
	    <div class="col-sm-4">
            <input type="text" name="alternate_number" value="<?php echo $_POST['alternate_number'];?>" class='form-control' placeholder="<?php echo __('Alternate Number');?>" />
	    </div>
	</div>
        <?php if($this->configuration['student_expiry']){?>
	<div class="form-group">
	     <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Expiry Days');?><span class="text-danger"></span></small></label>
	    <div class="col-sm-4">
            <input type="number" name="expiry_days" value="<?php echo $_POST['expiry_days'];?>" class='form-control' placeholder="<?php echo __('0 for Unlimited');?>" />
	    </div>
        </div>
	<?php }?>
	<div class="form-group text-left">
	    <div class="col-sm-offset-2 col-sm-8">
		<button type="submit" class="btn btn-success" name="submit"><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;<?php echo __('Save');?></button>
                 <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
			    
           </div>
	</div>
	</form>
    </div>
</div>