<div class="container">
        <div class="panel panel-custom mrg">
	<div class="panel-body">
		<div class="panel-heading"><?php echo __('Questions Attempt Count');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>            
		<div class="panel-body"><?php echo$msg;?>
		<form class="form-horizontal" action="<?php echo$this->url;?>&info=maxquestion" method="post">
		<?php  foreach($post as $k=>$value){ ?>
		<div class="form-group">
			<label for="group_name" class="col-sm-4 control-label"><?php echo$this->ExamApp->h($value['subject_name']);?>:</label>
		    <div class="col-sm-6">
			<input type="number" name="data[<?php echo$k;?>][max_question]"  class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo $post[$k]['max_question'];?>" />
			<input type="hidden" name="data[<?php echo$k;?>][subject_id]"  class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo $post[$k]['subject_id'];?>" />
			<input type="hidden" name="data[<?php echo$k;?>][exam_id]"  class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo $examId;?>" />
			<input type="hidden" name="data[<?php echo$k;?>][id]"  class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo$post[$k]['id'] ?> "/>
			</div>
		</div>
		<?php  }?>
		 <div class="form-group text-left">
                        <div class="col-sm-offset-4 col-sm-7">                            
                            <button type="submit" class="btn btn-success"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button>
			    <input type="hidden" name="id" value="<?php echo$id;?>">
                        </div>
                    </div>
		</div>
	</div>
		    </form>
		</div>
	    </div>
	</div>