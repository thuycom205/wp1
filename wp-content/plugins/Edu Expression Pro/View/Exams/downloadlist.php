<table class="table table-bordered">
   <tr>
      <th><?php echo __('S.No.');?></th>
      <th><?php echo __('Student Name');?></th>
      <th><?php echo __('Email');?></th>
      <th><?php echo __('Enrolment');?></th>
      <th><?php echo __('Phone');?></th>
      <th><?php echo __('Percentage');?></th>
      <th><?php echo __('Result Status');?></th>
   </tr>
   <?php foreach($examResult as $rank=>$examValue):?>
   <tr>
      <td><?php echo++$rank;?></td>
      <td><?php echo $this->ExamApp->h($examValue['name']);?></td>
      <td><?php echo$examValue['email'];?></td>      
      <td><?php echo get_user_meta($examValue['ID'],'examapp_enroll',true);?></td>
      <td><?php echo get_user_meta($examValue['ID'],'examapp_phone',true);?></td>
      <td><?php echo$examValue['percent'].'%';?></td>
      <td><span class="label label-<?php if($examValue['result']=="Pass")echo"success";else echo"danger";?>"><?php if($examValue['result']=="Pass"){echo __('PASSED');}else{echo __('FAILED');}?></span></td>
   </tr>
   <?php endforeach;unset($examValue);?>
</table>