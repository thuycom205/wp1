<?php 

if(!defined('ABSPATH')) exit;
if(!current_user_can('edit_published_posts')) exit;
if(!$wpdb) $wpdb=new wpdb(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST); else global $wpdb;
if(isset($_POST['period_range'])) $period_range=intval($_POST['period_range']); else $period_range=0;
$input_saved=0;

// Get Versions
  global $wp_version;
  global $dyt_version;
  $dyt_db_version=get_option('dyt_db_version');
  $dyt_pro_version='';
  $dyt_version_type='GPL';
  $pro_path=str_replace('dynamic-time','dynamic-time-PRO',plugin_dir_path( __FILE__ )).'pro_content.php';
  if(is_plugin_active('dynamic-time-PRO/pro_functions.php')) if(file_exists($pro_path)){include_once $pro_path; $dyt_version_type='PRO'; $dyt_pro_version=get_option('dyt_pro_version');}
  $get_version=$wpdb->get_results("SELECT @@version as version;",OBJECT);
  if($get_version) foreach($get_version as $row):$mysql_version=$row->version;endforeach;
  $db_config_mode=$wpdb->get_results("SELECT @@sql_safe_updates as mode;",OBJECT);
  if($db_config_mode) foreach($db_config_mode as $row):$config_mode=$row->mode;endforeach;

// Sync Configuration
  if($dyt_version!=$dyt_db_version) dyt_activate(1); // Run dbDelta upgrade

// Update Configuration
  if(!empty($_POST['dyt_config_time']) && check_admin_referer('config_time','dyt_config_time')) {
    $prompt=intval($_POST['prompt']);
    $notes=intval($_POST['notes']);
    $period=intval($_POST['period']);
    $weekbegin=intval($_POST['weekbegin']);
    $currency=sanitize_text_field($_POST['currency']);
    $dropdata=intval($_POST['dropdata']);
    $timeout=intval($_POST['timeout']);
    if(!empty($_POST['hide_survey'])) $hide_survey=intval($_POST['hide_survey']); else $hide_survey=0;
    $payroll_id=intval($_POST['payroll_id']);
    $wpdb->query("SET SQL_SAFE_UPDATES=0;");
    $delete_config=$wpdb->query("DELETE FROM {$wpdb->prefix}time_config;");
    $insert_config=$wpdb->query("INSERT INTO {$wpdb->prefix}time_config (Prompt,Notes,Period,WeekBegin,Payroll,Currency,DropData,Timeout)VALUES('$prompt','$notes','$period','$weekbegin','$payroll_id','$currency','$dropdata','$timeout');");
    if(isset($hide_survey)) update_option('dyt_hide_survey',$hide_survey,'no');
    $input_saved++;
  }

// Update User
  if(!empty($_POST['dyt_config_user']) && check_admin_referer('config_user','dyt_config_user')) {
    $wp_userid=intval($_POST['wp_userid']);
    $user_period=intval($_POST['user_period']);
    $rate=filter_var($_POST['rate'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
    $exempt=intval($_POST['exempt']);
    $supervisor_id=intval($_POST['supervisor_id']);
    $wpdb->query("SET SQL_SAFE_UPDATES=0;");
    $delete_user=$wpdb->query("DELETE FROM {$wpdb->prefix}time_user WHERE WP_UserID=$wp_userid;");
    $insert_user=$wpdb->query("INSERT INTO {$wpdb->prefix}time_user (WP_UserID,Period,Rate,Exempt,Supervisor)VALUES('$wp_userid','$user_period','$rate','$exempt','$supervisor_id');");
    $input_saved++;
  }

// Get Configuration
  $get_config=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}time_config LIMIT 1;",OBJECT);
  $hide_survey=get_option('dyt_hide_survey');
  $dyt_user=0;
  $not_set='&#9888; Not Set';
  if(strpos($_SERVER['REQUEST_URI'],'dyt_user=')!==false) $dyt_user=intval($_GET['dyt_user']);
  if($get_config): 
    foreach($get_config as $row):
      $prompt=$row->Prompt;
      $notes=$row->Notes;
      $period=$row->Period;
      $weekbegin=$row->WeekBegin;
      $payroll_id=$row->Payroll;
      $currency=htmlentities($row->Currency);
      $dropdata=$row->DropData;
      $timeout=$row->Timeout;
    endforeach;
    $dyt_setup=0;
  else:
    $prompt=-1;
    $notes=-1;
    $period=0;
    $weekbegin=-1;
    $payroll_id=-1;
    $currency='$';
    $dropdata=-1;
    $timeout=-1;
    $dyt_setup=1;
  endif;

// Get Users
  if($period_range===0) $condition="AND DATE_FORMAT(FROM_UNIXTIME(FLOOR((p.Date+1)*8.64e7)/1000),'%Y-%m-%d')>NOW()-INTERVAL 1.5 MONTH "; else $condition="";
  $get_users=$wpdb->get_results("
    SELECT u.*
    ,(SELECT user_email FROM {$wpdb->base_prefix}users WHERE ID=u.WP_UserID) as email
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.WP_UserID AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.WP_UserID AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.WP_UserID AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=u.WP_UserID)
      ,'[wp user deleted]'
    ) as name
    ,COALESCE(
      CONCAT((SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.Supervisor AND meta_key='first_name' AND LENGTH(meta_value)>0)
      ,(SELECT CONCAT(' ',meta_value) FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.Supervisor AND meta_key='last_name' AND LENGTH(meta_value)>0))
      ,(SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id=u.Supervisor AND meta_key='nickname' AND LENGTH(meta_value)>0)
      ,(SELECT display_name FROM {$wpdb->base_prefix}users WHERE ID=u.Supervisor)
      ,'[wp user deleted]'
    ) as supervisor_name
    ,Supervisor as supervisor_id
    ,DATE_FORMAT(p.Submitted,'%Y-%m-%d') as Submitted
    ,DATE_FORMAT(p.Approved,'%Y-%m-%d') as Approved
    ,DATE_FORMAT(p.Processed,'%Y-%m-%d') as Processed
    FROM {$wpdb->prefix}time_user u
    LEFT JOIN (
      SELECT WP_UserID, MAX(PeriodID) as PeriodID
      FROM {$wpdb->prefix}time_period
      GROUP BY WP_UserID
    )m ON m.WP_UserID=u.WP_UserID
    LEFT JOIN {$wpdb->prefix}time_period p ON p.PeriodID=m.PeriodID
    WHERE u.WP_UserID>0
    $condition 
    ORDER BY Submitted DESC, Approved DESC, Processed DESC;
  ",OBJECT);
  $user_count=count($get_users);
  $sup_count=0;
  foreach($get_users as $row) { if($row->supervisor_id>0) $sup_count++; } ?>

<div id='input_saved'>Saved</div>
<table id='dyt_head' class='dyt_control' style='width:100%;border:none;'>
  <tr>
    <td align='left'>
      <img style='height:20px;' src='<?php echo plugins_url('/assets/DynamicTime.png',__FILE__);?>'>
      <div class='dyt_links'>
        <a href="#!" onclick="dyt_expand('dyt_setup');">Setup <span class="dashicons dashicons-admin-plugins"></span></a><br>
        <a href="#!" onclick="dyt_expand('dyt_pro');" <?php if(!current_user_can('manage_options')) echo "style='display:none'"; ?>><span class='caps'>Dynamic Time <span class='pro'>Pro</span><span class="dashicons dashicons-chart-line" style='color:#b71b8a'></span><span style='color:#1177aa;font-weight:bold'><br></a>
        <a href="#!" onclick="dyt_expand('dyt_diag');">Support & Diagnostics <span class="dashicons dashicons-admin-tools"></span></a>
      </div>
      <br><hr style='width:50%;float:left'><br>
      <?php if(isset($dyt_user)) if($dyt_user>0) { ?>
        <a id='dyt_return' href='#!' onclick="dyt_switchScreen('dyt_admin');"><div id='dyt_return_icon'></div> Return to Admin</a>
      <?php } ?>
    </td>
</table>
<?php 

if($dyt_user>0) {
  if(empty($_GET['sup'])) check_admin_referer('view_user','dyt_view_user');?>
  <style>#dyt_setup td{padding-left:3em}#dyt_admin{display:none}#dyt_admin,#dyt_cal_admin{-webkit-transition:all .2s;-moz-transition:all .2s;transition:all .2s}#dyt_survey,#dyt_survey_button{display:none}#dyt_cal_admin #dyt_form{margin:0;width:99%}</style>
  <div id='dyt_cal_admin'>
    <?php echo do_shortcode('[dynamicTime]');?>
  </div>
<?php } ?>

<style>
#dyt_pro ul>li:before{padding:0;margin:0;content:'\276D';font-weight:700;color:#b71b8a;padding-right:6px}
a:focus{box-shadow:none}
#dyt_admin .button{width:100%;font-size:.9em;font-weight:normal}
#dyt_head .dyt_links{float:right;text-align:right}
#dyt_head .dyt_links a{line-height:1.8em}
#dyt_admin .dyt_control .dyt_link{display:block;background:#0073aa;color:#FFF;border-radius:3px;padding:1em 3em;text-align:center;font-weight:normal}
#dyt_admin .dyt_control .dyt_link:hover{background:#b71b8a;color:#FFF;text-decoration:none}
#dyt_admin .spin:hover,#dyt_admin .budge,#dyt_admin .dyt_expand{-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s}
#dyt_admin .dyt_expand{display:none;opacity:0;max-height:0;padding:2em}
#dyt_admin .setup_order{font-size:2em;vertical-align:text-bottom}
#dyt_admin table th{color:#0073AA;padding:0 .5em}
#dyt_admin table td{overflow:hidden;white-space:nowrap;padding:.3em 1em}
#dyt_admin table .even{background:#F6F6F6}
#dyt_admin table .odd{background:#E8E8E8}
#dyt_admin table tr:hover{background:#FFF;color:#0073AA}
#dyt_admin table .col_name{color:#777}
#dyt_admin table .dyt_disable{color:gray;opacity:.7;pointer-events:none}
.caps{color:#17a;font-weight:700;font-variant-caps:petite-caps}
#dyt_admin .attn{color:#FFF;border-radius:3px;background:#b71b8a;color:#fff;padding:1em;margin:1em 0}
#dyt_admin .spin:hover{transform:rotate(180deg)}
#dyt_admin .view{cursor:pointer;text-decoration:none}
#dyt_admin tr:hover>td>.budge{margin-right:-.1em}
#dyt_admin .dyt_new{padding:1em;background:#b71b8a;color:#fff;border-radius:3px;margin-bottom:1em}
#dyt_admin .dyt_new:after{border-width: 14px 7px 0;border-color:#b71b8a transparent;content:'';position:absolute;border-style:solid;display:block;width:0;margin:1em 0 0}
</style>

<div id='dyt_admin'>
  <form class='dyt_form' name='timeconfig' id='timeconfig' method='post' accept-charset='UTF-8 ISO-8859-1' action='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>&wp=0'>
    <?php echo wp_nonce_field('config_time','dyt_config_time');
    if(current_user_can('manage_options')) { ?>
    <table id='timesettings' class='dyt_control'>
      <tr style='background:transparent;color:#1177aa;font-weight:bold'><th align='left'>&#9881; Settings<hr></th></tr>
      <tr>
        <td nowrap>
          <?php if(isset($_GET['updated']) && 1==2) {?>
            <div class='dyt_new'><span class="dashicons dashicons-controls-play"></span> Auto Entry <span class='caps' style='color:#FFF'>Pro</span> <span style='font-style:italic;font-size:.9em'> records time automatically.  This is most practical for systems where users are active in WordPress throughout the workday.</span>
              <?php if($dyt_version_type!='PRO') { ?><br><br><a href='#!' style='color:#FFF' onclick="dyt_expand('dyt_pro');">Upgrade to <span class='caps' style='color:#FFF'>Pro</span> for this feature and more!</a><?php } ?>
            </div>
          <?php } ?>
          <select name='prompt' id='prompt' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='' disabled  <?php if($prompt<0) echo 'selected';?>>Select Entry Type
            <option value='0' <?php if($prompt==0) echo 'selected';?> title="One total field per day.">Simple &#9432;
            <option value='1' <?php if($prompt==1) echo 'selected';?> title="Multiple In/Out fields per day.">Itemized &#9432;
            <option value='3' <?php if($prompt==3) echo 'selected';?> title="Punch In/Out Only (Only admins can manually adjust time).">Punch Only &#9432;
            <option value='2' <?php if($prompt==2) echo 'selected'; if($dyt_version_type!='PRO') echo 'disabled'; ?> title="AUTO Entry - records time automatically (This is practical when a user is active in WordPress throughout the workday).">Auto Entry (PRO) &#9432;
          </select>
          <span id='prompt_sel' class='sel_status'><?php if($prompt<0) echo $not_set;?></span>

          <select name='timeout' id='timeout' onchange="dyt_config('timeconfig',0,0);" style='<?php if($dyt_version_type=='PRO' && $prompt>1) echo "display:block;margin:.5em 0 0"; else echo "display:none"; ?>' title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">
            <option value='' disabled <?php if($timeout<0) echo 'selected';?>>Session Timeout &#9432;
            <option value='10' <?php if($timeout==10) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">10 minutes
            <option value='20' <?php if($timeout==20) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">20 minutes
            <option value='30' <?php if($timeout==30) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">30 minutes (recommended)
            <option value='40' <?php if($timeout==40) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">40 minutes
            <option value='60' <?php if($timeout==60) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">1 hour
            <option value='90' <?php if($timeout==90) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">1.5 hours
            <option value='120' <?php if($timeout==120) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">2 hours
            <option value='240' <?php if($timeout==240) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">4 hours
            <option value='360' <?php if($timeout==360) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">6 hours
            <option value='480' <?php if($timeout==480) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">8 hours
            <option value='600' <?php if($timeout==600) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">10 hours
            <option value='720' <?php if($timeout==720) echo 'selected';?> title="If user inactivity in exceeds this time, Auto Entry will create an additional in/out period when activity resumes.">12 hours
          </select>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <select name='notes' id='notes' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='' disabled  <?php if($notes<0) echo 'selected';?>>Select Note Display
            <option value='1' <?php if($notes==1) echo 'selected';?> title="Display optional note field.">Display Notes &#9432;
            <option value='0' <?php if($notes==0) echo 'selected';?> title="Hide note field.">No Notes &#9432;
          </select>
          <span id='notes_sel' class='sel_status'><?php if($notes<0) echo $not_set;?></span>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <select name='period' id='period' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='' disabled <?php if($period==0) echo 'selected';?>>Select Pay Period
            <option value='7'  <?php if($period==7 ) echo 'selected';?> title='Pay periods are every week (52 per year).'>Weekly Pay Period &#9432;
            <option value='14' <?php if($period==14) echo 'selected';?> title='Pay periods are in two week groups (26 per year).'>BiWeekly Pay Period &#9432;
            <option value='15' <?php if($period==15) echo 'selected';?> title='Pay periods are twice per calendar month (24 per year).'>Semi-Monthly Pay Period &#9432;
            <option value='30' <?php if($period==30) echo 'selected';?> title='Pay periods are every calendar month (12 per year)'>Monthly Pay Period &#9432;
            <option value='-1' <?php if($period==-1) echo 'selected';?> title='Pay period differs between users'>Manage on User Level &#9432;
          </select>
          <span id='period_sel' class='sel_status'><?php if($period==0) echo $not_set;?></span>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <select name='weekbegin' id='weekbegin' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='' disabled <?php if($weekbegin<0) echo 'selected';?>>Select Week Begin
            <option value='0' <?php if($weekbegin==0 && $dyt_setup==0) echo 'selected';?>>Week Begins Sunday
            <option value='1' <?php if($weekbegin==1) echo 'selected';?>>Week Begins Monday
            <option value='2' <?php if($weekbegin==2) echo 'selected';?>>Week Begins Tuesday
            <option value='3' <?php if($weekbegin==3) echo 'selected';?>>Week Begins Wednesday
            <option value='4' <?php if($weekbegin==4) echo 'selected';?>>Week Begins Thursday
            <option value='5' <?php if($weekbegin==5) echo 'selected';?>>Week Begins Friday
            <option value='6' <?php if($weekbegin==6) echo 'selected';?>>Week Begins Saturday
          </select>
          <span id='weekbegin_sel' class='sel_status'><?php if($weekbegin<0) echo $not_set;?></span>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <select name='payroll_id' id='payroll_id' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='0' disabled <?php if($payroll_id<0) echo 'selected';?> title='Payroll receives notification when a pay period is approved. Payroll is also the default supervisor, if a user is not assigned one.'>Payroll Notifications &#9432;
            <option value='0' <?php if($payroll_id==0) echo 'selected';?>>No Notification
            <?php echo dyt_user_dropdown('payroll',$payroll_id);?>
          </select>
          <span id='payroll_id_sel' class='sel_status'><?php if($payroll_id<0) echo $not_set;?></span>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <select name='dropdata' id='dropdata' onchange="dyt_config('timeconfig',this.id,this.selectedIndex);">
            <option value='' disabled <?php if($dropdata<0) echo 'selected';?> title='Keep Data Safe will retain user entries and configuration data if the plugin is uninstalled.'>Uninstall Option &#9432;
            <option value='0' <?php if($dropdata===0) echo 'selected';?>>Keep Data Safe
            <option value='1' <?php if($dropdata==1) echo 'selected';?>>Delete All Plugin Data
          </select>
          <span id='dropdata_sel' class='sel_status'><?php if($dropdata<0) echo '&#9888; Not Set';?></span>
        </td>
      </tr>
      <tr>
        <td nowrap>
          <div style='width:200px;text-align:right'>Currency 
            <input type='text' name='currency' id='currency' placeholder='$' title='Currency Symbol' pattern=".{1,5}" required title="1 to 5 characters" style='max-width:5em' value='<?php if(empty($currency) || $dyt_setup>0) echo '$'; else echo $currency;?>' onchange="if(this.value>0) dyt_config('timeconfig',this.id,this.selectedIndex);">
          </div>
        </td>
      </tr>
    </table>
    <?php } 

    if($dyt_setup==0) { ?>
    <input type='hidden' name='hide_survey' id='hide_survey' value=<?php if($hide_survey==1) echo 1; else echo 0; ?>>
    <?php } ?>
  </form>

  <div id='dyt_diag' class='dyt_control dyt_expand' style='float:right'>
    <span style='color:#1177aa;font-weight:bold'><span class="dashicons dashicons-admin-tools"></span> Support & Diagnostics</span>
    <a onclick="dyt_expand('dyt_diag');" style='text-decoration:none;float:right'><span class="dashicons dashicons-dismiss"></span></a><hr>
    <br>
    <div id='dyt_diag_data'>
      <b>Configuration</b><br>
      Host <?php echo $_SERVER['HTTP_HOST']; ?><br>
      Path <?php echo substr(plugin_dir_path( __FILE__ ),-33);?><br>
      WP Version <?php echo $wp_version; if(is_multisite()) echo 'multi'; ?><br>
      PHP Version <?php echo phpversion();?><br>
      MYSQL Version <?php echo $mysql_version; if(!empty($config_mode)) echo $config_mode; ?><br>
      Dynamic Time Version <?php echo $dyt_version.' '.$dyt_version_type.' '.$dyt_pro_version; ?><br>
      Dynamic Time db <?php echo $dyt_db_version;?><br>
      <br>
      <b>Settings</b><br>
      Prompt <?php echo $prompt;?><br>
      Notes <?php echo $notes;?><br>
      Period <?php echo $period;?><br>
      Week Begin <?php echo $weekbegin;?><br>
      Payroll <?php echo $payroll_id;?><br>
      Currency <?php echo $currency;?><br>
      DropData <?php echo $dropdata;?><br>
      Survey <?php echo $hide_survey;?><br>
      Users <?php echo $user_count;?><br>
      Supv <?php echo $sup_count;?><br>
    </div>
    <br><input type='button' class='button caps' style='width:100%' value='copy diagnostics' id='dyt_diag_copy' onclick="dyt_copy('dyt_diag_data',this.id);">
    <div class='caps' style='color:#1177aa;font-weight:normal;padding:1em'>Please provide diagnostics when submitting issues.</div>
    <a class='dyt_link caps' href="https://richardlerma.com/r1cm/" target='_blank'>contact support</a>
  </div>

  <div id='dyt_pro' class='dyt_control dyt_expand' style='float:right'>
    Get <span class='caps'>Dynamic Time <span class='pro'>Pro</span></span><span class="dashicons dashicons-chart-line" style='color:#b71b8a;vertical-align:text-bottom'></span>
    <a onclick="dyt_expand('dyt_pro');" style='text-decoration:none;float:right'><span class="dashicons dashicons-dismiss"></span></a><hr>
    <br>
      <strong>Features</strong>
      <ul style='padding:unset'>
        <li>Auto Entry records time automatically if user is active in WordPress.
        <li>Copy & paste data into Excel or spreadsheet
        <li>Table-based overview. Filter by employee and pay period.
        <li>View and search employee note fields on one page.
        <li>Dedicated support with guaranteed 12 hour response time (~6 hour average)
        <li>Access to all FUTURE PRO features
        <li>PRO add-on installs quickly over existing plugin
      </ul>
    <?php if($dyt_version_type!='PRO') { ?><div style='line-height:3em'><div class='caps'>To upgrade, please agree to the following:</div><input id='pro_terms' type='checkbox'>I've <a href='<?php echo get_admin_url(null,'update-core.php');?>' target='_blank'>checked for updates</a>. My existing version of Dynamic Time is current.</div><?php } ?>
    <div><input id='pro_cmp' type='checkbox' <?php if($dyt_version_type=='PRO') echo 'checked disabled'; ?>>I understand the PRO <a href='https://richardlerma.com/r1cm/?dyt=terms' target='_blank'>terms & conditions</a>.</div>
    
    <?php if($dyt_version_type=='PRO') { ?>
      <div class='attn caps' style='font-weight:normal'><span class='dashicons dashicons-yes'></span> Thanks for using Pro!</div>
      <input type='button' class='button' value='Check for Updates' onclick="window.location.href='<?php echo get_admin_url(null,'admin.php?page=dynamic-time&pro_update=1');?>';"><?php 
    } else { ?><a onclick="if(document.getElementById('pro_terms').checked && document.getElementById('pro_cmp').checked) return true; else {alert('Please verify your existing version is current and agree to terms & conditions'); return false;}" class='dyt_link caps' style='margin-top:1em' href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UZDNT7Q8KD8P6" target='_blank'>Purchase Pro</a><br><?php } ?>
  </div>

  <div id='dyt_setup' class='dyt_control dyt_expand' style='float:left'>
    <span style='color:#0073AA;font-weight:bold'><span class="dashicons dashicons-admin-plugins"></span> Get Started</span>
    <a onclick="dyt_expand('dyt_setup');" style='text-decoration:none;float:right'><span class="dashicons dashicons-dismiss"></span></a><hr>
    <ul style='margin-left:1em'>
      <li><span class='setup_order'>&#10112;</span> Customize your organization's setup in the Settings module.
      <li><span class='setup_order'>&#10113;</span> Timecards are available to all users in the WordPress dashboard menu (Only Admins and assigned Supvs can see other timesheets).
      <li><span class='setup_order'>&#10114;</span> To publish the timesheet outside of the WordPress dashboard, use shortcode <a id='dyt_shortcode' style='display:inline-block' href='#!' onclick="dyt_copy(this.id,this.id);">[dynamicTime]</a> in a page. 
      <li><span class='setup_order'>&#10115;</span> <a target='_blank' href='<?php echo str_replace('&amp;','&',wp_nonce_url(get_admin_url(null,'admin.php?page=dynamic-time'),'view_user','dyt_view_user')).'&dyt_user='.get_current_user_id();?>'>Open your timesheet</a>. Press save to create the first record.
      <li><span class='setup_order'>&#10116;</span> Employees automatically show up in "<b>User Entries</b>" after saving a time card. Refresh <a href='#!' onclick="if(window.location.href.indexOf('dyt_view_user')>0) window.location='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>'; else location.reload(true);" title='Refresh'><span style='opacity:.5' class="spin dashicons dashicons-update"></span></a> to check for new entries.
    </ul>
    <br><span class="dashicons dashicons-warning" style='color:orange'></span> Important for all users: Dynamic Time may perform unexpectedly or inaccurately if accessed in private/incognito browsing mode.<br>
    <br><span class="dashicons dashicons-format-status"></span> Questions or concerns? View <a href='#!' onclick="dyt_expand('dyt_diag');">Support & Diagnostics</a>
  </div>

  <?php if($dyt_version_type=='PRO' && current_user_can('list_users') && function_exists('dyt_pro')) dyt_pro($prompt,$notes); ?>
  <table id='usersettings' cellspacing='0' cellpadding='0' class='dyt_control'>
    <tr style='background:transparent'>
      <th colspan='<?php if($period<0) echo '8'; else echo '7'; ?>' align='left'>
        &#128338; User Entries &nbsp;
        <form style='display:inline' method='post' accept-charset='UTF-8 ISO-8859-1' action='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>'>
          <select style='height:2em;min-width:20em;font-size:.9em;border:none;font-weight:normal' name='period_range' onchange="this.form.submit();"><option value='0' selected>Users Active in Last Month<option value='1' <?php if($period_range>0) echo 'selected'; ?>>Display All</select>
        </form><hr>
      </th>
      <th><a href='#!' onclick="if(window.location.href.indexOf('dyt_view_user')>0) window.location='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>'; else location.reload(true);" title='Refresh'><span style='float:right;opacity:.5;margin:-1.5em -1em 0 0' class="spin dashicons dashicons-update"></span></a></th>
    </tr><?php 
    if($user_count>0) { ?>
    <tr style='background:transparent'>
      <th class='col_name'>Name</th>
      <th class='col_name'>Rate</th>
      <th class='col_name'>Status</th><?php 
      if($period<0) { ?><th class='col_name'>Period</th><?php } ?>
      <th class='col_name'>Supervisor</th>
      <th class='col_name'>Submitted</th>
      <th class='col_name'>Approved</th>
      <th class='col_name'>Processed</th>
      <th class='col_name' align='right' style='min-width:70px;'>View</th>
    </tr><?php 

    $row_id=1; 
    foreach($get_users as $row) {
      if(strpos($row->name,'[wp ')!==false) $disabled="class='dyt_disable'"; else $disabled='';
      $uid=$row->WP_UserID; 
      $sid=$row->supervisor_id;
      $cid=dyt_userid();
      if($cid!=$sid && $cid!=$uid && !current_user_can('list_users')) continue; ?>

      <form class='form' name='userconfig' id='userconfig<?php echo $uid;?>' method='post' accept-charset='UTF-8 ISO-8859-1' action='<?php echo get_admin_url(null,'admin.php?page=dynamic-time');?>&wp=0'>
        <?php echo wp_nonce_field('config_user','dyt_config_user');?>
        <input type='hidden' name='wp_userid' value='<?php echo $uid;?>'>
        <tr class='<?php if($row_id % 2===0) echo 'odd'; else echo 'even';?>'>

          <td nowrap>
            <a title='User Account' style='opacity:.3' target='_blank' href='../wp-admin/user-edit.php?user_id=<?php echo $uid;?>' <?php echo $disabled;?>> &#128100;</a> &nbsp;
            <a title='Email' target='_blank' href='mailto:<?php echo $row->email;?>' <?php echo $row->email;?>' <?php echo $disabled;?>> &#9993;</a> &nbsp;
            <a title='View Timecard' class='view' <?php echo $disabled;?> onclick="dyt_switchScreen('<?php echo $uid;?>');"><?php echo $row->name;?></a>
          </td>

          <td nowrap>
            <?php if(!$row->Rate>0) echo '&#9888;'; else  echo $currency; ?>
            <input type='number' <?php if($cid==$uid && !current_user_can('list_users')) echo "style='pointer-events:none'"; ?> required step='0.01' min='0' max='150' value='<?php echo $row->Rate;?>' name='rate' class='rate' placeholder='Hourly Rate' onchange="dyt_config('userconfig<?php echo $uid;?>');">
          </td>

          <td nowrap>
            <select required name='exempt' onchange="dyt_config('userconfig<?php echo $uid;?>');">
              <option disabled  <?php if(!isset($row->Exempt)) echo 'selected';?>>Select Status
              <option value='0' <?php if($row->Exempt===0) echo 'selected';?> title='Overtime for hours worked in excess of 40/week.'>Overtime Eligible (Standard FLSA &#9432;)
              <option value='-1' <?php if($row->Exempt<0) echo 'selected';?> title='Overtime for hours worked in excess of 8/day OR 40/week.'>Overtime Eligible (California &#9432;)
              <option value='1' <?php if($row->Exempt==1) echo 'selected';?> title='Not overtime eligible'>Exempt &#9432;
            </select>
            <?php if(!isset($row->Exempt)) echo '<br>&#9888; Status Not Set';?>
          </td><?php 

          if($period<0) { ?>
            <td nowrap>
              <select name='user_period' id='user_period' onchange="dyt_config('userconfig<?php echo $uid;?>');">
                <option value='' disabled>Select Pay Period
                <option value='7'  <?php if($row->Period==7 ) echo 'selected';?> title='Pay periods are every week (52 per year).'>Weekly Pay Period &#9432;
                <option value='14' <?php if($row->Period==14) echo 'selected';?> title='Pay periods are in two week groups (26 per year).'>BiWeekly Pay Period &#9432;
                <option value='15' <?php if($row->Period==15) echo 'selected';?> title='Pay periods are twice per calendar month (24 per year).'>Semi-Monthly Pay Period &#9432;
                <option value='30' <?php if($row->Period==30) echo 'selected';?> title='Pay periods are every calendar month (12 per year)'>Monthly Pay Period &#9432;
              </select>
              <?php if(!isset($row->Period)) echo '<br>&#9888; Period Not Set';?>
            </td><?php 
          } ?>

          <td nowrap>
            <select value='<?php echo $sid;?>' <?php if(!current_user_can('list_users')) echo "style='pointer-events:none'"; ?> name='supervisor_id' title="<?php echo $row->supervisor_name;?>" onchange="dyt_config('userconfig<?php echo $uid;?>');">
              <option value='0' disabled title='A supervisor will receive notification when a pay period is submitted.'>Supervisor &#9432;
              <option value='0' <?php if(!$sid>0) echo 'selected';?>>None
              <?php echo dyt_user_dropdown('supervisor',$sid);?>
            </select>
          </td>

          <td nowrap align='right'><a class='view' onclick="dyt_switchScreen('<?php echo $uid;?>');"><?php echo $row->Submitted;?></a></td>
          <td nowrap align='right'><a class='view' onclick="dyt_switchScreen('<?php echo $uid;?>');"><?php echo $row->Approved;?></a></td>
          <td nowrap align='right'><a class='view' onclick="dyt_switchScreen('<?php echo $uid;?>');"><?php echo $row->Processed;?></a></td>
          <td align='right'><a title='View Timecard' class='view budge' style='font-size:2em;' onclick="dyt_switchScreen('<?php echo $uid;?>');">&#10162;</a></td>
        </tr>
      </form><?php 
      $row_id++;
    }} else echo "<tr><td colspan='7' style='width:300px;text-align:center;'>No Entries Yet<br><br>&#9888; Follow instructions under Get Started</td></tr>"; ?>
  </table>

  <?php if($dyt_setup==0 && $user_count>0 && $hide_survey!=1) { ?>
  <input type='button' class='button' style='width:100%' id='dyt_survey_button' value='Hide Survey'
    onclick="
      if(document.getElementById('hide_survey')) {
        if(document.getElementById('hide_survey').value==1) document.getElementById('hide_survey').value=0;
        else document.getElementById('hide_survey').value=1;
      }
      if(document.getElementById('timeconfig')) document.getElementById('timeconfig').submit();">
  <?php } ?>
</div>

<?php if($hide_survey!=1 && $user_count>0 && $dyt_setup==0) {?><iframe id='dyt_survey' style='width:100%;height:50em' src='https://www.surveymonkey.com/r/DB9PXDN'></iframe><?php } ?>

<script type='text/javascript'>
  var dyt_user='<?php echo $dyt_user;?>';<?php 
  if($dyt_version_type=='PRO') {$pro=get_option('dyt_pro'); if($pro=='new') update_option('dyt_pro','yes','no');} else $pro='no';
  if($pro=='new' || ($dyt_version_type=='GPL' && isset($_GET['updated']) && function_exists('gzcompress'))) { ?>
    var dyt_version_Interval=setInterval(function() {
      if(document.readyState==='complete') {clearInterval(dyt_version_Interval);dyt_expand('dyt_pro');window.location.hash='#dyt_pro';}
    },200);<?php 
  }

  if(!$dyt_user>0 && ($dyt_setup>0 || $user_count<1)) { ?>
    var dyt_setup_Interval=setInterval(function() {
      if(document.readyState==='complete') {clearInterval(dyt_setup_Interval);dyt_expand('dyt_setup');}
    },200);
  <?php } ?>

  if('<?php echo $input_saved;?>'>0) {
    save_msg=document.getElementById('input_saved');
    save_msg.style.opacity=1;
    save_msg.style.display='block';
    save_msg.innerHTML='Saved';
    setTimeout(function(){save_msg.style.opacity=0;},2000);
    setTimeout(function(){save_msg.style.display='none';},3000);
  }

  function dyt_switchScreen(screen) {
    var user;
    if(screen!='dyt_admin') {user=screen; screen='dyt_cal_admin';}
    if(dyt_user!=user && screen=='dyt_cal_admin') {
      dyt_config('userconfig');
      var url="<?php echo str_replace('&amp;','&',wp_nonce_url(get_admin_url(null,'admin.php?page=dynamic-time'),'view_user','dyt_view_user')); ?>";
      window.location=url+"&dyt_user="+user;
      return;
    }
    if(summary_updated>0 && screen=='dyt_admin') if(!confirm('You have unsaved changes, are you sure you would like to exit?')) return false;
    document.getElementById('dyt_cal_admin').style.opacity='0';
    document.getElementById('dyt_admin').style.opacity='0';
    setTimeout(function(){
      document.getElementById('dyt_return').style.display='none';
      document.getElementById('dyt_cal_admin').style.display='none';
      document.getElementById('dyt_admin').style.display='none';
    },100);
    
    setTimeout(function(){
      document.getElementById(screen).style.display='block';
      if(screen=='dyt_cal_admin')document.getElementById('dyt_return').style.display='block';
      setTimeout(function(){document.getElementById(screen).style.opacity='1';},100);
    },101);
  }

  function dyt_config(fid,sid,sel_index) {
    var setup_saved='<?php echo $dyt_setup;?>';
    if(fid=='timeconfig') {
      if(sel_index>0) document.getElementById(sid+'_sel').innerHTML='&#10004';
      if(document.getElementById('timeconfig').innerHTML.indexOf('26a0')!=-1) return false;
    }
    document.getElementById('dyt_admin').style.opacity='.3';
    
    if(document.getElementById('dyt_head')) var dyt_head=document.getElementById('dyt_head');
    if(dyt_head) dyt_head.innerHTML=dyt_head.innerHTML+"<progress id='loading' style='float:left;width:100%;' max='100'></progress>";
    if(setup_saved>0) { setTimeout(function(){ if(document.getElementById(fid)) document.getElementById(fid).submit(); },2000);}
    else if(document.getElementById(fid)) document.getElementById(fid).submit();
  }

  function dyt_copy(content_id,button_id) {
    document.getElementById(button_id).disabled=true;
    var contents=document.getElementById(content_id).innerHTML;
    
    if(contents.indexOf('&#9986;')>0) return false;
    var tmpEl;
    var copy_temp='copy_temp'+Math.floor((Math.random()*1000)+1);
    
    tmpEl=document.createElement(copy_temp);
    tmpEl.style.opacity=0;
    tmpEl.style.position="absolute";
    tmpEl.style.pointerEvents="none";
    tmpEl.style.zIndex=-1;
    tmpEl.innerHTML=contents;
    document.body.appendChild(tmpEl);

    var range=document.createRange();
    range.selectNode(tmpEl);
    
    var w=window.getSelection();
    if(w.rangeCount>0) w.removeAllRanges();
    w.addRange(range);

    if(!document.execCommand("copy")) {
      alert('Unable to auto-copy this content.\nPlease copy this content manually.');
      return false;
    }
    document.body.removeChild(tmpEl);
    
    var confirm="<div class='caps' style='position:absolute;background:#17A;color:#DDD;padding:2em;border-radius:3px;box-shadow:0px 3px 5px #888;z-index:99'>&#9986; Copied to clipboard!</div>";
    var opaque="<div style='opacity:.3;height:100%;width:100%;'>"+contents+"</div>";
    
    document.getElementById(content_id).innerHTML=confirm+opaque;
    
    setTimeout(function(){
      document.getElementById(content_id).innerHTML=contents;
      document.getElementById(button_id).disabled=false;
    },2000);
  }

  function dyt_expand(id) {
    if(!document.getElementById(id)) return false;
    var is_admin=1; if(document.getElementById('dyt_return')) {if(document.getElementById('dyt_return').style.display!='none') is_admin=0;}
    var target=document.getElementById(id);
    if(is_admin==0) dyt_switchScreen('dyt_admin');
    if(target.style.opacity==1 && is_admin==0) {target.style.opacity='0'; dyt_expand(id); return;}
    if(target.style.opacity!=1) {
      target.style.display='block';
      setTimeout(function(){
        target.style.opacity='1';
        target.style.maxHeight='99em';
        target.style.padding=target.style.overflow='';
      },10);
    } else {
      target.style.maxHeight=target.style.opacity=target.style.padding='0';
      target.style.overflow='none';
      setTimeout(function(){target.style.display='none';},500);
    }
  }
</script>