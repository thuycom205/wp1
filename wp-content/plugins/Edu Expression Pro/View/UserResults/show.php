<?php $order=$this->ExamApp->sortableOrder($_REQUEST);?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('My Results');?></h1></div></div>
<div class="panel">
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
				<th><?php echo __('S.No.');?></th>
				<th><?php echo __('Exam Name');?></th>
				<th><?php echo __('Attempt Date');?></th>
				<th><?php echo __('Marks Scored');?>/<br><?php echo __('Max.Marks');?></th>
				<th><?php echo __('Percentage');?></th>
				<th><?php echo __('Result');?></th>
				<th><?php echo __('Action');?></th>
			</tr>
			
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1; foreach($result as $post){$id=$post['Result.id'];$serialNo++;?>
			<tr>
				<td><?php echo$serialNo;?></td>
				<td><?php echo $this->ExamApp->h($post['name']);?></td>
				<td><?php echo$this->ExamApp->dateTimeFormat($post['start_time']);?></td>
				<td><?php echo number_format($post['obtained_marks'],2);?>/<?php echo number_format($post['total_marks'],2);?></td>
				<td><?php echo$post['percent'];?></td>
				<td><span class="label label-<?php if($post['result']=="Pass")echo"success";else echo"danger";?>"><?php if($post['result']=="Pass"){echo __('PASSED');}else{echo __('FAILED');}?></span></td>
				<td>
                                <a  href="<?php echo $this->url;?>&info=view&id=<?php echo$id;?>" data-toggle="tooltip" title="<?php echo __('View Details');?>" ><span class="fa fa-arrows-alt"></span>&nbsp;</a>
				<?php if($this->configuration['certificate']){?>
                                <a  href="<?php echo $this->ajaxUrl;?>&info=certificate&id=<?php echo$id;?>" data-toggle="tooltip" title="<?php echo __('Certificate');?>" ><span class="fa fa-certificate"></span>&nbsp;</a>
                                <?php }?>
                                </td>
			</tr><?php }?>
                </tbody>
            </table>
        </div>
        </form>
        <?php echo$paginate;?>
    </div>
</div>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content"></div>
</div>