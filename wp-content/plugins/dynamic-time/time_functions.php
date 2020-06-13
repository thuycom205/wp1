<?php
/*
Plugin Name: Dynamic Time
Plugin URI: http://richardlerma.com/r1cm
Description: A simple, dynamic calendar-based time solution.
Author: R1CM
Version: 3.3.8
Text Domain: r1cm
Author URI: http://richardlerma.com/r1cm
Copyright: (c) 2017 - 2018 - richardlerma.com - All Rights Reserved
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
global $dyt_version; $dyt_version='3.3.8';
if(!defined('ABSPATH')) exit;

//Create a constant for the dyt root path
define('DYT_DIR_PATH',plugin_dir_path( __FILE__ ));

function dyt_error() {file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());}
if(defined('WP_DEBUG') && true===WP_DEBUG) add_action('activated_plugin','dyt_error');

function dyt_adminMenu() {
  if(current_user_can('list_users')) add_menu_page('Dynamic Time','Dynamic Time','list_users','dynamic-time','dyt_admin','dashicons-clock','3'); // Admin
  else { // Supervisor
    global $wpdb;
    $dyt_user=dyt_userid();
    $get_sup=$wpdb->get_results("SELECT 1 FROM {$wpdb->prefix}time_user WHERE Supervisor='$dyt_user';",OBJECT);
    if($get_sup && current_user_can('edit_published_posts')) add_menu_page('Dynamic Time','Dynamic Time','edit_published_posts','dynamic-time','dyt_admin','dashicons-clock','3');
    else add_menu_page('Dynamic Time','Dynamic Time','read','dynamic-time','dynamicTime','dashicons-clock','3');
  }
}
add_action('admin_menu','dyt_adminMenu');

function dyt_admin() {
  global $wpdb;
  global $dyt_version;
  include_once(DYT_DIR_PATH.'time_admin.php');
  wp_enqueue_style('dyt_style',plugins_url('assets/time_min.css?v=0'.$dyt_version,__FILE__));
}
add_shortcode('dyt_admin','dyt_admin');

function dynamicTime() {
  global $wpdb;
  global $dyt_version;
  include_once(DYT_DIR_PATH.'time_cal.php');
  wp_enqueue_style('dyt_style',plugins_url('assets/time_min.css?v='.$dyt_version,__FILE__));

  //Allow JS to be moved and edited without losing changes during plugin updates
  $js_url=plugins_url('assets/time_min.js?v='.$dyt_version,__FILE__);
  if(function_exists('write_log')) write_log($js_url);
  wp_enqueue_script('dyt_script',apply_filters('dyt_js_file',$js_url));
}
add_shortcode('dynamicTime','dynamicTime');

function dyt_activate($update) {
  global $wpdb;
  global $dyt_version;
  require_once(ABSPATH.'wp-admin/includes/upgrade.php');
  update_option('dyt_db_version',$dyt_version,'no');
  
  $sql="
    CREATE TABLE {$wpdb->prefix}time_config
    (ConfigID INT NOT NULL AUTO_INCREMENT,
    Prompt TINYINT DEFAULT 0,
    Notes TINYINT DEFAULT 1,
    Period TINYINT DEFAULT 15,
    WeekBegin TINYINT DEFAULT 0,
    Payroll INT,
    Currency VARCHAR(8),
    DropData TINYINT DEFAULT NULL,
    Timeout INT DEFAULT 120,
    PRIMARY KEY  (ConfigID));";
  dbDelta($sql);

  $sql="
    CREATE TABLE {$wpdb->prefix}time_user
    (UserID INT NOT NULL AUTO_INCREMENT,
    WP_UserID INT,
    Period TINYINT DEFAULT 15,
    Rate DECIMAL(4,2),
    Exempt TINYINT DEFAULT 0,
    Supervisor INT,
    PRIMARY KEY  (UserID));";
  dbDelta($sql);

  $sql="
    CREATE TABLE {$wpdb->prefix}time_entry
    (EntryID INT NOT NULL AUTO_INCREMENT,
    WP_UserID INT,
    Date INT,
    Hours DECIMAL(4,2),
    HourType VARCHAR(3),
    TimeIn VARCHAR(8),
    TimeOut VARCHAR(8),
    Note VARCHAR(250),
    PRIMARY KEY  (EntryID));";
  dbDelta($sql);
  
  $sql="
    CREATE TABLE {$wpdb->prefix}time_period
    (PeriodID INT NOT NULL AUTO_INCREMENT,
    WP_UserID INT,
    Date INT,
    Rate DECIMAL(4,2),
    Reg DECIMAL(5,2),
    PTO DECIMAL(5,2),
    OT DECIMAL(5,2),
    Bonus DECIMAL(5,2),
    Note VARCHAR(250),
    Submitted DATETIME,
    Submitter INT,
    Approved DATETIME,
    Approver INT,
    Processed DATETIME,
    PRIMARY KEY  (PeriodID));";
  dbDelta($sql);

  if(function_exists('dyt_pro_ping'))dyt_pro_ping(2);
  ?><script type'text/javascript'>window.location.href='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>&updated=1';</script><?php 
}
register_activation_hook(__FILE__,'dyt_activate');

if(strpos($_SERVER['REQUEST_URI'],'/wp-admin/plugins.php')!==false && strpos($_SERVER['REQUEST_URI'],'plugin=dynamic-time')!==false) { ?><style type='text/css'>div.error{display:none!important}</style><?php }

function dyt_admin_notice() {
  if(strpos($_SERVER['REQUEST_URI'],'page=dynamic-time')===false){
    require_once(ABSPATH."wp-includes/pluggable.php");
    if(current_user_can('manage_options')) {
      $settings_url=get_admin_url(null,'admin.php?page=dynamic-time');?>
      <div class="notice notice-success is-dismissible" style='margin:0;'>
        <p><?php _e("The <em>Dynamic Time</em> plugin is active, but is not yet configured. Visit the <a href='$settings_url'>configuration page</a> to complete setup.",'Dynamic Time');?>
      </div><?php
    }
  }
}

function dyt_checkConfig() {
  global $wpdb;
  $get_config=$wpdb->get_results("SELECT 1 FROM {$wpdb->prefix}time_config LIMIT 1;",OBJECT);
  if(!$get_config) add_action('admin_notices','dyt_admin_notice');
}
add_action('admin_init','dyt_checkConfig');

function dyt_add_action_links($links) {
  $settings_url=get_admin_url(null,'admin.php?page=dynamic-time');
  $support_url='http://richardlerma.com/r1cm/';
  $links[]='<a href="'.$support_url.'">Support</a>';
  array_push($links,'<a href="'.$settings_url.'">Settings</a>');
  return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__),'dyt_add_action_links');

function dyt_uninstall() {
  global $wpdb;
  $get_config=$wpdb->get_results("SELECT 1 FROM {$wpdb->prefix}time_config WHERE DropData=0 LIMIT 1;",OBJECT);
  if(!$get_config) {
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}time_config;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}time_user;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}time_entry;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}time_period;");
    delete_option('dyt_hide_survey');
    delete_option('dyt_db_version');
    delete_option('dyt_pro');
    delete_option('dyt_pro_version');
  }
}
register_uninstall_hook(__FILE__,'dyt_uninstall');

function dyt_userid() {
  $userid=0;
  if(is_user_logged_in()) {
    $current_user=wp_get_current_user();
    $userid=$current_user->ID;
  }
  return $userid;
}

function dyt_user_dropdown($type,$userid) {
  global $wpdb;
  $role_criteria='';
  if($type=='payroll') $role_criteria="WHERE meta_value>=6";
  if($type=='user') $role_criteria="JOIN {$wpdb->prefix}time_user tu ON tu.WP_UserID=u.ID";
  $user_query="
    SELECT DISTINCT u.ID as userid
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.ID AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.ID AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.ID AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=u.ID)
      ,'[wp user deleted]'
    ) as name
    FROM {$wpdb->base_prefix}users u
    JOIN {$wpdb->base_prefix}usermeta l ON l.user_id=u.ID AND l.meta_key LIKE '%user_level'
    $role_criteria
    ORDER BY name;
  ";
  $users=$wpdb->get_results($user_query,OBJECT);
  $options='';
  if($users):
    foreach($users as $user):
      if($user->userid==$userid) $selected='selected'; else $selected='';
      $options.="<option value='{$user->userid}' $selected>{$user->name}";
    endforeach;
  else: $options="<option value='0' disabled>No Eligible Users</option>";
  endif;
  return $options;
}


// Email Functions
function dyt_html_mail() {return 'text/html';}
function dyt_mail_from($email) {return get_bloginfo('admin_email');}
function dyt_mail_name($name) {return get_bloginfo('name');}
function dyt_email($target_id,$target_name,$target_type,$user_id,$username) {
  $url=get_bloginfo('wpurl');
  if($target_type=='payroll') $url=get_admin_url(null,'admin.php?page=dynamic-time'); else $url.=$_SERVER['REQUEST_URI'].'?x=0';
  $url.='&sup='.$target_id.'&dyt_user='.$user_id;
  $target=get_userdata($target_id);
  $email=$target->user_email;
  if(strpos($email,'@')!==false) { // check for valid email
    require_once(ABSPATH.WPINC.'/pluggable.php');
    $subject=$username." - Pay Period Submission";
    $message="Dear $target_name<br><br>&nbsp;Please find a new pay period submission for $username at <a href='$url' target='_blank'>$url</a><br><br>";
    add_filter('wp_mail_content_type','dyt_html_mail');
    add_filter('wp_mail_from','dyt_mail_from');
    add_filter('wp_mail_from_name','dyt_mail_name');
    $sent=wp_mail($email,$subject,$message);
    remove_filter('wp_mail_content_type','dyt_html_mail');
  }
}
?>