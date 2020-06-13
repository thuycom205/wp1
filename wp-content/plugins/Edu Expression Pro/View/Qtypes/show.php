<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Diffculty Level');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
            <div class="btn-group">
                <a class="btn btn-warning" href="javascript:void(0);" name="editallfrm" id="editallfrm" onclick="check_perform_edit('<?php echo$this->ajaxUrl;?>');"><span class="fa fa-edit"></span>&nbsp;<?php echo __('Edit');?></a>
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
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'question_type');?>"><a href="<?php echo$this->url;?>&info=index&orderby=question_type&order=<?php echo$order;?>"><?php echo __('Name');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Action');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;
                    foreach($result as $post){$id=$post['id'];$serialNo++;?>
                    <tr>
                        <td><input type="checkbox" value="<?php echo$post['id'];?>" name="id[]" id="DeleteCheckbox<?php echo$id;?>" class="chkselect"></td>
                        <td><?php echo $serialNo;?></td>
                        <td><?php echo $this->ExamApp->h($post['question_type']);?></td>
                        <td>
                            <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo __('Action');?>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0);" name="editallfrm" onclick="check_perform_sedit('<?php echo $this->ajaxUrl;?>','<?php echo$id;?>');"><span class="fa fa-edit"></span>&nbsp;<?php echo __('Edit');?></a></li>
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