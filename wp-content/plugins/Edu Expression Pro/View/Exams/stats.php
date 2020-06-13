<div class="container">
        <div class="panel panel-custom mrg">
		<div class="panel-heading"><?php echo __('Exam Stats');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>            
		<div class="panel-body">
		<form class="form-horizontal" action="<?php echo$this->urlResult;?>&info=index" name="post_req" id="post_req" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
		   <div class="table-responsive">
			<table class="table table-bordered">
			    <tr>
				<th><?php echo __('Exam');?></th>
				<th><?php echo __('Overall Result');?></th>
				<th><?php echo __('Student Stats');?></th>							
			    </tr>
			    <tr>
				<td><strong class="text-danger"><?php echo $this->ExamApp->h($post['name']);?></strong><br/>
				<?php echo __('From');?>: <strong class="text-danger"><?php echo $this->ExamApp->dateFormat($post['start_date']);?></strong><br/>
				<?php echo __('To');?>: <strong class="text-danger"><?php  echo $this->ExamApp->dateFormat($post['end_date']);?></strong><br/>
				<a href="javascript:void(0);" onclick="examResult(<?php echo$id;?>);" class="btn btn-info"><?php echo __('Details');?></a>
				<p><strong class="text-success"><?php echo __('No of Student Passed');?>: <?php echo $examStats['StudentStat']['pass'];?></strong>&nbsp;&nbsp;
				<a href="<?php echo $this->ajaxUrl;?>&info=downloadlist&id=<?php echo $id;?>&type=Pass" class="fa fa-download"><?php echo __('Download');?></a>
				</p>
				<p><strong class="text-danger"><?php echo __('No of Student Failed');?>: <?php echo $examStats['StudentStat']['fail'];?></strong>&nbsp;&nbsp;
				<a href="<?php echo $this->ajaxUrl;?>&info=downloadlist&id=<?php echo $id;?>&type=Fail" class="fa fa-download"><?php echo __('Download');?></a>
				</p>
				<p><strong class="text-info"><?php echo __('No of Student Absent')?>: <?php echo $examStats['StudentStat']['absent'];?></strong>&nbsp;&nbsp;
				<a href="<?php echo $this->ajaxUrl;?>&info=downloadabsentlist&id=<?php echo $id;?>" class="fa fa-download"><?php echo __('Download');?></a>
				</p>
				</td>
				<td>
                                                        <?php $chartRerData=array();
                                                        $chartRerData[]=array(__('Pass'),(int) $examStats['StudentStat']['pass']);
                                                        $chartRerData[]=array(__('Fail'),(int) $examStats['StudentStat']['fail']);
                                                        $chartRerData[]=array(__('Absent'),(int) $examStats['StudentStat']['absent']);
                                                        $id=$recentValue['id'];
                                                        $chartStudentStat=json_encode(array(array('name'=>'Student','data'=>$chartRerData)));
                                                        $chartOverallResult=json_encode(array(array('name'=>__('Passing %age'),'data'=>array($examStats['OverallResult']['passing'])),
                                                                                              array('name'=>__('Average %age'),'data'=>array($examStats['OverallResult']['average']))));
                                                        ?>
							<div class="chart">
							<div id="mywrapperor1"></div>
							<script type="text/javascript">
                                                        //<![CDATA[
                                                        $(document).ready(function() {
                                                            // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
                                                            Highcharts.setOptions(
                                                                {"global":{"useUTC":true}}
                                                            );
                                                            // HIGHROLLER - HIGHCHARTS '' bar chart
                                                        
                                                            var mywrapperor1 = new Highcharts.Chart(
                                                                {"chart":{"renderTo":"mywrapperor1","type":"bar","width":350,"height":200},"title":{"text":null},"series":<?php echo$chartOverallResult;?>,"legend":{"enabled":true},"xAxis":{},"credits":{"enabled":false}}
                                                            );
                                                            
                                                            //for column drilldown
                                                            function setChart(name, categories, data, color) {
                                                                mywrapperor1.xAxis[0].setCategories(categories);
                                                                mywrapperor1.series[0].remove();
                                                                mywrapperor1.addSeries({
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
						<td>
							<div class="chart">
							<div id="mywrapperss1"></div>
							<script type="text/javascript">
                                                        //<![CDATA[
                                                        $(document).ready(function() {
                                                            // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
                                                            Highcharts.setOptions(
                                                                {"global":{"useUTC":true}}
                                                            );
                                                            // HIGHROLLER - HIGHCHARTS '' pie chart
                                                        
                                                            var mywrapperss1 = new Highcharts.Chart(
                                                                {"chart":{"renderTo":"mywrapperss1","type":"pie","width":300,"height":200},"title":{"text":null},"series":<?php echo$chartStudentStat;?>,"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.percentage:.1f}%<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
                                                            );
                                                            
                                                            //for column drilldown
                                                            function setChart(name, categories, data, color) {
                                                                mywrapperss1.xAxis[0].setCategories(categories);
                                                                mywrapperss1.series[0].remove();
                                                                mywrapperss1.addSeries({
                                                                    name: name,
                                                                    data: data,
                                                                    color: color || 'white'
                                                                });
                                                            }   
                                                        });
                                                        //]]>
                                                        </script>							</div>
						</td>
			    </tr>
			</table>
		    </div>
		    <input type="hidden" name="exam_id" id="ResultId" value="">
		    <input type="hidden" name="examWise" value="">
		    <input type="hidden" name="status" value="">
		    <input type="hidden" name="group_name[]" value="">
		    </form>
		</div>
	    </div>
	</div>
<script type="text/javascript">
function examResult(id)
{
    $(document).ready(function(){$('#ResultId').val(id);$("#post_req" ).submit();});
}
</script>