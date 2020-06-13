<div class="container">
    <div class="row"><?php $user_info = get_userdata($id);?>
        <div class="col-md-12">    
            <div class="panel panel-default mrg">
                <div class="panel-heading"><?php echo __('View Student Information');?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
                <div class="panel-body">
                    
                    <div class="table-responsive">
			<table class="table table-bordered">
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('User Name');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->h($post['userName']);?></small>
								       </td>
								       <td class="text-primary"><strong><small><?php echo __('Registered Email');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->h($post['name']);?></small>
								       </td>
								       	</tr>
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('First Name');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->h($user_info->first_name);?></small>
								       </td>
								       
								       <td class="text-primary"><strong><small><?php echo __('Last Name');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->h($user_info->last_name);?></small>
								       </td>
								       </tr>
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('Display Name');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->h($post['name']);?></small>
								       </td>
								       
								       <td class="text-primary"><strong><small><?php echo __('Groups');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->showGroupName("emp_student_groups","emp_groups","student_id",$id);?></small>
								       </td>
								       </tr>
									<tr>
								       <td class="text-primary"><strong><small><?php echo __('Phone Number');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $user_info->examapp_phone;?></small>
								       </td>
								       <td class="text-primary"><strong><small><?php echo __('Alternate Number');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $user_info->examapp_alternate_number;?></small>
								       </td>
								       </tr>
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('Address');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $user_info->examapp_address;?></small>
								       </td>
								       <td class="text-primary"><strong><small><?php echo __('Enrollment Number');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $user_info->examapp_enroll;?></small>
								       </td>
								       </tr>
									<tr>
								       <td class="text-primary"><strong><small><?php echo __('Register Date');?> :</small></strong>
			                                                <small class="text-primary"><?php echo $this->ExamApp->dateTimeFormat($post['date']);?></small>
								       </td>
								       <td class="text-primary"><strong><small>Status :&nbsp;&nbsp;<?php if($post['status']=="Active"){?><a href="<?php echo$this->url;?>&info=studentstatus&value=Suspend&id=<?php echo$id;?>" class='btn btn-success btn-xs'><?php echo __($post['status']);?></a> <?php }
									elseif($post['status']=="Pending"){?><a href="<?php echo$this->url;?>&info=studentstatus&value=Active&id=<?php echo$id;?>" class='btn btn-warning btn-xs'><?php echo __($post['status']);?></a><?php }
									elseif($post['status']=="Suspend"){?><a href="<?php echo$this->url;?>&info=studentstatus&value=Active&id=<?php echo$id;?>" class='btn btn-danger btn-xs'><?php echo __($post['status']);?></a> <?php }
									else{?><span class="label label-default"><?php echo __($post['status']);?></span> <?php }?></small></strong>
								       </td>
								       </tr>
								       <?php if($this->configuration['student_expiry']){?>
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('Expiry Days');?> :</small></strong>
			                                                <small class="text-primary"><?php if($user_info->examapp_expiry_days){ echo $this->ExamApp->h($user_info->examapp_expiry_days).' '.'Days';}elseif($user_info->examapp_expiry_days=='0'){echo __('Unlimited');}?></small>
								       </td>
								       <td class="text-primary"><?php if($user_info->examapp_expiry_days!=0){?><strong><small><?php echo __('Renewal Date');?> :</small></strong>
			                                                <small class="text-primary"><?php if($user_info->examapp_renewal_date){echo $this->ExamApp->dateFormat($user_info->examapp_renewal_date);}?></small><?php }?>
								       </td>
								       </tr>
								       <?php }?>
								       <tr>
								       <td class="text-primary"><strong><small><?php echo __('Last Login');?> :</small></strong>
			                                                <small class="text-primary"><?php if($user_info->examapp_last_login){ echo $this->ExamApp->dateTimeFormat($user_info->examapp_last_login);}?></small>
								       </td>
								       <td class="text-primary"><strong><small></small></strong>
			                                                <small class="text-primary"></small>
								       </td>
								       </tr>
								       
								</table>
						</div>
                    
                    
				</div>
		</div>
        </div>
    </div>
</div>