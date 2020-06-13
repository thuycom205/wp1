<div <?php if(!$isError){?>class="container"<?php }?>>    
    <div class="panel panel-custom mrg">
        <div class="panel-heading"><?php echo __('Edit').'&nbsp;'.__('Groups');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
        <div class="panel-body">
			<form class="form-horizontal validate" action="<?php echo$this->url;?>&info=edit" method="post">
			<?php foreach($resultArr as $k=>$post){?>
			<div class="panel panel-default">
				<div class="panel-heading"><strong><small class="text-danger"><?php echo __('Form');?> <?php echo$k;?></small></strong></div>
				<div class="panel-body">
					<div class="form-group">
						<label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Name');?></small></label>
						<div class="col-sm-9">
						<input type="text" name="data[<?php echo$k;?>][group_name]" value="<?php echo $this->ExamApp->h($post['group_name']);?>" class="form-control" placeholder="<?php echo __('Name');?>" required="required" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-9">
						<input type="hidden" name="data[<?php echo$k;?>][id]" value="<?php echo$post['id'];?>" class="form-control" />
						</div>
					</div>
				</div>
			</div>
			<?php }?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-success" name="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Update');?></button>
					<?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
					<a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
					<input type="hidden" name="id" value="<?php echo$_REQUEST['id'];?>">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>