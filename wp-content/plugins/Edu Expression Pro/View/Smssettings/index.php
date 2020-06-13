<div class="panel panel-custom">
    <div class="panel-heading"><?php echo __('Sms Settings');?></div>
               <div class="panel-body">
	       <form class="form-horizontal" action="<?php echo$this->url;?>&info=index" method="post">
                <div class="form-group">
                         <label for="site_name" class="col-sm-2 control-label"><?php echo __('API Link');?></label>
                        <div class="col-sm-4">
			  <input type="text" name="data[api]" class="form-control" placeholder="<?php echo __('API Link');?>" value="<?php echo $this->ExamApp->h($post['api']);?>"  /> 
                        </div>
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Sender ID');?></label>
                        <div class="col-sm-4">
			   <input type="text" name="data[senderid]" class="form-control" placeholder="<?php echo __('Sender ID');?>" value="<?php echo $this->ExamApp->h($post['senderid']);?>"  /> 
			</div>
                    </div>
                    <div class="form-group">
                        <label for="site_name" class="col-sm-2 control-label"><?php echo __('User Name');?></label>
                        <div class="col-sm-4">
			    <input type="text" name="data[username]" class="form-control" placeholder="<?php echo __('User Name');?>" value="<?php echo $this->ExamApp->h($post['username']);?>"  /> 
                        </div>
                        <label for="site_name" class="col-sm-2 control-label"><?php echo __('Password');?></label>
                        <div class="col-sm-4">
			    <input type="text" name="data[password]" class="form-control" placeholder="<?php echo __('Password');?>" value="<?php echo $this->ExamApp->h($post['password']);?>"  /> 
                        </div>                        
                    </div>
		    <div class="form-group">
                         <label for="site_name" class="col-sm-2 control-label"><?php echo __('Heading Username');?></label>
                        <div class="col-sm-4">
			   <input type="text" name="data[husername]" class="form-control" placeholder="<?php echo __('Username field provided by sms gateway');?>" value="<?php echo $this->ExamApp->h($post['husername']);?>"  />
                        </div>
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Heading Password');?></label>
                        <div class="col-sm-4">
			    <input type="text" name="data[hpassword]" class="form-control" placeholder="<?php echo __('Password field provided by sms gateway');?>" value="<?php echo $this->ExamApp->h($post['hpassword']);?>"  />
		         </div>
                    </div>
		    <div class="form-group">
                         <label for="site_name" class="col-sm-2 control-label"><?php echo __('Heading Mobile No');?></label>
                        <div class="col-sm-4">
			   <input type="text" name="data[hmobile]" class="form-control" placeholder="<?php echo __('Mobile No field provided by sms gateway');?>" value="<?php echo $this->ExamApp->h($post['hmobile']);?>"  />
                         </div>
			<label for="site_name" class="col-sm-2 control-label"><?php echo __('Heading Message');?></label>
                        <div class="col-sm-4">
			   <input type="text" name="data[hmessage]" class="form-control" placeholder="<?php echo __('Message field provided by sms gateway');?>" value="<?php echo $this->ExamApp->h($post['hmessage']);?>"  />
			</div>
                    </div>
		    <div class="form-group">
                         <label for="site_name" class="col-sm-2 control-label"><?php echo __('Heading Sender Id');?></label>
                        <div class="col-sm-4">
			   <input type="text" name="data[hsenderid]" class="form-control" placeholder="<?php echo __('Sender Id field provided by sms gateway');?>" value="<?php echo $this->ExamApp->h($post['hsenderid']);?>"  />
                        </div>
                    </div>
		        <div class="form-group text-left">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success"><span class="fa fa-refresh"></span>&nbsp;<?php echo __('Save Settings');?></button>
                        </div>
                    </div>
                </form>
                </div>
            </div>