<?php $editorType="absolute";$tinymce=new Tinymce();$configLanguage=get_locale();$dirType='ltr';?>
<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Add Help Contents');?></div>
    <div class="panel-body">
    <form class="form-horizontal validate" action="<?php echo$this->url;?>&info=add" method="post">
            <div class="form-group">
                <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Title');?>:</small></label>
                <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="<?php echo __('Title');?>" value="<?php echo $this->ExamApp->h($_POST['name']);?>" required="required" />
                </div>
            </div>
            <div class="form-group">
                <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Discription');?>:</small></label>
                <div class="col-sm-10">
                <?php echo$tinymce->input('description',$_POST['description'],array('placeholder'=>__('Discription'),'class'=>'form-control','cols'=>'30','rows'=>'6'),array('language'=>$configLanguage,'directionality'=>$dirType),$editorType);?>
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
