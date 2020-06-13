<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Leader Boards');?></h1></div></div>
<div class="panel">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
		    <th><?php echo __('Rank');?></th>
		    <th><?php echo __('Name');?></th>
		    <th><?php echo __('Average Percentage(%)');?></th>
		    <th><?php echo __('Exam Given');?></th>
		</tr>
                </thead>
                <tbody>
                    <?php foreach($scoreboard as $k=>$post):$k++?>
		<tr>
		    <td><?php echo$k;?></td>
		    <td><?php echo $this->ExamApp->h($post['name']);?></td>
		    <td><?php echo$post['points'];?>%</td>
		    <td><?php echo$post['exam_given'];?></td>
		</tr>
		<?php endforeach;unset($post);?>
                </tbody>
            </table>
        </div>
       
    </div>
</div>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content"></div>
</div>