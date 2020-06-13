<?php if($mathEditor){?><script>MathJax.Hub.Typeset();</script><?php }?>
	<div class="mrg"><?php echo$paginate;?>
        </div>

        <div>
        
	       <div class="col-md-12">
			<div class="btn-group">
				<a href="<?php echo $this->urlexam;?>&info=index" class="btn btn-info"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back').' '.__('To').' '.__('Exam');?></a>
			</div>
		<?php 
                foreach($Attemptedpapers as $post){?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="widget">
						<h4 class="widget-title"><?php echo __('Attempted Papers of');?> <span><?php echo$post['examName'];?></span></h4>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-bordered">                            
						<tr>
							<td><?php echo __('Student').' '.__('Name');?></td>
							<td><?php echo $post['studentName'];?></td>
							<td><?php echo __('Student').' '.__('Email');?></td>
							<td><?php echo $post['studentEmail'];?></td>
						</tr>
						<tr>
							<td><?php echo __('Total Marks');?></td>
							<td><?php echo$post['total_marks'];?></td>
							<td><?php echo __('Obtained Marks');?></td>
							<td><?php echo$post['obtained_marks'];echo ($post['user_id']==0)? __('Pending'):"";?></td>
						</tr>
                                               <?php
                                               $SQL1 = "SELECT id as resultId FROM ".$this->wpdb->prefix."emp_exam_results WHERE id=".$post['ExamResultId'];
			                              $this->autoInsert->iFetch($SQL1,$examCount1);
			                              $examResultId=$examCount1['resultId'];
			                        ?>
						<tr>
							<td><?php echo __('Result Finalized');?></td>
							<td><?php if($post['user_id']==0){?><a href="<?php echo $this->ajaxUrl;?>&info=finalize&pageId=<?php echo $pageNumber;?>&examResultId=<?php echo $examResultId;?>&id=<?php echo $id;?>" class="btn btn-success"><?php echo __('Finalize It');?></a><?php }
							else{?><span class="label label-<?php if($post['result']=="Pass")echo"success";else echo"danger";?>"><?php if($post['result']=="Pass"){echo __('PASSED');}else{echo __('FAILED');}?></span><?php }?></td>
							<td><?php echo __('Finalized By');?></td>
							<td><?php echo $post['adminName'];?></td>
						</tr>                           
					</table>
				</div>
                                <?php $sqlExamStat="select *,`ExamStat`.`answer` As `ExamStat.answer`,`ExamStat`.`id` As `ExamStat.id`,`ExamStat`.`user_id` as `ExamStat.user_id`,Qtype.type As Qtype from ".$this->wpdb->prefix."emp_exam_stats As ExamStat left JOIN `".$this->wpdb->prefix."emp_questions` AS `Question` ON(`Question`.`id`=`ExamStat`.`question_id`) left JOIN `".$this->wpdb->prefix."emp_qtypes` AS `Qtype` ON(`Qtype`.`id`=`Question`.`qtype_id`)  where exam_result_id=".$post['ExamResultId']."  ORDER BY `ExamStat`.`ques_no` ASC";
                                                       $this->autoInsert->iWhileFetch($sqlExamStat,$ExamStat);?>
						
				<div class="panel-body">
					<div class="col-md-13">                    
						<div class="panel-group" id="accordion">
                                                	<?php foreach($ExamStat as $k=>$ques){?>
								<div class="panel panel-default">
									<div class="panel-heading">
									 <a data-toggle="collapse" href="#collapse<?php echo$ques['ques_no'];?>">
										 <?php if($ques['Qtype']=="M"){?><span class="<?php echo($ques['option_selected'] == $ques['correct_answer'] ? "text-success" : "text-danger");?>"><?php }?>
										  <?php if($ques['Qtype']=="T"){?><span class="<?php echo($ques['true_false'] == $ques['correct_answer'] ? "text-success" : "text-danger");?>"><?php }?>
										  <?php if($ques['Qtype']=="F"){?><span class="<?php echo($ques['fill_blank'] == $ques['correct_answer'] ? "text-success" : "text-danger");?>"><?php }?>
										  <?php if($ques['Qtype']=="S"){?><span class="text-info"><?php  }?>
										  <strong><?php echo __('Question No');?>.<?php echo $ques['ques_no'];?>&nbsp;(<?php echo$ques['name'];?>)</strong></span>
										</a>
									</div>
									<div id="collapse<?php echo$ques['ques_no'];?>" class="collapse<?php echo($k==0)?"in":"";?>">
										<div class="table-responsive">                    
											<table class="table table-bordered">
												
												<tr>
													<td colspan="4"><?php echo str_replace("<script","",$ques['question']);?></td>                                
												</tr>
												<?php if($ques['Qtype']=="M"){?>
												<?php if(strlen($ques['option1'])>0){?>
												<tr class="text-left">
													<td><strong class="text-warning"><?php echo __('Option1');?></strong></td>
													<td colspan="3"><?php echo str_replace("<script","",$ques['option1']);?></td>
												</tr>
												<?php }?>
												<?php if(strlen($ques['option2'])>0){?>
												<tr class="text-left">
												  <td><strong class="text-warning"><?php echo __('Option2');?> </strong></td>
												  <td colspan="3"><?php echo str_replace("<script","",$ques['option2']);?></td>
												</tr>
												<?php }?>
												<?php if(strlen($ques['option3'])>0){?>												
												<tr class="text-left">
												  <td><strong class="text-warning"><?php echo __('Option3');?></strong></td>
												  <td colspan="3"><?php echo str_replace("<script","",$ques['option3']);?></td>												
												</tr>
												<?php }?>
												<?php if(strlen($ques['option4'])>0){?>
												<tr class="text-left">
												  <td><strong class="text-warning"><?php echo __('Option4');?></strong></td>
												  <td colspan="3"><?php echo str_replace("<script","",$ques['option4']);?></td>
												</tr>
												<?php }?>
												<?php if(strlen($ques['option5'])>0){?>
												<tr class="text-left">
												  <td><strong class="text-warning"><?php echo __('Option5');?></strong></td>
												  <td colspan="3"><?php echo str_replace("<script","",$ques['option5']);?></td>
												</tr>
												<?php }?>
												<?php if(strlen($ques['option6'])>0){?>
												<tr class="text-left">
												  <td><strong class="text-warning"><?php echo __('Option6');?></strong></td>
												  <td colspan="3"><?php echo str_replace("<script","",$ques['option6']);?></td>
												</tr>
												<?php }}
												if($ques['Qtype']=="M")
												{
													$correctAnswer="";$userAnswer="";
													if(strlen($ques['answer'])>1)
													{
													    $correctAnswerExp=explode(",",$ques['answer']);
													    foreach($correctAnswerExp as $option){
														$correctAnswer1="option".$option;		
														$correctAnswer.=" ".$ques[$correctAnswer1];
													}unset($option);
													    if(strlen($ques['option_selected'])>1)
													    {
														$userAnswerExp=explode(",",$ques['option_selected']);
														foreach($userAnswerExp as $option)
														    $userAnswer1="option".$option;
														    $userAnswer.=" ".$ques[$userAnswer1];
													    }unset($option);
													    
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
												if($ques['Qtype']=="T")
												{
												    $userAnswer=$ques['true_false'];
												    $correctAnswer=$ques['true_false'];
												}
												if($ques['Qtype']=="F")
												{
												    $userAnswer=$ques['fill_blank'];
												    $correctAnswer=$ques['fill_blank'];
												}
												if($ques['Qtype']=="S")
												{
												    $userAnswer=$ques['ExamStat.answer'];
												    $correctAnswer="";
												}?>
												<tr>
													<td colspan="2"><?php echo __('Marked Answer');?>: <?php echo $userAnswer;?></td>
													<td colspan="2"><span class="text-success"><?php echo __('Correct Answer');?>: <?php echo $correctAnswer;?></span></td>
												</tr>
												<tr>
													<td><?php echo __('Time Taken');?>: <?php  echo $this->ExamApp->secondsToWords($ques[0]['time_taken'],"Not Attempted");?></td>
													<td><?php echo __('Marks');?>: <?php echo$ques['marks'];?></td>
													<td><?php echo __('Marks Obtained');?>: <?php if($ques['Qtype']=="S" && $post['user_id']==0){$id=$ques['exam_id'];$statId=$ques['ExamStat.id'];?>
													    <form  action="<?php echo$this->ajaxUrl;?>&info=marksupdate" method="post">
                                                                                                                <input type="text" name="marks_obtained" value="<?php echo$ques['marks_obtained'];?>" size="4" maxlength="5" autocomplete="off">
                                                                                                                <input type="hidden" name="page" value="<?php echo$pageNumber;?>">
                                                                                                                <input type="hidden" name="statId" value="<?php echo$statId;?>">
                                                                                                                <input type="hidden" name="id" value="<?php echo$id;?>">
                                                                                                                <input type="submit" name="update" value="<?php echo __('Update');?>" class="btn btn-primary">
                                                                                                                </form>
                                                                                                                &nbsp;<?php                                     
													}else{echo$ques['marks_obtained'];}?></td>
													<?php $userName="";
													if($ques['Qtype']=="S")
													{
														foreach($UserArr as $User){
														if($User['ID']==$ques['ExamStat.user_id'])
														{
															$userName=$User['display_name'];
															break;
														}
														}unset($User);
													}?>                                
													<td><?php echo __('Checked by');?>: <?php echo($ques['ExamStat.user_id']=='0')?__('System'):$userName;?></td>
												</tr>
												<tr>
													<td colspan="4"><hr/></td>
												</tr>
											</table>
										</div>
										
									</div>	
								</div>	
							<?php }unset($ques);?> 
                                                                </div>
					</div>
				</div>
			</div>
			<?php } unset($post);?>
		</div>
	</div>
	