<?php $examDuration=$post['duration'];
$viewUrl=$this->ajaxUrl.'&info=submit&id='.$examId.'&examResultId='.$examResultId;
$targetUrl=$this->ajaxUrl.'&info=examwarning&id='.$examId.'&examResultId='.$examResultId;
$finishUrl=$this->ajaxUrl.'&info=finish&id='.$examId.'&examResultId='.$examResultId.'&warn=warn';
?>
<div class="col-sm-offset-3 col-md-6" id="exam-loading" style="display: none;"><?php echo $this->ExamApp->getImage('img/loading-lg.gif');?></div>
<div id="printajax">
<?php if($mathEditor){?><script>MathJax.Hub.Typeset();</script><?php }?>
<div class="col-md-12">
<form name="post_req" id="post_req" action="<?php echo$this->ajaxUrl;?>&info=save" method="post">
	<div class="col-md-9">
		<div class="panel">
			<div class="panel-body">
			<input type="hidden" name="id" value="<?php echo$examId;?>">
			<input type="hidden" name="ques" value="<?php echo$oquesNo;?>">
			<?php echo$msg;?>
				<div class="exam-heading"><?php echo $this->ExamApp->h($post['name']);?></div>
					<div class="panel panel-default">
						<div class="table-responsive exam-panel">
							<table class="table">
								<thead>
								<tr>
									<td><h4><div class="pull-left mrg-left"><strong><?php echo __('Question');?> <?php echo$userExamQuestion['ques_no'];?></strong></div> <div class="pull-right mrg-left mrg-right"><strong><?php echo __('Correct Marks');?>: <span class="text-success"><?php echo$userExamQuestion['marks'];?></span> , <?php echo __('Negative Marks');?>: <span class="text-danger"><?php echo$userExamQuestion['negative_marks'];?></span></strong></div></h4></td>
								</tr>
								<tr>
									<td><div class="mrg-left"><strong><?php echo str_replace("<script","",$userExamQuestion['question']);?></strong></div></td>
								</tr>
								</thead>
								<?php if(strlen($userExamQuestion['hint'])>0){?>
								<tr>
									<td><div class="mrg-left"><strong><?php echo __('Hint');?> : </strong><?php echo str_replace("<script","",$userExamQuestion['hint']);?></div></td>
								</tr>
								<?php }?>
								<?php if($userExamQuestion['type']=="M")
								{
									$options=array();
									$optColor1_1='<span>';$optColor1_2='<span>';$optColor1_3='<span>';
									$optColor1_4='<span>';$optColor1_5='<span>';$optColor1_6='<span>';$optColor2='</span>';
									if($post['instant_result']==1 && $userExamQuestion['answered']==1)
									{
										if(strlen($userExamQuestion['answer'])>1)
										{
											$selDanger='<span class="text-danger"><b>';
											$selSuccess='<span class="text-success"><b>';
											foreach(explode(",",$userExamQuestion['option_selected']) as $value)
											{
												$opt=$value;
												$varName1='optColor1'.'_'.$opt;
												$$varName1=$selDanger;
											}
											unset($value);
											foreach(explode(",",$userExamQuestion['correct_answer']) as $value)
											{
												$opt=$value;
												$varName1='optColor1'.'_'.$opt;
												$$varName1=$selSuccess;												
											}
											unset($value);
										}
										else
										{
											$selDanger='<span class="text-danger"><b>';
											$selSuccess='<span class="text-success"><b>';
											$opt=$userExamQuestion['option_selected'];
											$varName1='optColor1'.'_'.$opt;
											$$varName1=$selDanger;
											$opt=$userExamQuestion['correct_answer'];
											$varName1='optColor1'.'_'.$opt;
											$$varName1=$selSuccess;	
										}																		
									}
									$optionKeyArr=explode(",",$userExamQuestion['options']);
									foreach($optionKeyArr as $value)
									{
										$optKey="option".$value;
										$doptCol='optColor1'.'_'.$value;
										if(strlen($userExamQuestion[$optKey])>0)
										$options[$value]=$$doptCol.str_replace("<script","",$userExamQuestion[$optKey]).$optColor2;
									}
									unset($value);
									?>
								<tr>
									<td>								
										<?php if(strlen($userExamQuestion['answer'])>1)
										{	
											$optionSelected=array();
											$optionSelected=explode(",",$userExamQuestion['option_selected']);
											foreach($options as $key=>$value):?>
											<div class="checkbox">
											<label><input type="checkbox" name="option_selected[]" id="ExamOptionSelected<?php echo$key;?>" value="<?php echo$key;?>" <?php foreach($optionSelected as $checkValue): if($checkValue==$key){echo 'checked';}endforeach;unset($checkValue); ?>/><span><?php echo$value; ?></span></label>
											</div><?php endforeach;unset($key,$value);
										}
										else
										{
										 $optionSelected=$userExamQuestion['option_selected'];	
										foreach($options as $key=>$value):?>
										<div class="radio">
								                <label><input type="radio" name="option_selected" id="ExamOptionSelected<?php echo$key;?>" value="<?php echo$key;?>" <?php if($userExamQuestion['option_selected']==$key){echo 'checked';} ?>/><span><?php echo$value; ?></span></label>			    
										</div>
										<?php endforeach;unset($key,$value);}?>
									
									</td>
								</tr>				
								<?php }?>
								<?php if($userExamQuestion['type']=="T")
								{?>
								<tr><td>
								<div class="radio">
								<label><input type="radio" name="true_false" id="true_false" value="True" <?php if($userExamQuestion['ExamStat.true_false']=="True"){echo 'checked';} ?> /><?php echo __('True');?></label></div>
								<div class="radio"><label><input type="radio" id="true_false" name="true_false" value="False" <?php if($userExamQuestion['ExamStat.true_false']=="False"){echo 'checked';}?> /><?php echo __('False');?></label></div>
								</td></tr>
								<?php }?>
								<?php if($userExamQuestion['type']=="F")
								{?>
								<tr><td><input type="text" name="fill_blank" id="fill_blank" value="<?php echo$userExamQuestion['ExamStat.fill_blank'];?>" autocomplete="off"></td></tr>
								<?php }?>
								<?php if($userExamQuestion['type']=="S")
								{?>
								<tr><td><textarea  name="answer" id="answer" class="form-control" rows="7"><?php echo $userExamQuestion['ExamStat.answer'];?></textarea></td></tr>
								<?php }?>
							</table>
						</div>
						<div class="panel-body">
							<div class="row">
							<?php $navigationUrl=$this->ajaxUrl.'&info=ajaxcontentview';$reviewUrl='';$unreviewUrl='';$saveUrl='';$resetUrl='';$savenextUrl='';?>
								<div class="col-sm-2">
								<button type="button" rel='<?php echo$navigationUrl;?>' onclick="navigation(<?php echo$examId;?>,<?php echo$pquesNo;?>)" class='btn btn-default btn-sm btn-block navigation'>&larr;<?php echo __('Prev');?></button>
								</div>
								<div class="col-sm-2">
								<?php $saveUrl=$this->ajaxUrl.'&info=save';?>
								<button type="button" id='save' class='btn btn-success btn-sm btn-block'><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo __('Save');?></button>
								</div>
								<?php if($totalQuestion!=$oquesNo){?>
								<div class="col-sm-2">
								<?php $savenextUrl=$this->ajaxUrl.'&info=save';?>
								<button type="button" id='savenext' class='btn btn-success btn-sm btn-block'><span class="glyphicon glyphicon-log-out"></span>&nbsp;<?php echo __('Save & Next');?></button>
								</div>
								<?php }?>
								<?php if($userExamQuestion['review']==1){?>
								<div class="col-sm-2">
								<?php $reviewUrl=$this->ajaxUrl.'&info=unreviewAnswer';?>
								<button type="button" id='unreview' class='btn btn-primary btn-sm btn-block'><span class="fa fa-dot-circle-o"></span>&nbsp;<?php echo __('Unreview');?></button>
								</div><?php }else{?>
								<div class="col-sm-2">
								<?php $reviewUrl=$this->ajaxUrl.'&info=reviewAnswer';?>
								<button type="button"  id='review' class='btn btn-primary btn-sm btn-block'><span class="fa fa-dot-circle-o"></span>&nbsp;<?php echo __('Review');?></button>
								</div><?php }?>
								<div class="col-sm-2">
								<?php $resetUrl=$this->ajaxUrl.'&info=resetAnswer';?>
								<button type="button" id='reset' class='btn btn-primary btn-sm btn-block'><span class="glyphicon glyphicon-ban-circle"></span>&nbsp;<?php echo __('Reset Answer');?></button>
								</div>
								<?php if($totalQuestion==$oquesNo){?>
								<div class="col-sm-2">
								<button type="button" onclick="show_modal('<?php echo$viewUrl;?>')" class='btn btn-default btn-sm btn-block'><span class="glyphicon glyphicon-ban-circle"></span>&nbsp;<?php echo __('Finish');?>&rarr;</button>
								</div><?php }else{?>
								<div class="col-sm-2">
								<button type="button"  class='btn btn-default btn-sm btn-block navigation' rel="<?php echo$navigationUrl;?>" onclick="navigation(<?php echo$examId;?>,<?php echo$nquesNo;?>)" >&nbsp;<?php echo __('Next');?>&rarr;</button>
								</div><?php }?>							
							</div>
						</div>
					</div>
			</div>
		</div>
		<input type="hidden" name="saveNext" value="">		
	</div>
	</form>
	<div class="col-md-3">	
		<div id="timer"><div id="maincount"></div></div>
		<div class="panel-group" id="accordion">
			<?php foreach($userSectionQuestion as $subjectName=>$quesArr):
			$subjectNameId=str_replace(" ","",$this->ExamApp->h($subjectName));
			?>			
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#<?php echo$subjectNameId;?>"><?php echo $this->ExamApp->h($subjectName);?></a></h4>
				</div>
				<div id="<?php echo$subjectNameId;?>" class="panel-collapse collapse <?php echo($this->ExamApp->h($currSubjectName)==$this->ExamApp->h($subjectName)) ? "in" : "";?>">
					<div class="panel-body">
						<div class="row">
							<?php foreach($quesArr as $value):
							if($oquesNo==$value['ques_no'])
							$btn_type="info";
							elseif($value['review']==1)
							$btn_type="primary";
							elseif($value['answered']==1)
							$btn_type="success";
							elseif($value['opened']==1)
							$btn_type="warning";							
							else
							$btn_type="default";?>
							<div class="col-md-2 col-xs-2 col-sm-2 mrg-1">
							<?php $quesNo=$value['ques_no'];?>
							<button rel="<?php echo$navigationUrl;?>" onclick="navigation(<?php echo$examId;?>,<?php echo$quesNo;?>)" class="btn btn-<?php echo$btn_type;?> btn-circle btn-sm navigation lnav"><?php echo$quesNo;?></button>
							</div>
							<?php endforeach;unset($quesArr);?>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;unset($i);unset($value);?>
			<button  onClick="show_modal('<?php echo$viewUrl;?>')" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-lock"></span>&nbsp;<?php echo __('Finish').' '.$post['type'];?></button>
			<div class="mrg-1">
				<div class="panel panel-default">
					<div class="panel-heading">
					<h4 class="panel-title"><?php echo __('Legend');?></h4>
					</div>
					<div class="panel-body">
						<div class="mrg-1"><div id="currentLegend" class="btn btn-circle btn-info btn-xs">&nbsp;&nbsp;&nbsp;</div>&nbsp;<strong><?php echo __('Current Question');?></strong></div>
						<div class="mrg-1"><div id="notattemptedLegend" class="btn btn-circle btn-default btn-xs">&nbsp;&nbsp;&nbsp;</div>&nbsp;<strong><?php echo __('Not Attempted');?></strong></div>			
						<div class="mrg-1"><div id="answerLegend" class="btn btn-circle btn-success btn-xs">&nbsp;&nbsp;&nbsp;</div>&nbsp;<strong><?php echo __('Answered');?>&nbsp;</strong></div>
						<div class="mrg-1"><div id="notanswerLegend" class="btn btn-circle btn-warning btn-xs">&nbsp;&nbsp;&nbsp;</div>&nbsp;<strong><?php echo __('Not Answered');?></strong></div>
						<div class="mrg-1"><div id="reviewLegend" class="btn btn-circle btn-primary btn-xs">&nbsp;&nbsp;&nbsp;</div>&nbsp;<strong><?php echo __('Review');?></strong></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$endTime=$this->ExamApp->getStringDateFormat('M d, Y H:i:s',strtotime($examResult['start_time'])+($post['duration']*60));
$startTime=$this->ExamApp->getStringDateFormat('M d, Y H:i:s',strtotime($examResult['start_time']));
$expiryUrl=$this->ajaxUrl.'&info=finish&id='.$examId;
$serverTimeUrl=$this->ajaxUrl.'&info=servertimes&id='.$examId;
?>
<script type="text/javascript">
<?php if($examDuration>0){ ?>
$(document).ready(function(){
liftoffTime=new Date("<?php echo$endTime;?>");
$("#maincount").countdown({until: liftoffTime, format: 'HMS',serverSync: serverTime,alwaysExpire: true,expiryUrl:"<?php echo$expiryUrl;?>"});
  function serverTime() {
    var time = null; 
    $.ajax({url: "<?php echo$serverTimeUrl;?>", 
        async: false, dataType: 'text', 
        success: function(text) {            
            time = new Date(text);
        }, error: function(http, message, exc) { 
            time = new Date();
    }}); 
    return time; 
}
});
<?php } else{ ?>
$(document).ready(function(){
startTime=new Date("<?php echo$startTime;?>");
	$('#maincount').countdown({since: startTime,format: 'HMS',serverSync: serverTime});
	function serverTime() {
    var time = null; 
    $.ajax({url: "<?php echo$serverTimeUrl;?>", 
        async: false, dataType: 'text', 
        success: function(text) {
            time = new Date(text);
        }, error: function(http, message, exc) { 
            time = new Date();
    }}); 
    return time; 
}
});
<?php }?>
function callUserAnswerSaveNext()
{
	document.post_req.saveNext.value="Yes";
	document.post_req.submit();
}
function callUserAnswerSave()
{
	document.post_req.submit();
}
<?php if($post['browser_tolrance']==1 && $ajaxView=="No"){?>
$(window).on("blur", function(e) {
  $.ajax({
      method: "GET",
      cache: false ,
      url: '<?php echo$targetUrl;?>'})
      .done(function(response) {
      if(response=="Yes")
      {
	   window.location='<?php echo$finishUrl;?>';
      }
      else
      {
	   $('#myModal').modal({
		   backdrop: 'static',
		   keyboard: false
	       })
      }
      });
});
<?php }?>
$(document).ready(function(){
	$('#currentLegend').click(function(){
	$('.lnav').show();
});
$('#notattemptedLegend').click(function(){
	$('.lnav').hide();
	$('.btn-default').show();
  $('.panel-collapse:not(".in")')
    .collapse('show');
});
$('#answerLegend').click(function(){
	$('.lnav').hide();
	$('.btn-success').show();
  $('.panel-collapse:not(".in")')
    .collapse('show');
});
$('#notanswerLegend').click(function(){
	$('.lnav').hide();
	$('.btn-warning').show();
  $('.panel-collapse:not(".in")')
    .collapse('show');
});	
$('#reviewLegend').click(function(){
	$('.lnav').hide();
	$('.btn-primary').show();
  $('.panel-collapse:not(".in")')
    .collapse('show');
});	
$('#reset').click(function (){$.ajax({method: "POST",data:$('#post_req').serialize(),url: '<?php echo$resetUrl;?>',beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});});	
$('#review').click(function (){$.ajax({method: "POST",data:$('#post_req').serialize(),url: '<?php echo$reviewUrl;?>',beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});});
$('#unreview').click(function (){$.ajax({method: "POST",data:$('#post_req').serialize(),url: '<?php echo$reviewUrl;?>',beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});});
$('#save').click(function (){$.ajax({method: "POST",data:$('#post_req').serialize(),url: '<?php echo$saveUrl;?>',beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});});
$('#savenext').click(function (){$.ajax({method: "POST",data:$('#post_req').serialize()+ '&saveNext=Yes',url: '<?php echo$savenextUrl;?>',beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});});
});
function navigation(examId,quesNo){targetUrl=$('.navigation').attr('rel')+'&id='+examId+'&ques='+quesNo;$.ajax({method: "GET",url: targetUrl,beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {$('#exam-loading').hide();$('#printajax').html(data);});}
</script>
<style type="text/css">
.modal-backdrop {background-color:#ff0000;}
.modal-backdrop.in{opacity: .8;}
</style>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">        
  </div>
</div>
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo __('Navigated Away');?></h4>
      </div>
      <div class="modal-body">
        <p><blockquote><?php global$current_user;echo $current_user->display_name;?>, <?php echo __('you had navigated away from the test window. This will be reported to Moderator');?></blockquote></p>
	<p><blockquote><span class="text-danger"><?php echo __('Do not repeat this behaviour');?></span> <?php echo __('Otherwise you may get disqualified');?></blockquote></p>
	<div class="text-center"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Continue');?></button></div>
      </div>      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>