<?php

if(!defined('ABSPATH')) exit;
if(!is_user_logged_in()) {?><script type='text/javascript'>window.location="<?php echo wp_login_url().'?redirect_to='.urlencode($_SERVER['REQUEST_URI']);?>"</script><?php return; }
if(!current_user_can('read')) return;
//if(!empty($_GET['sup'])) $sup=intval($_GET['sup']);
if(!empty($_GET['dyt_user'])) $dyt_user=intval($_GET['dyt_user']);

// Sync Configuration
global $wp_version;
global $dyt_version;
$dyt_db_version=get_option('dyt_db_version');
if($dyt_version!=$dyt_db_version) dyt_activate(1); // Run dbDelta upgrade

$admin_view=$db_sup=0;
$wp_userid=dyt_userid(); // Default to Self

if(!empty($dyt_user)) {
  if(current_user_can('list_users')) {$wp_userid=$dyt_user; $admin_view=1;} // Admin
  else { // Supervisor
    $get_sup=$wpdb->get_results("SELECT Supervisor FROM {$wpdb->prefix}time_user WHERE WP_UserID='$dyt_user';",OBJECT);
    if($get_sup) foreach($get_sup as $row):$db_sup=$row->Supervisor;endforeach;
    if($wp_userid==$db_sup && $db_sup!=$dyt_user) { //$sup==$wp_userid
      $wp_userid=$dyt_user;
      $admin_view=1;
    }
    if($admin_view<1 && $dyt_user>0 && $dyt_user!=$wp_userid) {echo "<div class='dyt_control' style='background:#fff;margin:2em;padding:1em 2em'><h2>You need a higher level of permission.</h2><p>Sorry, you are not allowed to access this user.</p></div>"; return;}
  }
}

if($wp_userid>0) {
  $action=$pto=$ot='';
  // Pay Period Meta
  if(!empty($_POST['action']))$action=sanitize_text_field($_POST['action']);
  if(!empty($_POST['Reg']))   $reg=filter_var($_POST['Reg'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  if(!empty($_POST['PTO']))   $pto=filter_var($_POST['PTO'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  if(!empty($_POST['OT']))    $ot=filter_var($_POST['OT'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  if(!empty($_POST['Bonus'])) $bonus=filter_var($_POST['Bonus'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  if(!empty($_POST['period_note'])) $period_note=sanitize_text_field($_POST['period_note']);

  // Time Entry Meta
  if(!empty($_POST['df_date'])) $df_date=intval($_POST['df_date']);
  if(!empty($_POST['date']))    $date=array_map('ABSINT',$_POST['date']);
  if(!empty($_POST['hours']))   $hours=array_map('sanitize_text_field',$_POST['hours']);
  if(!empty($_POST['hourtype']))$hourtype=array_map('sanitize_text_field',$_POST['hourtype']);
  if(!empty($_POST['time_in'])) $time_in=array_map('sanitize_text_field',$_POST['time_in']);
  if(!empty($_POST['time_out']))$time_out=array_map('sanitize_text_field',$_POST['time_out']);
  if(!empty($_POST['note']))    $note=array_map('sanitize_text_field',$_POST['note']);
  $input_saved=0;

  // Configuration
  $config=$wpdb->get_results("
    SELECT WP_UserID
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id='$wp_userid' AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id='$wp_userid' AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id='$wp_userid' AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID='$wp_userid')
      ,'[wp user deleted]'
    ) as Uname
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Supervisor AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=Supervisor AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Supervisor AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=Supervisor)
      ,'[wp user deleted]'
    ) as Sname
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Payroll AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=Payroll AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Payroll AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=Payroll)
      ,'[wp user deleted]'
    ) as Pname
    ,CASE WHEN c.Period<0 THEN u.Period ELSE c.Period END as Period
    ,Rate,Prompt,Notes,Exempt,WeekBegin,Supervisor,Payroll,Currency
    FROM {$wpdb->prefix}time_config c
    LEFT JOIN {$wpdb->prefix}time_user u ON u.WP_UserID='$wp_userid'
    LIMIT 1;
  ",OBJECT);

  if($config): 
    foreach($config as $row):
      $user=$row->Uname;
      $rate=$row->Rate;
      $prompt=$row->Prompt;
      $notes=$row->Notes;
      $exempt=$row->Exempt;
      $period=$row->Period;
      $weekbegin=$row->WeekBegin;
      $sid=$row->Supervisor;
      $sname=$row->Sname;
      $pid=$row->Payroll;
      $currency=$row->Currency;
      $pname=$row->Pname;
    endforeach;
    else: $input_saved='-3';
  endif;


  if(!empty($_POST['dyt_save_time']) && check_admin_referer('save_time','dyt_save_time')) {
    
    // Insert User If Not Exists
    $get_login=$wpdb->get_results("SELECT WP_UserID FROM {$wpdb->prefix}time_user WHERE WP_UserID='$wp_userid' LIMIT 1; ",OBJECT);
    if(!$get_login) $insert_config=$wpdb->get_results("INSERT INTO {$wpdb->prefix}time_user (WP_UserID,Rate,Exempt,Supervisor) VALUES('$wp_userid',NULL,0,NULL); ",OBJECT);

    if(count(array_filter($hours))>0) { // If Hours Array is not empty, Delete Matching Entries before Insert
      $wpdb->query("SET SQL_SAFE_UPDATES=0;");
      foreach($date as $index=>$dateval) $delete_entry=$wpdb->get_results("DELETE FROM {$wpdb->prefix}time_entry WHERE WP_UserID=$wp_userid AND Date='$dateval'; ",OBJECT);
    }

    foreach($date as $index=>$dateval) { // Insert Entries
      reset($date);
      if($hours[$index]>0) $insert_entry=$wpdb->get_results("INSERT INTO {$wpdb->prefix}time_entry (WP_UserID,Date,Hours,HourType,TimeIn,TimeOut,Note)VALUES($wp_userid,'$dateval','{$hours[$index]}','{$hourtype[$index]}','{$time_in[$index]}','{$time_out[$index]}','{$note[$index]}'); ",OBJECT);
    }

    if($action=='reverse') { // Reverse all Stamps
      reset($date);
      foreach($date as $index=>$dateval) $reverse_period=$wpdb->get_results("DELETE FROM {$wpdb->prefix}time_period WHERE WP_UserID='$wp_userid' AND Date='$dateval'; ",OBJECT);
    } else {

      // Update Period Totals
      $update_period=$wpdb->get_results("UPDATE {$wpdb->prefix}time_period p JOIN {$wpdb->prefix}time_user u ON u.WP_UserID=p.WP_UserID SET p.Rate=u.Rate,Reg='$reg',PTO='$pto',OT='$ot',Bonus='$bonus',Note='$period_note' WHERE p.WP_UserID='$wp_userid' AND Date='$dateval';",OBJECT);
      $insert_period=$wpdb->get_results("INSERT INTO {$wpdb->prefix}time_period (WP_UserID,Date,Rate,Reg,PTO,OT,Bonus,Note)
        SELECT '$wp_userid','$dateval',Rate,'$reg','$pto','$ot','$bonus','$period_note'
        FROM {$wpdb->prefix}time_user
        WHERE WP_UserID='$wp_userid' AND NOT EXISTS (SELECT PeriodID FROM {$wpdb->prefix}time_period WHERE WP_UserID='$wp_userid' AND Date='$dateval')",OBJECT);
      $action_user=dyt_userid();
    }

    if($action=='send') { // Submit Pay Period
      $submit_period=$wpdb->get_results("UPDATE {$wpdb->prefix}time_period p JOIN {$wpdb->prefix}time_user u ON u.WP_UserID=p.WP_UserID SET p.Rate=u.Rate, Submitter='$action_user',Submitted=NOW(),Reg='$reg',PTO='$pto',OT='$ot',Bonus='$bonus',Note='$period_note' WHERE p.WP_UserID='$wp_userid' AND Date='$dateval';",OBJECT);
      if($sid>0) dyt_email($sid,$sname,'supervisor',$wp_userid,$user);
      elseif($pid>0) dyt_email($pid,$pname,'payroll',$wp_userid,$user);
    }

    if($action=='approve') { // Approve Submission
      $approve_period=$wpdb->get_results("UPDATE {$wpdb->prefix}time_period p JOIN {$wpdb->prefix}time_user u ON u.WP_UserID=p.WP_UserID SET p.Rate=u.Rate, Approver='$action_user',Approved=NOW(),Reg='$reg',PTO='$pto',OT='$ot',Bonus='$bonus',Note='$period_note' WHERE p.WP_UserID='$wp_userid' AND Date='$dateval';",OBJECT);
      $action_user=dyt_userid();
      if($pid>0 && $sid>0) dyt_email($pid,$pname,'payroll',$wp_userid,$user);
    }

    if($action=='process') // Process Submission
      $process_period=$wpdb->get_results("UPDATE {$wpdb->prefix}time_period SET Processed=NOW() WHERE WP_UserID='$wp_userid' AND Date='$dateval';",OBJECT);

    $input_saved++;
  }
  
  // Pay Period Meta
  $periods=$wpdb->get_results("
    SELECT WP_UserID,Date,Rate,Bonus,Note
    ,DATE_FORMAT(Submitted,'%b %D %h:%i%p') as Submitted
    ,DATE_FORMAT(Approved,'%b %D %h:%i%p') as Approved
    ,DATE_FORMAT(Processed,'%b %D %h:%i%p') as Processed
    ,CASE WHEN Submitter>0 THEN COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Submitter AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=Submitter AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Submitter AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=Submitter)
    ) ELSE '' END as Submitter
    ,CASE WHEN Approver>0 THEN COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Approver AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=Approver AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=Approver AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=Approver)
      ,'[wp user deleted]'
    ) ELSE '' END as Approver
    FROM {$wpdb->prefix}time_period
    WHERE WP_UserID='$wp_userid';
  ",OBJECT);
  
  $period_end='';
  $period_rate='';
  $period_bonus='';
  $period_note='';
  $submitted='';
  $submitter='';
  $approved='';
  $approver='';
  $processed='';
  
  if($periods):
    foreach($periods as $row): 
    $period_end=$period_end."'".$row->Date."',";
    $period_rate=$period_rate."'".$row->Rate."',";
    $period_bonus=$period_bonus."'".$row->Bonus."',";
    $period_note=$period_note."'".$row->Note."',";
    $submitted=$submitted."'".$row->Submitted."',";
    $submitter=$submitter."'".$row->Submitter."',";
    $approved=$approved."'".$row->Approved."',";
    $approver=$approver."'".$row->Approver."',";
    $processed=$processed."'".$row->Processed."',";
    endforeach;
  endif;


  //Allow filtering the $query_string in order to extend plugin
  $entry_query_string="
    SELECT WP_UserID,Date,SUM(Hours) as Hours,HourType,TimeIn,TimeOut,Note
    FROM {$wpdb->prefix}time_entry
    WHERE WP_UserID='$wp_userid'
    GROUP BY WP_UserID,Date,HourType,TimeIn,TimeOut,Note
    ORDER BY Date ASC, HourType DESC, TimeIn;
    ";
  $entry_query_string=apply_filters('dyt/entries/query',$entry_query_string);

  // Entry Meta
  $entries=$wpdb->get_results($entry_query_string,OBJECT);

  //Add a filter to allow the user to edit how entry data is loaded
  $load_entry_path=DYT_DIR_PATH.'time_load_entries.php';
  include(apply_filters('dyt/entries/load_entry_path',$load_entry_path));
}

if($input_saved>=0) { ?>
<form id='dyt_form' method='post' accept-charset='UTF-8 ISO-8859-1'>
  <?php echo wp_nonce_field('save_time','dyt_save_time');?>

  <div id='nav' class='dyt_nav' onclick="show_time(0,0);">
    <a onclick='add_week(-1);' class='dyt_bkw noprint'>Prev Period</a>
    <a onclick='add_week(1);' class='dyt_fwd noprint'>Next Period</a>
    <div id='period_disp' class='dyt_title' style='margin-top:1em;display:inline-block'></div>
  </div>

  <div id='dyt_cal'></div>

  <div id='dyt_sum' onclick="show_time(0,0);">
    <div class='dyt_title'><?php echo $user;?></div>

    <table style='float:left;margin:1em;width:auto'>
      <tr><td colspan='2'>Regular (Reg)</td><td style='text-align:right'><input type='text' id='Reg' name='Reg' readonly></td></tr>
      <tr id='pto_row'><td colspan='2'>Paid Time Off (PTO)</td><td style='text-align:right'><input type='text' id='PTO' name='PTO' readonly></td></tr>
      <tr id='ot_row'><td colspan='2'>OverTime</td><td style='text-align:right'><input type='text' id='OT' name='OT' readonly></td></tr>
      <tr><td colspan='2'>Total Hours</td><td style='text-align:right'><input type='text' id='TOT' readonly></td></tr>
      <tr><td colspan='2'>Bonus</td><td style='text-align:right'><input type='text' id='Bonus' name='Bonus' <?php if($admin_view>0) echo "style='pointer-events:inherit' onchange='pay_bonus=this.value;sumrows();dyt_form.submit();'";?>></td></tr>
      <tr><td colspan='2'>Total</td><td style='text-align:right'><input type='text' id='TOTamt' readonly></td></tr>
      <tr><td colspan='3'>Note <input type='text' name="period_note" id="period_note" placeholder="note" style='text-align:left;pointer-events:inherit;width:211px' onchange='dyt_form.submit();'></td></tr>
    </table>

    <div id='dyt_actions' style='float:right;margin:1em'>
      <input id='dyt_save' type='submit' value='Save' name='save' class='noprint' onclick="action.value='save'; show_save(-1);">
      <input id='dyt_print' type='button' value='Print' class='noprint' onclick='window.print();'>
      <input id='dyt_send'  type='submit' disabled name='send'  value='Submit for Approval' style='height:auto;white-space:normal' onclick="if(confirm('&#9888; Are you sure you want to submit this pay period?\n\nYour supervisor (if assigned) will be notified of this submission.')) {action.value='send'; show_save(-2);} else return false;">
      <?php if($admin_view>0) { ?>
      <input id='dyt_approve' type='submit' disabled name='approve' class='pre_lock_btn' value='Approve' style='height:auto;white-space:normal' onclick="if(confirm('&#9888; Are you sure you want to approve this pay period?\n\nThis period will be locked, and a payroll processor (if assigned) will be notified of this approval.')) {action.value='approve'; show_save(-2);} else return false;">
      <input id='dyt_process' type='submit' disabled name='process' class='pre_lock_btn' value='Mark as Processed' onclick="if(confirm('&#9888; Are you sure you want to mark this period as processed?')) {action.value='process'; show_save(-2);} else return false;">
      <input id='dyt_reverse' type='submit' name='reverse' class='noprint' value='Reset Period' onclick="if(!confirm('&#9888; Reset submission, approval and processing stamps for this period?\n\nThis will also reset the pay period end date if it differs from the current pay period configuration.')) return false; action.value='reverse'; show_save(-1);">
      <?php } else if($prompt==3) { ?>
      <style>
        .dyt_pop input[type=time]{pointer-events:none!important}
        .dyt_pop .stepup,.dyt_pop .stepdown{display:none!important}
      </style>
      <?php } ?>
      <input id='action' type='hidden' name='action' value=''>
    </div>
  </div>
</form>
<?php } ?>

<div id='input_saved'>Saved</div>