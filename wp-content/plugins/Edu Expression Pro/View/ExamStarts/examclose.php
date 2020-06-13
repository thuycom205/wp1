<script type="text/javascript">
//<![CDATA[
function closeExamWindow(){var ww = window.open(window.location, '_self'); ww.close();}
//]]>
</script>
    <script type="text/javascript">
    $(document).ready(function(){
    $('#myModal').modal({
    backdrop: 'static',
    keyboard: false
})});
    </script>
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo __('Exceeded permissible limit for Navigating away');?></h4>
      </div>
      <div class="modal-body">
        <p><blockquote><?php global$current_user;echo $current_user->display_name;?>, <?php echo __('You have exceeded the permissible limit for navigating away from your test');?></blockquote></p>
	<p><blockquote><?php echo __('Your test has finished. You can close the window now');?></blockquote></p>
	<div class="text-center">
	<?php if($examFeedback){?><a href="<?php echo ajaxUrl.'&info=feedbacks&id='.$id.'&examResultId='.$examResultId;?>" class="btn btn-default"><?php }else{
	?><a href="javascript:void();" onClick="closeExamWindow();" class="btn btn-default"><?php echo __('Close');?></a><?php }?></div>
      </div>      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->