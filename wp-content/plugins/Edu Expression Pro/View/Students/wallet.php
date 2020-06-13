<div <?php if(!$isError){?>class="container"<?php }?>>
    <div class="panel panel-custom mrg">
	<div class="panel-heading"><?php echo __('Students Wallet');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>            
                <div class="panel-body">
		<form class="form-horizontal validate" action="<?php echo$this->url;?>&info=wallet" method="post">
                 <?php foreach ($resultArr as $k=>$post): $id=$post['id'];$form_no=$k;?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong class="text-danger"><small><?php echo __('Transaction Form');?> <?php echo$form_no?></small></strong></div>
			    <div class="panel-body">
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Email');?></small></label>
				    <div class="col-sm-9">
					<?php echo $this->ExamApp->h($post['email']);?>
				    </div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Name');?></small></label>
				    <div class="col-sm-9">
					<?php echo $this->ExamApp->h($post['name']);?>
				    </div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Mobile');?></small></label>
				    <div class="col-sm-9">
					<?php echo get_user_meta($id,'examapp_phone',true);?>
				    </div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Balance');?></small></label>
				    <div class="col-sm-9">
					<?php echo (empty($post['balance'])) ? $currency."0.00" : $currency.$post['balance'];?>
				    </div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Amount');?></small></label>
				    <div class="col-sm-3">
				    <input type="number" name="data[<?php echo$k;?>][amount]" value="<?php echo $post['amount'];?>" class='form-control' placeholder="<?php echo __('Amount');?>" />
	                         </div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Action');?></small></label>
					<div class="col-sm-3">
					<select name="data[<?php echo$k;?>][action]" class='form-control' required>
					<option value=""><?php echo __('Please Select');?></option>
					<option value="Added" <?php if($post['action']=="Added"){ echo 'selected';} ?>><?php echo __('ADD');?></option>
					<option value="Deducted" <?php if($post['action']=="Deducted"){ echo 'selected';} ?>><?php echo __('DEDUCT');?></option>
					</select>
					</div>
				</div>
				<div class="form-group">
				    <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Remarks');?></small></label>
				    <div class="col-sm-3">
				    <textarea name="data[<?php echo$k;?>][remarks]" class='form-control' placeholder="<?php echo __('Remarks');?>"  cols="15" rows="5"><?php echo $post['remarks'];?></textarea>
				    </div>
				</div>
				<div class="form-group text-left">
				    <div class="col-sm-offset-3 col-sm-7">
				    <input type="hidden" name="data[<?php echo$k;?>][id]" value="<?php echo $id;?>">
			       </div>
				</div>
			    </div>
		    </div>				
                    <?php endforeach; ?>
                        <?php unset($post); ?>
                        <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">                            
                            <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Update');?></button>
		           <?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
		           <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
		</div>
                    </div>
               </form>
        </div>
    </div>
</div>