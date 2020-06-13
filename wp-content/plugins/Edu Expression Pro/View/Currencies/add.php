<div class="col-md-12">
        <div class="panel panel-default">
        <div class="panel-heading"><?php echo __('Add Currency');?></div>
        <div class="panel-body">
                <div class="panel-body">
                <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Currency Name');?></small></label>
                        <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" maxlength="100" placeholder="<?php echo __('Currency Name');?>" value="<?php echo $this->ExamApp->h($_POST['name']);?>" required="required" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Short Name');?></small></label>
                        <div class="col-sm-9">
                        <input type="text" name="short" class="form-control" maxlength="3" placeholder="<?php echo __('Short Name');?>" value="<?php echo $this->ExamApp->h($_POST['short']);?>" required="required" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Upload Currency (Less or equal to 50*50)');?></small></label>
                        <div class="col-sm-9">
                        <input type="file" name="photo" value="" required="required">
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">
                           <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
                           <a href="<?php echo$this->url;?>" class="btn btn-danger"><span class="fa fa-close"></span>&nbsp;<?php echo __('Close');?></a>
                   </div>
            </div>
        </form>
        </div>
    </div>
</div>