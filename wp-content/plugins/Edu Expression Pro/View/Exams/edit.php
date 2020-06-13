<?php if($mathEditor)$editorType="math";else$editorType="full";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';?>
<script type='text/javascript' src='<?php echo plugin_dir_url(__FILE__);?>../../js/main.custom.min.js'></script>
<div <?php if(!$isError){?>class="container"<?php }?>>    
    <div class="panel panel-custom mrg">
        <div class="panel-heading"><?php echo __('Edit').'&nbsp;'.__('Exams');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
        <div class="panel-body">
			<form class="form-horizontal validate" action="<?php echo$this->url;?>&info=edit" method="post">
			<?php foreach($resultArr as $k=>$post){ $id=$post['id']; ?>
			<script type="text/javascript">
	    $(document).ready(function(){        
	    $('#start_date<?php echo$id;?>').datetimepicker({locale:'<?php echo $configLanguage;?>',format:'<?php echo $this->ExamApp->datePickerFormat();?> HH:mm'});
	    $('#end_date<?php echo$id;?>').datetimepicker({locale:'<?php echo $configLanguage;?>',format:'<?php echo $this->ExamApp->datePickerFormat();?> HH:mm',useCurrent: false //Important! See issue #1075
	    });
	    $("#start_date<?php echo$id;?>").on("dp.change", function (e) {
		$('#end_date<?php echo$id;?>').data("DateTimePicker").minDate(e.date);
	    });
	    $("#end_date<?php echo$id;?>").on("dp.change", function (e) {
		$('#start_date<?php echo$id;?>').data("DateTimePicker").maxDate(e.date);
	    });	
	    $('#<?php echo$k;?>ExamPaidExam').click(function(){
	    $('#<?php echo$k;?>paidExam').hide();
	    });
	    $('#<?php echo$k;?>ExamPaidExam1').click(function(){
	    $('#<?php echo$k;?>paidExam').show();
	    });
	    $('#<?php echo$k;?>paidExam').hide();
	    <?php if($post['paid_exam']==1){?>
	    $('#<?php echo$k;?>paidExam').show();<?php }?>
});
</script>
			<div class="panel panel-default">
				<div class="panel-heading"><strong><small class="text-danger"><?php echo __('Form');?> <?php echo$k;?></small></strong></div>
				<div class="panel-body">
				<div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Name of Exam');?></small></label>
                        <div class="col-sm-4">
			    <input type="text" name="data[<?php echo$k;?>][name]" class="form-control" placeholder="<?php echo __('Name of Exam');?>" value="<?php echo $this->ExamApp->h($post['name']);?>" required="required" /> 
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Passing Percentage');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="data[<?php echo$k;?>][passing_percent]" class="form-control" placeholder="<?php echo __('Passing Percentage');?>" value="<?php echo $this->ExamApp->h($post['passing_percent']);?>" required="required" /> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Instruction');?></small></label>
                        <div class="col-sm-10">
			    <?php echo$tinymce->input("data[$k][instruction]",$post['instruction'],array('placeholder'=>__('Instruction'),'class'=>'form-control','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>			    
                        </div>                        
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Exam Duration (Min.)');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="data[<?php echo$k;?>][duration]" class="form-control" placeholder="<?php echo __('0 for unlimited duration');?>" value="<?php echo $this->ExamApp->h($post['duration']);?>" required="required" /> 
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Attempt Count');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="data[<?php echo$k;?>][attempt_count]" class="form-control" placeholder="<?php echo __('0 for unlimited attempt');?>" value="<?php echo $this->ExamApp->h($post['attempt_count']);?>" required="required" /> 
                           <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Start Date');?></small></label>
                        <div class="col-sm-4">
						<div class="input-group date" id="start_date<?php echo$id;?>">
						<input type="text" name="data[<?php echo$k;?>][start_date]" class="form-control" value="<?php echo $this->ExamApp->dateTimePickerValueFormat($post['start_date']);?>" required/> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i>
					     </div>
						</div>
					     <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('End Date');?></small></label>
							    
					     <div class="col-sm-4">
						<div class="input-group date" id="end_date<?php echo$id;?>">
						<input type="text" name="data[<?php echo$k;?>][end_date]" class="form-control" value="<?php echo $this->ExamApp->dateTimePickerValueFormat($post['end_date']);?>" required/> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i>
					        </div>
						
					     </div>
			      </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Show Answer Sheet');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][declare_result]" value="Yes" <?php if($post["declare_result"]=="Yes"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][declare_result]" value="No" <?php if($post["declare_result"]=="No"){echo "checked";}?> /> <?php echo __('No');?>
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Select Group');?></small></label>
                        <div class="col-sm-4">
			<select name="data[<?php echo$k;?>][group_name][]" class="form-control multiselectgrp" multiple="true">
			<?php echo$groupNameEditArr[$k];?>
			</select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Negative Marking');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][negative_marking]" value="Yes" <?php if($post["negative_marking"]=='Yes'){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][negative_marking]" value="No" <?php  if($post["negative_marking"]=='No'){echo "checked";}?> /> <?php echo __('No');?>
			</div>	    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Random Question');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][ques_random]" value="1" <?php if($post["ques_random"]==1){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][ques_random]" value="0" <?php if($post["ques_random"]==0){echo "checked";}?> /> <?php echo __('No');?>
			</div>                                                                               
                    </div>
		     <?php if($this->configuration['paid_exam']>0){?>
		     <div class="form-group" >
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Paid Exam');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" id="<?php echo$k;?>ExamPaidExam1" name="data[<?php echo$k;?>][paid_exam]" value="1" <?php if($post["paid_exam"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" id="<?php echo$k;?>ExamPaidExam" name="data[<?php echo$k;?>][paid_exam]" value="" <?php if($post["paid_exam"]==""){echo "checked";}?> /> <?php echo __('No');?>
			</div>
			<div id="<?php echo$k;?>paidExam">
                        <label for="group_name" class="col-sm-1 control-label"><small><?php echo __('Amount');?></small></label>
                        <div class="col-sm-2">
			    <input type="number" name="data[<?php echo$k;?>][amount]" id="amount" class="form-control" placeholder="<?php echo __('Amount');?>" value="<?php echo $this->ExamApp->h($post['amount']);?>" />
	                </div>
			<?php if($this->configuration['exam_expiry']){?>
			<label for="group_name" class="col-sm-1 control-label"><small><?php echo __('Expiry Days');?></small></label>
			<div class="col-sm-2">
			    <input type="number" name="data[<?php echo$k;?>][expiry]" id="expiry" class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo $this->ExamApp->h($post['expiry']);?>"/>
	                </div>			
			<?php }?>
			</div>
                    </div>
		    <?php }?>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Result After Finish');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][finish_result]" value="1" <?php if($post["finish_result"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][finish_result]" value="0" <?php if($post["finish_result"]=="0"){echo "checked";}?> /> <?php echo __('No');?>
			</div>
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Browser Tolrence');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][browser_tolrance]" value="1" <?php if($post["browser_tolrance"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][browser_tolrance]" value="0" <?php if($post["browser_tolrance"]=="0"){echo "checked";}?> /> <?php echo __('No');?>
                        </div>			
		    </div>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Instant Result');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][instant_result]" value="1" <?php if($post["instant_result"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][instant_result]" value="0" <?php if($post["instant_result"]=="0"){echo "checked";}?> /> <?php echo __('No');?>
			</div>
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Option Shuffle');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="data[<?php echo$k;?>][option_shuffle]" value="1" <?php if($post["option_shuffle"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="data[<?php echo$k;?>][option_shuffle]" value="0" <?php if($post["option_shuffle"]=="0"){echo "checked";}?> /> <?php echo __('No');?>
                        </div>			
		    </div>
		    <div class="form-group">
						<div class="col-sm-9">
						<input type="hidden" name="data[<?php echo$k;?>][id]" value="<?php echo$post['id'];?>" class="form-control" />
						</div>
					</div>
			<?php }?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-success" name="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Update');?></button>
					<?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{?>
					<a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a><?php }?>
					<input type="hidden" name="id" value="<?php echo$_REQUEST['id'];?>">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>