<?php if($mathEditor){?><script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
<script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
        <div class="col-md-9 ">
                <div class="panel panel-default">
      <a href="javascript:void(0);" class="btn btn-info" onclick="javascript:history.back(-1);"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back');?></a>
      
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Assesment Report for');?> <?php echo $this->ExamApp->h($examDetails['name']);?></span></h4>
		</div>
	    </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
					<tr>
						<td><strong class="text-danger"><?php echo __('Email ID');?></strong></td>
						<td><?php echo $this->ExamApp->h($examDetails['user_email']);?></td>
					</tr>
					<tr>
						<td><strong class="text-danger"><?php echo __('Taken Date');?></strong></td>
						<td><?php echo $this->ExamApp->dateTimeFormat($examDetails['start_time']);?></td>
					</tr>
					<tr>
						<td><strong class="text-danger"><?php echo __('Percentage');?></strong></td>
						<td><?php echo$examDetails['percent']." %";?></td>
					</tr>
					<tr>
						<td><strong class="text-danger"><?php echo __('Time Taken');?></strong></td>
						<td><?php echo $this->ExamApp->secondsToWords($examDetails['time_taken']);?></td>
					</tr>
					<tr>
						<td><strong class="text-danger"><?php echo __('Total Time');?></strong></td>
						<td><?php echo $this->ExamApp->secondsToWords($examDetails['duration']*60);?></td>
					</tr> 
					<tr>
						<td><strong class="text-danger"><?php echo __('Nos. of Browser tolrance attempt');?></strong></td>
						<td><?php echo $examWarning;?></td>
					</tr>
					<tr>
						<td><strong class="text-danger"><?php echo __('Result');?></strong></td>
						<td><span class="label label-<?php if($examDetails['result']=="Pass")echo"success";else echo"danger";?>"><?php if($examDetails['result']=="Pass"){echo __('PASSED');}else{ echo __('FAILED');}?></span></td>
					</tr>                                
			</table>
			</div>
                </div>
            </div>
	      <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Total Score');?></span></h4>
		</div>
	    </div>
	    <div class="panel-body" style="min-height: 260px;">
		<div class="text-center"><h2><?php echo $examDetails['obtained_marks'];?> /</h2>
		<h2><?php echo$examDetails['total_marks'];?></h2></div>
	    </div>
		</div>
	      </div>
	      <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Student Marks Distribution');?></span></h4>
		</div>
	    </div>
	    <div class="panel-body">
		<div class="chart">
		  <div id="mywrapperor"></div>
		  <script type="text/javascript">
		    //<![CDATA[
		    $(document).ready(function() {
			// HIGHROLLER - HIGHCHARTS UTC OPTIONS 
			Highcharts.setOptions(
			    {"global":{"useUTC":true}}
			);
			// HIGHROLLER - HIGHCHARTS '' scatter chart
		    
			var mywrapperor = new Highcharts.Chart(
			    {"chart":{"renderTo":"mywrapperor","type":"scatter","height":200},"title":{"text":null},"series":<?php echo$timeSeries;?>,"legend":{"enabled":false},"tooltip":{"enabled":true,"formatter":function() { return '<b>'+ this.series.name +'<\/b><br\/>Score:'+ this.x +' <?php echo __('Frequency');?>:'+ this.y;}},"xAxis":{},"credits":{"enabled":false}}
			);
			
			//for column drilldown
			function setChart(name, categories, data, color) {
			    mywrapperor.xAxis[0].setCategories(categories);
			    mywrapperor.series[0].remove();
			    mywrapperor.addSeries({
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
	      <?php if($configuration['exam_feedback']){?>
	       <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Feedback');?></span></h4>
		</div>
	    </div>
	    <div class="panel-body" style="min-height: 230px;">
		<?php echo$examDetails['comments'];?>
	    </div>
		</div>
	      </div>
	      <?php }?>
	      
	      <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><?php echo __('Marks Sheet');?></h4>
		</div>
	    </div>		
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th><?php echo __('Section Name');?></th>
				    <th><?php echo __('Section proportion');?></th>
                                    <th><?php echo __('No. of Questions');?></th>
                                    <th><?php echo __('Actual');?></th>
				    <th><?php echo __('Your Percentage');?></th>
				    <th><?php echo __('Time Taken');?></th>
                                </tr>
                                <?php foreach($userMarksheet as $userValue):?>
                                <tr>                                    
                                    <td class="text-danger"><strong><?php echo $this->ExamApp->h($userValue['Subject']['name']);?></strong></td>
                                    <td><?php echo number_format($userValue['Subject']['marks_weightage']);?></td>
                                    <td><?php echo$userValue['Subject']['total_question'];?></td>
				    <td><?php echo (int)$userValue['Subject']['obtained_marks'].'/'.(int)$userValue['Subject']['total_marks'];?></td>
				    <td><?php echo number_format($userValue['Subject']['percent']);?></td>
				    <td><?php  echo$this->ExamApp->secondsToWords($userValue['Subject']['time_taken'],'-');?></td>
                                </tr>
                                <?php endforeach;unset($userValue);?>                               
                            </table>
                        </div>                            
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Section Summary');?></span></h4>
		</div>
	    </div>		
                        <div class="chart">
                            <div id="mywrapperdl"></div>
                            &nbsp;
			    <script type="text/javascript">
			      //<![CDATA[
			      $(document).ready(function() {
				  // HIGHROLLER - HIGHCHARTS UTC OPTIONS 
				  Highcharts.setOptions(
				      {"global":{"useUTC":true}}
				  );
				  // HIGHROLLER - HIGHCHARTS '' column chart
			      
				  var mywrapperdl = new Highcharts.Chart(
				      {"chart":{"renderTo":"mywrapperdl","type":"column","width":500},"title":{"text":null},"series":<?php echo$subjectSeries;?>,"legend":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"style":{}}},"column":{"series":{"dataLabels":{"style":{}}},"column":null,"dataLabels":{"style":{},"enabled":true}}},"xAxis":{"categories":<?php echo $subjectxAxis;?>},"yAxis":{"style":{},"title":{"text":"<?php echo __('Score');?>"}},"credits":{"enabled":false}}
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
	    <div class="col-md-4">
                <div class="panel panel-default">
		<div class="panel-heading">
		<div class="widget">
		    <h4 class="widget-title"><span><?php echo __('Section Summary');?></span></h4>
		</div>
	    </div>
	    <div class="table-responsive">
                  <table class="table">
			<tr>
			<td><div style="margin:5px;height:15px;width:15px;background-color:#0d233a;";>&nbsp;</div></td>
			<td><strong><?php echo __('Maximum Score');?></strong></td>
			</tr>
			<tr>
			<td><div style="margin:5px;height:15px;width:15px;background-color:#39ae39;";>&nbsp;</div></td>
			<td><strong><?php echo __('Developed Skill');?></strong></td>
			</tr>
			<tr>
			<td><div style="margin:5px;height:15px;width:15px;background-color:#f79327;";>&nbsp;</div></td>
			<td><strong><?php echo __('Needs Development');?></strong></td>
			</tr>
			<tr>
			<td><div style="margin:5px;height:15px;width:15px;background-color:#eb1d1d;";>&nbsp;</div></td>
			<td><strong><?php echo __('Lack of Skill');?></strong><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><br/></td>
			</tr>
			
		  </table>
	    </div>                  
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="widget">
                            <h4 class="widget-title"><?php echo __('MCQ  answered');?></h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th><?php echo __('S.N.');?></th>
                                <th><?php echo __('Description');?></th>
                                <th><?php echo __('Marked Answer');?></th>
                                <th><?php echo __('Marks Scored');?></th>
                                <th><?php echo __('Correct Answers');?></th>
                                <th><?php echo __('Max Marks');?></th>
                                <th><?php echo __('Time Taken');?></th>
                            </tr>
                            <?php foreach($post as $k=>$ques): 
                            if($ques['type']=="M")
                            {
                                $correctAnswer="";$userAnswer="";
                                if(strlen($ques['answer'])>1)
                                {
                                    $correctAnswerExp=explode(",",$ques['answer']);
                                    foreach($correctAnswerExp as $option):
                                        $correctAnswer1="option".$option;		
                                        $correctAnswer.=" ".$ques[$correctAnswer1];
                                    endforeach;unset($option);
                                    if(strlen($ques['option_selected'])>1)
                                    {
                                        $userAnswerExp=explode(",",$ques['option_selected']);
                                        foreach($userAnswerExp as $option):
                                            $userAnswer1="option".$option;
                                            $userAnswer.=" ".$ques[$userAnswer1];
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
                            ?>
                            <tr <?php if($ques['ques_status']=='R'){?>class="text-success"<?php }elseif($ques['ques_status']=='W'){?>class="text-danger"<?php }else{?>class="text-info"<?php }?>>
                                <td><strong><?php echo $ques['ques_no'];?></strong></td>
                                <td><?php echo str_replace("<script","",$ques['question']);?></td>
                                <td><?php echo$userAnswer;?></td>
                                <td><?php echo$ques['marks_obtained'];?></td>
                                <td><?php echo$correctAnswer;?></td>
                                <td><?php echo$ques['marks'];?></td>
                                <td><?php echo $this->ExamApp->secondsToWords($ques['time_taken'],__('Not Attempted'));?></td>
                            </tr>
                            <?php endforeach;unset($ques);?>
                        </table>
                    </div>
                </div>
            </div>
            </div>
    
<a href="javascript:void(0);" class="btn btn-info" onclick="javascript:history.back(-1);"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back');?></a>