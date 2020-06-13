<style type="text/css">
.modal-backdrop {background-color:#000;}
.modal-backdrop.in{opacity: .5;}
</style>
<div class="container">
	<div class="row">
	<div class="col-md-7 col-sm-offset-2 mrg">
			<div class="panel panel-default">
			<div class="panel-heading">
				<div class="widget-modal">
					<h4 class="widget-modal-title"><span><?php echo __('Finalize');?> <?php echo$post['type'];?></span>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</h4>
				</div>
			</div>
			<div class="panel-body">
			<p><?php echo __('Do you wish to submit and close the');?> <?php echo$post['type'];?> <?php echo __('Once you submit, you will not be able to review the');?> <?php echo$post['type'];?></p>
			<p><?php echo __('Summary of your attempts in this');?> <?php echo$post['type'];?> <?php echo __('as show below');?></p>
			<div class="row"><div class="col-xs-4"><h4><?php echo __('Attempted');?></h4></div><div class="col-xs-3"><h4><span class="label label-default"><?php echo$attempted;?></span></h4></div></div>
			<div class="row"><div class="col-xs-4"><h4><?php echo __('Not Attempted');?></h4></div><div class="col-xs-3"><h4><span class="label label-default"><?php echo$notAttempted;?></span></h4></div></div>
			<div class="row"><div class="col-xs-4"><h4><?php echo __('Answered');?></h4></div><div class="col-xs-3"><h4><span class="label label-success"><?php echo$answered;?></span></h4></div></div>
			<div class="row"><div class="col-xs-4"><h4><?php echo __('Not Answered');?></h4></div><div class="col-xs-3"><h4><span class="label label-warning"><?php echo$notAnswered;?></span></h4></div></div>
			<div class="row"><div class="col-xs-4"><h4><?php echo __('Review');?></h4></div><div class="col-xs-3"><h4><span class="label label-primary"><?php echo$review;?></span></h4></div></div>
			<div class="row">
				<div class="col-sm-4">
					<form action="<?php echo $this->ajaxUrl;?>&info=finish&id=<?php echo$examId.'&examResultId='.$examResultId;?>" name="post_56becbd04e9fd987227702" id="post_56becbd04e9fd987227702" style="display:none;" method="post"><input type="hidden" name="_method" value="POST"></form>
					<a href="javascript:void(0)" class="btn btn-success" onclick="document.post_56becbd04e9fd987227702.submit(); event.returnValue = false; return false;"><span class="glyphicon glyphicon-lock"></span>&nbsp;<?php echo __('Finish').'&nbsp;'.$post['type'];?></a>					
				</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span><?php echo __('Cancel');?></button>
				</div>
				<div class="col-sm-4">
				<a class='btn btn-warning' href="<?php echo $this->ajaxUrl;?>&info=start&id=<?php echo$examId;?>&ques=1">&larr;<?php echo __('Return To First Question');?></a>
					
				</div>
			</div>
			</div>
		</div>
	</div>
	</div>
</div>
