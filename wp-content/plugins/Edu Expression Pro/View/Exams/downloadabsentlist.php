<table class="table table-bordered">
   <tr>
      <th><?php echo __('S.No.');?></th>
      <th><?php echo __('Student Name');?></th>
      <th><?php echo __('Email');?></th>
      <th><?php echo __('Enrolment');?></th>
      <th><?php echo __('Phone');?></th>
   </tr>
   <?php foreach($examResult as $rank=>$examValue):?>
   <tr>
      <td><?php echo++$rank;?></td>
      <td><?php echo $this->ExamApp->h($examValue['name']);?></td>
      <td><?php echo$examValue['email'];?></td>      
      <td><?php echo get_user_meta($examValue['ID'],'examapp_enroll',true);?></td>
      <td><?php echo get_user_meta($examValue['ID'],'examapp_phone',true);?></td>
   </tr>
   <?php endforeach;unset($examValue);?>
</table>