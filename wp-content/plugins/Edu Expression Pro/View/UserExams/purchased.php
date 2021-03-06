<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('My Exams');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
        <div class="btn-group">			
			<a href="<?php echo $this->url;?>&info=index" class="btn btn-default"><?php echo __('Todays Exam');?></a>
                        <?php if($this->configuration['paid_exam']){?><a href="<?php echo $this->url;?>&info=purchased" class="btn btn-success"><?php echo __('Purchased Exam');?></a><?php }?>
                        <a href="<?php echo $this->url;?>&info=upcoming" class="btn btn-default"><?php echo __('Upcoming Exam');?></a>
                        <?php if($this->configuration['paid_exam'] && $this->configuration['exam_expiry']){?><a href="<?php echo $this->url;?>&info=expired" class="btn btn-default"><?php echo __('Expired Exam');?></a><?php }?>

		</div>
    </div>
<div class="panel-body">
		<div class="panel panel-default">
		<div class="panel-heading">
			<div class="widget">
				<h4 class="widget-title"><?php echo __('Purchased Exam');?></h4>
			</div>
		</div>
			<div class="table-responsive">
				<table class="table table-striped">
					<?php if($purchasedExam){?>
					<tr>
						<th colspan="9"><?php echo __('These are the exam(s) which has been purchased');?></th>
					</tr>
					<tr>						
					</tr>
					<tr>
					<?php echo$this->UserExam->showExamList("purchased",$purchasedExam);?>
					<?php }else{?>
					<tr>
						<th colspan="9"><?php echo __('No Exams found');?></th>
					</tr>
					<?php }?>
				</table>
			</div>
		</div> 
	</div>	
</div>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">        
  </div>
</div>