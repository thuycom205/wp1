<?php $editorType="absolute";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';
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
    $('#SendemailType').change(function(){
    if($('#SendemailType').val()=="Student")
    {
	$('#students').show();
	$('#teachers').hide();
	$('#any').hide();
    }
    else if($('#SendemailType').val()=="Teacher")
    {
	$('#teachers').show();
	$('#students').hide();
	$('#any').hide();
    }
    else if($('#SendemailType').val()=="Any")
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
    $('#SendemailEmailTemplate').change(function() {
    $('#SendemailMessage').val($('#SendemailEmailTemplate').val());
    });
    });
</script>
<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Send Emails');?></div>
               <div class="panel-body">
	       <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post" accept-charset="utf-8">                
		    <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Type');?></label>
			<div class="col-sm-10">
			<select name="data[type]" id="SendemailType" class='form-control' required >
			<option value=""><?php echo __('Please Select');?></option>
			<option value="Student" <?php if($_POST['type']=="Student"){echo 'selected';}?>><?php echo __('Student');?></option>
			<option value="Teacher" <?php if($_POST['type']=="Teacher"){echo 'selected';}?>><?php echo __('Teachers/Users');?></option>
			<option value="Any" <?php if($_POST['type']=="Any"){echo 'selected';}?>><?php echo __('Any Email');?></option>
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
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Any Email');?></label>
			<div class="col-sm-10">
			<input type="text" name="data[any_email]" class="form-control" placeholder="<?php echo __('Type any email comma seprated');?>"   /> 
                    </div>			
		    </div>
		    <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Subject');?></label>
			<div class="col-sm-10">
			<input type="text" name="data[subject]" class="form-control" placeholder="<?php echo __('Type subject');?>" required/> 
			</div>
		    </div>
		    <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Select Email Template');?></label>
			<div class="col-sm-10">
			<select name="data[email_template]" id="SendemailEmailTemplate" class="form-control">
			<option value=""><?php echo __('Please Select');?></option>
			<?php echo$emailTemplate;?>
			</select>
			</div>			
		    </div>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Email Template');?>:</label>
			<div class="col-sm-10">
			<?php echo$tinymce->input('data[message]',$_POST['message'],array('id'=>'SendemailMessage','placeholder'=>__('If you do not want to select email template then simply type email message. Once you load editor then you can not select template go to reset button'),'class'=>'form-control','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
			</div>
		    </div>
		    <div class="form-group text-left">
			<div class="col-sm-offset-2 col-sm-10">
			    <button type="submit" class="btn btn-success"><span class="fa fa-send"></span>&nbsp;<?php echo __('Send');?></button>
			    <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Reset');?></a>
			</div>
		    </div>
		    </form>
                </div>
            </div>