<?php if($mathEditor)$editorType="math";else$editorType="full";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';?>
<script type="text/javascript">
    $(document).ready(function(){        
        $('#start_date').datetimepicker({locale:'<?php echo $configLanguage;?>',format:'<?php echo $this->ExamApp->datePickerFormat();?> HH:mm'});
        $('#end_date').datetimepicker({locale:'<?php echo $configLanguage;?>',format:'<?php echo $this->ExamApp->datePickerFormat();?> HH:mm',useCurrent: false //Important! See issue #1075
        });
        $("#start_date").on("dp.change", function (e) {
            $('#end_date').data("DateTimePicker").minDate(e.date);
        });
        $("#end_date").on("dp.change", function (e) {
            $('#start_date').data("DateTimePicker").maxDate(e.date);
        });	
});
</script>
<script type="text/javascript">
	$(document).ready(function(){
	    $("#addsubject").click(function () {
            var valid = $("#modalForm").validationEngine('validate');
            var vars = $("#modalForm").serialize();
            if (valid == true)
	    {
		var subid='sub'+$('#subjectId').val();
                if($("#" + subid).length == 0)
		showSubject();
		else
		alert("Subject already exist");
            }
	    else
	    {
                $("#modalForm").validationEngine();
            }
	    });
	 $('#showExam').hide();
	 $('#paidExam').hide();
	    $('#ExamTypePrepration').click(function(){
	    $('#showExam').show();
	    });
	$('#ExamTypeExam').click(function(){
	    $('#showExam').hide();
	    });
	$('#ExamPaidExam').click(function(){
	    $('#paidExam').hide();
	    });
	$('#ExamPaidExam1').click(function(){
	    $('#paidExam').show();
	    });
	$('#subjectModal').on('show.bs.modal', function (event) {
	    $('#subjectId').val('');
	    $('#quesNo').val('');
	    $('#type').val('');
	    $('#level').val('');
	    $('#maxQuestion').val('');
	    $('#modalMsg').html('');
	    });
	});
	function showSubject(){
	    sub_arr=$('#subjectId option:selected').text().split(' (Q)');
	    subject_name=sub_arr[0];
	    $('#showdetails').append('<div class="col-sm-12"><div id=sub'+$('#subjectId').val()+'><div><label for="group_name" class="col-sm-2 control-label"><small>'+subject_name+'</small></lable></div>'+
				     '<div><label for="group_name" class="col-sm-2 control-label"><small>'+$('#quesNo').val()+'</small></lable></div>'+
				     '<div><label for="group_name" class="col-sm-2 control-label"><small>'+$('#maxQuestion').val()+'</small></lable></div>'+
				     '<div><label for="group_name" class="col-sm-2 control-label"><small>'+$('#type option:selected').text()+'</small></lable></div>'+
				     '<div><label for="group_name" class="col-sm-2 control-label"><small>'+$('#level option:selected').text()+'</small></lable></div>'+
				     '<div><input type="hidden" name="data[ExamPrep]['+$('#subjectId').val()+'][subject_id]" value="'+$('#subjectId').val()+'"</div>'+
				     '<div><input type="hidden" name="data[ExamPrep]['+$('#subjectId').val()+'][ques_no]" value="'+$('#quesNo').val()+'"</div>'+
				     '<div><input type="hidden" name="data[ExamPrep]['+$('#subjectId').val()+'][max_question]" value="'+$('#maxQuestion').val()+'"</div>'+
				     '<div><input type="hidden" name="data[ExamPrep]['+$('#subjectId').val()+'][type]" value="'+$('#type').val()+'"</div>'+
				     '<div><input type="hidden" name="data[ExamPrep]['+$('#subjectId').val()+'][level]" value="'+$('#level').val()+'"</div>'+
				     '<div class="col-sm-2"><button type="button" class="btn btn-danger" onclick="delItem('+$('#subjectId').val()+');">Remove</button></div>'+
				     '</div>');
	    $('#subjectId').val('');
	    $('#quesNo').val('');
	    $('#type').val('');
	    $('#level').val('');
	    $('#maxQuestion').val('');
	    $('#modalMsg').html('<span class="text-success"><strong><?php echo __('Subject Added successfully');?>!</strong></span>');
	}
	function delItem(id)
	{
	    $('#sub'+id+'').remove();
	}
</script>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Add Exam');?></h1></div></div><div class="panel"><div class="panel">
    <div class="panel-heading">
		</div>	    
                <div class="panel-body">
		<form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Name of Exam');?></small></label>
                        <div class="col-sm-4">
			    <input type="text" name="name" class="form-control" placeholder="<?php echo __('Name of Exam');?>" value="<?php echo $this->ExamApp->h($_POST['name']);?>" required="required" /> 
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Passing Percentage');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="passing_percent" class="form-control" placeholder="<?php echo __('Passing Percentage');?>" value="<?php echo $this->ExamApp->h($_POST['passing_percent']);?>" required="required" /> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Instruction');?></small></label>
                        <div class="col-sm-10">
			    <?php echo$tinymce->input('instruction',$_POST['instruction'],array('placeholder'=>__('Instruction'),'class'=>'form-control','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
                        </div>                        
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Exam Duration (Min.)');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="duration" class="form-control" placeholder="<?php echo __('0 for unlimited duration');?>" value="<?php echo $this->ExamApp->h($_POST['duration']);?>" required="required" /> 
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Attempt Count');?></small></label>
                        <div class="col-sm-4">
			    <input type="number" name="attempt_count" class="form-control" placeholder="<?php echo __('0 for unlimited attempt');?>" value="<?php echo $this->ExamApp->h($_POST['attempt_count']);?>" required="required" /> 
                           <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Start Date');?></small></label>
                        <div class="col-sm-4">
						<div class="input-group date" id="start_date" >
						<input type="text" name="start_date" class="form-control" value="<?php echo $this->ExamApp->h($_POST['start_date']);?>" required="required" /> 
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </div>
						</div>
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('End Date');?></small></label>
                        <div class="col-sm-4">
						<div class="input-group date" id="end_date" >
						<input type="text" name="end_date" class="form-control" value="<?php echo $this->ExamApp->h($_POST['end_date']);?>" required="required" /> 
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </div>
						</div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Show Answer Sheet');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="declare_result" value="Yes" <?php if($_POST["declare_result"]=="Yes"){echo "checked";}?> checked="checked"/> <?php echo __('Yes');?>
			    <input type="radio" name="declare_result" value="No" <?php if($_POST["declare_result"]=="No"){echo "checked";}?>/> <?php echo __('No');?>
                        </div>                    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Select Group');?></small></label>
                        <div class="col-sm-4">
			<select name="group_name[]" class="form-control multiselectgrp" multiple="true">
                        <?php echo$groupName;?>
			</select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Negative Marking');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="negative_marking" value="Yes" <?php if($_POST["negative_marking"]=="Yes"){echo "checked";}?> checked="checked"/> <?php echo __('Yes');?>
			    <input type="radio" name="negative_marking" value="No" <?php if($_POST["negative_marking"]=="No"){echo "checked";}?>/> <?php echo __('No');?>
			</div>	    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Random Question');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="ques_random" value="1" <?php if($_POST["ques_random"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="ques_random" value="0" <?php if($_POST["ques_random"]=="0"){echo "checked";}?> checked/> <?php echo __('No');?>
			</div>                                                                               
                    </div>
		     <?php if($this->configuration['paid_exam']>0){?>
		     <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Paid Exam');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio"  id="ExamPaidExam1"name="paid_exam" value="1" <?php if($_POST["paid_exam"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" id="ExamPaidExam" name="paid_exam" value="" <?php if($_POST["paid_exam"]==""){echo "checked";}?> checked="checked"/> <?php echo __('No');?>
			</div>
			<div id="paidExam">
                        <label for="group_name" class="col-sm-1 control-label"><small><?php echo __('Amount');?></small></label>
                        <div class="col-sm-2">
			    <input type="number" name="amount" id='amount' class="form-control" placeholder="<?php echo __('Amount');?>" value="<?php echo $this->ExamApp->h($_POST['amount']);?>" />
	                </div>
			<?php if($this->configuration['exam_expiry']){?>
			<label for="group_name" class="col-sm-1 control-label"><small><?php echo __('Expiry Days');?></small></label>
			<div class="col-sm-2">
			    <input type="number" name="expiry" id='expiry' class="form-control" placeholder="<?php echo __('0 for Unlimited');?>" value="<?php echo $this->ExamApp->h($_POST['expiry']);?>" />
	                </div>
			<?php }?>
			</div>
                    </div>
		    <?php }?>
		    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Browser Tolrence');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="browser_tolrance" value="1" <?php if($_POST["browser_tolrance"]=="1"){echo "checked";}?> checked="checked"/> <?php echo __('Yes');?>
			    <input type="radio" name="browser_tolrance" value="0" <?php if($_POST["browser_tolrance"]=="0"){echo "checked";}?>/> <?php echo __('No');?>
                        </div>			
		    </div>	
		    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Result After Finish');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="finish_result" value="1" <?php if($_POST["finish_result"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="finish_result" value="0" <?php if($_POST["finish_result"]=="0"){echo "checked";}?> checked="checked"/> <?php echo __('No');?>
			</div>
			<label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Mode');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" id="ExamTypeExam" name="type" value="Exam" <?php if($_POST["type"]=="Exam"){echo "checked";}?> checked/> <?php echo __('Exam');?>
			    <input type="radio" id="ExamTypePrepration" name="type" value="Prepration" <?php if($_POST["type"]=="Prepration"){echo "checked";}?>/> <?php echo __('Prepration');?>
			</div>                                                                               
                    </div>
		    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Instant Result');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="instant_result" value="1" <?php if($_POST["instant_result"]=="1"){echo "checked";}?>/> <?php echo __('Yes');?>
			    <input type="radio" name="instant_result" value="0" <?php if($_POST["instant_result"]=="0"){echo "checked";}?> checked="checked"/> <?php echo __('No');?>
			</div>	    
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Option Suffle');?></small></label>
                        <div class="col-sm-4">
			    <input type="radio" name="option_shuffle" value="1" <?php if($_POST["option_shuffle"]=="1"){echo "checked";}?>  checked="checked"/> <?php echo __('Yes');?>
			    <input type="radio" name="option_shuffle" value="0" <?php if($_POST["ques_random"]=="0"){echo "checked";}?>/> <?php echo __('No');?>
			</div>                                                                               
                    </div>
		    <div id="showExam">
		    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small>&nbsp;</small></label>
                        <div class="col-sm-4">
                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#subjectModal"><?php echo __('Add Subjects To Exams');?></button>
			</div>	    
                        <div class="col-sm-2">                           
                        </div>                                                                               
                    </div>
		    <div class="form-group">
			<div><label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Subject Name');?></small></label></div>
			<div><label for="group_name" class="col-sm-2 control-label"><small><?php echo __('No. of Questions');?></small></label></div>
			<div><label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Ques Attempt Count');?></small></label></div>
			<div><label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Question Type');?></small></label></div>
			<div><label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Difficulty Level');?></small></label></div>
			<div><label for="group_name" class="col-sm-2 control-label"><small>&nbsp;</small></label></div>
		    </div>
		    <div class="form-group" id="showdetails">
		    </div>
		    </div>
		    <div class="form-group text-left">
                        <div class="col-sm-offset-2 col-sm-7">
                            <button type="submit" class="btn btn-success" name="submit"><span class="glyphicon glyphicon-plus-sign"></span> <?php echo __('Save');?></button>
                            <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
			    </div>
                    </div>
                </form>
                </div>
            </div>
<div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel"><?php echo __('Add Subjects To Exams');?></h4>
      </div>
      <div class="modal-body">
        <form name="modalForm" id="modalForm" class="form-horizontal">
	<div id="modalMsg" align="center"></div>
          <div class="form-group">
            <label for="group_name" class="col-sm-4 control-label"><?php echo __('Subject');?>:</label>
	    <div class="col-sm-8">
	    <select name="subject_id" class="form-control validate[required]"  id='subjectId'>
                <option value=""><?php echo __('Please Select Subject');?></option>
                 <?php echo$subjectName;?>
            </select>
            </div>
          </div>
          <div class="form-group">
            <label for="group_name" class="col-sm-4 control-label"><?php echo __('No. of Questions');?>:</label>
            <div class="col-sm-8">
		<input type="number" name="ques_no" id='quesNo' class="form-control validate[required]" placeholder="<?php echo __('No. of Questions');?>" value="<?php echo $this->ExamApp->h($_POST['ques_no']);?>" required/>
	    </div>
          </div>
	  <div class="form-group">
            <label for="group_name" class="col-sm-4 control-label"><?php echo __('Questions Attempt Count');?>:</label>
            <div class="col-sm-8">
		<input type="number" name="max_question" id='maxQuestion' class="form-control" placeholder="<?php echo __('Leave blank for not showing (0 for unlimited)');?>" value="<?php echo $this->ExamApp->h($_POST['max_question']);?>" />
	    </div>
          </div>
	  <div class="form-group">
            <label for="group_name" class="col-sm-4 control-label"><?php echo __('Question Type');?>:</label>
	    <div class="col-sm-8">
	    <select name="type" class="form-control validate[required]" multiple id='type'>
                <?php echo$qtypeName;?>
            </select>
	    <span><?php echo __('ctrl+click to add multiples');?></span>
	    </div>
          </div>
	  <div class="form-group">
            <label for="group_name" class="col-sm-4 control-label"><?php echo __('Difficulty Level');?>:</label>
	    <div class="col-sm-8">
	    <select name="level" class="form-control validate[required]" multiple id='level'>
                <?php echo$diffName;?>
            </select>
	    <span><?php echo __('ctrl+click to add multiples');?></span>
	    </div>
          </div>
	</form>
      </div>
      <div class="modal-footer">
        <button type="button" id="addsubject" class="btn btn-primary"><?php echo __('Add');?></button>
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close');?></button>
      </div>
    </div>
  </div>
</div>
</div>