<div class="container">
	<div class="panel panel-custom mrg">
        <div class="panel-heading"><?php  echo$post['type'];?> <?php echo __('Details');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<td><strong class="text-primary"><?php echo __('Name');?></strong></td>
						<td><strong class="text-success"><?php echo $this->ExamApp->h($post['name']);?></strong></td>
						<td><strong class="text-primary"><?php echo __('Type');?></strong></td>
						<td><strong class="text-success"><?php echo __($post['type']);?></strong></td>
					</tr>
					<tr>
						<td><strong class="text-primary"><?php echo __('Passing Percentage');?></strong></td>
						<td><strong class="text-success"><?php echo$post['passing_percent'];?>%</strong></td>
						<td><strong class="text-primary"><?php echo __('Duration');?></strong></td>
						<td><strong class="text-success"><?php echo __($this->ExamApp->secondsToWords($post['duration']*60));?></strong></td>
					</tr>
					<tr>
						<td><strong class="text-primary"><?php echo __('Start Date');?></strong></td>
						<td><strong class="text-success"><?php echo$this->ExamApp->dateTimeFormat($post['start_date']);?></strong></td>
						<td><strong class="text-primary"><?php echo __('End Date');?></strong></td>
						<td><strong class="text-success"><?php echo$this->ExamApp->dateTimeFormat($post['end_date']);?></strong></td>
					</tr>
					<tr>
						<td><strong class="text-primary"><?php echo __('Negative Marking');?></strong></td>
						<td><strong class="text-success"><?php echo __($post['negative_marking']);?></strong></td>
						<td><strong class="text-primary"><?php if($totalMarks>0)echo __("Total Marks");?></strong></td>
						<td><strong class="text-success"><?php if($totalMarks>0)echo$totalMarks;?></strong></td>
					</tr>
					<?php if($post['paid_exam']==1){?>
					<tr>
						<td><strong class="text-primary"><?php echo __('Amount');?></strong></td>
						<td colspan="3"><strong class="text-warning"><?php echo$this->ExamApp->getCurrency().$post['amount'];?></strong></td>					
					</tr><?php }?>
				</table>
				<?php if(is_array($subjectDetail)){?>
				<table class="table">
				<tr>
				<th><strong class="text-primary"><?php echo __('Subject');?></strong></th>
				<th><strong class="text-primary"><?php echo __('Total Question');?></strong></th>
				<?php if($examCount){?><th><strong class="text-primary"><?php echo __('Questions Attempt Count');?></strong></th><?php }?>
				</tr>
				<?php $totalQuestion=0;$totalAttemptQuestion=0;
				foreach ($subjectDetail as $k=>$sd):
				$totalQuestion=$totalQuestion+$sd['total_question'];
				$totalAttemptQuestion=$totalAttemptQuestion+$sd['total_attempt_question'];?>
				<tr>
				<td><strong class="text-success"><?php echo $this->ExamApp->h($k);?></strong></td>
				<td><strong class="text-success"><?php echo $sd['total_question'];?></strong></td>
				<?php if($examCount){?><td><strong class="text-success"><?php echo$sd['total_attempt_question']?></strong></td><?php }?>
				</tr>
				<?php endforeach;?> <?php unset($sd);?>
				<tr>
				<td><strong class="text-danger"><?php echo __('Total');?></strong></td>
				<td><strong class="text-danger"><?php echo$totalQuestion;?></strong></td>
				<?php if($examCount){?><td><strong class="text-danger"><?php echo$totalAttemptQuestion;?></strong></td><?php }?>
				</tr>
				<?php if($showType=='today' || $showType=='purchased'){?>
				<tr>
				<td colspan="4">
				<a href="<?php echo$this->ajaxUrl;?>&info=guidelines&id=<?php echo$id;?>" data-toggle="tooltip" title="<?php echo __('Attempt Now');?>" class="btn btn-success" target="_blank" class="btn btn-info"><span class="fa fa-sign-in"></span>&nbsp;<?php echo __('Attempt Now');?></a>
				</td>
				</tr><?php }?>
				</table>
				<?php }?>
			</div>
		</div>
	</div>
</div>