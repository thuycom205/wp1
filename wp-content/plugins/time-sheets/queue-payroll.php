<?php
class time_sheets_queue_payroll {
	function count_pending_payroll() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$timesheets = $this->return_payroll_list();
		
		return count ($timesheets);
	}

	function show_payroll_list() {
		global $wpdb;
		$db = new time_sheets_db();
		$main = new time_sheets_main();
		$queue_approval = new time_sheets_queue_approval();

		$approver = $queue_approval->employee_approver_check();
		$options = get_option('time_sheets');
		$common = new time_sheets_common();

		$timesheets = $this->return_payroll_list();

		if ($timesheets) {
			echo "<form name='payroll_timesheets'><table border='1' cellspacing='0'><tr><td>Timesheet</td>";
			if ($approver<>0) {
				echo "<td>Reject</td>";
			}
			echo "<td>View</td><td>Completed</td><td>User</td><td>Start Date</td><td>Client</td><td>Project Name</td><td>Hours</td>";

			if ($options['mileage']) {
				echo "<td>Mileage</td>";
			}
			if ($options['per_diem']) {
				echo "<td>Per Diem Days</td>";
			}
			if ($options['flight_cost']) {
				echo "<td>Flight/Train Cost</td>";
			}
			if ($options['hotel']) {
				echo "<td>Hotel Charges</td>";
			}
			if ($options['rental_car']) {
				echo "<td>Rental Car</td>";
			}
			if ($options['tolls']) {
				echo "<td>Tolls</td>";
			}
			if ($options['other_expenses']) {
				echo "<td>Other Expenses</td>";
			}

			echo "</tr>";
			foreach ($timesheets as $timesheet) {
				echo "<td align='center'>{$timesheet->timesheet_id}</td>";
				if ($approver<>0) {
					echo "<td align='center'><a href='./admin.php?page=payroll_timesheet&timesheet_id={$timesheet->timesheet_id}&action=reject'><img src='". plugins_url( 'x.png' , __FILE__) ."' width='15' height='15'></a></td>";
				}
				echo "<td align='center'><a href='./admin.php?page=payroll_timesheet&timesheet_id={$timesheet->timesheet_id}&action=preinvoice' target='_blank'><img src='". plugins_url( 'view.png' , __FILE__) ."' width='15' height='15'></a></td><td align='center'><input type='checkbox' name='timesheet_{$timesheet->timesheet_id}' value=1></td><td>{$common->replace(' ', '&nbsp;', $timesheet->display_name)}</td><td>{$common->f_date($timesheet->start_date)}</td><td>{$common->clean_from_db($timesheet->client_name)}</td><td>{$common->clean_from_db($timesheet->project_name)}</td><td>{$common->clean_from_db($timesheet->total_hours)}</td>";
			if ($options['mileage']) {
				echo "<td align='center'>{$timesheet->mileage}</td>";
			}
			if ($options['per_diem']) {
				echo "<td align='center'>{$timesheet->per_diem_days}</td>";
			}
			if ($options['flight_cost']) {
				echo "<td align='center'>{$timesheet->flight_cost}</td>";
			}
			if ($options['hotel']) {
				echo "<td align='center'>{$timesheet->hotel_charges}</td>";
			}
			if ($options['rental_car']) {
				echo "<td align='center'>{$timesheet->rental_car_charges}</td>";
			}
			if ($options['tolls']) {
				echo "<td align='center'>{$timesheet->tolls}</td>";
			}
			if ($options['other_expenses']) {
				echo "<td align='center'>{$timesheet->other_expenses}</td>";
			}

			echo "</tr>";
			}
			echo "</table><br><input type='submit' name='submit' value='Record as Processed' class='button-primary'><input type='hidden' name='page' value='payroll_timesheet'><input type='hidden' name='action' value='approve'>";

			wp_nonce_field( 'timesheet_payroll_processing' );

			echo "</form>";
		} else {
			echo "No time sheets to process for payroll.";
		}
	}

	function payroll_timesheet(){
		$entry = new time_sheets_entry();

		if ($_GET['action']=='preinvoice') {
			$entry->show_timesheet();
		} else {
			echo "<br><table border='0'><tr><td valign='top'>";
			$this->show_payroll_list();
			echo "</td></tr>";
		}
	}

	function do_timesheet_rejectpayroll() {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');

		if ($options['queue_order']=='parallel') {
			echo '<div if="message" class="update-nag"><p>This timesheet may be in the payroll queue and may need to be adjusted.</p></div><br>';
		}

		$sql = "update {$wpdb->prefix}timesheet
			set invoiced = 0,
				invoiced_by = NULL,
				invoiced_date = NULL,
				payrolled = NULL
			where timesheet_id = %d";
		$params = array(intval($_GET['timesheet_id']));
		$db->query($sql, $params);

		echo '<div if="message" class="updated"><p>Timesheet has been rejected.</p></div>';
	}

	function return_payroll_list() {
		global $wpdb;
		$db = new time_sheets_db();
		$options = get_option('time_sheets');

	$sql = "select t.timesheet_id, u.user_login, t.start_date, c.ClientName client_name, cp.ProjectName project_name, t.invoiceid, cp.notes, t.per_diem_days, t.hotel_charges, t.rental_car_charges, t.tolls, t.other_expenses, t.mileage, t.flight_cost, u.display_name, t.total_hours
			from {$wpdb->prefix}timesheet t
			JOIN {$wpdb->prefix}timesheet_clients c ON t.ClientId = c.ClientId
			JOIN {$wpdb->prefix}timesheet_client_projects cp ON t.ProjectId = cp.ProjectId
			join {$wpdb->users} u on t.user_id = u.ID";

			if ($options['queue_order']=='parallel') {
				$sql = "{$sql}			where t.approved = 1 and payrolled = 0";
			} else {
				$sql = "{$sql}			where t.invoiced = 1 and payrolled = 0";
			}

			$sql = "{$sql} order by t.start_date";

		$timesheets = $db->get_results($sql);

		return $timesheets;
	}


	function do_timesheet_payroll(){
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		check_admin_referer( 'timesheet_payroll_processing' );

		$timesheets = $this->return_payroll_list();		

		foreach ($timesheets as $timesheet) {
			$valuename = "timesheet_{$timesheet->timesheet_id}";
			if ($_GET[$valuename] == 1) {
				$sql = "update {$wpdb->prefix}timesheet
					set payrolled= 1,
						payrolled_by = {$user_id},
					payrolled_on = CURDATE()
					where timesheet_id = {$timesheet->timesheet_id}";

				$db->query($sql);
			} 
		}

		echo '<div if="message" class="updated"><p>Timesheet(s) have been updated.</p></div>';
	}

	function employee_payroll_check() {
		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();

		$sql = "select count(*) from {$wpdb->prefix}timesheet_payrollers where user_id = {$user_id}";

		$count = $db->get_var($sql);
		
		return $count;
	}




}
