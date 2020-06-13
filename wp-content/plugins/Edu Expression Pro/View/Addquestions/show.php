<?php if($mathEditor){?><script>MathJax.Hub.Typeset();</script><?php }?>
<?php
$order=$this->ExamApp->sortableOrder($_REQUEST);
?>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Add Questions To Exam');?></h1></div></div>
    <div class="panel">
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
                        <a href="<?php echo $this->url;?>&examId=<?php echo$_REQUEST['examId'];?>" class='btn btn-warning'><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Reset');?></a>
                 </div>
		</div>
		</form>
    
            <div class="btn-group">
                <a class="btn btn-success" href="javascript:void(0);" onclick="all_question('add');"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Add').' '.__('To').' '.__('Exam');?></a>
                <a class="btn btn-danger" href="javascript:void(0);" name='deleteallfrm' id='deleteallfrm' onclick="all_question('delete');"><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete').' '.__('To').' '.__('Exam');?></a>
                <a href="<?php echo $this->urlExam;?>" class='btn btn-info'><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back').' '.__('To').' '.__('Exam');?></a>
            </div>
    <?php echo$paginate;?>
    <form name="deleteallfrm" method="post" action="<?php echo$this->url;?>&info=adddelete">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" value="deleteall" name="selectAll" id="selectAll"></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'id');?>"><a href="<?php echo$this->url;?>&info=index&orderby=id&order=<?php echo$order;?>"><?php echo __('#');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'Subject.subject_name');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Subject.subject_name&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Subject');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'Qtype.question_type');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Qtype.question_type&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Type');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'question');?>"><a href="<?php echo$this->url;?>&info=index&orderby=question&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Body of Question');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'Diff.diff_level');?>"><a href="<?php echo$this->url;?>&info=index&orderby=Diff.diff_level&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Level');?><span class="sorting-indicator"></span></a></th>
                        <th class="<?php echo $this->ExamApp->recordSorting($_REQUEST,'marks');?>"><a href="<?php echo$this->url;?>&info=index&orderby=marks&order=<?php echo$order;?>&examId=<?php echo $_REQUEST['examId'];?>"><?php echo __('Marks');?><span class="sorting-indicator"></span></a></th>
                        
                        <th><?php echo __('Action');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo=$mainSerial-1;
                    foreach($result as $post){$id=$post['id'];$serialNo++;?>
                    <tr>
                        <td><input type="checkbox" value="<?php echo$post['id'];?>" name="id[]" id="DeleteCheckbox<?php echo$id;?>" class="chkselect"></td>
                        <td><?php echo $serialNo;?></td>
                        <td><?php echo $this->ExamApp->h($post['subjectName']);?></td>
                        <td><?php echo $this->ExamApp->h($post['qtypeName']);?></td>
                        <td><?php echo str_replace("<script","",($post['question']));?></td>
                        <td><?php echo $this->ExamApp->h($post['diffName']);?></td>
                        <td><?php echo $this->ExamApp->h($post['Marks']);?></td>
                        <td>
                        <?php $is_question=false;                            
                            foreach($resultExamQuestion as $eq)
                            {
                            if($eq['question_id']==$id)
                            {
                                $is_question=true;break;
                            }
                            $is_question=false;
                            }
                    
                            if($is_question==true)                            
                            {?>
                             <a class="btn btn-danger" href="javascript:void(0);" onclick="single_question('delete','<?php echo$id;?>');"><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete').' '.__('To').' '.__('Exam');?></a>  
                            <?php }else {?>
                            <a class="btn btn-success" href="javascript:void(0);" onclick="single_question('add','<?php echo$id;?>');"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Add').' '.__('To').' '.__('Exam');?></a>
                            <?php }?>
                        
                        
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="examId" value="<?php echo$_REQUEST['examId'];?>">
        <input type="hidden" name="subject_id" value="<?php echo$_REQUEST['subject_id'];?>">
        <input type="hidden" name="qtype_id" value="<?php echo$_REQUEST['qtype_id'];?>">
        <input type="hidden" name="diff_id" value="<?php echo$_REQUEST['diff_id'];?>">
        <input type="hidden" name="action" value="">
        </form>
        <?php echo$paginate;?>
    </div>
</div>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content"></div>
</div>