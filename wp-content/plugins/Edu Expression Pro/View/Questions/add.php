<?php if($mathEditor)$editorType="math";else$editorType="full";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#qtype_id1').prop('checked', true);
	$('#tf').hide();
        $('#ftb').hide();
	<?php if($_POST){?>
	$('#myquestiontab').hide();
        <?php if($_POST['qtype_id']==1){?>
        $('#myquestiontab').show();<?php }
        elseif($_POST['qtype_id']==2){?>
        $('#tf').show();<?php }
        elseif($_POST['qtype_id']==3){?>
        $('#ftb').show();<?php }}?>
	
	$('#qtype_id1').click(function() {
            $('#myquestiontab').show();
            $('#tf').hide();
            $('#ftb').hide();
        });
        $('#qtype_id2').click(function() {
            $('#tf').show();
            $('#myquestiontab').hide();
            $('#ftb').hide();
        });
        $('#qtype_id3').click(function() {
            $('#ftb').show();
            $('#myquestiontab').hide();
            $('#tf').hide();
        });
        $('#qtype_id4').click(function() {
            $('#ftb').hide();
            $('#myquestiontab').hide();
            $('#tf').hide();
        });        
        });
</script>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Add New Questions');?></h1></div></div>
<div class="panel">    
                <div class="panel-body">
                <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
		 <div class="col-md-12">
                 <div class="row">
                 <h5><strong><?php echo __('Question Type');?></strong></h5>
                 <div class="panel panel-default">
                 <div class="panel-body">                 
                 <?php foreach($records as $record):?>
                    <label><input type="radio" class="radio-inline" name="qtype_id" id="<?php echo "qtype_id".$record['id'];?>" value="<?php echo $record['id'];?>"  >&nbsp;&nbsp;&nbsp;  <?php echo $this->ExamApp->h($record['question_type']);?></label>&nbsp;&nbsp;&nbsp;
                 <?php endforeach;unset($record);?>
                 </div>
                 </div>
                 </div>
                 </div></div>
                
                <ul class="nav nav-tabs" id="myquestiontab">
                <li class="active" ><a href="#Question" data-toggle="tab"><?php echo __('Question');?></a></li>                
                <li><a href="#Answer1" data-toggle="tab"><?php echo __('Option1');?></a></li>
                <li><a href="#Answer2" data-toggle="tab"><?php echo __('Option2');?></a></li>
                <li><a href="#Answer3" data-toggle="tab"><?php echo __('Option3');?></a></li>
                <li><a href="#Answer4" data-toggle="tab"><?php echo __('Option4');?></a></li>
		<li><a href="#Answer5" data-toggle="tab"><?php echo __('Option5');?></a></li>
		<li><a href="#Answer6" data-toggle="tab"><?php echo __('Option6');?></a></li>
                <li><a href="#CorrectAnswer" data-toggle="tab"><?php echo __('Correct Answers');?></a></li>                
                </ul>                    
                    <div class="tab-content">
                    <div class="tab-pane active" id="Question">
		    <h4><?php echo __('Question');?></h4><hr/>
		       <?php echo$tinymce->input('question',$_POST['question'],array('placeholder'=>__('Type your question here'),'class'=>'form-control','id'=>'question','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                    </div>
								<div class="tab-pane" id="Answer1">
								    <?php echo$tinymce->input('option1',$_POST['option1'],array('placeholder'=>__('option1'),'class'=>'form-control','id'=>'option1','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer2">
								    <?php echo$tinymce->input('option2',$_POST['option2'],array('placeholder'=>__('option2'),'class'=>'form-control','id'=>'option2','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer3">
								    <?php echo$tinymce->input('option3',$_POST['option3'],array('placeholder'=>__('option3'),'class'=>'form-control','id'=>'option3','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
							        </div>
								<div class="tab-pane" id="Answer4">
								    <?php echo$tinymce->input('option4',$_POST['option4'],array('placeholder'=>__('option4'),'class'=>'form-control','id'=>'option4','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
							        </div>
								<div class="tab-pane" id="Answer5">
								    <?php echo$tinymce->input('option5',$_POST['option5'],array('placeholder'=>__('option5'),'class'=>'form-control','id'=>'option5','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
							        </div>
								<div class="tab-pane" id="Answer6">
								    <?php echo$tinymce->input('option6',$_POST['option6'],array('placeholder'=>__('option6'),'class'=>'form-control','id'=>'option6','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
							        </div>
                                                                <div class="tab-pane" id="CorrectAnswer">
                                                                <?php for($i=1;$i<=6;$i++){?>
                                                                <input type="checkbox" name="answer1[]" value="<?php echo $i;?>"  <?php if($_POST['answer1']){ foreach($_POST['answer1'] as $checkValue){ if($checkValue==$i){echo 'checked';} }} ?>/>  <?php echo 'Answer'.$i;?><br/>
                                                                <?php }?>
                                                                </div>
							</div>
                                                         <div class="panel-body" id="tf">
                                                         <div class="col-md-12">
                                                         <div class="row">
                                                         <h5><strong><?php echo __('True/False');?></strong></h5>
                                                         <div class="panel panel-default">
                                                         <div class="panel-body"> 
                                                        <input type="radio" name="true_false" value="True" <?php if($_POST["true_false"]=="True"){echo "checked";}?>/> <?php echo __('True');?>
                                                        <input type="radio" name="true_false" value="False" <?php if($_POST["true_false"]=="False"){echo "checked";}?>/> <?php echo __('False');?>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
							 <div class="panel-body">
                                                  <div class="form-group" id="ftb">
						  <?php echo __('Blank Space');?>
						  <textarea name="fill_blank"  placeholder="<?php echo __('Blank Space');?>" class="form-control" cols="37" rows="6"><?php echo $this->ExamApp->h($_POST["fill_blank"]);?></textarea>
                                                     
                                                  </div>
						<label for="group_name" class="col-sm-3 control-label"><?php echo __('Explanation (Optional)');?></label>
						<div class="form-group">
						    <?php echo$tinymce->input('explanation',$_POST['explanation'],array('placeholder'=>__('Provide explanation in support of correct answer'),'class'=>'form-control','id'=>'explanation','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                </div>
                                                <div class="form-group">
								<label for="group_name" class="col-sm-1 control-label"><?php echo __('Groups');?></label>
								<div class="col-sm-5">
                                                                <select name="group_name[]" class="form-control multiselectgrp" multiple="true">
                                                                    <?php echo$groupName;?>
                                                                 </select>
								</div>
								<label for="group_name" class="col-sm-1 control-label"><?php echo __('Subject');?></label>
								<div class="col-sm-5">
								    <select name="subject_id" class="form-control " required>
                                                                    <option value=""><?php echo __('Please Select');?></option>
                                                                    <?php echo$subjectName;?>
                                                                 </select>
								</div>
							    </div>
							     <div class="col-sm-12">
							     <div class="row">
                                                             <label for="group_name" class="col-sm-3 control-label"><?php echo __('Hint(Optional)');?></label>
							 <div class="form-group">
                                                                    <input type="text" name="hint" class="form-control" placeholder="<?php echo __('Hint to help answer correctly');?>" value="<?php echo $this->ExamApp->h($_POST['hint']);?>"  />
                                                  		</div>
							     </div>
							     </div>
								<div class="col-md-4">
									<div class="row">
										<div class="form-group">
                                                                                <label for="group_name" class="col-sm-1 control-label"><?php echo __('Marks');?></label>
										    <input type="number" step="any" name="marks" class="form-control" placeholder="<?php echo __('Marks');?>" value="<?php echo $this->ExamApp->h($_POST['marks']);?>" required="required" />
										</div>
									</div>
								</div>
								<div class="col-md-4">
                                                                <label for="group_name" class="col-sm-5 control-label"><?php echo __('Negative Marks');?></label>
                                                                        <div class="form-group">
                                                                            <input type="number" step="any" name="negative_marks" class="form-control" placeholder="<?php echo __('without minus sign');?>" value="<?php echo $this->ExamApp->h($_POST['negative_marks']);?>" required="required" /> 
                                                                       </div>
								</div>								
								<div class="col-md-4">
                                                                <label for="group_name" class="col-sm-4 control-label"><?php echo __('Diffculty Level');?></label>
                                                                    <div class="form-group">
                                                                    <select name="diff_id" class="form-control " required>
                                                                    <option value=""><?php echo __('Please Select');?></option>
                                                                    <?php echo$diffName;?>
                                                                 </select>
                                                                    </div>
								</div>
								<div class="form-group text-left">
								<div class="col-sm-7">
								    <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                                                                    <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
            						    </div>
							    </div>
							    </form>
</div>
</div>