<table class="table table-bordered">
   <tr class="success">
      <th><?php echo __('S.No.');?></th>
      <th><?php echo __('Student Name');?></th>
      <th><?php echo __('Email');?></th>
      <th><?php echo __('Test');?></th>
      <th><?php echo __('Max Marks');?></th>
      <th><?php echo __('Marks Scored');?></th>
      <th><?php echo __('Percent');?></th>
      <th><?php echo __('Result Status');?></th>
   </tr>
   <?php foreach($examResult as $rank=>$examValue):
   $id=$examValue['id'];?>
   <tr>
      <td><?php echo++$rank;?></td>
      <td><?php echo $this->ExamApp->h($examValue['Student.name']);?></td>
      <td><?php echo$examValue['email'];?></td>
      <td><?php echo$examValue['Exam.name'];?></td>
      <td><?php echo$examValue['total_marks'];?></td>
      <td><?php echo$examValue['obtained_marks'];?></td>
      <td><?php echo$examValue['percent'].'%';?></td>
      <td><span class="label label-<?php if($examValue['result']=="Pass")echo"success";else echo"danger";?>"><?php if($examValue['result']=="Pass"){echo __('PASSED');}else{echo __('FAILED');}?></span></td>
   </tr>
   <?php endforeach;unset($examValue);?>
</table>