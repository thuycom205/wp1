<?php $configArr=$this->ExamApp->configuration();$mathEditor=$configArr['math_editor'];?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="google-translate-customization" content="839d71f7ff6044d0-328a2dc5159d6aa2-gd17de6447c9ba810-f">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php bloginfo('title');?></title>
	<meta name="description" content="<?php bloginfo('description');?>" />
	<link href="<?php echo get_stylesheet_directory_uri();?>/favicon.ico" type="image/x-icon" rel="icon" />
        <link href="<?php echo get_stylesheet_directory_uri();?>/favicon.ico" type="image/x-icon" rel="shortcut icon" />
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700" />
        <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__);?>../../css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__);?>../../css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__);?>../../css/core.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__);?>../../css/jquery.countdown.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__);?>../../css/style.css" />
        <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/html5shiv.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/respond.min.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/waiting-dialog.min.js"></script>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/jquery.plugin.min.js"></script>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/bootstrap-multiselect.js"></script>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/jquery.countdown.min.js"></script>
	<?php if($mathEditor){?><script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
	<script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>../../js/custom.min.js"></script>
	<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    'use strict';
    document.body.oncopy = function() { return false; }
    document.body.oncut = function() { return false; }
    document.body.onpaste = function() { return false; }
});

//]]>
</script> 
</head>
  <body>
    <div class="col-md-12">
      <div class="col-md-9">
	<div class="exam-logo"><?php echo get_bloginfo();?></div>
      </div>
      <div class="col-md-3 exam-photo"><?php global $current_user;echo get_avatar(get_current_user_id(),60,null,$current_user->display_name);?></div>
    </div>
    <div class="col-md-12"><div class="exam-border">&nbsp;</div></div>    
    <div>