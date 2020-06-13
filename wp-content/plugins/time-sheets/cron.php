<?php
class time_sheets_cron {

	function email_retainers_due() {
		//echo "starting email_retainers_due<br>";
		global $wpdb;
		$options = get_option('time_sheets');
		$entry = new time_sheets_entry();
		$db = new time_sheets_db();
		$common = new time_sheets_common();

		$daysinmonth = date('t');
		$today = date('m');

		if ($today != $daysinmonth) {
			return;
		}

		if ($options['update_retainer_hours']) {
			//echo "Fixing retainer hours avaialble.<br>";
			$sql = "update {$wpdb->prefix}timesheet_client_projects tcp
	set tcp.MaxHours = tcp.HoursUsed + (select MonthlyHours from {$wpdb->prefix}timesheet_recurring_invoices_monthly rim where tcp.ClientId = rim.client_id)
where tcp.IsRetainer = 1
order by tcp.ProjectId";

			//var_dump($sql);

			$db->query($sql);

		}

		if ($options['email_retainer_due']) {
			$sql = "select DISTINCT u.user_email
				from {$wpdb->prefix}timesheet t
				join {$wpdb->users} u on t.user_id = u.ID
				join {$wpdb->prefix}timesheet_client_projects tcp on t.ProjectId = tcp.ProjectId
					and tcp.IsRetainer = 1
				WHERE start_date > DATE_ADD(now(), INTERVAL -60 DAY)";

			$timesheets = $db->get_results($sql);

			if ($timesheets) {
				$subject = "Retainers for this month are due";
				$body = "Timesheets for your retainer clients are due today for the prior month. Please submit them by tonight so invoices can be processed in the morning for last month.";

				foreach ($timesheets as $timesheet) {
					$common->send_email ($timesheet->user_email, $subject, $body);
				}
			}

			$sql = "SELECT DISTINCT u.user_email
				FROM {$wpdb->users} u
				JOIN {$wpdb->prefix}timesheet_approvers ta on u.ID = ta.user_id
				UNION
				SELECT DISTINCT u.user_email
				FROM {$wpdb->users} u
				JOIN {$wpdb->prefix}timesheet_invoicers ti on u.ID = ti.user_id";

			$users = $db->get_results($sql);

			if ($users) {
				$subject = "Monthly retainer time sheets will be ready soon";
				$body = "Monthly time sheets should be ready for processing tomorrow morning. This is your reminder to process them."; 
				foreach ($users as $user) {
					$common->send_email($user->user_email, $subject, $body);
				}
			}

		}
	}

	function email_late_timesheets() {
		global $wpdb;
		$options = get_option('time_sheets');

		if (date('w') != $options['day_of_week_timesheet_reminders']) {
			return;
		}

		$entry = new time_sheets_entry();
		$common = new time_sheets_common();
		$db = new time_sheets_db();

		if ($options['email_late_timesheets']) {
			$sql = "select t.timesheet_id, u.user_email, DATE_ADD(start_date, INTERVAL 14 DAY)
				from {$wpdb->prefix}timesheet t
				join {$wpdb->users} u on t.user_id = u.ID
				where start_date > DATE_ADD(now(), INTERVAL -14 DAY)
					and week_complete = 0";
			$timesheets = $db->get_results($sql);

			if ($timesheets) {
				foreach ($timesheets as $timesheet) {
					$subject = "Timesheet pending completion";
					$body = "Timesheet <a href='http://$_SERVER[HTTP_HOST]/wp-admin/admin.php?page=enter_timesheet&timesheet_id={$timesheet->timesheet_id}'>{$timesheet->timesheet_id}</a> has not been marked as completed.<BR>";

					$common->send_email ($timesheet->user_email, $subject, $body);
				}
			}
		}
	}

	function process_email($all = NULL) {
		global $wpdb;
		$options = get_option('time_sheets');
		$db = new time_sheets_db();
		if ($all) {
			$sql = "select max(entered_on) entered_on from {$wpdb->prefix}timesheet_emailqueue";
		} else {
			$sql = "select max(entered_on) entered_on from {$wpdb->prefix}timesheet_emailqueue where entered_on not between date_add(now(),interval -5 minute) and now()";
		}
		$anything = $db->get_var ($sql);

		if (is_null($anything)) {
			echo "nothing to do";
			return;
		}

		if ($anything) {
			$sql = "select send_to, send_from_email, send_from_name, subject, count(*) ct
				from {$wpdb->prefix}timesheet_emailqueue
				group by send_to, send_from_email, send_from_name, subject";

			$groups = $db->get_results($sql);

			foreach ($groups as $group) {

				$sql = "select email_id, message_body
					from {$wpdb->prefix}timesheet_emailqueue
					where send_to = %s and send_from_email = %s and send_from_name = %s and subject = %s
					order by email_id";
				$parms = array($group->send_to, $group->send_from_email, $group->send_from_name, $group->subject);

				$rows = $db->get_results($sql, $parms);

				$message_body = "";
				$ids = "-1";

				foreach ($rows as $row) {
					$message_body = "{$message_body}<br>{$row->message_body}";
					$ids = "{$ids}, {$row->email_id}";
				}

				$header[] = "From: {$group->send_from_name} <{$group->send_from_email}>";
				$header[] = 'content-type: text/html';

				$success = wp_mail($group->send_to, $group->subject, $message_body, $header);

				if ($success==false) {
					echo "<div if='message' class='error'><p>Error sending email to {$group->send_to}.</p></div>";
				} else {
					if ($options['show_email_notice']) {
						echo "<div if='message' class='updated'><p>Email sent to {$group->send_to}.</p></div>";
					}
				}

				$sql = "delete from {$wpdb->prefix}timesheet_emailqueue where email_id in ({$ids})";
				$db->query($sql);
			}
		}

		$header[] = "From: {$options['email_name']} <{$options['email_from']}>";
		$header[] = 'content-type: text/html';

		$success = wp_mail($to, $subject, $body, $header);

		if ($success==false) {
			echo "<div if='message' class='error'><p>Error sending email to {$to}.</p></div>";
		} else {
			if ($options['show_email_notice']) {
				echo "<div if='message' class='updated'><p>Email sent to {$to}.</p></div>";
			}
		}
	}

	function add_cron(){
		if (!has_action('time_sheets_monthly_cron') || !has_action('time_sheets_email_check') ) {
			add_settings_error('action_not_enabled', 'error_action_not_enabled', 'The cron is not setup correctly.  Try deactivating and reactivating the plugin.', 'error');
		}

		if ( ! wp_next_scheduled( 'email_retainers_due') ) {
			$time = $this->time_to_wptime(strtotime("tomorrow 1am"));
			if ($time < current_time('timestamp')) {
				$time = $this->time_to_wptime(strtotime("tomorrow 1am"));
			}
			wp_schedule_event($time, 'daily', 'email_retainers_due');
		}

		if ( ! wp_next_scheduled( 'time_sheets_monthly_cron') ) {
			$time = $this->time_to_wptime(strtotime("tomorrow 1am"));
			if ($time < current_time('timestamp')) {
				$time = $this->time_to_wptime(strtotime("tomorrow 1am"));
			}
			wp_schedule_event($time, 'daily', 'time_sheets_monthly_cron');
		}

		if (! wp_next_scheduled('time_sheets_email_check') ) {
			$time = time();
			$time = $time+60;
			wp_schedule_event($time, 'minutes_5', 'time_sheets_email_check');
		}

		if (! wp_next_scheduled('time_sheets_ema il_late_timesheets') ) {
			$time = $this->time_to_wptime(strtotime("tomorrow 6pm"));
			if ($time < current_time('timestamp')) {
				$time = $this->time_to_wptime(strtotime("tomorrow 6pm"));
			}
			wp_schedule_event($time, 'daily', 'time_sheets_email_late_timesheets');
		}

	}

	function time_to_wptime($time) {
		$wp_time = current_time('timestamp');
		$php_time = time();
		$diff = $php_time-$wp_time;
		$time = $time+$diff;
		return $time;
	}

	function remove_cron() {

		$timestamp = wp_next_scheduled( 'email_retainers_due');
		wp_unschedule_event( $timestamp, 'email_retainers_due');

		$timestamp = wp_next_scheduled( 'time_sheets_monthly_cron');
		wp_unschedule_event( $timestamp, 'time_sheets_monthly_cron');

		$timestamp = wp_next_scheduled('time_sheets_email_check');
		wp_unschedule_event($timestamp, 'time_sheets_email_check');

		$timestamp = wp_next_scheduled('time_sheets_email_late_timesheets');
		wp_unschedule_event($timestamp, 'time_sheets_email_late_timesheets');
	}

	function InsertMonthlyInvoices() {
		global $wpdb;
		$db = new time_sheets_db();
		$entry = new time_sheets_entry();
		$options = get_option('time_sheets');
		$common = new time_sheets_common();
		$user_id = $options['cron_user'];

		$currentTime = getdate();
		if ($currentTime['mday']!=1)
		{
			return;
		}

		$sql = "insert into {$wpdb->prefix}timesheet_client_projects
		(ClientId, ProjectName, IsRetainer, MaxHours, HoursUsed, Active)
		SELECT ClientId, 'Monthly retainer was sent out', 0, 0, 0, 0
		FROM {$wpdb->prefix}timesheet_recurring_invoices_monthly a
		WHERE NOT EXISTS (SELECT * FROM {$wpdb->prefix}timesheet_client_projects b WHERE a.client_id = b.ClientId and a.ProjectName = 'Monthly retainer was sent out')
			and NOT EXISTS (SELECT * FROM {$wpdb->prefix}timesheet_recurring_invoices_monthly c ON a.ClientId = c.ClientId and a.ProjectName = c.ProjectName)";
		$db->get_results($sql);

		$sql = "insert into {$wpdb->prefix}timesheet (user_id, start_date, entered_date, ProjectId, monday_hours, tuesday_hours, wednesday_hours, thursday_hours, friday_hours, saturday_hours, sunday_hours, total_hours, other_expenses_notes, week_complete, marked_complete_by, marked_complete_date, ClientId, Approved, Approved_by, approved_date, payrolled, payrolled_on, payrolled_by)
SELECT $user_id, CURDATE(), CURDATE(), b.ProjectId, 0, 0, 0, 0, 0, 0, 0, 0, 'Bill Client for Monthly Retainer and zero out last months retainer hours.', 1, 1, CURDATE(), a.client_id, 1, 1, CURDATE(), 1, CURDATE(), 1
FROM {$wpdb->prefix}timesheet_recurring_invoices_monthly a
JOIN {$wpdb->prefix}timesheet_client_projects b ON a.client_id = b.ClientId
where MonthlyHours <> 0
and IsRetainer = 1
";

		$db->get_results($sql);

		$sql = "select user_email
		from {$wpdb->users} u
		join {$wpdb->prefix}timesheet_approvers a on u.ID = a.user_id";

		$users = $db->get_results($sql);

		$subject = "Monthly retainers have been entered into the timesheet system.";
		$body = "Monthly retainers have been entered into the timesheet system and need to be processed.  Last months retainer hour's need to be zeroed out as well.

It can be viewed and invoiced from the <a href='http://$_SERVER[HTTP_HOST]/wp-admin/admin.php?page=invoice_timesheet'>invoicing menu</a>.";
		if ($options['enable_email']) {
			foreach ($users as $user) {
				$common->send_email ($user->user_email, $subject, $body);
			}
		}
	}
}