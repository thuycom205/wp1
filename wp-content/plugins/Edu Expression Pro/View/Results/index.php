<?php if($_GET['msg']=='invalid'){echo $this->ExamApp->showMessage("Invalid Post !",'danger');}?>
		<?php if(!isset($studentId)){?>
			<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Exam Wise');?></h1></div></div>
			<div class="panel">
				<div class="panel-body">
					<form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">
					<div class="form-group">
					<label for="subject_name" class="col-sm-2 control-label"><small><?php echo __('Group');?></small></label>
						<div class="col-sm-6">
						<select name="group_name[]" class="form-control multiselectgrp" multiple="true">
                                                     <?php echo$groupName;?>
                                                 </select>
						</div>
					</div>
					<div class="form-group">
					<label for="subject_name" class="col-sm-2 control-label"><small><?php echo __('Name of Exam');?></small></label>
						<div class="col-sm-4">
						<select name="exam_id" class="form-control" >
                                                  <option value=""><?php echo __('Please Select Exam');?></option>
                                                  <?php echo$examName;?>
						 </select>
						</div>
					<label for="subject_name" class="col-sm-2 control-label"><small><?php echo __('Status of Result');?></small></label>
						<div class="col-sm-4">
						<select name="status" class="form-control" >
                                                  <option value=""><?php echo __('Please Select Result Status');?></option>
                                                  <option value="Pass" <?php if($_POST['status']=='Pass'){echo 'Selected';}?>><?php echo __('PASSED');?></option>
						  <option value="Fail" <?php if($_POST['status']=='Fail'){echo 'Selected';}?>><?php echo __('FAILED');?></option>
						 </select>
						</div>
					</div>
					<div class="form-group text-left">
					    <div class="col-sm-offset-2 col-sm-7">
					    <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-search"></span>&nbsp;<?php echo __('Search');?></button>
			         	    </div>
					</div>
					<input type="hidden" name="examWise" value="">
					</form>
					<?php if($isExam){$status=$_POST['status'];$examId=$_POST['exam_id'];$studentGroup=null;
					if(is_array($_POST['group_name'])){$studentGroup=implode(",",$_POST['group_name']);}?>
					<div class="text-left"><a href="<?php echo $this->ajaxUrl;?>&info=downloadresult&examId=<?php echo$examId;?>&stuentGroup=<?php echo$studentGroup;?>&status=<?php echo$status;?>" class="btn btn-info"><span class="fa fa-download"></span>&nbsp;<?php echo __('Download Result');?></a></div>
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<tr class="default">
							    <th><?php echo __('Rank');?></th>
							    <th><?php echo __('Student Name');?></th>
							    <th><?php echo __('Email');?></th>
							    <th><?php echo __('Test');?></th>
							    <th><?php echo __('Max Marks');?></th>
							    <th><?php echo __('Marks Scored');?></th>
							    <th><?php echo __('Percent');?></th>
							    <th><?php echo __('Result');?></th>
							    <th><?php echo __('Action');?></th>
							</tr>
							<?php  foreach($examDetails as $rank=>$examValue):
							
							$id=$examValue['id'];?>
							<tr>
								<td><?php echo++$rank;?></td>
								<td>
								<a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=view&id=<?php echo $id;?>');"> <?php echo $this->ExamApp->h($examValue['display_name']);?></a>
								</td>
								<td><?php echo$examValue['user_email'];?></td>
								<td><?php echo$examValue['name'];?></td>
								<td><?php echo$examValue['total_marks'];?></td>
								<td><?php echo$examValue['obtained_marks'];?></td>
								<td><?php echo$examValue['percent'].'%';?></td>
								<td><span class="label label-<?php if($examValue['result']=="Pass")echo"success";else echo"danger";?>"><?php if($examValue['result']=="Pass"){echo __('PASSED');}else{ echo __('FAILED');}?></span></td>
								<td>
								<a  href="<?php echo $this->url;?>&info=result&id=<?php echo $examValue['id'];?>" data-toggle='tooltip' title="<?php echo __('View Result');?>" ><span class="fa fa-arrows-alt"></span>&nbsp;</a>
								<a  href="<?php echo $this->ajaxUrl;?>&info=stdexamresult&id=<?php echo$id;?>" data-toggle='tooltip' title="<?php echo __('Download / Print Result');?>" target='_blank' ><span class="a fa-print"></span>&nbsp;</a>
								</td>
							</tr>
							<?php endforeach;unset($examValue);?>
						</table>
						</div></form>
					<?php }?>
					</div>	
			</div>
				<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Student Wise');?></h1></div></div>
			<div class="panel">
				<div class="panel-body">
				<form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">
					<div class="form-group">
					<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('Name');?></small></label>
						<div class="col-sm-3">
						<input type="text" name="name" value="<?php echo$_POST['name']; ?>" class="form-control input-sm" placeholder="<?php echo __('Student Name');?>"  />
						</div>
					</div>
					<div class="form-group">
					<label for="subject_name" class="col-sm-3 control-label"><small><?php echo __('Group');?></small></label>
						<div class="col-sm-3">
						<select name="group_name[]" class="form-control multiselectgrp" multiple="true">
                                                     <?php echo$groupName;?>
						</select>
						</div>
					</div>
					<div class="form-group text-left">
						<div class="col-sm-offset-3 col-sm-7">
						<button type="submit" class="btn btn-success" name="submit"><span class="fa fa-search"></span>&nbsp;<?php echo __('Search');?></button>
			         	       </div>
					</div>
					<input type="hidden" name="studentWise" value="">
					</form>
				</div>	
					<?php if($isStudent){?>
						<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<tr class="default">
								<th><?php echo __('Student Name');?></th>
								<th><?php echo __('Email');?></th>
								<th><?php echo __('Enrolment Number');?></th>
								<th><?php echo __('Group');?></th>						
							</tr>
							<?php foreach($studentDetails as $studentValue):?>
							<tr>
								<td><a href="<?php echo $this->url;?>&info=index&id=<?php echo$studentValue['ID'];?>"><?php echo $this->ExamApp->h($studentValue['display_name']);?></a></td>
								<td><?php echo $this->ExamApp->h($studentValue['user_email']);?></td>
								<td><?php echo $this->ExamApp->h(get_user_meta($studentValue['ID'],'examapp_enroll',true));?></td>
								<td><?php echo $this->ExamApp->showGroupName("student_groups","groups","student_id",$studentValue['ID']);?></td>
							</tr>
							<?php endforeach;unset($studentValue);?>
						</table>
						</div>
					<?php }}?>
					<?php if(isset($examDetails) && isset($studentId)){?><br/>
					<a href="#" onclick="javascript:history.back(-1);" class='btn btn-info'><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back');?></a>
					<a href="<?php echo $this->ajaxUrl;?>&info=dwstdresult&studentId=<?php echo$studentId;?>" class="btn btn-info"><span class="fa fa-download"></span>&nbsp;<?php echo __('Download Result');?></a>	
						<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<tr class="default">
								<th>#</th>
								<th><?php echo __('Student Name');?></th>
								<th><?php echo __('Email');?></th>
								<th><?php echo __('Test');?></th>
								<th><?php echo __('Max Marks');?></th>
								<th><?php echo __('Marks Scored');?></th>
								<th><?php echo __('Percent');?></th>
								<th><?php echo __('Result Status');?></th>
								<th><?php echo __('Action');?></th>
							</tr>
							<?php foreach($examDetails as $rank=>$examValue):
							$id=$examValue['id'];?>
							<tr>
								<td><?php echo++$rank;?></td>
								<td>
								<a href="javascript:void(0);" onclick="show_modal('<?php echo $this->ajaxUrl;?>&info=view&id=<?php echo $id;?>');"><?php echo $this->ExamApp->h($examValue['display_name']);?></a>
								</td>
								<td><?php echo$examValue['user_email'];?></td>
								<td><?php echo$examValue['name'];?></td>
								<td><?php echo$examValue['total_marks'];?></td>
								<td><?php echo$examValue['obtained_marks'];?></td>
								<td><?php echo$examValue['percent'].'%';?></td>
								<td><span class="label label-<?php if($examValue['result']=="Pass")echo"success";else echo"danger";?>"><?php if($examValue['result']=="Pass"){echo __('PASSED');}else{ echo __('FAILED');}?></span></td>
								<td>
								<a  href="<?php echo $this->url;?>&info=result&id=<?php echo $examValue['id'];?>" data-toggle='tooltip' title="<?php echo __('View Result');?>" ><span class="fa fa-arrows-alt"></span>&nbsp;</a>
								<a  href="<?php echo $this->ajaxUrl;?>&info=stdexamresult&id=<?php echo $examValue['id'];?>" data-toggle='tooltip' title="<?php echo __('Download / Print Result');?>" target='_blank' ><span class="a fa-print"></span>&nbsp;</a>
								</td>
								</tr>
							<?php endforeach;unset($examValue);?>
						</table>
						</div>
					<?php }?>
			</div>
			<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-content"></div>
			</div>
			
			