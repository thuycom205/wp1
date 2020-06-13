<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
    Highcharts.setOptions(
        {"global":{"useUTC":true}}
    );
    // HIGHROLLER - HIGHCHARTS 'Question Count' pie chart

    var piewrapperqc = new Highcharts.Chart(
        {"chart":{"renderTo":"piewrapperqc","type":"pie"},"title":{"text":"<?php echo$questionCountTitle;?>","align":"center"},"series":[<?php echo$questionCountSeries;?>],"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.y}<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
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
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
    Highcharts.setOptions(
        {"global":{"useUTC":true}}
    );
    // HIGHROLLER - HIGHCHARTS 'Question Bank Difficulty Wise' areaspline chart

    var mywrapperdl = new Highcharts.Chart(
        {"chart":{"renderTo":"mywrapperdl","type":"areaspline"},"title":{"text":"<?php echo$questionSubjectTile;?>"},"series":<?php echo$questionSubjectSeries;?>,"legend":{"enabled":true},"tooltip":{"shared":true},"xAxis":{"categories":<?php echo$questionSubjectxAxis;?>},"credits":{"enabled":false}}
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
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
    Highcharts.setOptions(
        {"global":{"useUTC":true}}
    );
    // HIGHROLLER - HIGHCHARTS 'Student Details' column chart

    var mywrapperd2 = new Highcharts.Chart(
        {"chart":{"renderTo":"mywrapperd2","type":"column"},"title":{"text":"<?php echo$studentDetailTitle;?>"},"series":<?php echo$studentDetailSeries;?>,"legend":{"enabled":true},"tooltip": {"headerFormat": '<span style="font-size:10px">{point.key}</span><table>',"pointFormat": '<tr><td style="color:{series.color};padding:0">{series.name}: </td><td style="padding:0"><b>{point.y}</b></td></tr>',"footerFormat": "</table>","shared": true,"useHTML": true},"plotOptions":{"column":{"dataLabels":{"style":{}},"formatter":{"formatter":""},"pointPadding":0.4}},"xAxis":{"categories":<?php echo$studentDetailXaxis;?>},"labels":{"formatter":{"formatter":""}},"dataLabels":{"formatter":{"formatter":""}},"yAxis":{"style":{},"title":{"text":""}},"credits":{"enabled":false}}
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
</script>
<div class="col-sm-12">
	<div class="col-sm-3">
		<div class="xe-widget xe-counter" data-count=".num" data-from="0" data-to="<?php echo$totalInprogressExam;?>" data-suffix="" data-duration="2">
			<div class="xe-icon"> <i class="fa fa-ellipsis-h"></i></div>
			<div class="xe-label"> <strong class="num"><?php echo$totalInprogressExam;?></strong> <span><?php echo __('In Progress Exam');?></span> </div>
		</div>
		<div class="xe-widget xe-counter xe-counter-purple" data-count=".num" data-from="0" data-to="<?php echo$totalUpcomingExam;?>" data-suffix="" data-duration="3" data-easing="false">
			<div class="xe-icon"> <i class="fa fa-cloud"></i></div>
			<div class="xe-label"> <strong class="num"><?php echo$totalUpcomingExam;?></strong> <span><?php echo __('Upcoming Exam');?></span></div>
		</div>
		<div class="xe-widget xe-counter xe-counter-info" data-count=".num" data-from="0" data-to="<?php echo$totalCompletedExam;?>" data-duration="4" data-easing="true">
			<div class="xe-icon"> <i class="fa fa-check"></i> </div>
			<div class="xe-label"> <strong class="num"><?php echo$totalCompletedExam;?></strong> <span><?php echo __('Completed Exam');?></span> </div>
		</div>
		<div class="xe-widget xe-counter xe-counter-info" data-count=".num" data-from="0" data-to="<?php echo$totalStudents;?>" data-duration="4" data-easing="true">
			<div class="xe-icon"> <i class="fa fa-graduation-cap"></i> </div>
			<div class="xe-label"> <strong class="num"><?php echo$totalStudents;?></strong> <span><?php echo __('Students');?></span> </div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="panel panel-custom">
			<div class="panel-heading"><?php echo __('In Progress & Upcoming Exams');?></div>
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th><?php echo __('Date');?></th>
						<th><?php echo __('Exam Name');?></th>
						<th><?php echo __('Group');?></th>
						<th><?php echo __('Marks');?></th>
						<th><?php echo __('Duration');?></th>
					</tr>
					<tr>
					<?php $i=0; foreach($examStat as $post):$i++;?>
					<tr>
						<td><?php echo$this->ExamApp->dateFormat($post['start_date']);?></td>
						<td><?php echo $this->ExamApp->h($post['name']);?></td>
						<td><?php echo$this->ExamApp->showGroupName("emp_exam_groups","emp_groups","exam_id",$post['id']);?></td>
						<td><?php echo $post['total_marks'];?></td>
						<td><?php echo __($this->ExamApp->secondsToWords($post['duration']*60));?></td>
					</tr>
					<?php endforeach;?>
					<?php unset($post);?>
					<?php for($j=$i;$j<3;$j++):?>
					<tr><td colspan="5">&nbsp;</td></tr>
					<?php endfor;?>
					<?php unset($i);unset($j);?>
				</table>
			</div>					
		</div>
	</div>
</div>
<div>
	<form action="<?php echo$this->url;?>&info=index" name="post_req" id="post_req" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
	<div class="col-sm-12">
		<div class="panel panel-custom">
		<div class="panel-heading"><?php echo __('Recent Exam Results');?></div>
			<div class="table-responsive">
			<table class="table table-bordered">
				<tr>
					<th><?php echo __('Exam');?></th>
					<th><?php echo __('Overall Result');?></th>
					<th><?php echo __('Student Stats');?></th>							
				</tr>
				<?php foreach($recentExamResult as $k=>$recentValue):
				$id=$recentValue['RecentExam']['id'];?>
					<tr>
						<td><strong class="text-danger"><?php echo $this->ExamApp->h($recentValue['RecentExam']['name']);?></strong><br/>
						<?php echo __('From');?>: <strong class="text-danger"><?php echo$this->ExamApp->dateFormat($recentValue['RecentExam']['start_date']);?></strong><br/>
						<?php echo __('To');?>: <strong class="text-danger"><?php echo$this->ExamApp->dateFormat($recentValue['RecentExam']['end_date']);?></strong><br/>
						<a href="javascript:void(0);" onclick="examResult(<?php echo$id;?>);" class="btn btn-info"><?php echo __('Details');?></a>
                                                </td>
						<td>
                                                        <?php $chartRerData=array();
                                                        $chartRerData[]=array(__('Pass'),(int) $recentValue['RecentExam']['StudentStat']['pass']);
                                                        $chartRerData[]=array(__('Fail'),(int) $recentValue['RecentExam']['StudentStat']['fail']);
                                                        $chartRerData[]=array(__('Absent'),(int) $recentValue['RecentExam']['StudentStat']['absent']);
                                                        $id=$recentValue['RecentExam']['id'];
                                                        $chartStudentStat=json_encode(array(array('name'=>'Student','data'=>$chartRerData)));
                                                        $chartOverallResult=json_encode(array(array('name'=>__('Passing %age'),'data'=>array($recentValue['RecentExam']['OverallResult']['passing'])),
                                                                                              array('name'=>__('Average %age'),'data'=>array($recentValue['RecentExam']['OverallResult']['average']))));
                                                        ?>
							<div class="chart">
							<div id="mywrapperor<?php echo$k;?>"></div>
							<script type="text/javascript">
                                                        //<![CDATA[
                                                        $(document).ready(function() {
                                                            // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
                                                            Highcharts.setOptions(
                                                                {"global":{"useUTC":true}}
                                                            );
                                                            // HIGHROLLER - HIGHCHARTS '' bar chart
                                                        
                                                            var mywrapperor<?php echo$k;?> = new Highcharts.Chart(
                                                                {"chart":{"renderTo":"mywrapperor<?php echo$k;?>","type":"bar","width":350,"height":200},"title":{"text":null},"series":<?php echo$chartOverallResult;?>,"legend":{"enabled":true},"xAxis":{},"credits":{"enabled":false}}
                                                            );
                                                            
                                                            //for column drilldown
                                                            function setChart(name, categories, data, color) {
                                                                mywrapperor<?php echo$k;?>.xAxis[0].setCategories(categories);
                                                                mywrapperor<?php echo$k;?>.series[0].remove();
                                                                mywrapperor<?php echo$k;?>.addSeries({
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
							<div id="mywrapperss<?php echo$k;?>"></div>
							<script type="text/javascript">
                                                        //<![CDATA[
                                                        $(document).ready(function() {
                                                            // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
                                                            Highcharts.setOptions(
                                                                {"global":{"useUTC":true}}
                                                            );
                                                            // HIGHROLLER - HIGHCHARTS '' pie chart
                                                        
                                                            var mywrapperss<?php echo$k;?> = new Highcharts.Chart(
                                                                {"chart":{"renderTo":"mywrapperss<?php echo$k;?>","type":"pie","width":300,"height":200},"title":{"text":null},"series":<?php echo$chartStudentStat;?>,"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.percentage:.1f}%<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
                                                            );
                                                            
                                                            //for column drilldown
                                                            function setChart(name, categories, data, color) {
                                                                mywrapperss<?php echo$k;?>.xAxis[0].setCategories(categories);
                                                                mywrapperss<?php echo$k;?>.series[0].remove();
                                                                mywrapperss<?php echo$k;?>.addSeries({
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
					<?php endforeach;?>
					<?php unset($recentValue);?>
			</table>
			</div>
		</div> 
	</div>
        <input type="hidden" name="exam_id" id="ResultId" value=""><input type="hidden" name="examWise" value=""><input type="hidden" name="status" value=""><input type="hidden" name="group_name[]" value="">
        </form>
</div>
<div>
	<div class="col-sm-12">
		<div class="panel panel-custom">
		<div class="panel-heading"><?php echo __('Top 10 Student Group Wise');?></div>
			<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<tr>
					<td>
					<div class="chart">
					<div id="mywrapperd2"></div>
					</div>
					</td>
					
			</table>
			</div>
		</div>
	</div>
</div>
<div>
	<div class="col-sm-12">
		<div class="panel panel-custom">
			<div class="panel-heading"><?php echo __('Student Statistic Table');?></div>
			<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<tr>
					<th><?php echo __('Group');?></th>
					<th><?php echo __('Total Students');?></th>
					<th><?php echo __('Total Active');?></th>
					<th><?php echo __('Total Pending');?></th>
					<th><?php echo __('Total Suspended');?></th>
				</tr>
				<?php foreach($studentStatitics as $studentValue):?>
				<tr>
					<td><?php echo $this->ExamApp->h($studentValue['GroupName']['name']);?></td>
					<td><?php echo $studentValue['GroupName']['total_student'];?></td>
					<td><?php echo __($studentValue['GroupName']['active']);?></td>
					<td><?php echo __($studentValue['GroupName']['pending']);?></td>
					<td><?php echo __($studentValue['GroupName']['suspend']);?></td>
				</tr>
				<?php endforeach;unset($studentValue);?>												
			</table>
			</div>
		</div> 
	</div>
</div>
<div>
	<div class="col-sm-12">
		<div class="panel panel-custom">
		<div class="panel-heading"><?php echo __('Top 10 Question Bank Subject Wise');?></div>									
			<div class="chart">
			<div id="piewrapperqc"></div>
			</div>
		</div>
	</div>
</div>
<div>
	<div  class="col-sm-12">
		<div class="panel panel-custom">
		<div class="panel-heading"><?php echo __('Top 10 Difficulty Level of Questions');?></div>
			<div class="chart">
			<div id="mywrapperdl"></div>
			</div>
		</div>
	</div>
</div>
<div>
    <div class="col-sm-12">
        <div class="panel panel-custom">
        <div class="panel-heading"><?php echo __('Question Count Table');?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr>
                            <th><?php echo __('Bank Name');?></th>
                            <th><?php echo __('Total Question');?></th>
                            <th><?php echo __('Total').' '.$this->ExamApp->getDiffLevel('E');?></th>
                            <th><?php echo __('Total').' '.$this->ExamApp->getDiffLevel('M');?></th>
                            <th><?php echo __('Total').' '.$this->ExamApp->getDiffLevel('D');?></th>							
                    </tr>
                    <?php foreach($Subject as $sd):?>
                    <tr><td><?php echo $this->ExamApp->h($subjectName=$sd['subject_name']);?></td>
                    <td><?php echo$DifficultyDetail[$subjectName]['total_question'];?></td>
                    <?php $i=0; foreach($DiffLevel as $diff):?>
                    <td><?php echo$DifficultyDetail[$subjectName][$i];?></td>
                    <?php $i++;endforeach;?>
                    </tr>
                    <?php endforeach;unset($sd);?>                                
                </table>
            </div>
        </div> 
    </div>
</div>
<script type="text/javascript">
function examResult(id)
{
    $(document).ready(function(){$('#ResultId').val(id);$("#post_req" ).submit();});
}
</script>