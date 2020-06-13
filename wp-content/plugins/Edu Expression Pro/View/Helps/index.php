<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Help');?></h1></div></div>
<div class="panel">
    <div class="panel-body">
                <div class="panel-group" id="accordion">
                    <?php foreach($helpPost as $k=>$post):$id=$post['id'];?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-toggle="collapse" href="#collapse<?php echo$id;?>"><strong><?php echo $this->ExamApp->h($post['name']);?></strong></a></h4>                        
                    </div>
                        <div id="collapse<?php echo$id;?>" class="collapse<?php echo($k==0)?"in":"";?>">
                            <div class="panel-body">
                                <?php echo str_replace("<script","",$post['description']);?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;unset($post);?>                
                </div>
		</div>
</div>