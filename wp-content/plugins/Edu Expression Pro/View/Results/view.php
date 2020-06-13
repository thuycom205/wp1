<div class="container">

    <div class="panel panel-custom mrg">
        <div class="panel-heading"><?php echo __('Exam Details');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
	    <div class="panel-body">
                    <div class="table-responsive"> 
			<table class="table">
			    <tr>
				<td>
				    <div class="chart">
				    <div id="piewrapperqc"></div>
				    <script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
    Highcharts.setOptions(
        {"global":{"useUTC":true}}
    );
    // HIGHROLLER - HIGHCHARTS 'Question Difficulty Level' pie chart

    var piewrapperqc = new Highcharts.Chart(
        {"chart":{"renderTo":"piewrapperqc","type":"pie"},"title":{"text":"<?php echo$diffLevelTitle;?>","align":"center"},"series":<?php echo$diffLevelSeries;?>,"legend":{"enabled":true,"layout":"vertical","align":"right","verticalAlign":"middle"},"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"<b>{point.name}<\/b>: {point.y}"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
    );
    
    //for column drilldown
    function setChart(name, categories, data, color) {
        piewrapperqc.xAxis[0].setCategories(categories);
        piewrapperqc.series[0].remove();
        piewrapperqc.addSeries({
            name: name,
            data: data,
            color: color || 'white'
        });
    }   
});
//]]>
</script>
				    </div>
				</td>
			    </tr>
			</table>
		    </div>
		    <div class="table-responsive"> 
			<table class="table">			
			    <tr>
				<td>
				    <div class="chart">
				    <div id="mywrapperdl"></div>
				    <script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
    Highcharts.setOptions(
        {"global":{"useUTC":true}}
    );
    // HIGHROLLER - HIGHCHARTS 'Demo Student Performance' line chart

    var mywrapperdl = new Highcharts.Chart(
        {"chart":{"renderTo":"mywrapperdl","type":"line"},"title":{"text":"<?php echo$performanceTitle;?>","align":"center"},"series":<?php echo$performanceSeries;?>,"tooltip":{"backgroundColor":{},"enabled":true,"formatter":function() { return '<b>'+ this.series.name +'<\/b><br\/>'+ this.x +': '+ this.y +'% Marks';}},"plotOptions":{"series":{"series":{"dataLabels":{"style":{}}},"line":null,"showInLegend":true}},"xAxis":{"categories":<?php echo$xAxisCategories;?>},"labels":{"formatter":{"formatter":""}},"dataLabels":{"formatter":{"formatter":""}},"yAxis":{"style":{},"title":{"text":"Percentage"}},"credits":{"enabled":false}}
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
				</td>
			    </tr>
			</table>
		    </div>
                </div>
            </div>
        </div>