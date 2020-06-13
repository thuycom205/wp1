<div class="panel">
    <div class="panel-heading"><div class="title-env"> <h3 class="title"><?php echo __('Import/Export Qusetions');?></h3></div>
        <div class="btn-group">
	   <a href="<?php echo $this->question;?>&info=index" class="btn btn-info"><span class="fa fa-arrow-left"></span>&nbsp;<?php echo __('Back To Questions');?></a>
           <a href="<?php echo $this->ajaxUrl;?>&info=export" class="btn btn-success"><span class="fa fa-upload"></span>&nbsp;<?php echo __('Export Questions');?></a>
	</div>
    </div>
        <div class="panel-body">
	        <form class="form-horizontal" name="import" enctype="multipart/form-data" action="<?php echo$this->url;?>&info=import" method="post">
                    <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Select File');?></label>
                        <div class="col-sm-3">
			    <input type="file" name="file" required="required">
                        </div>
			<div class="col-sm-6"><a href="<?php echo plugin_dir_url(__FILE__);?>../../tmp/download/sample-question.xls" target="_blank"><span class="text-danger">Click here to download excel file format</span></a></div>
                    </div>
		     <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Group');?></label>
                        <div class="col-sm-9">
			    <select name="group_name[]" class="form-control multiselectgrp" multiple="true">
			    <?php echo$groupName;?>
			    </select>                           
			    </div>
                    </div>
                     <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Subject');?></label>
                        <div class="col-sm-9">
			    <select name="subject_id" class="form-control"  required>
			    <option value=""><?php echo __('Please Select');?></option>
				 <?php echo$subjectName;?>
			    </select>
			 </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">
			    <button class='btn btn-success' name="submit"><span class="fa fa-download"></span>&nbsp;<?php echo __('Import Questions');?></button>
			</div>
                    </div>
                </form>
	</div>
</div>
