<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<?php if($mathEditor){?><script>MathJax.Hub.Typeset();</script><?php }?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Questions');?></h1></div></div>
<div class="panel">
<div class="panel-heading">
            <div class="btn-group">
                <a class="btn btn-success" href="<?php echo$this->url;?>&info=add"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Add New');?></a>
                <a class="btn btn-danger" href="javascript:void(0);" onclick=check_perform_delete();><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete');?></a>
                <a class="btn btn-info" href="<?php echo $this->Iequestions;?>&info=index" ><span class="fa fa-exchange"></span>&nbsp;<?php echo __('Import/Export Question');?></a> 
            </div>
    </div>
 <form name="searchfrm" method="post" action="<?php echo$this->ajaxUrl;?>&info=index">    
        	<div class="row mrg">
		    <div  class="col-md-3">
                     <select name="subject_id" id="subject_id" class="form-control">
                        <option value=""><?php echo __('Subject');?></option>
                        <?php echo$subjectName;?>
                     </select>
		    </div>
		    <div  class="col-md-3">
                    <select name="qtype_id" id="qtype_id" class="form-control" >
                        <option value=""><?php echo __('Type');?></option>
                        <?php echo$qtypeName;?>
                    </select>
		    </div>
		    <div  class="col-md-3">
                    <select name="diff_id" id="diff_id" class="form-control" >
                        <option value=""><?php echo __('Diffculty Level');?></option>
                        <?php echo$diffName;?>
                    </select>
                    <input type="hidden" name="examId" value="<?php echo $_REQUEST['examId'];?>">
		    </div>
		    <div  class="col-md-3 ">
                        <button type="button" id="search" class="btn btn-success"><span class="fa fa-search"></span>&nbsp;<?php echo __('Search');?></button>
                        <a href="<?php echo $this->url;?>" class='btn btn-warning'><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Reset');?></a>
                 </div>
		</div>
		</form>
    
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=deleteall">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" value="deleteall" name="selectAll" id="selectAll"></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Question.id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'question');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Question.question&order=<?php echo$order;?>"><?php echo __('Question');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'subject_name');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Subject.subject_name&order=<?php echo$order;?>"><?php echo __('Subject');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'Qtype.question_type');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Qtype.question_type&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Type');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'Diff.diff_level');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Diff.diff_level&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Level');?><span class="sorting-indicator"></span></a></th>
                        <th><?php echo __('Group');?></th>
                        <th><?php echo __('Action');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;
                    foreach($result as $post){$id=$post['id'];$serialNo++;?>
                    <tr>
                        <td><input type="checkbox" value="<?php echo$post['id'];?>" name="id[]" id="DeleteCheckbox<?php echo$id;?>" class="chkselect"></td>
                        <td><?php echo $serialNo;?></td>
                        <td><?php echo str_replace("<script","",($post['question']));?></td>
                        <td><?php echo $this->ExamApp->h($post['subject_name']);?></td>
                        <td><?php echo $this->ExamApp->h($post['qtypeName']);?></td>
                        <td><?php echo $this->ExamApp->h($post['diffLevel']);?></td>
                        <td><?php echo $this->ExamApp->showGroupName("emp_question_groups","emp_groups","question_id",$id);?></td>
                        
                        <td>
                            <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo __('Action');?>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=viewquestion&id=<?php echo $id;?>');"><span class="fa fa-arrows-alt"></span>&nbsp;<?php echo __('View');?></a></li>
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