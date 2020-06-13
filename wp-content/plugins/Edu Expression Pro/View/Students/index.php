<?php
$sortArr=$this->ExamApp->sortableString($_REQUEST);
$order=$sortArr['order'];$orderClause=$sortArr['orderClause'];
if($_GET['msg']=='invalid'){echo $this->ExamApp->showMessage("Invalid Post !",'danger');}
if($_GET['msg']=='sstatus'){echo $this->ExamApp->showMessage("Student has been sucessfully updated",'success');}
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
	
	$(".Keyword").blur(function(e) {
	$('.Keyword').val($(this).val());
	});
	
	$(".ipage").change(function(){
	$(".loading-div").show(); //show loading element
	var page = $(this).val(); //get page number from dropdown
	var keyword = $('.Keyword').val(); //get value from search
	$("#results").load("<?php echo$this->ajaxUrl;?>&info=index",{<?php echo$order.$orderClause;?>"ipage":page,"keyword":keyword}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	});
	});
});
</script>
<div class="loading-div"><img src="<?php echo plugin_dir_url(__FILE__);?>../../img/loading-lg.gif" ></div>

<div id="results">
</div>