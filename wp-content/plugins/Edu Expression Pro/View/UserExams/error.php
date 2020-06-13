 <script type="text/javascript">
//<![CDATA[
function closeExamWindow(){var ww = window.open(window.location, '_self'); ww.close();}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
setTimeout(function(){closeExamWindow(); }, 1500);
//]]>
</script>
<?php if($_GET['msg']=='invalid'){echo $this->ExamApp->showMessage("Invalid Post !",'danger');}
if($_GET['msg']=='maximumexam'){echo $this->ExamApp->showMessage("You have attempted maximum exam",'danger');}?>