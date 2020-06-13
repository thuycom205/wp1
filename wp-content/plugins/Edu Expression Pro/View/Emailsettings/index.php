<script type="text/javascript">
    $(document).ready(function(){
	<?php if($post['type']=="Smtp"){?>$('#smtp').show();<?php }else{?>$('#smtp').hide();<?php }?>
	$('#EmailsettingTypeSmtp').click(function(){$('#smtp').show();});
	$('#EmailsettingTypeMail').click(function(){$('#smtp').hide();});
	});
</script>

<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('E-Mail Settings');?></div>
               <div class="panel-body">
	       <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">
                
                    <div class="form-group">
                        <label for="site_name" class="col-sm-2 control-label"><?php echo __('Email Type');?></label>
                        <div class="col-sm-4">
			<label class="radio-inline"><input type="radio" name="data[type]" id="EmailsettingTypeMail" value="Mail" <?php if($post['type']=="Mail"){echo 'checked';} ?> />LOCALHOST</label>
			<label class="radio-inline"><input type="radio" name="data[type]" id="EmailsettingTypeSmtp" value="Smtp" <?php if($post['type']=="Smtp"){echo 'checked';} ?>/>SMTP	
			</div>                        
                    </div>
		    <div id="smtp">
			<div class="form-group">
			    <label for="site_name" class="col-sm-2 control-label"><?php echo __('Server Name / Host');?></label>
			    <div class="col-sm-4">
			    <input type="text" name="data[host]" class="form-control" placeholder="<?php echo __('Server Name / Host');?>" value="<?php echo $this->ExamApp->h($post['host']);?>"  /> 
                            </div>
			    <label for="site_name" class="col-sm-2 control-label"><?php echo __('Port');?></label>
			    <div class="col-sm-4">
			    <input type="text" name="data[port]" class="form-control" placeholder="<?php echo __('Port');?>" value="<?php echo $this->ExamApp->h($post['port']);?>"  /> 
                            </div>
			</div>
			<div class="form-group">
			    <label for="site_name" class="col-sm-2 control-label"><?php echo __('User Name');?></label>
			    <div class="col-sm-4">
			    <input type="text" name="data[username]" class="form-control" placeholder="<?php echo __('User Name');?>" value="<?php echo $this->ExamApp->h($post['username']);?>"  /> 
                            </div>
			    <label for="site_name" class="col-sm-2 control-label"><?php echo __('Password');?></label>
			    <div class="col-sm-4">
			    <input type="password" name="data[password]" class="form-control" placeholder="<?php echo __('Password');?>" value="<?php echo $this->ExamApp->h($post['password']);?>"  /> 
                            </div>                        
			</div>
		    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Save Settings');?></button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        
   	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    