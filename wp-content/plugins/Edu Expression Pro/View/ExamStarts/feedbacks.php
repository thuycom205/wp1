<script type="text/javascript">
//<![CDATA[
function closeExamWindow(){var ww = window.open(window.location, '_self'); ww.close();}
//]]>
</script>
<?php 
if($isClose=="Yes"){
    ?><script type="text/javascript">
//<![CDATA[
setTimeout(function(){closeExamWindow(); }, 1500);
//]]>
</script> <?php }?>
<div class="col-md-9 col-sm-offset-2">
    <div class="panel panel-default">
	<div class="panel-heading"><center><?php if($isClose=="Yes"){echo$msg;}else{echo __("Thank you for using the test");}?></center></div>
	<div class="panel-body">
	    <form action="<?php echo$this->ajaxUrl;?>&info=feedbacks" name="post_req" id="post_req" class="form-horizontal" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('1. The test instructions were');?></small></label>
		<div class="col-sm-4">
		    <select name="test_instruction" class="form-control"><?php $this->ExamApp->getDropdownArray($_POST['test_instruction'],$option,array('Largely Clear'=>__('Largely Clear'),'Medium Clear'=>__('Medium Clear'),'Not Clear'=>__('Not Clear')));echo$option;$option=null;?></select>
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('2. Language of question was');?></small></label>
		<div class="col-sm-4">
		    <select name="question_language" class="form-control"><?php $this->ExamApp->getDropdownArray($_POST['question_language'],$option,array('Largely Clear'=>__('Largely Clear'),'Medium Clear'=>__('Medium Clear'),'Not Clear'=>__('Not Clear')));echo$option;$option=null;?></select>		   
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('3. Overall test experience was');?></small></label>
		<div class="col-sm-4">
		    <select name="test_experience" class="form-control"><?php $this->ExamApp->getDropdownArray($_POST['test_experience'],$option,array('Good'=>__('Good'),'Better'=>__('Better'),'Best'=>__('Best')));echo$option;$option=null;?></select>		  
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('Any other feedback suggestion');?></small></label>
		<div class="col-sm-4">
		   <textarea name="comments" class="form-control" required="required"><?php echo$_POST['comments'];?></textarea>
		</div>
	    </div>
	    <div class="form-group text-left">
		<div class="col-sm-offset-3 col-sm-7">
		    <button type="submit" class="btn btn-success"><span class="fa fa-plus"></span> <?php echo __('Submit');?></button>
		    <button type="button" class="btn btn-danger" onClick="closeExamWindow();"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></button>		    
		</div>
	    </div>
	    <input type="hidden" name="id" value="<?php echo$_REQUEST['id'];?>"/><input type="hidden" name="examResultId" value="<?php echo$_REQUEST['examResultId'];?>"/>
	    </form>
	</div>
    </div>
</div>