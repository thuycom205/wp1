<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Group Perfomance');?></h1></div></div>
<div class="panel">
    <div class="panel-body">
        <div id="mywrapperdl"></div>
       <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function() {
            // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
            Highcharts.setOptions(
                {"global":{"useUTC":true}}
            );
            // HIGHROLLER - HIGHCHARTS '' line chart
        
            var mywrapperdl = new Highcharts.Chart(
                {"chart":{"renderTo":"mywrapperdl","type":"line"},"title":{"text":null,"align":"center"},"series":<?php echo$groupSeries;?>,"legend":{"enabled":true},"tooltip":{"enabled":true,"formatter":function() { return '<b>'+ this.series.name +'<\/b><br\/>'+ this.x +': '+ this.y +'% <?php echo __('Marks');?>';}},"xAxis":{"categories":<?php echo$groupxAxis;?>},"labels":{"formatter":{"formatter":""}},"dataLabels":{"formatter":{"formatter":""}},"yAxis":{"style":{},"title":{"text":"<?php echo __('Percentage');?>"}},"credits":{"enabled":false}}
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
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content"></div>
</div>