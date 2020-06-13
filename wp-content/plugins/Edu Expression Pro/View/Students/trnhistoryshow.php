<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Transaction')." ".__('History');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
        <a href="#" class="btn btn-info" onclick="javascript:history.back(-1);"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back');?></a>
          
    </div>
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
		    
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'user_email');?>"><a href="<?php echo$this->url;?>&info=index&orderby=email&order=<?php echo$order;?>"><?php echo __('E-Mail');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'in_amount');?>"><a href="<?php echo$this->url;?>&info=index&orderby=name&order=<?php echo$order;?>"><?php echo __('Credit');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'out_amount');?>"><a href="<?php echo$this->url;?>&info=index&orderby=phone&order=<?php echo$order;?>"><?php echo __('Debit');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'balance');?>"><a href="<?php echo$this->url;?>&info=index&orderby=phone&order=<?php echo$order;?>"><?php echo __('Balance');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'date');?>"><a href="<?php echo$this->url;?>&info=index&orderby=phone&order=<?php echo$order;?>"><?php echo __('Date & Time');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'type');?>"><a href="<?php echo$this->url;?>&info=index&orderby=phone&order=<?php echo$order;?>"><?php echo __('Payment Through');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'remarks');?>"><a href="<?php echo$this->url;?>&info=index&orderby=status&order=<?php echo$order;?>"><?php echo __('Remarks');?><span class="sorting-indicator"></span></a></th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;$currency=$this->ExamApp->getCurrency();
                    foreach($result as $k=>$post){$id=$post['id'];
			$serialNo++;?>
                    <tr>
			<td><?php echo$serialNo;?></td>
			<td><?php echo$post['user_email'];?></td>
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
