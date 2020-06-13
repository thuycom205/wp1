<style type="text/css">
@page {margin: 45px 30px 15px 95px;}
    .cname{position: absolute;top: 305px;font-size: 30px;font-family:serif;color: #29007d;text-align: center;left:250px;}
    .cemail{position: absolute;top: 375px;font-size: 24px;font-family:serif;color: #29007d;text-align: center;left:250px;}
    .cexam{position: absolute;top: 430px;font-size: 24px;font-family:serif;color: #29007d;text-align: center;left:200px;}
    .cdate{position: absolute;top: 555px;font-size: 26px;left: 222px;font-family:serif;color: #29007d;}
    .csign{position: absolute;top: 575px;font-size: 26px;left: 570px;}
    .cphoto{position: absolute;top: 200px;font-size: 26px;left: 675px;}
</style>
<?php global $current_user; echo$this->ExamApp->getImage('img/certificate.jpg');?>
<div class="cname"><?php echo __('This is to certify that');?> <strong><?php echo$current_user->display_name;?></strong><br><?php echo __('Email');?>: <strong><?php echo$current_user->user_email;?><strong></div>
<div class="cexam"> <?php echo __('has succesfully completed the');?> <strong><?php echo$post['name'];?></strong> <?php echo ('with');?> <strong><?php echo number_format($post['obtained_marks'],2);?>/<?php echo number_format($post['total_marks'],2);?> (<?php echo$post['percent'];?>%)</strong>
 <br/><?php echo __('in course program offered by');?> <strong><?php echo get_bloginfo();;?></strong></div>
 <div class="cdate"><?php echo$this->ExamApp->dateFormat($post['start_time']);?></div>
 <div class="csign"><?php echo$this->ExamApp->getImage('img/'.$signature);?></div>
 <div class="cphoto"><?php echo get_avatar($this->ExamApp->getCurrentUserId,150);?></div>