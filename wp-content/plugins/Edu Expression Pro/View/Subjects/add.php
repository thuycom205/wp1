<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Add New').'&nbsp;'.__('Subjects');?></div>
    <div class="panel-body">
        <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
        <div class="form-group">
            <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Group');?></small></label>
            <div class="col-sm-9">
                <select name="group_name[]" class="form-control multiselectgrp" multiple="true">
                <?php echo$groupName;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Name');?></small></label>
            <div class="col-sm-9">
                <input type="text" name="subject_name" class="form-control" placeholder="<?php echo __('Name');?>" value="<?php echo $this->ExamApp->h($_POST['subject_name']);?>" required="required" />
            </div>
        </div>
        <div class="form-group text-left">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
            </div>
        </div>
        </form>
    </div>
</div>