<div class="container">
        <div class="panel panel-custom mrg">
        <div class="panel-heading"><?php echo __('Exam Details');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>            
	<div class="panel-body">
                    <div class="table-responsive"> 
						<table class="table table-bordered">
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Exam Name');?></small></strong></td>
								<td><?php echo $this->ExamApp->h($resultArr['name']);?></td>
								<td colspan=2><?php if($resultArr['status']=="Inactive" && $resultArr['type']=="Exam"){?><a href="<?php echo $this->urlAquestions;?>&examId=<?php echo$resultArr['id'];?>" class='btn btn-success'><?php echo __('Add Questions');?></a><?php }?>
                                                               <?php if($resultArr['status']=="Active"){?><a href="<?php echo$this->urlAttemptedpapers;?>&info=index&id=<?php echo $id;?>" class='btn btn-success'><?php echo __('Finalize Result');?></a><?php }?>
                                                               <?php if($resultArr['status']=="Closed"){?><a href="<?php echo$this->urlAttemptedpapers;?>&info=index&id=<?php echo $id;?>" class='btn btn-primary'><?php echo __('Attempted Papers');?></a><?php }?></td>
							</tr>
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Group');?></small></strong></td>
								<td><?php  echo "(".$this->ExamApp->showGroupName("emp_exam_groups","emp_groups","exam_id",$id).")";?></td>
								<td colspan=2><?php if($resultArr['status']=="Active"){?><a href="<?php echo admin_url('admin-ajax.php').'?action=examapp_Attemptedpaper';;?>&info=closeexam&id=<?php echo $id;?>" class='btn btn-danger'><?php echo __('Close Exam');?></a><?php }?>
                                                                <?php if($resultArr['status']=="Inactive"){?><a href="<?php echo$this->ajaxUrl;?>&info=activateexam&value=Active&id=<?php echo $id;?>" class='btn btn-info'><?php echo __('Activate Exam');?></a><?php }?>
                                                                <?php if($resultArr['status']=="Closed"){?><a href="<?php echo$this->ajaxUrl;?>&info=activateexam&value=Active&id=<?php echo $id;?>" class='btn btn-danger'><?php echo __('Re-Activate Exam');?></a><?php }?>
                                                                </td>
							</tr>
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Exam Instructions');?></small></strong></td>
								<td colspan='3'><?php echo str_replace("<script","",$resultArr['instruction']);?></td>
								
							</tr>
							
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Start Date');?></small></strong></td>
								<td ><?php echo $this->ExamApp->dateTimeFormat($resultArr['start_date']);?></td>
								<td><strong><small class="text-primary"><?php echo __('End Date');?></small></strong></td>
								<td><?php  echo $this->ExamApp->dateTimeFormat($resultArr['end_date']);?></td>
							</tr>
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Show Answer Sheet');?></small></strong></td>
								<td ><?php if($resultArr['declare_result']){echo __('Yes');}else{echo __('No');};?></td>
								<td><strong><small class="text-primary"><?php echo __('Browser Tolrence');?></small></strong></td>
								<td ><?php if($resultArr['browser_tolrance']){echo __('Yes');}else{echo __('No');};?></td>								
							</tr>
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Paid Exam');?></small></strong></td>
								<td><?php if($resultArr['paid_exam']=="1"){echo __('Yes');}else{echo __('No');};?></td>								
								<td><strong><small class="text-primary"><?php echo __('Amount');?></small></strong></td>
								<td><?php if($resultArr['paid_exam']=="1"){echo$this->ExamApp->getCurrency().$resultArr['amount'];}?></td>
							</tr>
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Result After Finish');?></small></strong></td>
								<td><?php if($resultArr['finish_result']){echo __('Yes');}else{echo __('No');};?></td>
								<td><strong><small class="text-primary"><?php echo __('Mode');?></small></strong></td>
								<td><?php echo __($resultArr['type']);?></td>
							</tr>
							
							<tr>
								<td><strong><small class="text-primary"><?php echo __('Duration');?></small></strong></td>
								<td><?php echo __($this->ExamApp->secondsToWords($resultArr['duration']*60));?></td>
								<td><strong><small class="text-primary">Exam Attempt Count</small></strong></td>
								<td><?php if($resultArr['attempt_count']==0){echo __('Unlimited');}else{echo $resultArr['attempt_count'];}?></td>
							</tr>
							<tr>
								<?php if($this->configuration['exam_expiry']){?><td><strong><small class="text-primary"><?php echo __('Expiry');?></small></strong></td>
								<td><?php if($resultArr['expiry']==0){echo __('Unlimited');}else{echo $resultArr['expiry']." ".__('Days');};?></td><?php  }?>
								<?php if($resultArr['type']=="Exam"){?><td><strong><small class="text-primary"><?php echo __('Total Marks');?></small></strong></td>
								<td><?php echo $totalMarks;?></td><?php  }?>
								
							</tr>							
						</table>
					</div>
					<?php if($resultArr['type']=="Exam"){?>
					<div class="table-responsive"> 	
						<table class="table table-bordered">
							<tr class="text-primary">
								<th><small><?php echo __('Subject');?></small></th>
								<th><small><?php echo __('Subjective');?></small></th>
								<th><small><?php echo __('Objective');?></small></th>
								<th><small><?php echo __('True &amp; False');?></small></th>
								<th><small><?php echo __('Fill in the blanks');?></small></th>
								<th><small><?php echo __('Difficulty Level');?></small></th>
								<th><small><?php echo __('Total Questions');?></small></th>
								<?php if($examCount){?><th><small><?php echo __('Question Attempt Count');?></small></th><?php }?>
							</tr>                    
							<?php $totalSubjective=0;$totalObjective=0;$totalTrueFalse=0;$totalFillBlank=0;$totalQuestion=0;$totalAttemptQuestion=0;
							$totalEasy=0;$totalMedium=0;$totalHard=0;
							foreach($SubjectDetail as $sd){?>
							<tr><td><?php echo$subject_name=$this->ExamApp->h($sd['subject_name']);?></td>
								<?php for($i=0;$i<4;$i++){?>
								<td><?php echo$QuestionDetail[$subject_name][$i];?></td>
								<?php if($i==0)$totalSubjective=$totalSubjective+$QuestionDetail[$subject_name][0];
								if($i==1)$totalObjective=$totalObjective+$QuestionDetail[$subject_name][1];
								if($i==2)$totalTrueFalse=$totalTrueFalse+$QuestionDetail[$subject_name][2];
								if($i==3)$totalFillBlank=$totalFillBlank+$QuestionDetail[$subject_name][3];
								}?>								
								<td>
								<?php $i=0;$sum=0;
								foreach($DiffLevel as $diff){
								$sum=$sum+$DifficultyDetail[$subject_name][$i];
								?>
								<?php if($diff['type']=="D")$diffType=__('H');else$diffType=__($diff['type']);
								echo$DifficultyDetail[$subject_name][$i]."(".$diffType.")";?>
								<?php if($i==0)$totalEasy=$totalEasy+$DifficultyDetail[$subject_name][0];
								if($i==1)$totalMedium=$totalMedium+$DifficultyDetail[$subject_name][1];
								if($i==2)$totalHard=$totalHard+$DifficultyDetail[$subject_name][2];?>
								<?php $i++;}?></td>
								<td><?php echo$sum;?></td>
								<?php if($examCount){?><td><?php if($sd['max_question']==0){echo $sum;}else{echo $this->ExamApp->h($sd['max_question']);}?></td><?php }?>
							</tr>
							<?php $totalQuestion=$totalQuestion+$sum;
							if($sd['max_question']=='0'){$totalAttemptQuestion=$totalAttemptQuestion+$sum;}else{$totalAttemptQuestion=$totalAttemptQuestion+$sd['max_question'];}
							}?>
							<?php unset($sd);?>
							<tr>
								<td><strong><?php echo __('Total');?></strong></td>
								<td><strong><?php echo$totalSubjective;?></strong></td>
								<td><strong><?php echo$totalObjective;?></strong></td>
								<td><strong><?php echo$totalTrueFalse;?></strong></td>
								<td><strong><?php echo$totalFillBlank;?></strong></td>
								<td><strong><?php echo$totalEasy;?>(<?php echo __('E');?>) <?php echo$totalMedium;?>(<?php echo __('M');?>)(<?php echo$totalHard;?><?php echo __('H');?>)</strong></td>
								<td><strong><?php echo$totalQuestion;?></strong></td>
								<?php if($examCount){?><td><strong><?php echo$totalAttemptQuestion;?></strong></td><?php }?>
							</tr>
                        </table>
					</div>
					<div class="table-responsive"> 	
						<table class="table table-bordered">
							<tr>
								<?php $i=0;
								foreach($SubjectDetail as $value):
								$chartData=array();
								$subject_id=$value['subject_id'];
								$subject_name=$this->ExamApp->h($value['subject_name']);
								$j=0;
								foreach($DiffLevel as $diff)
								{
									$tot_ques=(float) $DifficultyDetail[$subject_name][$j];
									$chartData[]=array($diff['diff_level'],$tot_ques);
									$j++;
									
								}
								$levelSeries=json_encode(array(array("name"=>__('Difficulty Level'),'data'=>$chartData)));
								?>	
								<td>
									<div class="chart">	
										<div id="piewrapper<?php echo$i;?>"></div>
										<script type="text/javascript">
										//<![CDATA[
										$(document).ready(function() {
										    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
										    Highcharts.setOptions(
											{"global":{"useUTC":true}}
										    );
										    // HIGHROLLER - HIGHCHARTS 'Physics' pie chart
										
										    var piewrapper<?php echo$i;?> = new Highcharts.Chart(
											{"chart":{"renderTo":"piewrapper<?php echo$i;?>","type":"pie","width":250,"height":300},"title":{"text":"<?php echo$subject_name;?>","align":"left"},"series":<?php echo$levelSeries;?>,"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"<b>{point.y}<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}});
										    
										    //for column drilldown
										    function setChart(name, categories, data, color) {
											piewrapper<?php echo$i;?>.xAxis[0].setCategories(categories);
											piewrapper<?php echo$i;?>.series[0].remove();
											piewrapper<?php echo$i;?>.addSeries({
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
								<?php $i++;endforeach;unset($value);?>
							</tr>
						</table>
					</div>
					<?php }else{?>
					<div class="table-responsive"> 	
						<table class="table table-bordered">
							<tr class="text-primary">
								<th><small><?php echo __('Subject');?></small></th>
								<th><small><?php echo __('Question Type');?></small></th>
								<th><small><?php echo __('Difficulty Level');?></small></th>
								<th><small><?php echo __('Total Question');?></small></th>
								<?php if($examCount){?><th><small><?php echo __('Questions Attempt Count');?></small></th><?php }?>
							</tr>                    
							<?php $totalQuestion=0;$totalAttemptQuestion=0;
							foreach($SubjectDetail as $sd):
							if($sd['MaxQuestion']==0)$attempQuestion=$sd['QuesNo'];else$attempQuestion=$sd['MaxQuestion'];
							$totalQuestion=$totalQuestion+$sd['QuesNo'];
							$totalAttemptQuestion=$totalAttemptQuestion+$attempQuestion;
							?>
							<tr>
							<td><?php echo $this->ExamApp->h($sd['Subject']);?></td>
							<td><?php echo $this->ExamApp->h($sd['Type']);?></td>
							<td><?php echo $this->ExamApp->h($sd['Level']);?></td>
							<td><?php echo $this->ExamApp->h($sd['QuesNo']);?></td>
							<?php if($examCount){?><td><?php echo$attempQuestion;?></td><?php }?>
							</tr>
							<?php endforeach;?>
							<?php unset($sd);?>
							<tr><td>&nbsp;</td><td>&nbsp;</td><td><strong><?php echo __('Total');?></strong></td>
							<td><strong><?php echo$totalQuestion;?></strong></td>
							<?php if($examCount){?><td><strong><?php echo$totalAttemptQuestion;?></strong></td><?php }?></tr>
                        </table>
					</div>
					<div class="chart">	
										<div id="piewrappersub"></div>
										<script type="text/javascript">
										//<![CDATA[
										$(document).ready(function() {
										    // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
										    Highcharts.setOptions(
											{"global":{"useUTC":true}}
										    );
										    // HIGHROLLER - HIGHCHARTS 'Subject Wise Question Count' pie chart
										
										    var piewrappersub = new Highcharts.Chart(
											{"chart":{"renderTo":"piewrappersub","type":"pie"},"title":{"text":"<?php echo$prepTitle;?>","align":"center"},"series":<?php echo$prepSeries;?>,"plotOptions":{"pie":{"dataLabels":{"style":{},"enabled":true,"format":"{point.name}:<b>{point.y}<\/b>"},"formatter":{"formatter":""},"showInLegend":true}},"xAxis":{},"credits":{"enabled":false}}
										    );
										    
										    //for column drilldown
										    function setChart(name, categories, data, color) {
											piewrappersub.xAxis[0].setCategories(categories);
											piewrappersub.series[0].remove();
											piewrappersub.addSeries({
											    name: name,
											    data: data,
											    color: color || 'white'
											});
										    }   
										});
										//]]>
										</script>
										
									</div>
					<?php }?>
					
                    </div>
                </div>
            </div>