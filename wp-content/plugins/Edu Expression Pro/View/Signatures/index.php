
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Certificate Signature');?></h1></div></div>
<div class="panel">
    <div class="panel-heading">
    </div>         
    <div class="panel-body">
        <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Upload Image(* height less than 75px)');?></small></label>
            <div class="col-sm-6">
	        <input type="file" name="photo" value="" required="required">
            </div>
        </div>
        <div class="form-group text-left">
            <div class="col-sm-offset-3 col-sm-7">
	    <button type="submit" class="btn btn-success" name="submit"><span class="fa fa-plus-circle"></span>&nbsp;<?php echo __('Save');?></button>
            <a href="<?php echo$this->url;?>&info=deleteall" class="btn btn-danger"><span class="fa fa-trash"></span>&nbsp;<?php echo __('Delete Signature');?></a>
	    </div>
        </div>
    </form>
    </div>
</div>