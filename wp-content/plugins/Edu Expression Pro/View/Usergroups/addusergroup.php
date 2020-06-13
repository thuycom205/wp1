<script type='text/javascript' src='<?php echo plugin_dir_url(__FILE__);?>../../js/main.custom.min.js'></script>
<div <?php if(!$isError){?>class="container"<?php }?>>    
    <div class="panel panel-custom mrg">
        <div class="panel-heading"><?php echo __('Set').'&nbsp;'.__('User Group');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
        <div class="panel">
			<form class="form-horizontal validate" action="<?php echo$this->url;?>&info=addusergroup" method="post">
				<div class="panel-body">
				
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Select Group');?></small></label>
                        <div class="col-sm-4">
			<select name="group_name[]" class="form-control multiselectgrp" multiple="true">
			<?php echo$groupName;?>
			</select>
                        </div>
                    </div>
		    <div class="mrg"></div>
                    	<div class="form-group">
				<div class="col-sm-offset-2 col-sm-6">
					<button type="submit" class="btn btn-success" name="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Save');?></button>
					<?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
					<a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
					<input type="hidden" name="id" value="<?php echo$_REQUEST['id'];?>">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>