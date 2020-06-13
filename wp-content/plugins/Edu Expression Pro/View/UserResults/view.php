<?php if($mathEditor){?><script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
<script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php $bookmarkUrl=$this->ajaxUrl.'&info=bookmark&examResultId='.$id;
if($_GET['msg']=='invalid'){$msg=$this->ExamApp->showMessage("You can find your result here",'success');}?>
<script type="text/javascript">
function navigation(quesNo){$('.exam-panel').hide();$('#quespanel'+quesNo).show();}
function callPrev(quesNo){if(quesNo!=1)quesNo--;$('.exam-panel').hide();$('#quespanel'+quesNo).show();}
function callNext(quesNo){if($('#totalQuestion').text()!=quesNo)quesNo++;$('.exam-panel').hide();$('#quespanel'+quesNo).show();}
function callComparePrev(rank){rank--;$('.compare').hide();$('#comppanel'+rank).show(20,'linear');}
function callCompareNext(rank){rank++;$('.compare').hide();$('#comppanel'+rank).show(20,'linear');}
function callBoomark(quesNo){$.ajax({method: "POST",url: '<?php echo$bookmarkUrl;?>',data:'&id='+quesNo,beforeSend: function(){$('#exam-loading').show();}}).done(function(data) {
	if(data=='Y'){$('#navbtn'+quesNo).addClass('btn-success');
	$('#bookmark'+quesNo).addClass('btn-danger');
	$('#bookmark'+quesNo).html('<span class="fa fa-star-o"></span> Unbookmark');}
	else{$('#navbtn'+quesNo).removeClass('btn-success');
	$('#bookmark'+quesNo).removeClass('btn-danger');
	$('#bookmark'+quesNo).html('<span class="fa fa-star"></span> Bookmark');}
	$('#exam-loading').hide();});}
$(document).ready(function(){
$('.exam-panel').hide();
$('#quespanel1').show();
$('.compare').hide();
$('#comppanel0').show();
});
</script>



<style type="text/css">
		/* bootstrap hack: fix content width inside hidden tabs */
.tab-content > .tab-pane,.pill-content > .pill-pane {display: block;     /* undo display:none          */
height: 0;          /* height:0 is also invisible */
overflow-y: hidden; /* no-overflow                */
}
.tab-content > .active,.pill-content > .active {height: auto;       /* let the content decide it  */
} /* bootstrap hack end */
.row{margin-left: 0px;margin-right: 0px;}
</style>
<div id="exam-loading" style="display: none;"><?php echo $this->ExamApp->getImage('img/loading-lg.gif');?></div>
<div style="display: none;"><label id="totalQuestion"><?php echo$examDetails['Result.total_question'];?></label></div>
<?php echo$msg;?>
<div class="mrg">
<a href="<?php echo$this->url;?>&info=index" class="btn btn-info"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back');?></a>
</div>
<div class="row my-result">
	<div class="col-md-12">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#score-card" data-toggle="tab"><?php echo __('SCORE CARD');?></a></li>
			<li><a href="#subject-report" data-toggle="tab"><?php echo __('SUBJECT REPORT');?></a></li>
			<li><a href="#time-management" data-toggle="tab"><?php echo __('TIME MANAGEMENT');?></a></li>
			<?php if($examDetails['Exam.declare_result']=="Yes"){?>
			<li><a href="#question" data-toggle="tab"><?php echo __('QUESTION REPORT');?></a></li>
			<li><a href="#solution" data-toggle="tab"><?php echo __('SOLUTION');?></a></li><?php }?>
			<li><a href="#compare-report" data-toggle="tab"><?php echo __('COMPARE REPORT');?></a></li>
		</ul>		  
		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="score-card">
				<div class="rtest_heading"><strong><?php echo __('Score Card For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<td><?php echo __('Total No. of Student');?></td>
							<td><strong class="text-primary"><?php echo $totalStudentCount;?></strong></td>
							<td><?php echo __('My Marks');?></td>
							<td><strong class="text-primary"><?php if($examDetails['Result.obtained_marks']){echo $examDetails['Result.obtained_marks'];}else{echo '0';}?></strong></td>
							<td><?php echo __('Correct Question');?></td>
							<td><strong class="text-primary"><?php echo $correctQuestion;?></strong></td>
							<td><?php echo __('Incorrect Question');?></td>
							<td><strong class="text-danger"><?php echo $incorrectQuestion;?></strong></td>
						</tr>
						<tr>
							<td><?php echo __('Total Marks of Test');?></td>
							<td><strong class="text-primary"><?php echo $examDetails['Result.total_marks'];?></strong></td>
							<td><?php echo __('My Percentile');?></td>
							<td><strong class="text-primary"><?php echo$percentile;?></strong></td>
							<td><?php echo __('Right Marks');?></td>
							<td><strong class="text-primary"><?php echo $rightMarks;?></strong></td>
							<td><?php echo __('Negative Marks');?></td>
							<td><strong class="text-danger"><?php echo str_replace("-","",$negativeMarks);?></strong></td>
						</tr>
						<tr>
							<td><?php echo __('Total Question in Test');?></td>
							<td><strong class="text-primary"><?php echo $examDetails['Result.total_question'];?></strong></td>
							<td><?php echo __('Total Attempt Question in Test');?></td>
							<td><strong class="text-primary"><?php echo $examDetails['Result.total_attempt'];?></strong></td>
							<td><?php echo __('Left Question');?></td>
							<td><strong class="text-danger"><?php echo $leftQuestion;?></strong></td>
							<td><?php echo __('Left Question Marks');?></td>
							<td><strong class="text-danger"><?php echo $leftQuestionMarks;?></strong></td>
						</tr>
						<tr>
							<td><?php echo __('Total Time of Test');?></td>
							<td><strong class="text-primary"><?php echo $this->ExamApp->secondsToWords($examDetails['Exam.duration']*60);?></strong></td>
							<td><?php echo __('My Time');?></td>
							<td><strong class="text-primary"><?php echo $this->ExamApp->secondsToWords(strtotime($examDetails['Result.end_time'])-strtotime($examDetails['Result.start_time']));?></strong></td>
							<td><?php echo __('My Rank');?></td>
							<td><strong class="text-primary"><?php echo $myRank;?></strong></td>
							<td><?php echo __('Result');?></td>
							<td><span class="label label-<?php if($examDetails['Result.result']=="Pass")echo"success";else echo"danger";?>"><?php if($examDetails['Result.result']=="Pass"){echo __('PASSED');}else{echo __('FAILED');}?></span></td>
						</tr>	                              
					</table>
				</div>
			<div class="col-sm-6">
			<div class="rtest_heading"><strong><?php echo __('Performance Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
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
					{"chart":{"renderTo":"mywrapperd2","type":"column"},"title":{"text":null},"series":<?php echo$performanceSeries;?>,"legend":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"style":{}}},"column":{"series":{"dataLabels":{"style":{}}},"column":null,"dataLabels":{"style":{},"enabled":true}}},"xAxis":{"categories":["<?php echo __('Student Performance');?>"]},"yAxis":{"style":{},"title":{"text":"<?php echo __('Score');?>"}},"credits":{"enabled":false}}
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
			</div>
			</div>
			<div class="col-sm-6">
			<div class="rtest_heading"><strong><?php echo __('Question & Marks Wise Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="chart">
				<div id="mywrapperd3"></div>
				<script type="text/javascript">
				//<![CDATA[
				$(document).ready(function() {
				    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
				    Highcharts.setOptions(
					{"global":{"useUTC":true}}
				    );
				    // HIGHROLLER - HIGHCHARTS '' pie chart
				
				    var mywrapperd3 = new Highcharts.Chart(
					{"chart":{"renderTo":"mywrapperd3","type":"pie"},"title":{"text":null,"align":"center"},"series":<?php echo$qmReportSeries;?>,"tooltip":{"enabled":true,"pointFormat":"<b>{point.y}<\/b>"},"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.y}<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
				    );
				    
				    //for column drilldown
				    function setChart(name, categories, data, color) {
					mywrapperd3.xAxis[0].setCategories(categories);
					mywrapperd3.series[0].remove();
					mywrapperd3.addSeries({
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
		<div class="tab-pane" id="subject-report">
			<div class="rtest_heading"><strong><?php echo __('Subject Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th><?php echo __('Name');?></th>
						<th><?php echo __('Total Questions');?></th>
						<th><?php echo __('Correct');?>/<br><?php echo __('Incorrect Question');?></th>
						<th><?php echo __('Marks Scored')?>/<br><?php echo __('Negative Marks');?></th>
						<th><?php echo __('Unattempted Questions');?>/<br><?php echo __('Marks');?></th>
						</tr>
					<?php foreach($userMarksheet as $userValue):?>
					<tr>                                    
						<td class="text-primary"><strong><?php echo $this->ExamApp->h($userValue['Subject']['name']);?></strong></td>
						<td><?php echo$userValue['Subject']['total_question'];?></td>
						<td><span class="text-success"><?php echo$userValue['Subject']['correct_question'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['incorrect_question'];?></span></td>
						<td><span class="text-success"><?php echo$userValue['Subject']['marks_scored'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['negative_marks'];?></span></td>
						<td><span class="text-warning"><?php echo$userValue['Subject']['unattempted_question'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['unattempted_question_marks'];?></span></td>
					</tr>
					<?php endforeach;unset($userValue);?>
				</table>
			</div>
			<div class="rtest_heading"><strong><?php echo __('Graphical Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="col-md-12 col-sm-12">
				<div class="chart">
					<div id="mywrapperdl"></div>
					<script type="text/javascript">
					//<![CDATA[
					$(document).ready(function() {
					    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
					    Highcharts.setOptions(
						{"global":{"useUTC":true}}
					    );
					    // HIGHROLLER - HIGHCHARTS '' column chart
					
					    var mywrapperdl = new Highcharts.Chart(
						{"chart":{"renderTo":"mywrapperdl","type":"column"},"title":{"text":null},"series":<?php echo$grReportSeries;?>,"legend":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"style":{}}},"column":{"series":{"dataLabels":{"style":{}}},"column":null,"dataLabels":{"style":{},"enabled":true}}},"xAxis":{"categories":<?php echo$grxAxis;?>},"yAxis":{"style":{},"title":{"text":"<?php echo __('Score');?>"}},"credits":{"enabled":false}}
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
		<div class="tab-pane" id="time-management">
			<div class="rtest_heading"><strong><?php echo __('Time Management For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th><?php echo __('Name');?></th>
						<th><?php echo __('Total Questions');?></th>
						<th><?php echo __('Correct');?>/<br><?php echo __('Incorrect Question');?></th>
						<th><?php echo __('Marks Scored');?>/<br><?php echo __('Negative Marks');?></th>
						<th><?php echo __('Percentage');?></th>
						<th><?php echo __('Unattempted Questions');?>/<br><?php echo __('Marks');?></th>
						<th><?php echo __('Total Time');?></th>
						</tr>
					<?php foreach($userMarksheet as $userValue):?>
					<tr>                                    
						<td class="text-primary"><strong><?php echo $this->ExamApp->h($userValue['Subject']['name']);?></strong></td>
						<td><?php echo$userValue['Subject']['total_question'];?></td>
						<td><span class="text-success"><?php echo$userValue['Subject']['correct_question'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['incorrect_question'];?></span></td>
						<td><span class="text-success"><?php echo$userValue['Subject']['marks_scored'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['negative_marks'];?></span></td>
						 <td><?php echo number_format($userValue['Subject']['percent'],2);?></td>
						<td><span class="text-warning"><?php echo$userValue['Subject']['unattempted_question'];?></span>/<span class="text-danger"><?php echo$userValue['Subject']['unattempted_question_marks'];?></span></td>
						<td><?php echo $this->ExamApp->secondsToWords($userValue['Subject']['time_taken'],'-');?></td>
					</tr>
					<?php endforeach;unset($userValue);?>
				</table>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="chart">
					<div id="piewrapperqc"></div>
					<script type="text/javascript">
					//<![CDATA[
					$(document).ready(function() {
					    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
					    Highcharts.setOptions(
						{"global":{"useUTC":true}}
					    );
					    // HIGHROLLER - HIGHCHARTS 'Subject Wise Time Taken' pie chart
					
					    var piewrapperqc = new Highcharts.Chart(
						{"chart":{"renderTo":"piewrapperqc","type":"pie"},"title":{"text":"<?php echo$tmReportTitle;?>","align":"center"},"series":<?php echo$tmReportSeries;?>,"tooltip":{"enabled":true,"pointFormat":"{point.mylabel}<\/b>"},"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.mylabel}<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
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
			</div>
		</div>
		<?php if($examDetails['Exam.declare_result']=="Yes"){?>
		<div class="tab-pane" id="question">
			<div class="rtest_heading"><strong><?php echo __('Question Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="table-responsive">
				<table class="table table-bordered">
				<tr>
					<th><?php echo __('Q.No.');?></th>
					<th><?php echo __('Question Status');?></th>
					<th><?php echo __('Your Answer');?></th>
					<th><?php echo __('Correct Answer');?></th>
					<th><?php echo __('Your Score');?></th>
					<th><?php echo __('Your Time');?></th>
					<th><?php echo __('Level');?></th>
				</tr>
				<?php foreach($post as $k=>$ques):$quesNo=$ques['ques_no'];
				if($ques['type']=="M")
				{
					$correctAnswer="";$userAnswer="";
					if(strlen($ques['answer'])>1)
					{
						$correctAnswerExp=explode(",",$ques['answer']);
						foreach($correctAnswerExp as $option):
							$correctAnswer1="option".$option;		
							$correctAnswer.=$ques[$correctAnswer1]."<br>";
						endforeach;unset($option);
						if(strlen($ques['option_selected'])>1)
						{
							$userAnswerExp=explode(",",$ques['option_selected']);
							foreach($userAnswerExp as $option):
							$userAnswer1="option".$option;
							$userAnswer.=$ques[$userAnswer1]."<br>";
							endforeach;unset($option);
						}
					}		    
					else
					{
						if($ques['option_selected'])
						{
							$userAnswer="option".$ques['option_selected'];
							$userAnswer=$ques[$userAnswer];
						}
						$correctAnswer="option".$ques['answer'];			
						$correctAnswer=$ques[$correctAnswer];
					}
				}
				if($ques['type']=="T")
				{
					$userAnswer=$ques['true_false'];
					$correctAnswer=$ques['true_false'];
				}
				if($ques['type']=="F")
				{
					$userAnswer=$ques['fill_blank'];
					$correctAnswer=$ques['fill_blank'];
				}
				if($ques['type']=="S")
				{
					$userAnswer=$ques['answer'];
					$correctAnswer="";
				}
				if($ques['ques_status']=="R")
				$quesStatus="text-success";
				elseif($ques['ques_status']=="W")
				$quesStatus="text-danger";
				else
				$quesStatus="text-info";
				?>
				<tr class="<?php echo$quesStatus;?>">
					<td><strong><?php echo $ques['ques_no'];?></strong></td>
					<td><?php echo str_replace("<script","",$ques['question']);?></td>
					<td><?php echo __($userAnswer);?></td>
					<td><?php echo __($correctAnswer);?></td>
					<td><?php echo$ques['marks_obtained'];?></td>
					<td><?php echo $this->ExamApp->secondsToWords(strtotime($ques['modified'])-strtotime($ques['attempt_time']));?></td>
					<td><?php echo __($ques['diff_level']);?></td>
				</tr>
				<?php endforeach;unset($ques);?>
				</table>
			</div>
		</div>
		<div class="tab-pane" id="solution">
			<div class="rtest_heading"><strong><?php echo __('Solution For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="col-sm-9">
				<?php foreach($post as $k=>$ques):$quesNo=$ques['ques_no'];?>
				<div class="exam-panel" id="quespanel<?php echo$quesNo;?>">
					<div class="table-responsive">
						<table class="table table-bordered">
							<?php
							if($ques['type']=="M")
							{
								$options=array();
								$optionKeyArr=explode(",",$ques['options']);
								$index=0;
								foreach($optionKeyArr as $value)
								{
									$optKey="option".$value;
									if(strlen($ques[$optKey])>0)
									{
										$index++;
										$options[$index]=str_replace("<script","",$ques[$optKey]);
									}
								}
								unset($value,$key);
								$correctAnswer="";$userAnswer="";
								if(strlen($ques['answer'])>1)
								{
									$correctAnswerExp=explode(",",$ques['answer']);
									foreach($correctAnswerExp as $option):
										$correctAnswer[]="Option".$option;
									endforeach;unset($option);
									$correctAnswer=implode(",",$correctAnswer);
									
									$userAnswerExp=explode(",",$ques['option_selected']);
									foreach($userAnswerExp as $option):
									$userAnswer[]="Option".$option;
									endforeach;unset($option);
									
								}		    
								else
								{
									if($ques['option_selected'])
									{
										$userAnswer="Option".$ques['option_selected'];
									}
									$correctAnswer="Option".$ques['answer'];
								}
							}
							if($ques['type']=="T")
							{
								$userAnswer=$ques['true_false'];
								$correctAnswer=$ques['true_false'];
							}
							if($ques['type']=="F")
							{
								$userAnswer=$ques['fill_blank'];
								$correctAnswer=$ques['fill_blank'];
							}
							if($ques['type']=="S")
							{
								$userAnswer=$ques['answer'];
								$correctAnswer="";
							}
							if($ques['ques_status']=="R")
							$quesStatus="text-success";
							elseif($ques['ques_status']=="W")
							$quesStatus="text-danger";
							else
							$quesStatus="text-info";
							
							?>
							<tr class="<?php echo$quesStatus;?>">
							<tr><td colspan="3"><?php echo '<strong>'.__('Question').': '.$quesNo.'</strong>&nbsp;&nbsp;'.str_replace("<script","",$ques['question']);?></td></tr>
							<tr><td colspan="3">
							<?php $correctIcon=$this->ExamApp->getImage('img/correct_icon.png');$incorrectIcon=$this->ExamApp->getImage('img/incorrect_icon.png');
							if($ques['type']=="M")
							{
								$correctImg="";$incorrectImg="";
								foreach($options as $opt=>$option):
								if(strlen($ques['answer'])>1)
								{
									$correctImg="";$incorrectImg="";
									foreach(explode(",",$ques['option_selected']) as $value){
									if($opt==$value && $ques['ques_status']=='W'){$incorrectImg=$incorrectIcon;break;}}
									unset($value);
									foreach(explode(",",$ques['correct_answer']) as $value){
									if($opt==$value){$incorrectImg=$correctIcon;break;}}
									unset($value);
								}
								else
								{
									if($opt==$ques['correct_answer']){$correctImg=$correctIcon;}else{$correctImg="";}
									if($opt==$ques['option_selected'] && $ques['ques_status']=='W'){$incorrectImg=$incorrectIcon;}else{$incorrectImg="";}
								}
								echo '<p>'.$opt.'. '.$incorrectImg.$correctImg.' '.$option.'</p>';
								endforeach;unset($option);
							}
							if($ques['type']=="T")
							{
								$correctImgTrue="";$correctImgFalse="";$incorrectImgTrue="";$incorrectImgFalse="";
								if($ques['true_false']=="True")
								{
									$correctImgTrue=$correctIcon;
								}
								else
								{
									$correctImgFalse=$correctIcon;
								}
								if($ques['ques_status']=='W' && $ques['true_false']=="True")
								{
									$incorrectImgTrue=$incorrectIcon;
								}
								if($ques['ques_status']=='W' && $ques['true_false']=="False")
								{
									$incorrectImgFalse=$incorrectIcon;
								}
								echo $correctImgTrue.$incorrectImgTrue.__('True').' / '.$correctImgFalse.$incorrectImgFalse.__('False');
							}
							?>
							</td></tr>
							<tr>
								<td><?php if($ques['ques_status']==NULL)echo'<strong class="text-info">'.__('Not Attempt').'</strong>';else echo'<strong class="text-warning">'.__('Attempt').'</strong>';?></strong></td>
								<?php if($ques['ques_status']=='R'){?><td><strong class="text-success"><?php echo __('Correct');?></strong></td><?php }?>
								<?php if($ques['ques_status']=='W'){?><td><strong class="text-danger"><?php echo __('Incorrect');?></strong></td>
								<td><strong><?php echo __('Your Answers');?> :</strong>&nbsp;<strong class="text-danger"><?php echo __($userAnswer);?></strong>
								<?php if($ques['type']!="S"){?><strong><?php echo __('Correct Answers');?> :</strong>&nbsp;<strong class="text-success"><?php echo __($correctAnswer);?></strong><?php }?></td><?php }else{?>
								<?php if($ques['type']!="S"){?><td><strong><?php echo __('Correct Answers');?> :</strong>&nbsp;<strong class="text-success"><?php echo __($correctAnswer);?></strong></td><?php }}?>
			
							</tr>
							<tr><td><strong><?php echo __('Max Marks');?> :</strong>&nbsp;&nbsp;<?php echo$ques['marks'];?></td>
							<td><strong><?php echo __('Marks Scored');?> :</strong>&nbsp;&nbsp;<?php echo$ques['marks_obtained'];?></td>
							<td><strong><?php echo __('Time Taken');?> :</strong>&nbsp;&nbsp;<?php echo $this->ExamApp->secondsToWords($ques['time_taken'],"Not Attempted");?></td>
							</tr>
							<?php if($ques['explanation']){?><tr><td colspan="3"><strong><?php echo __('Solution');?> :</strong>&nbsp;&nbsp;<?php echo str_replace("<script","",$ques['explanation']);?></td></tr><?php }?>
						</table>
					</div>
					<div class="col-sm-2">
					<button type='button' class='btn btn-default btn-sm btn-block' onclick='callPrev(<?php echo$quesNo;?>);'><?php echo __('Prev');?>&larr;</button>
					</div>
					<div class="col-sm-2">
					<button type='button' class='btn btn-default btn-sm btn-block' onclick='callNext(<?php echo$quesNo;?>);'><?php echo __('Next');?>&rarr;</button>
					</div>
					<div class="col-sm-2">
					<?php if($ques['bookmark']=="Y"){$btnBookmark='<span class="fa fa-star-o"></span>&nbsp;'. __('Unbookmark');$btnColor='btn-danger';}else{$btnBookmark='<span class="fa fa-star"></span>&nbsp;'. __('Bookmark');$btnColor='btn-success';}?>
					<button type='button' id='bookmark<?php echo$quesNo;?>' class='btn  <?php echo$btnColor;?> btn-sm btn-block' onclick='callBoomark(<?php echo$quesNo;?>);'><?php echo$btnBookmark;?></button>
					</div>
				</div>
				<?php endforeach;unset($ques);?>
			</div>
			<div class="col-sm-3">
				<div class="panel-group" id="accordion">
				<?php $i=0; foreach($userSectionQuestion as $subjectName=>$quesArr):$i++;
				$subjectNameId=str_replace(" ","",$this->ExamApp->h($subjectName));
				?>			
				<div class="panel panel-default" style="max-height: 375px;overflow-y: scroll;">
					<div class="panel-heading">
					<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#<?php echo$subjectNameId;?>"><?php echo $this->ExamApp->h($subjectName);?></a></h4>
					</div>
					<div id="<?php echo$subjectNameId;?>" class="panel-collapse collapse<?php if($i==1){?> in<?php }?>">
						<div class="panel-body">
							<div class="row">
								<?php foreach($quesArr as $value):$quesNo=$value['ques_no'];
								if($value['bookmark']=="Y")$btnColor="btn-success";else$btnColor="btn-default";?>
								<div class="col-md-3 cols-sm-3 col-xs-3 mrg-1"><button type="button" id="navbtn<?php echo$quesNo;?>" class='btn btn-circle <?php echo$btnColor;?> btn-sm navigation' onclick="navigation(<?php echo$quesNo;?>);"><?php echo$quesNo;?></button></div>
								<?php endforeach;unset($quesArr);?>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach;unset($i);unset($value);?>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="tab-pane" id="compare-report">
			<div class="rtest_heading"><strong><?php echo __('Compare Report For');?>  </strong><?php echo $this->ExamApp->h($examDetails['Exam.name']);?></div>
			<div class="com-md-12 col-sm-12 col-xs-12">
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<td><?php echo __('Total Ques.');?></td>
								<td><strong><?php echo $examDetails['Result.total_attempt'];?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Maximum Marks');?></td>
								<td><strong><?php echo $examDetails['Result.total_marks'];?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Attempted Ques.');?></td>
								<td><strong class="text-success"><?php echo $attemptedQuestion;?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Unattempted Ques.');?></td>
								<td><strong class="text-danger"><?php echo $leftQuestion;?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Correct Ques.');?></td>
								<td><strong class="text-success"><?php echo $correctQuestion;?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Incorrect Ques.');?></td>
								<td><strong class="text-danger"><?php echo $incorrectQuestion;?></strong></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<td><?php echo __('Total Score');?></td>
								<td><strong class="text-primary"><?php echo $examDetails['Result.obtained_marks'];?>/<?php echo $examDetails['Result.total_marks'];?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Percentage');?></td>
								<td><strong><?php echo$examDetails['Result.percent'];?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Percentile');?></td>
								<td><strong><?php echo$percentile;?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Total Time');?></td>
								<td><strong><?php echo $this->ExamApp->secondsToWords(strtotime($examDetails['Result.end_time'])-strtotime($examDetails['Result.start_time']));?></strong></td>
							</tr>
							<tr>
								<td><?php echo __('Rank');?></td>
								<td valign="top" rowspan="2"><?php global $current_user;echo get_avatar(get_current_user_id(),60,null,$current_user->display_name);?></td>
							</tr>
							<tr>
								<td><div class="rank"><?php echo $myRank;?></div></td>
							</tr>
						</table>
					</div>
				</div>
				<?php foreach($compareArr as $k=>$compPost):?>
				<div id="comppanel<?php echo$k;?>" class="compare">
					<div class="col-md-3 col-sm-6 col-xs-6">
						<div class="table-responsive">
							<table class="table">
								<tr>
									<td><?php echo __('Total Ques.');?></td>
									<td><strong><?php echo $compPost[0]['Result.total_attempt'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Maximum Marks');?></td>
									<td><strong><?php echo $compPost[0]['Result.total_marks'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Attempted Ques.');?></td>
									<td><strong class="text-success"><?php echo $compPost['attempted_question'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Unattempted Ques.');?></td>
									<td><strong class="text-danger"><?php echo $compPost['left_question'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Correct Ques.');?></td>
									<td><strong class="text-success"><?php echo $compPost['correct_question'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Incorrect Ques.');?></td>
									<td><strong class="text-danger"><?php echo $compPost['incorrect_question'];?></strong></td>
								</tr>
							</table>
						</div>
						<div class="col-md-4 col-sm-7 col-xs-7"><?php if($k!=0){?><button type="button" class='btn btn-sm btn-primary' onclick="callComparePrev(<?php echo$k;?>);"><?php echo __('Previous');?></button><?php }else{?><button type="button" class='btn btn-sm btn-primary' disabled="disabled"><?php echo __('Previous');?></button><?php }?></div>
						<div class="col-md-3 col-sm-5 col-xs-5"><?php if($k<$compareCount){?><button type="button" class='btn btn-sm btn-primary' onclick="callCompareNext(<?php echo$k;?>);"><?php echo __('Next');?></button><?php }else{?><button type="button" class='btn btn-sm btn-primary' disabled="disabled"><?php echo __('Next');?></button><?php }?></div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6">
						<div class="table-responsive">
							<table class="table">
								<tr>
									<td><?php echo __('Total Score');?></td>
									<td><strong class="text-primary"><?php echo $compPost[0]['Result.obtained_marks'];?>/<?php echo $compPost[0]['Result.total_marks'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Percentage');?></td>
									<td><strong><?php echo$compPost[0]['Result.percent'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Percentile');?></td>
									<td><strong><?php echo$compPost['percentile'];?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Total Time');?></td>
									<td><strong><?php echo $this->ExamApp->secondsToWords(strtotime($compPost[0]['Result.end_time'])-strtotime($compPost[0]['Result.start_time']));?></strong></td>
								</tr>
								<tr>
									<td><?php echo __('Rank');?></td>
									<td valign="top" rowspan="2"><?php echo get_avatar($compPost[0]['student_id'],60,null,$compPost[0]['Student.name']);?>
									<div class="rank_name"><?php echo$compPost[0]['Student.name'];?></div></td>
								</tr>
								<tr>
									<td><div class="rank"><?php echo $compPost['rank'];?></div></td>
								</tr>
							</table>
						</div>					
					</div>
				</div>
				<?php endforeach;unset($compPost);?>
				<div style="display: none;"><label id="totalRank"><?php echo$compareCount;?></label></div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="chart">
					<div id="mywrapperd5"></div>
					<script type="text/javascript">
					//<![CDATA[
					$(document).ready(function() {
					    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
					    Highcharts.setOptions(
						{"global":{"useUTC":true}}
					    );
					    // HIGHROLLER - HIGHCHARTS '' column chart
					
					    var mywrapperd5 = new Highcharts.Chart(
						{"chart":{"renderTo":"mywrapperd5","type":"column"},"title":{"text":null},"series":<?php echo$crReportSeries;?>,"legend":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"style":{}}},"column":{"series":{"dataLabels":{"style":{}}},"column":null,"dataLabels":{"style":{},"enabled":true}}},"xAxis":{"categories":<?php echo$crxAxis;?>},"yAxis":{"max":100,"title":{"text":"<?php echo$cryAxis;?>"}},"credits":{"enabled":false}}
					    );
					    
					    //for column drilldown
					    function setChart(name, categories, data, color) {
						mywrapperd5.xAxis[0].setCategories(categories);
						mywrapperd5.series[0].remove();
						mywrapperd5.addSeries({
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
</div>
</div>