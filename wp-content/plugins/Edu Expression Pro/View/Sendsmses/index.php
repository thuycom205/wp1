<?php
$studentUrl=$this->ajaxUrl."&info=studentssearch";
$teacherUrl=$this->ajaxUrl."&info=teacherssearch";
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#studentId').select2({
        minimumInputLength: 1,
	tags: true,
        ajax: {
          url: '<?php echo$studentUrl;?>',
          dataType: 'json',
          data: function (term, page) {
            return {
              q: term
            };
          },          
          results: function (data, page) {
            return { results: data };
          }
        }
      });
	$('#teacherId').select2({
        minimumInputLength: 1,
	tags: true,
        ajax: {
          url: '<?php echo$teacherUrl;?>',
          dataType: 'json',
          data: function (term, page) {
            return {
              q: term
            };
          },          
          results: function (data, page) {
            return { results: data };
          }
        }
      });	
	$('#students').hide();
	$('#teachers').hide();
	$('#any').hide();
	$('#SendsmsType').change(function(){
    if($('#SendsmsType').val()=="Student")
    {
	$('#students').show();
	$('#teachers').hide();
	$('#any').hide();
    }
    else if($('#SendsmsType').val()=="Teacher")
    {
	$('#teachers').show();
	$('#students').hide();
	$('#any').hide();
    }
    else if($('#SendsmsType').val()=="Any")
    {
	$('#any').show();
	$('#students').hide();
	$('#teachers').hide();
    }
    else
    {
	$('#any').hide();
	$('#students').hide();
	$('#teachers').hide();
    }
    });
    $('#SendsmsSmsTemplate').change(function() {
    $('#SendsmsMessage').val($('#SendsmsSmsTemplate').val());
    sms_character_count();
    });
    $('#SendsmsMessage').keyup(function () {
	sms_character_count();
    });
    $('#SendsmsMessage').focus(function () {
	sms_character_count();
    });    
    });
</script>
<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Send Sms');?></div>
               <div class="panel-body">
	       <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">                
		    <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Type');?></label>
			<div class="col-sm-10">
			<select name="data[type]" id="SendsmsType" class='form-control' required >
			<option value=""><?php echo __('Please Select');?></option>
			<option value="Student" <?php if($_POST['type']=="Student"){echo 'selected';}?>><?php echo __('Student');?></option>
			<option value="Teacher" <?php if($_POST['type']=="Teacher"){echo 'selected';}?>><?php echo __('Teachers/Users');?></option>
			<option value="Any" <?php if($_POST['type']=="Any"){echo 'selected';}?>><?php echo __('Any Sms');?></option>
			</select>
	         	</div>			
		    </div>
		    <div class="form-group" id="students">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Students');?></label>
			<div class="col-sm-10">
			<input type="text" name="data[student_id]" id="studentId" class="form-control" placeholder="<?php echo __('Default all students if you add manually then search student email');?>"   /> 
                    	</div>			
		    </div>
		    <div class="form-group" id="teachers">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Teachers');?></label>
			<div class="col-sm-10">
			<input type="text" name="data[teacher_id]" id="teacherId" class="form-control" placeholder="<?php echo __('Default all teachers/users if you add manually then search teacher email');?>"   /> 
                    	</div>			
		    </div>
		     <div class="form-group" id="any">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Any Number');?></label>
			<div class="col-sm-10">
			<input type="text" name="data[any_sms]" class="form-control" placeholder="<?php echo __('Type any number comma seprated');?>"   /> 
			</div>			
		    </div>
		    <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Select Sms Template');?></label>
			<div class="col-sm-10">
			<select name="data[sms_template]" id="SendsmsSmsTemplate" class="form-control">
			<option value=""><?php echo __('Please Select');?></option>
			<?php echo$smsTemplate;?>
			</select>
			</div>			
		    </div>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Sms Template');?>:</label>
			<div class="col-sm-8">
			<textarea name="data[message]" class="form-control" placeholder="If you do not want to select sms template then simply type sms message" cols="20" rows="5" id="SendsmsMessage"><?php echo $_POST['message'];?></textarea>
			</div>
			<div class="span2"><div id="characterLeft"></div></div>
		    </div>
		    <div class="form-group text-left">
			<div class="col-sm-offset-2 col-sm-10">
			    <button type="submit" class="btn btn-success"><span class="fa fa-mobile"></span>&nbsp;<?php echo __('Send');?></button>
			    <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Reset');?></a>
			</div>
		    </div>
		    </form>
                </div>
            </div>