<?php if($_GET['msg']=='invalidpaypal'){$msg=$this->ExamApp->showMessage("Paypal Payment not set",'danger');}
if(isset($_GET['msg']) && $_GET['msg']==1){$msg=$this->ExamApp->showMessage("You have been logged in successfully",'success');}?>
<?php echo$msg;?>
    <div class="col-md-4 col-sm-4">                                
	<div class="panel">		
	    <div class="panel-body"><h3><?php echo __('My Exam Stats');?></h3>
		<div class="table-responsive">
		    <table class="table">
			<tr>
			    <td><strong><?php echo __('Total Exam Given');?> : </strong><strong class="text-success"><?php echo$totalExamGiven;?></strong></td>					
			</tr>
			<tr>
			    <td><strong><?php echo __('Absent Exams');?> : </strong><strong class="text-danger"><?php echo$userTotalAbsent;?></strong></td>					
			</tr>
			<tr>
			    <td><strong><?php echo __('Best Score in');?> : </strong><strong class="text-success"><?php echo $this->ExamApp->h($bestScore);?></strong></td>
			</tr>
			<tr>
			    <td><strong><?php echo __('On');?> : </strong><strong class="text-info"><?php echo$bestScoreDate?></strong></td>
			</tr>
			<tr>
			    <td><strong><?php echo __('Failed in');?> : </strong><strong class="text-danger"><?php echo$failedExam;?> Exam</strong></td>
			</tr>
			<tr>
			    <td><strong><?php echo __('Average Percentage');?> : </strong><strong class="text-info"><?php echo$averagePercent;?>%</strong></td>
			</tr>
			<tr>
			    <td><strong><?php echo __('Your Rank');?> : </strong><strong class="text-info"><?php echo$rank;?></strong></td>
			</tr>
		    </table>
		</div>
	    </div>
	</div>
    </div>
    <div class="col-md-8 col-sm-8">
	<div class="panel">
	    <div class="panel-body"><h3><?php echo __('Month Wise Performance');?></h3>
		<div class="chart">
		    <div id="mywrapperdl"></div>
		    <script type="text/javascript">
		    //<![CDATA[
		    $(document).ready(function() {
			// HIGHROLLER - HIGHCHARTS UTC OPTIONS 
			Highcharts.setOptions(
			    {"global":{"useUTC":true}}
			);
			// HIGHROLLER - HIGHCHARTS '' spline chart
		    
			var mywrapperdl = new Highcharts.Chart(
			    {"chart":{"renderTo":"mywrapperdl","type":"spline"},"title":{"text":null,"align":"center"},"series":<?php echo$monthSeries;?>,"tooltip":{"enabled":true,"formatter":function() { return '<b>'+ this.series.name +'<\/b><br\/>'+ this.x +': '+ this.y +'%';}},"plotOptions":{"series":{"series":{"dataLabels":{"style":{}}},"spline":null,"showInLegend":true}},"xAxis":{"categories":<?php echo$monthxAxis;?>},"labels":{"formatter":{"formatter":""}},"dataLabels":{"formatter":{"formatter":""}},"yAxis":{"max":100,"title":{"text":"<?php echo $monthyAxisTitleText;?>"}},"credits":{"enabled":false}}
			);
			
			//for column drilldown
			function setChart(name, categories, data, color) {
			    mywrapperdl.xAxis[0].setCategories(categories);
			    mywrapperdl.series[0].remove();
			    mywrapperdl.addSeries({
				name: name,
				data: data,
				color: color || 'white'
			    });
			}   
		    });
		    //]]>
		    </script>
		</div>
	    </div>
	</div>
    </div>
    <div class="col-md-12">
	<div class="panel">
	    <div class="panel-body"><h3><?php echo __('Exam Wise Performance');?> (<strong><span class="text-info"><?php echo __('Top 10');?></span></strong>)</h3>
		<div class="chart">
		    <div id="mywrapperd2"></div>
		    <script type="text/javascript">
		    //<![CDATA[
		    $(document).ready(function() {
			// HIGHROLLER - HIGHCHARTS UTC OPTIONS 
			Highcharts.setOptions(
			    {"global":{"useUTC":true}}
			);
			// HIGHROLLER - HIGHCHARTS '' column chart
		    
			var mywrapperd2 = new Highcharts.Chart(
			    {"chart":{"renderTo":"mywrapperd2","type":"column"},"title":{"text":null,"align":"center"},"series":<?php echo$examSeries;?>,"tooltip":{"enabled":true,"formatter":function() { return ''+ this.x +': '+ this.y +'%';}},"plotOptions":{"series":{"series":{"dataLabels":{"style":{}}},"column":null,"showInLegend":true}},"xAxis":{"categories":<?php echo$examxAxis;?>},"labels":{"formatter":{"formatter":""}},"dataLabels":{"formatter":{"formatter":""}},"yAxis":{"max":100,"title":{"text":"<?php echo$examyAxisTitleText;?>"}},"credits":{"enabled":false}}
			);
			
			//for column drilldown
			function setChart(name, categories, data, color) {
			    mywrapperd2.xAxis[0].setCategories(categories);
			    mywrapperd2.series[0].remove();
			    mywrapperd2.addSeries({
				name: name,
				data: data,
				color: color || 'white'
			    });
			}   
		    });
		    //]]>
		    </script
		</div>
	    </div>
	</div>
    </div>
    <div class="col-md-12">
	<div class="panel">
	    <div class="panel-body"><h3><?php echo __('Todays Exam');?>  (<strong><span class="text-info"><?php echo __('Top');?> <?php echo$limit;?></span></strong>)</h3>
		<div class="table-responsive">
		    <table class="table table-striped">
			<?php if($todayExam){?>
			<tr>
			    <th colspan="8"><?php echo __('These are the exam(s) that can be taken right now');?></th>
			</tr>
			<?php echo$this->UserExam->showExamList("today",$todayExam);?>
			<?php }else{?>
			<tr>
				<th colspan="8"><?php echo __('No Exams found for today');?></th>
			</tr>
			<?php }?>
		    </table>
		</div>
	    </div>
	</div>
    </div>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">        
  </div>
</div>