
<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
 
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Exams');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
            <div class="btn-group">
                <a class="btn btn-success" href="<?php echo$this->url;?>&info=add"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Add New');?></a>
                <a class="btn btn-warning" href="javascript:void(0);" name="editallfrm" id="editallfrm" onclick="check_perform_edit('<?php echo$this->ajaxUrl;?>');"><span class="fa fa-edit"></span>&nbsp;<?php echo __('Edit');?></a>
                <a class="btn btn-danger" href="javascript:void(0);" onclick=check_perform_delete();><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete');?></a>
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
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Exam.id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'name');?>"><a href="<?php echo$this->url;?>&info=index&orderby=name&order=<?php echo$order;?>"><?php echo __('Exam Name');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'start_date');?>"><a href="<?php echo$this->url;?>&info=index&orderby=start_date&order=<?php echo$order;?>"><?php echo __('Start & End Date');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Group');?></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'type');?>"><a href="<?php echo$this->url;?>&info=index&orderby=type&order=<?php echo$order;?>"><?php echo __('Type');?><span class="sorting-indicator"></span></a></th>
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
                        <td><?php echo $this->ExamApp->h($post['name']);?></td>
                        <td><?php echo $this->ExamApp->dateFormat($post['start_date']);?> to <?php echo $this->ExamApp->dateFormat($post['end_date']);?></td>
                        <td><?php echo $this->ExamApp->showGroupName("emp_exam_groups","emp_groups","exam_id",$id);?></td>
                        <td><?php echo $this->ExamApp->h($post['type']);?></td>
                        <td>
                        <?php if($post['status']=="Active"){?><a href="<?php echo$this->ajaxUrl;?>&info=activateexam&value=Inactive&id=<?php echo $id;?>" class='btn btn-success'><?php echo __($post['status']);?></a> <?php }
			    elseif($post['status']=="Inactive"){?><a href="<?php echo$this->ajaxUrl;?>&info=activateexam&value=Active&id=<?php echo $id;?>" class='btn btn-info'><?php echo __($post['status']);?></a> <?php }
                            elseif($post['status']=="Closed"){   echo'<span class="label label-danger">'.__($post['status']).'</span>';}
			    else {?><a href="#" class='btn btn-info'><?php echo __($post['status']);?></a><?php }?>
                        </td>    
                        <td>
                            <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo __('Action');?>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=view&id=<?php echo $id;?>');"><span class="fa fa-arrows-alt"></span>&nbsp;<?php echo __('View');?></a></li>
                            <?php if($post['type']=="Exam" && $post['status']=="Inactive"){?>
                            <li><a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=maxquestion&id=<?php echo $id;?>');"><span class="fa fa-server"></span>&nbsp;<?php echo __('Questions Attempt Count');?></a></li>
                            <?php }?>
                            <?php if($post['status']=="Closed"){?>
                            <li>
                            <a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=stats&id=<?php echo $id;?>');"><span class="fa fa-server"></span>&nbsp;<?php echo __('Stats');?></a>
                            </li>
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