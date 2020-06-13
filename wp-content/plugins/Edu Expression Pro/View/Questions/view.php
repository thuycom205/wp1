<?php if($mathEditor){?><script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
	<script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php
$answer_select=array();
                if($resultArr['qtype_id']==1)
                {
                    $answer_select=$resultArr['answer'];
                }
?>

                    <div class="table-responsive">
			<table class="table table-bordered">
			    <tr>
				<td><strong><small class="text-primary"><?php echo __('Question Type');?></small></strong></td>
				<td><?php echo $this->ExamApp->h($resultArr['qtypename']);?></td>
				<td><strong><small class="text-primary"><?php echo __('Subject');?></small></strong></td>
				<td colspan="3"><?php echo $this->ExamApp->h($resultArr['subjectname']);?></td>				
			    </tr>
			    <tr>
				<td><strong><small class="text-primary"><?php echo __('Group');?></small></strong></td>
				<td colspan="5"><?php  echo "(".$this->ExamApp->showGroupName("emp_question_groups","emp_groups","question_id",$id).")";?></td>
			    </tr>
			    <tr>
				<td colspan="6">                   
                    <div id="Question">
                    <strong><?php echo __('Question');?> : </strong>
                    <?php echo str_replace("<script","",$resultArr['question']);?><hr/>
                    </div>
		    <div id="myquestiontab">
                                                                <?php if(strlen($resultArr['option1'])>0){?>
								<div class="tab-pane" id="Answer1">
									<strong><?php echo __('Option')." ".__('1');?> : </strong><?php echo str_replace("<script","",$resultArr['option1']);?><hr/>
								</div>
                                                                <?php }?>
                                                                <?php if(strlen($resultArr['option2'])>0){?>
								<div class="tab-pane" id="Answer2">
									<strong><?php echo __('Option')." ".__('2');?> : </strong><?php echo str_replace("<script","",$resultArr['option2']);?><hr/>
								</div>
								<?php }?>
                                                                <?php if(strlen($resultArr['option3'])>0){?>
                                                                <div class="tab-pane" id="Answer3">
									<strong><?php echo __('Option')." ".__('3');?> : </strong><?php echo str_replace("<script","",$resultArr['option3']);?><hr/>
								</div>
                                                                <?php }?>
                                                                <?php if(strlen($resultArr['option4'])>0){?>
								<div class="tab-pane" id="Answer4">
                                                                	<strong><?php echo __('Option')." ".__('4');?>  : </strong><?php echo str_replace("<script","",$resultArr['option4']);?><hr/>
								</div>
                                                                <?php }?>
								<?php if(strlen($resultArr['option5'])>0){?>
								<div class="tab-pane" id="Answer5">
									<strong><?php echo __('Option')." ".__('5');?>  : </strong><?php echo str_replace("<script","",$resultArr['option5']);?><hr/>
								</div>
								<?php } if(strlen($resultArr['option6'])>0){?>
								<div class="tab-pane" id="Answer6">
									<strong><?php echo __('Option')." ".__('6');?>  : </strong><?php echo str_replace("<script","",$resultArr['option6']);?><hr/>
								</div>
								<?php }?>
                                                                <?php if($answer_select){?>
                                                                <div class="tab-pane" id="CorrectAnswer">
                                                                    <p><br/><strong><?php echo __('Correct Answer');?> : <?php echo __("Option")." ".$answer_select;?> </strong></p>
								</div>
                                                                <?php }?>
								</div>
                                                         <div id="tf">
                                                         <?php if(strlen($resultArr['true_false'])>0){?>
                                                         <p><br/><strong><?php echo __('Answer');?> : </strong><?php echo ucfirst(strtolower($resultArr['true_false']));?></p>
                                                         <?php }?>
                                                         </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                         </div>
                                                  <div class="form-group" id="ftb">
                                                   <?php if(strlen($resultArr['fill_blank'])>0){?>
                                                                    <p><br/><strong><?php echo __('Answer');?> : </strong><?php echo $resultArr['fill_blank'];?></p>
						   <?php }?>
                                                  </div>
				</td>
			    </tr>
			    <?php if(strlen($resultArr['explanation'])>0){?>
			    <tr>
				<td><strong><small class="text-primary"><?php echo __('Explanation');?></small></strong></td>
				<td colspan="5"><?php echo str_replace("<script","",$resultArr['explanation']);?></td>				
			    </tr>
			    <?php }?>
			    <?php if(strlen($resultArr['hint'])>0){?>
			    <tr>
				<td><strong><small class="text-primary"><?php echo __('Hint');?></small></strong></td>
				<td colspan="5"><?php echo$this->ExamApp->h($resultArr['hint']);?></td>				
			    </tr>
			    <?php }?>
			    <tr>
				<td><strong><small class="text-primary"><?php echo __('Marks');?></small></strong></td>
				<td><?php echo $this->ExamApp->h($resultArr['marks']);?></td>
				<td><strong><small class="text-primary"><?php echo __('Negative Marks');?></small></strong></td>
				<td><?php echo $this->ExamApp->h($resultArr['negative_marks']);?></td>
				<td><strong><small class="text-primary"><?php echo __('Difficulty Level');?></small></strong></td>
				<td><?php echo $this->ExamApp->h($resultArr['diffname']);?></td>
			    </tr>			   
			</table>
		    </div>