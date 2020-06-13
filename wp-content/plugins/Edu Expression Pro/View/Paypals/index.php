
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Paypal Payment Option');?></h1></div></div><div class="panel"><div class="panel">
    <div class="panel-body">
                <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">
                    <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('User Name');?></label>
                        <div class="col-sm-9">
			<input type="text" name="data[username]" class="form-control" placeholder="<?php echo __('User Name');?>" value="<?php echo $this->ExamApp->h($post['username']);?>"  /> 
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Password');?></label>
                        <div class="col-sm-9">
			<input type="password" name="data[password]" class="form-control" placeholder="<?php echo __('Password');?>" value="<?php echo $this->ExamApp->h($post['password']);?>"  /> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Signature');?></label>
                        <div class="col-sm-9">
			<input type="text" name="data[signature]" class="form-control" placeholder="<?php echo __('Signature');?>" value="<?php echo $this->ExamApp->h($post['signature']);?>"  /> 
                        </div>
                    </div>		    
                    <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Sandbox Mode');?></label>
                        <div class="col-sm-9">
			<input type="checkbox" name="data[sandbox_mode]"  class="form-control" value="1" <?php if($post['sandbox_mode']){echo 'checked';}?>  />&nbsp;<?php echo __('True');?>
		     </div>
                    </div>    
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">
			<button class="btn btn-success" type="submit"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Save Settings');?></button>
                 </div>
                    </div>
                </form>
                </div>
            </div></div>