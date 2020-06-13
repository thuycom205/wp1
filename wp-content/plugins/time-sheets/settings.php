<?php
class time_sheets_settings {

	function employees_allways_to_payroll() {
		if ($_POST['action']=="save"){
			$this->employees_always_to_payroll_update_emps();
		}

		$this->employees_always_to_payroll_show_emps();
	}

	function employees_always_to_payroll_update_emps() {
		global $wpdb;
		$db = new time_sheets_db();

		check_admin_referer( 'add_user_to_payroll');

		$sql = "select u.ID, u.user_login, eatp.user_id, u.display_name
			from {$wpdb->users} u
			left outer join {$wpdb->prefix}timesheet_employee_always_to_payroll eatp on u.ID = eatp.user_id
			order by u.user_login";

		$users = $db->get_results($sql);
		if ($users) {
			foreach ($users as $user) 
			{
				$id = "user_{$user->ID}";

				if ($_POST[$id]=="1" && $user->user_id == "") {
					$sql = "INSERT INTO {$wpdb->prefix}timesheet_employee_always_to_payroll (user_id) VALUES ({$user->ID})";
					$db->query($sql);
				}

				IF ($_POST[$id]!="1" && $user->user_id != "") {
					$sql = "DELETE FROM {$wpdb->prefix}timesheet_employee_always_to_payroll WHERE user_id = {$user->ID}";
					$db->query($sql);
				}
			}
		}

		echo '<div if="message" class="updated">Employees Updated.</div>';
	}

	function employees_always_to_payroll_show_emps() {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.ID, u.display_name, u.user_login, eatp.user_id, u.display_name
			from {$wpdb->users} u
			left outer join {$wpdb->prefix}timesheet_employee_always_to_payroll eatp on u.ID = eatp.user_id
			order by u.display_name";

		$users = $db->get_results($sql);

		if ($users) {
			echo "<BR><BR><form method='POST' name='force_employee'><table border='1' cellspacing='0'><tr align='center'><td><b>Force Employee<br>Timesheets to<br>Payroll</b></td><td><b>Employee</b></td><td><b>User Name</b></td></tr>";
			foreach ($users as $user) {
				echo "<tr><td align='center'><input type='checkbox' name='user_{$user->ID}' value='1'";
				if ($user->user_id) {
					echo " checked";
				}
				echo "></td><td>{$user->display_name}</td><td>{$user->user_login}</td></tr>";
			}
			echo "</table><input type='submit' name='submit' value='Save Settings' class='button-primary'>
<input type='hidden' name='page' value='employees_allways_to_payroll'><input type='hidden' name='action' value='save'>";

			wp_nonce_field( 'add_user_to_payroll' );

			echo "</form>";
		} else {
			echo "You have no logins, this shouldn't be possible. Something is very wrong.";
		}
	}

	

	function setup_approval_teams() {
		if ($_GET['action']=='add') {
			$this->add_member_to_team();
		}
		if ($_GET['action']=='delete') {
			$this->remove_member_from_team();
		}

		$this->show_users_as_team_leads();

		if ($_GET['approver_user_id']) {
			$this->show_leads_team(intval($_GET['approver_user_id']));
		}
	}

	function add_member_to_team() {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "insert into {$wpdb->prefix}timesheet_approvers_approvies (approver_user_id, approvie_user_id)
			values (%d, %d)";
		$values = array(intval($_GET['approver_user_id']), intval($_GET['approvie_user_id']));

		$db->query($sql, $values);

	}

	function remove_member_from_team() {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "delete from {$wpdb->prefix}timesheet_approvers_approvies where approver_user_id = %d and approvie_user_id = %d";
		$values = array(intval($_GET['approver_user_id']), intval($_GET['approvie_user_id']));

		$db->query($sql, $values);
	}

	function show_leads_team($approver_user_id){
		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();
		$folder = plugins_url();

		$users = $common->return_approvers_team_list($approver_user_id, 0);

		echo "<table border='1' cellpadding='0' cellspacing='0'><tr><td>Add Team Member</td><td>Remove Team Member</td><td>User</td></td><td>User Name</td></tr>";
		foreach ($users as $user) {
			echo "<tr><td align='center'>";
			if (!$user->a) {
				echo "<a href='admin.php?page=setup_approval_teams&action=add&approvie_user_id={$user->id}&approver_user_id={$approver_user_id}'><img src='{$folder}/time-sheets/check.png' height='13' width='13'></a>";
			}
			echo "</td><td align='center'>";
			if ($user->a) {
				echo "<a href='admin.php?page=setup_approval_teams&action=delete&approvie_user_id={$user->id}&approver_user_id={$approver_user_id}'><img src='{$folder}/time-sheets/x.png' height='13' width='13'></a>";
			}
			echo "</td><td>{$user->display_name}</td><td>{$user->user_login}</td></tr>";
		}
		echo "</table>";

	}

	function show_users_as_team_leads() {
		global $wpdb;
		$db = new time_sheets_db();
		$common = new time_sheets_common();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers ta ON u.id = ta.user_id
		order by u.display_name";

		$users = $db->get_results($sql);

		echo "<form method='GET'>";
		echo "Select Approver To Modify: ";
		echo "<select name='approver_user_id'>";

		foreach ($users as $user) {
			echo "<option value='{$user->id}'{$common->is_match($user->id, $_GET['approver_user_id'], ' SELECTED')}>{$user->display_name} ({$user->user_login})</option>";
		}
		echo "</select>";
		echo "<input type='hidden' name='page' value='setup_approval_teams'>";
		echo '<br class="submit"><input name="submit" type="submit" class="button-primary" value="Select User" /></div>';
		echo "</form>";
	}


	function show_settings_page() {
		settings_errors();
		echo '<div class="wrap">';
		echo '<H2>Time Sheets Settings</H2>';

		echo '<form name="settingsFrm" action="options.php" method="post">';
		settings_fields('time_sheets');
		do_settings_sections('time_sheets');
		echo '<p class="submit"><input name="submit" type="submit" class="button-primary" value="Save Changes" /></form>
<br>
<br>If you wish to display the New Timesheet form on a page within your site, use the shortcode "[timesheet_entry]" within the page.
<br>If you wish to display the Open Timesheets form on a page within your site, use the shortcode "[timesheet_search]" within the page.
<br>Both these shortcodes will only work if the user is logged in, and will display an error message if the user is not logged in.</div>';
	}

	function settings_header_email() {
		echo "Email settings for the time sheets application.";
	}

	function settings_validate($input) {
		return $input;
	}

	function settings_header_automation() {
		echo "";
	}

	function settings_header_display() {
		echo "";
	}

	function user_can_enter_notes($user_id, $setting, $array_setting) {
		$options = get_option('time_sheets');
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.user_login, ta.user_id a, u.id
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers_approvies taa on u.id = taa.approvie_user_id
		join {$wpdb->prefix}timesheet_approvers ta ON taa.approver_user_id = ta.user_id
		where u.ID = {$user_id}";

		$users = $db->get_results($sql);

		$array_value = $options[$array_setting];

		if (!$array_value) {
			$array_value = array();
		}

		if ($users) {
			foreach ($users as $user) {
				if ($options[$setting]=='all_except') {
					if (in_array($user->id, $array_value)) {
						return 0;
					}
				} else { //only_these
					if (in_array($user->id, $array_value)) {
						return 1;
					}
				}
			}
			if ($options[$setting]=='all_except') {
				return 1;
			} else { //only_these
				return 0;
			}
		} else {
			if ($options[$setting]=='all_except') {
				return 1;
			} else { //only_these
				return 0;
			}
		}		
	}

	function display_notes_list() {
		$options = get_option('time_sheets');
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers ta ON u.ID = ta.user_id
		order by u.user_login";

		$users = $db->get_results($sql);

		$rows = count($users);

		if ($rows > 6) {
			$rows = 6;
		}

		echo "<select multiple size='{$rows}' name='time_sheets[display_notes_list][]'>";

		foreach ($users as $user) {
			$match = "";
			if (in_array($user->id, $options['display_notes_list'])) {
				$match = " selected";
			}
			echo "<option value='{$user->id}'{$match}>{$user->display_name} ({$user->user_login})</option>";
		}

		echo "</select> (Hold control key for multiple values)";
	}

	function display_notes_toggle() {
		$options = get_option('time_sheets');

		if ($options['display_notes_toggle'] == 'all_except' || $options['display_notes_toggle'] == '') {
			$all_except = ' checked';
		} else {
			$only_these = ' checked';
		}
		echo "<input type='radio' name='time_sheets[display_notes_toggle]' value='all_except'{$all_except}> All groups except... <BR>
<input type='radio' name='time_sheets[display_notes_toggle]' value='only_these'{$only_these}> Only these groups...";
	}

	function display_expenses_list() {
		$options = get_option('time_sheets');
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers ta ON u.ID = ta.user_id
		order by u.user_login";

		$users = $db->get_results($sql);

		$rows = count($users);

		if ($rows > 6) {
			$rows = 6;
		}

		echo "<select multiple size='{$rows}' name='time_sheets[display_expenses_list][]'>";

		foreach ($users as $user) {
			$match = "";
			if (in_array($user->id, $options['display_expenses_list'])) {
				$match = " selected";
			}
			echo "<option value='{$user->id}'{$match}>{$user->display_name} ({$user->user_login})</option>";
		}

		echo "</select> (Hold control key for multiple values)";
	}



	function display_expenses_toggle() {
		$options = get_option('time_sheets');

		if ($options['display_expenses_toggle'] == 'all_except' || $options['display_expenses_toggle'] == '') {
			$all_except = ' checked';
		} else {
			$only_these = ' checked';
		}
		echo "<input type='radio' name='time_sheets[display_expenses_toggle]' value='all_except'{$all_except}> All groups except... <BR>
<input type='radio' name='time_sheets[display_expenses_toggle]' value='only_these'{$only_these}> Only these groups...";
	}

	function blank() {

	}

	function day_of_week_timesheet_reminders() {
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		echo "<select name='time_sheets[day_of_week_timesheet_reminders]'>
			<option value='0'{$common->is_match($options['day_of_week_timesheet_reminders'], 0, ' selected')}>Sunday</option>
			<option value='1'{$common->is_match($options['day_of_week_timesheet_reminders'], 1, ' selected')}{$common->is_match($options['day_of_week_timesheet_reminders'], '', ' selected')}>Monday</option>
			<option value='2'{$common->is_match($options['day_of_week_timesheet_reminders'], 2, ' selected')}>Tuesday</option>
			<option value='3'{$common->is_match($options['day_of_week_timesheet_reminders'], 3, ' selected')}>Wednesday</option>
			<option value='4'{$common->is_match($options['day_of_week_timesheet_reminders'], 4, ' selected')}>Thursday</option>
			<option value='5'{$common->is_match($options['day_of_week_timesheet_reminders'], 5, ' selected')}>Friday</option>
			<option value='6'{$common->is_match($options['day_of_week_timesheet_reminders'], 6, ' selected')}>Saturday</option>
		</select>";
	}

	function week_starts() {
	$options = get_option('time_sheets');
		$common = new time_sheets_common();

		echo "<select name='time_sheets[week_starts]'>
			<option value='0'{$common->is_match($options['week_starts'], 0, ' selected')}>Sunday</option>
			<option value='1'{$common->is_match($options['week_starts'], 1, ' selected')}{$common->is_match($options['week_starts'], '', ' selected')}>Monday</option>
			<option value='2'{$common->is_match($options['week_starts'], 2, ' selected')}>Tuesday</option>
			<option value='3'{$common->is_match($options['week_starts'], 3, ' selected')}>Wednesday</option>
			<option value='4'{$common->is_match($options['week_starts'], 4, ' selected')}>Thursday</option>
			<option value='5'{$common->is_match($options['week_starts'], 5, ' selected')}>Friday</option>
			<option value='6'{$common->is_match($options['week_starts'], 6, ' selected')}>Saturday</option>
		</select>";
	}

	function register_settings() {
		register_setting('time_sheets', 'time_sheets', array(&$this, 'settings_validate'));

		add_settings_section('time_sheets_automation', __('Automation', ''), array(&$this, 'settings_header_automation'), 'time_sheets');
		add_settings_field('cron_user', __('User For Automatic Entries:', ''), array(&$this, 'cron_user'), 'time_sheets', 'time_sheets_automation');

		add_settings_section('time_sheets_display', __('Display Settings', ''), array(&$this, 'settings_header_display'), 'time_sheets');
		add_settings_field('display_notes_toggle', __('Display notes fields to:', ''), array(&$this, 'display_notes_toggle'), 'time_sheets', 'time_sheets_display');
		add_settings_field('display_notes_list', __('Notes To/Except list (based on prior setting):', ''), array(&$this, 'display_notes_list'), 'time_sheets', 'time_sheets_display');
		add_settings_field('display_expenses_toggle', __('Display expenses fields to:', ''), array(&$this, 'display_expenses_toggle'), 'time_sheets', 'time_sheets_display');
		add_settings_field('display_expenses_list', __('Expenses To/Except list (based on prior setting):', ''), array(&$this, 'display_expenses_list'), 'time_sheets', 'time_sheets_display');
		add_settings_field('menu_location', __('Time Sheets below:', ''), array(&$this, 'menu_location'), 'time_sheets', 'time_sheets_display');
		add_settings_field('users_override_location', __('Users can override menu location:', ''), array(&$this, 'users_override_location'), 'time_sheets', 'time_sheets_display');
		add_settings_field('show_header_queues', __('Show Queues in Admin Header:', ''), array(&$this, 'show_header_queues'), 'time_sheets', 'time_sheets_display');
		add_settings_field('show_header_open_invoices', __('Show Users Open Timesheets in Admin Header:', ''), array(&$this, 'show_header_open_invoices'), 'time_sheets', 'time_sheets_display');
		add_settings_field('rel_url_to_timesheet', __('Relative URL to Timesheet Entry Page:', ''), array(&$this, 'rel_url_to_timesheet'), 'time_sheets', 'time_sheets_display');
		add_settings_field('override_date_format', __('Override Site Wide Date Format:', ''), array(&$this, 'override_date_format'), 'time_sheets', 'time_sheets_display');
		add_settings_field('new_date_format', __('New Date Format:', ''), array(&$this, 'new_date_format'), 'time_sheets', 'time_sheets_display');
		add_settings_field('user_specific_date_format', __('Allow users to set their own date format:', ''), array(&$this, 'user_specific_date_format'), 'time_sheets', 'time_sheets_display');
		add_settings_field('hide_client_project', __('Hide the Client and Project fields if only one option:', ''), array(&$this, 'hide_client_project'), 'time_sheets', 'time_sheets_display');
		add_settings_field('remove_embargo', __('Remove embargo options:', ''), array(&$this, 'remove_embargo'), 'time_sheets', 'time_sheets_display');
		add_settings_field('week_starts', __('Week starts:', ''), array(&$this, 'week_starts'), 'time_sheets', 'time_sheets_display');
		add_settings_field('queue_order', __('Payroll Queue Processing:', ''), array(&$this, 'queue_order'), 'time_sheets', 'time_sheets_display');
		add_settings_field('sales_override', __('Sales Person Priorty:', ''), array(&$this, 'sales_override'), 'time_sheets', 'time_sheets_display');


		add_settings_section('time_sheets_retainer', __('Retainer Settings', ''), array(&$this, 'blank'), 'time_sheets');
		add_settings_field('update_retainer_hours', __('Update allowed hours for retainers monthly:', ''), array(&$this, 'update_retainer_hours'), 'time_sheets', 'time_sheets_retainer');

		add_settings_section('time_sheets_basics', __('Email Settings', ''), array(&$this, 'settings_header_email'), 'time_sheets');
		add_settings_field('enable_email', __('Enable Email:', ''), array(&$this, 'enable_email'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('email_late_timesheets', __('Email users when timesheets are overdue:', ''), array(&$this, 'email_late_timesheets'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('email_retainer_due', __('Email users that retainer timesheets are due:', ''), array(&$this, 'email_retainer_due'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('email_from', __('From Email Address:', ''), array(&$this, 'email_from'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('email_name', __('From Email Name:', ''), array(&$this, 'email_name'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('show_email_notice', __('Show Notice On Email Send:', ''), array(&$this, 'show_email_notice'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('hide_dcac_ad', __('Hide Application Ad:', ''), array(&$this, 'hide_dcac_ad'), 'time_sheets', 'time_sheets_basics');
		add_settings_field('day_of_week_timesheet_reminders', __('Day of week for Time Sheet Reminders:', ''), array(&$this, 'day_of_week_timesheet_reminders'), 'time_sheets', 'time_sheets_basics');


		add_settings_section('time_sheets_process_payroll', __('Payroll Triggers', ''), array(&$this, 'process_payroll'), 'time_sheets');
		add_settings_field('mileage', __('Mileage', ''), array(&$this, 'mileage'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('per_diem', __('Per Diem', ''), array(&$this, 'per_diem'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('flight_cost', __('Flight/Train Cost', ''), array(&$this, 'flight_cost'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('hotel', __('Hotel Charges', ''), array(&$this, 'hotel'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('rental_car', __('Rental Car', ''), array(&$this, 'rental_car'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('tolls', __('Tolls', ''), array(&$this, 'tolls'), 'time_sheets', 'time_sheets_process_payroll');
		add_settings_field('other_expenses', __('Other Expenses', ''), array(&$this, 'other_expenses'), 'time_sheets', 'time_sheets_process_payroll');
	}

	function sales_override() {
		$common = new time_sheets_common();
		$options = get_option('time_sheets');
		echo "<select name='time_sheets[sales_override]'>
				<option value='customer'{$common->is_match($options['sales_override'], 'customer', ' selected', 0)}>Customer is priority</option>
				<option value='project' {$common->is_match($options['sales_override'], 'project', ' selected', 0)}>Project is priority</option>
			</select>";
	}
	
	function queue_order() {
		$common = new time_sheets_common();
		$options = get_option('time_sheets');
		echo "<select name='time_sheets[queue_order]'>
			<option value='after'{$common->is_match($options['queue_order'], 'after', ' selected')}{$common->is_match($options['queue_order'], '', ' selected')}>After Invoicing</option>
			<option value='parallel'{$common->is_match($options['queue_order'], 'parallel', ' selected')}>In Parallel to Invoicing</option>
		</select>";
	}

	function remove_embargo() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[remove_embargo]' value=' checked'{$options['remove_embargo']}> This will not unembargo any time sheets which are embargoed. That needs to be done before this setting is enabled.";
	}

	function hide_client_project() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[hide_client_project]' value=' checked'{$options['hide_client_project']}> If checked and the user only has access to a single client and a single project then the client and project will be hidden from view.";
	}

	function override_date_format() {
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		if ($options['override_date_format'] <> 'system_defined' && $options['override_date_format'] <> 'admin_defined') {
			$options['override_date_format']= 'system_defined';
		}

		echo "<input type='radio' name='time_sheets[override_date_format]' onClick='toggleSetting()' value='system_defined' {$common->is_match($options['override_date_format'], 'system_defined', ' checked')}> Use the system wide settings<br>";
		echo "<input type='radio' name='time_sheets[override_date_format]' onClick='toggleSetting()' value='admin_defined' {$common->is_match($options['override_date_format'], 'admin_defined', ' checked')}> Use the setting defined below";

		echo "<script>
function toggleSetting() {
	if (settingsFrm.elements['time_sheets[override_date_format]'].value=='system_defined') {
		settingsFrm.elements['time_sheets[new_date_format]'].disabled=true;
	} else {
		settingsFrm.elements['time_sheets[new_date_format]'].disabled=false;
	}

}

toggleSetting();
			</script>";
	}

	function new_date_format() {
		$options = get_option('time_sheets');
		echo "<input type='text' name='time_sheets[new_date_format]'value='{$options['new_date_format']}'> <a href='https://codex.wordpress.org/Formatting_Date_and_Time'>Documentation on Date Formatting</a><BR>Use of a non-ISO date format can cause search issues when searching for old time sheets.";
	}

	function user_specific_date_format() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[user_specific_date_format]' value='checked' {$options['user_specific_date_format']}>";
	}

	function rel_url_to_timesheet() {
		$options = get_option('time_sheets');
		echo "<input type='text' name='time_sheets[rel_url_to_timesheet]' value='{$options['rel_url_to_timesheet']}'> (This setting is only needed when using shortcut codes for time sheet entry and time sheet search pages.)";
	}

	function show_header_open_invoices() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[show_header_open_invoices]' value='checked' {$options['show_header_open_invoices']}>";
	}

	function update_retainer_hours() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[update_retainer_hours]' value='checked' {$options['update_retainer_hours']}>";
	}

	function show_header_queues() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[show_header_queues]' value='checked' {$options['show_header_queues']}> (Queues are only shown when there are items in the queue to be processed.)";
	}

	function menu_location() {
		$options = get_option('time_sheets');
		$common = new time_sheets_common();
		echo "<select name='time_sheets[menu_location]'>
			<option value='1'{$common->is_match('1', $options['menu_location'], ' selected')}>Top Menu</option>
			<option value='3'{$common->is_match('3', $options['menu_location'], ' selected')}>Dashboard</option>
			<option value='6'{$common->is_match('6', $options['menu_location'], ' selected')}>Posts</option>
			<option value='11'{$common->is_match('11', $options['menu_location'], ' selected')}>Media</option>
			<option value='16'{$common->is_match('16', $options['menu_location'], ' selected')}>Links</option>
			<option value='21'{$common->is_match('21', $options['menu_location'], ' selected')}>Pages</option>
			<option value='26'{$common->is_match('26', $options['menu_location'], ' selected')}>Comments</option>
			<option value='61'{$common->is_match('61', $options['menu_location'], ' selected')}>Appearance</option>
			<option value='66'{$common->is_match('66', $options['menu_location'], ' selected')}>Plugins</option>
			<option value='71'{$common->is_match('71', $options['menu_location'], ' selected')}>Users</option>
			<option value='76'{$common->is_match('76', $options['menu_location'], ' selected')}>Tools</option>
			<option value='81'{$common->is_match('81', $options['menu_location'], ' selected')}>Settings</option>
			<option value='10000'{$common->is_match('10000', $options['menu_location'], ' selected')}>Bottom of List</option>
			<option value=''{$common->is_match('', $options['menu_location'], ' selected')}>Default Location - Where ever WordPress wants</option>
		</select>";
	}

	function users_override_location() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[users_override_location]' value='checked' {$options['users_override_location']}>";
	}

	function email_late_timesheets() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[email_late_timesheets]' value='checked' {$options['email_late_timesheets']}>";

	}

	function email_retainer_due() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[email_retainer_due]' value='checked' {$options['email_retainer_due']}>";
	}

	function flight_cost() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[flight_cost]' value='checked' {$options['flight_cost']}>";
	}

	function per_diem() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[per_diem]' value='checked' {$options['per_diem']}>";
	}

	function hotel() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[hotel]' value='checked' {$options['hotel']}>";
	}

	function rental_car() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[rental_car]' value='checked' {$options['rental_car']}>";
	}

	function tolls() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[tolls]' value='checked' {$options['tolls']}>";
	}

	function other_expenses() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[other_expenses]' value='checked' {$options['other_expenses']}>";
	}

	function mileage() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[mileage]' value='checked' {$options['mileage']}>";
	}

	function process_payroll() {
		echo "Select the Expense categories which should trigger timesheets being sent to the Payrol workflow.";
	}


	function get_holidays() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[get_holidays]' value='checked' {$options['get_holidays']}>Allows the application to call out to the Denny Cherry & Associates Consulting web servers to request a list of holidays in order to mark days in the timesheet as a holiday.";
	}

	function license_key() {
		$options = get_option('time_sheets');
		echo "<input type='text' name='time_sheets[license_key]' value='{$options['license_key']}'>";

		if (!$options['license_key']) {
			echo "<div if='message' class='error'><p>No license key was provided.  Application will be fully functional, however the number of clients and projects will be limited to two clients and 5 projects.</p></div>";
		}
	}

	function cron_user() {
		global $wpdb;
		$db = new time_sheets_db();
		$options = get_option('time_sheets');
		$sql = "select * from {$wpdb->users} u order by u.display_name";

		$users = $db->get_results($sql);

		echo "<select name='time_sheets[cron_user]'>";
		foreach ($users as $user) {
			echo "<option value='{$user->ID}'";
			if ($user->ID==$options['cron_user']) {
				echo " selected";
			}
			echo ">{$user->display_name} ({$user->user_login})</option>";
		}
		echo "</select>";
	}

	function show_email_notice() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[show_email_notice]' value='checked' {$options['show_email_notice']}> (Mostly used for troubleshooting.)";
	}

	function hide_dcac_ad() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[hide_dcac_ad]' value='checked' {$options['hide_dcac_ad']}>";
	}

	function email_name() {
		$options = get_option('time_sheets');
		echo "<input type='text' name='time_sheets[email_name]' value='{$options['email_name']}'>";
	}

	function email_from() {
		$options = get_option('time_sheets');
		echo "<input type='text' name='time_sheets[email_from]' value='{$options['email_from']}'><br>
		If you are having problems with this setting <a href='http://www.dcac.co/applications/wordpress-plugins/time-sheets/smtp-settings-wont-stay'>refer to this page</a>.";
	}

	function enable_email() {
		$options = get_option('time_sheets');
		echo "<input type='checkbox' name='time_sheets[enable_email]' value='checked' {$options['enable_email']}>";
	}

	function manage_approvers() {
		if ($_GET['action']=='add') {
			$this->add_person('approvers');
		}

		if ($_GET['action']=='delete') {
			$this->delete_person('approvers');
		}
		$this->show_approvers();
	}

	function add_person($table) {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "insert into {$wpdb->prefix}timesheet_{$table} (user_id) values (%d)";
		$params = array($_GET['user_id']);

		$db->query($sql, $params);
	}

	function delete_person($table) {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "delete from {$wpdb->prefix}timesheet_{$table} where user_id = %d";
		$params = array($_GET['user_id']);

		$db->query($sql, $params);
	}

	function show_approvers() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$folder = plugins_url();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		left outer join {$wpdb->prefix}timesheet_approvers ta ON u.ID = ta.user_id
		order by u.display_name";

		$users = $db->get_results($sql);
		echo "<br><table border='1' cellpadding='0' cellspacing='0'><tr><td><b>User<b></td><td><b>User Name</b></td><td><b>Current State</b></td><td><b>Add Permission</b></td><td><b>Remove Permission</b></td></tr>";
		foreach ($users as $user) {
			echo "<tr><td>{$user->display_name}</td><td>{$user->user_login}</td><td>";
			if ($user->a) {
				echo "Has Access";
			} else {
				echo "No Access";
			}

			echo "</td><td align='center'>";
			if (!$user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_approvers&action=add&user_id={$user->id}'><img src='{$folder}/time-sheets/check.png' height='13' width='13'></a>";
			}
			echo "</td><td align='center'>";
			if ($user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_approvers&action=delete&user_id={$user->id}'><img src='{$folder}/time-sheets/x.png' height='13' width='13'></a>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function show_invoicers() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$folder = plugins_url();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		left outer join {$wpdb->prefix}timesheet_invoicers ta ON u.ID = ta.user_id
		order by u.display_name";

		$users = $db->get_results($sql);
		echo "<br><table border='1' cellpadding='0' cellspacing='0'><tr><td><b>User</b></td><td><b>User Name</b></td><td><b>Current State</b></td><td><b>Add Permission</b></td><td><b>Remove Permission</b></td></tr>";
		foreach ($users as $user) {
			echo "<tr><td>{$user->display_name}</td><td>{$user->user_login}</td><td>";
			if ($user->a) {
				echo "Has Access";
			} else {
				echo "No Access";
			}

			echo "</td><td align='center'>";
			if (!$user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_invoicers&action=add&user_id={$user->id}'><img src='{$folder}/time-sheets/check.png' height='13' width='13'></a>";
			}
			echo "</td><td align='center'>";
			if ($user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_invoicers&action=delete&user_id={$user->id}'><img src='{$folder}/time-sheets/x.png' height='13' width='13'></a>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function show_payrollers() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$folder = plugins_url();

		$sql = "select u.user_login, ta.user_id a, u.id, u.display_name
		from {$wpdb->users} u
		left outer join {$wpdb->prefix}timesheet_payrollers ta ON u.ID = ta.user_id
		order by u.display_name";

		$users = $db->get_results($sql);
		echo "<br><table border='1' cellpadding='0' cellspacing='0'><tr><td><b>User</b></td><td><b>User Name</b></td><td><b>Current State</b></td><td><b>Add Permission</b></td><td><b>Remove Permission</b></td></tr>";
		foreach ($users as $user) {
			echo "<tr><td>{$user->display_name}</td><td>{$user->user_login}</td><td>";
			if ($user->a) {
				echo "Has Access";
			} else {
				echo "No Access";
			}

			echo "</td><td align='center'>";
			if (!$user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_payroll&action=add&user_id={$user->id}'><img src='{$folder}/time-sheets/check.png' height='13' width='13'></a>";
			}
			echo "</td><td align='center'>";
			if ($user->a) {
				echo "<a href='admin.php?page=time_sheets_manage_payroll&action=delete&user_id={$user->id}'><img src='{$folder}/time-sheets/x.png' height='13' width='13'></a>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function manage_payrollers() {
		if ($_GET['action']=='add') {
			$this->add_person('payrollers');
		}

		if ($_GET['action']=='delete') {
			$this->delete_person('payrollers');
		}
		$this->show_payrollers();
	}

	function manage_invoicers() {
		if ($_GET['action']=='add') {
			$this->add_person('invoicers');
		}

		if ($_GET['action']=='delete') {
			$this->delete_person('invoicers');
		}
		$this->show_invoicers();
	}
}
