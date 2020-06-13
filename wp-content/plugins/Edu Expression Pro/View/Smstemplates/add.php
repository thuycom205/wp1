<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Add Sms Template');?></div>
    <div class="panel-body">
    <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
            <div class="form-group">
                <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Name');?>:</small></label>
                <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="<?php echo __('Name');?>" value="<?php echo $this->ExamApp->h($_POST['name']);?>" required="required" />
                </div>
            </div>
            <div class="form-group">
                <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Sms Template');?>:</small></label>
                <div class="col-sm-10">
                <textarea name="description" cols="10" rows="5" class="form-control" placeholder="<?php echo __('Sms Template'); ?>"><?php echo $_POST['description'];?></textarea>
                </div>
            </div>
            <div class="form-group text-left">
                <div class="col-sm-offset-2 col-sm-6">
                    <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
                </div>
            </div>
        </form>
        </div>
    </div>
