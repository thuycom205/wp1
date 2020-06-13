<?php if($mathEditor)$editorType="math";else$editorType="full";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';?>
<script type='text/javascript' src='<?php echo plugin_dir_url(__FILE__);?>../../js/main.custom.min.js'></script>
 <?php
		$answerSelect=array();
                if($resultArr[1]['qtype_id']==1)
                { 
                    $answerSelect=explode(",",$resultArr[1]['answer']);
                }
?>		
<script type="text/javascript">
    $(document).ready(function(){
	$('#myquestiontab').hide();
        $('#tf').hide();
        $('#ftb').hide();
    	<?php if($resultArr){?>
	$('#myquestiontab').hide();
        <?php if($resultArr[1]['qtype_id']==1){?>
        $('#myquestiontab').show();<?php }
        elseif($resultArr[1]['qtype_id']==2){?>
        $('#tf').show();<?php }
        elseif($resultArr[1]['qtype_id']==3){?>
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
<div <?php if(!$isError){?>class="container"<?php }?>>
<div class="panel panel-custom mrg">
<div class="panel-heading"><?php echo __('Edit').'&nbsp;'.__('Question');?><?php if($isError==false){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
<div class="panel"> <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=edit" method="post">  
                <div class="panel-body">
		 <div class="col-md-12">
                 <div class="row">
                 <h5><strong><?php echo __('Question Type');?></strong></h5>
                 <div class="panel panel-default">
                 <div class="panel-body">
		 <?php
                 foreach($records as $record):?>
                 <label><input type="radio" class="radio-inline" name="data[qtype_id]" id="<?php echo "qtype_id".$record['id'];?>" value="<?php echo $record['id'];?>" <?php if($record['id']==$resultArr[1]['qtype_id']){echo 'checked';}?> ></label>&nbsp;&nbsp;&nbsp;  <?php echo __($this->ExamApp->h($record['question_type']));?>&nbsp;&nbsp;&nbsp;
                 <?php endforeach;unset($record);?>
                 </div>
                 </div>
                 </div>
                 </div></div>
                
                <ul class="nav nav-tabs" id="myquestiontab">
                <li class="active"><a href="#Question" data-toggle="tab"><?php echo __('Question');?></a></li>                
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
		         <?php echo$tinymce->input("data[question]",$resultArr[1]['question'],array('placeholder'=>__('Type your question here'),'class'=>'form-control','id'=>'question','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                   </div>
								<div class="tab-pane" id="Answer1">
								    <?php echo$tinymce->input("data[option1]",$resultArr[1]['option1'],array('placeholder'=>__('option1'),'class'=>'form-control','id'=>'option1','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer2">
                                                                    <?php echo$tinymce->input("data[option2]",$resultArr[1]['option2'],array('placeholder'=>__('option2'),'class'=>'form-control','id'=>'option2','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer3">
								   <?php echo$tinymce->input("data[option3]",$resultArr[1]['option3'],array('placeholder'=>__('option3'),'class'=>'form-control','id'=>'option3','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer4">
								    <?php echo$tinymce->input("data[option4]",$resultArr[1]['option4'],array('placeholder'=>__('option4'),'class'=>'form-control','id'=>'option4','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer5">
								   <?php echo$tinymce->input("data[option5]",$resultArr[1]['option5'],array('placeholder'=>__('option5'),'class'=>'form-control','id'=>'option5','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
								<div class="tab-pane" id="Answer6">
									<?php echo$tinymce->input("data[option6]",$resultArr[1]['option6'],array('placeholder'=>__('option6'),'class'=>'form-control','id'=>'option6','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                                </div>
                                                                <div class="tab-pane" id="CorrectAnswer">
                                                                <?php for($i=1;$i<=6;$i++){?>
                                                                <input type="checkbox" name="data[answer1][]" value="<?php echo $i;?>"  <?php foreach($answerSelect as $checkValue){ if($checkValue==$i){echo 'checked';} }?>/>  <?php echo 'Answer'.$i;?><br/>
                                                                <?php }?>
                                                                </div>
							</div>
                                                         <div class="panel-body" id="tf">
                                                         <div class="col-md-12">
                                                         <div class="row">
                                                         <h5><strong><?php echo __('True/False');?></strong></h5>
                                                         <div class="panel panel-default">
                                                         <div class="panel-body"> 
                                                        <input type="radio" name="data[true_false]" value="True" <?php if($resultArr[1]['true_false']=="True"){echo "checked";}?>/> <?php echo __('True');?>
                                                        <input type="radio" name="data[true_false]" value="False" <?php if($resultArr[1]['true_false']=="False"){echo "checked";}?>/> <?php echo __('False');?>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
							 <div class="panel-body">
                                                  <div class="form-group" id="ftb">
						  <?php echo __('Blank Space');?>
						  <textarea name="data[fill_blank]"  placeholder="<?php echo __('Blank Space');?>" class="form-control" cols="37" rows="6"><?php echo $this->ExamApp->h($resultArr[1]['fill_blank']);?></textarea>
                                                     
                                                  </div>
						<label for="group_name" class="control-label"><?php echo __('Explanation (Optional)');?></label>
						<div class="form-group">
						     <?php echo$tinymce->input("data[explanation]",$resultArr[1]['explanation'],array('placeholder'=>__('Provide explanation in support of correct answer'),'class'=>'form-control','id'=>'explanation','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                                                </div>
                                                <div class="form-group">
								<label for="group_name" class="col-sm-1 control-label"><?php echo __('Groups');?></label>
								<div class="col-sm-5">
                                                                <select name="data[group_name][]" class="form-control multiselectgrp" multiple="true">
                                                                    <?php echo$groupNameEditArr;?>
                                                                 </select>
								</div>
								<label for="group_name" class="col-sm-1 control-label"><?php echo __('Subject');?></label>
								<div class="col-sm-5">
								    <select name="data[subject_id]" class="form-control " required>
                                                                    <option value=""><?php echo __('Please Select');?></option>
                                                                    <?php echo$subjectName;?>
                                                                 </select>
								</div>
							    </div>
							     <div class="col-sm-12">
							     <div class="row">
                                                             <label for="group_name" class="control-label"><?php echo __('Hint(Optional)');?></label>
							     <div class="form-group">
                                                                    <input type="text" name="data[hint]" class="form-control" placeholder="<?php echo __('Hint to help answer correctly');?>" value="<?php echo $this->ExamApp->h($resultArr[1]['hint']);?>"  />
                                                  		</div>
							     </div>
							     </div>
								<div class="col-md-4">
									<div class="row col-sm-12">
										<div class="form-group">
                                                                                <label for="group_name" class="control-label"><?php echo __('Marks');?></label>
										    <input type="number" step="any" name="data[marks]" class="form-control" placeholder="<?php echo __('Marks');?>" value="<?php echo $this->ExamApp->h($resultArr[1]['marks']);?>" required="required" />
										</div>
									</div>
								</div>
								<div class="col-md-4">
								<div class="row col-sm-12">
                                                                <label for="group_name" class="control-label"><?php echo __('Negative Marks');?></label>
                                                                        <div class="form-group">
                                                                            <input type="number" step="any"  name="data[negative_marks]" class="form-control" placeholder="<?php echo __('without minus sign');?>" value="<?php echo $this->ExamApp->h($resultArr[1]['negative_marks']);?>" required="required" /> 
                                                                       </div>
								</div>
								</div>
								<div class="col-md-4">
								<div class="row col-sm-12">
                                                                <label for="group_name" class="control-label"><?php echo __('Diffculty Level');?></label>
                                                                    <div class="form-group" >
                                                                    <select name="data[diff_id]" class="form-control" required>
                                                                    <option value=""><?php echo __('Please Select');?></option>
                                                                    <?php echo$diffName;?>
                                                                 </select>
                                                                    </div>
								</div>
								</div>
								<div class="form-group">
								    <div class="col-sm-9">
									<input type="hidden" name="data[id]" value="<?php echo$resultArr[1]['id'];?>" class="form-control" />
								    </div>
								</div>
								<div class="form-group text-left">
								<div class="col-sm-7">
								    <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                                                                    <?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
								    <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
								    <input type="hidden" name="id" value="<?php echo$_REQUEST['id'];?>">
							    </div>
							    </div>
							    </form>
</div>
</div>
