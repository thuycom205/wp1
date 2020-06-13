<?php if($mathEditor){?><script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
<script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php
if($_GET['msg']=='success'){echo $this->ExamApp->showMessage("Exam Added Successfully. Add questions in exam",'success');}
$sortArr=$this->ExamApp->sortableString($_REQUEST);
$order=$sortArr['order'];
if($_REQUEST['subject_id'])
$subjectId=$_REQUEST['subject_id'];
else
$subjectId='';
if($_REQUEST['qtype_id'])
$qtypeId=$_REQUEST['qtype_id'];
else
$qtypeId='';
if($_REQUEST['diff_id'])
$diffId=$_REQUEST['diff_id'];
else
$diffId='';
$orderClause=$sortArr['orderClause'];$orderClause.='"examId":'.$_REQUEST['examId'].',"diff_id":"'.$diffId.'","subject_id":"'.$subjectId.'","qtype_id":"'.$qtypeId.'",';
?>
<script type='text/javascript' src='<?php echo plugin_dir_url(__FILE__);?>../../js/main.custom.min.js'></script>
<script type="text/javascript">
$(window).load(function() {
	$("#results" ).load( "<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>}); //load initial records
});
$(document).ready(function() {
	//executes code below when user click on pagination links
	$(".inav").click(function(){
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	var ipage = $('.ipage').val(); //get page number from dropdown
	var keyword = $('.Keyword').val(); //get value from search
	$("#results").load("<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>"page":page,"ipage":ipage,"keyword":keyword}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	});
	});
	
	$(".search-btn-main").click(function(){
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	var ipage = $('.ipage').val(); //get page number from dropdown
	var keyword = $('.Keyword').val(); //get value from search
	$("#results").load("<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>"page":page,"ipage":ipage,"keyword":keyword}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	});
	});
	
	$(".ipage").change(function(){
	$(".loading-div").show(); //show loading element
	var page = $(this).val(); //get page number from dropdown
	var keyword = $('.Keyword').val(); //get value from search
	$("#results").load("<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>"ipage":page,"keyword":keyword}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	});
	});
	
	$(".Keyword").blur(function(e) {
	$('.Keyword').val($(this).val());
	});
	
	$("#search").click(function(){
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page");
	var keyword = $('.Keyword').val(); //get value from search
	var subjectId= $('#subject_id').val(); //get value from search
	var qtypeId= $('#qtype_id').val(); //get value from search
	var diffId= $('#diff_id').val(); //get value from search
	$("#results").load("<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>"ipage":page,"keyword":keyword,"subject_id":subjectId,"qtype_id":qtypeId,"diff_id":diffId}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	});
	});
});
</script>
<div class="loading-div"><img src="<?php echo plugin_dir_url(__FILE__);?>../../img/loading-lg.gif" ></div>

<div id="results">
</div>