<?php $order=$this->ExamApp->sortableOrder($_REQUEST);?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Transaction History');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
    </div>
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
				<th><?php echo __('S.No.');?></th>
				<th><?php echo __('Credit');?></th>
				<th><?php echo __('Debit');?></th>
				<th><?php echo __('Balance');?></th>
				<th><?php echo __('Date & Time');?></th>
				<th><?php echo __('Payment Through');?></th>
				<th><?php echo __('Remarks')?></th>
		    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;$currency=$this->ExamApp->getCurrency();
                    foreach($result as $post){$id=$post['id'];$serialNo++;
			
			?>
                    <tr>
                        <td><?php echo $serialNo;?></td>
                        <td><?php if($post['in_amount']!=NULL)echo$currency.$post['in_amount'];?></td>
			<td><?php if($post['out_amount']!=NULL)echo$currency.$post['out_amount'];?></td>
			<td><?php echo$currency.$post['balance'];?></td>
			<td><?php echo $this->ExamApp->dateTimeFormat($post['date']);?></td>
			<td><?php echo$paymentTypeArr[$post['type']];?></td>
			<td><?php echo $this->ExamApp->h($post['remarks']);?></td>
                    </tr>
                    <?php }?>
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