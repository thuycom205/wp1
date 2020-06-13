<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('User Groups');?></h1></div></div>
<div class="panel">
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'username');?>"><a href="<?php echo$this->url;?>&info=index&orderby=username&order=<?php echo$order;?>"><?php echo __('User Name');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'email');?>"><a href="<?php echo$this->url;?>&info=index&orderby=email&order=<?php echo$order;?>"><?php echo __('E-Mail');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'name');?>"><a href="<?php echo$this->url;?>&info=index&orderby=name&order=<?php echo$order;?>"><?php echo __('Name');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Role');?></th>
                        <th><?php echo __('Groups');?></th>
                        <th><?php echo __('Action');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;
                    foreach($result as $post){$id=$post['id'];$serialNo++;?>
                    <tr>
                        <td><?php echo $serialNo;?></td>
                        <td><?php echo $post['username'];?></td>
                        <td><?php echo $this->ExamApp->h($post['email']);?></td>
                        <td><?php echo $this->ExamApp->h($post['name']);?></td>
                        <td><?php echo __('Contributor');?></td>
                        <td><?php echo $this->ExamApp->showGroupName("emp_user_groups","emp_groups","user_id",$id);?></td>
                        <td>
                           <a href="javascript:void(0);" class="btn btn-success" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=addusergroup&id=<?php echo $id;?>');"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Set User Group');?></a> 
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