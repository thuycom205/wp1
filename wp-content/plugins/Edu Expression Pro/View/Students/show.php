<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Students');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
            <div class="btn-group">
                <a class="btn btn-success" href="<?php echo$this->url;?>&info=add"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Add New');?></a>
                <a class="btn btn-warning" href="javascript:void(0);" name="editallfrm" id="editallfrm" onclick="check_perform_edit('<?php echo$this->ajaxUrl;?>');"><span class="fa fa-edit"></span>&nbsp;<?php echo __('Edit');?></a>
                <a class="btn btn-danger" href="javascript:void(0);" onclick=check_perform_delete();><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete');?></a>
                <a class="btn btn-info" href="<?php echo $this->Iestudent;?>&info=index" ><span class="fa fa-exchange"></span>&nbsp;<?php echo __('Import/Export Students');?></a>
		<?php if($this->configuration['paid_exam'] > 0){?>
                <a class="btn btn-default" href="javascript:void(0);" onclick="check_perform_select('<?php echo $this->ajaxUrl;?>','wallet');"><span class="fa fa-shopping-cart"></span>&nbsp;<?php echo __('Wallet');?></a>
                <a class="btn btn-primary" href="<?php echo $this->url;?>&info=trnhistory" ><span class="fa fa-briefcase"></span>&nbsp;<?php echo __('Transaction History');?></a>
                <?php }?>
            </div>
    </div>
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" value="deleteall" name="selectAll" id="selectAll"></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'user_login');?>"><a href="<?php echo$this->url;?>&info=index&orderby=user_login&order=<?php echo$order;?>"><?php echo __('User Name');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'email');?>"><a href="<?php echo$this->url;?>&info=index&orderby=email&order=<?php echo$order;?>"><?php echo __('E-Mail');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'name');?>"><a href="<?php echo$this->url;?>&info=index&orderby=name&order=<?php echo$order;?>"><?php echo __('Name');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Groups');?></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'register');?>"><a href="<?php echo$this->url;?>&info=index&orderby=register&order=<?php echo$order;?>"><?php echo __('Register Date');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'status');?>"><a href="<?php echo$this->url;?>&info=index&orderby=status&order=<?php echo$order;?>"><?php echo __('Status');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Action');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;
                    foreach($result as $post){$id=$post['id'];$serialNo++;?>
                    <tr>
                        <td><input type="checkbox" value="<?php echo$post['id'];?>" name="id[]" id="DeleteCheckbox<?php echo$id;?>" class="chkselect"></td>
                        <td><?php echo $serialNo;?></td>
                        <td><?php echo $this->ExamApp->h($post['userName']);?></td>
                        <td><?php echo $this->ExamApp->h($post['email']);?></td>
                        <td><?php echo $this->ExamApp->h($post['name']);?></td>
                        <td><?php echo $this->ExamApp->showGroupName("emp_student_groups","emp_groups","student_id",$id);?></td>
                        <td><?php echo $this->ExamApp->dateFormat($post['register']);?></td>
                        <td><?php if($post['status']=="Active"){?><a href="<?php echo$this->ajaxUrl;?>&info=studentstatus&value=Suspend&id=<?php echo$id;?>" class='btn btn-success btn-xs'><?php echo __($post['status']);?></a> <?php }
			    elseif($post['status']=="Pending"){?><a href="<?php echo$this->ajaxUrl;?>&info=studentstatus&value=Active&id=<?php echo$id;?>" class='btn btn-warning btn-xs'><?php echo __($post['status']);?></a><?php }
                            elseif($post['status']=="Suspend"){?><a href="<?php echo$this->ajaxUrl;?>&info=studentstatus&value=Active&id=<?php echo$id;?>" class='btn btn-danger btn-xs'><?php echo __($post['status']);?></a> <?php }
                            else{?><span class="label label-default"><?php echo __($post['status']);?></span> <?php }?>
                            </td>
                        <td>
                            <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo __('Action');?>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0);" name="walletallfrm" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=view&id=<?php echo $id;?>');"><span class="fa fa-arrows-alt"></span>&nbsp;<?php echo __('View');?></a></li>
                            <?php if($this->configuration['paid_exam'] > 0){?>
                            <li><a href="javascript:void(0);" name="walletallfrm" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=wallet&id=<?php echo $id;?>');"><span class="fa fa-shopping-cart"></span>&nbsp;<?php echo __('Wallet');?></a></li>
                             <?php }?>                    
                            <li><a href="javascript:void(0);" name="editallfrm" onclick="check_perform_sedit('<?php echo $this->ajaxUrl;?>','<?php echo$id;?>');"><span class="fa fa-edit"></span>&nbsp;<?php echo __('Edit');?></a></li>
                            <li><a href="javascript:void(0);" onclick="check_perform_sdelete('<?php echo$id;?>');"><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete');?></a></li>
                            </ul>
                            </div>
                        </td>
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