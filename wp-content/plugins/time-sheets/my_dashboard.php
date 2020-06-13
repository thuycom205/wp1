<?php

class time_sheets_mydashboard{
	function show_dashboard() {
		$user_id = get_current_user_id();
		$common = new time_sheets_common();

		if ($user_id == 0) {
			echo "You must be logged in to view this page.";
			return;
		}
		$common->show_datestuff();

		echo "<BR><table border='0' cellspacing='2' cellpadding='0'>
		<tr><td width='50%' valign='top'>";
			$this->search_timesheet();
		echo "</td><td valign='top'>";
		$common->show_clients_on_retainer(1);
		echo "</td></tr>
		</table>";
	}

	function search_timesheet() {
		$user_id = get_current_user_id();
		$common = new time_sheets_common();
		$approval = new time_sheets_queue_approval();

		if ($user_id == 0) {
			echo "You must be logged in to view this page.";
			return;
		}


		$start_date = $common->f_date($common->clean_from_db($_GET['start_date']));
		$end_date = $common->f_date($common->clean_from_db($_GET['end_date']));
		$include_completed = $common->clean_from_db($_GET['include_completed']);

		if ($_GET['include_me']) {
			$include_me = ' checked';
		} else {
			IF (!$_GET['submit']) {
				$include_me = ' checked';
			} else {
				$include_me = '';
			}
		}

		if ($_GET['filter_by_client']) {
			$filter_by_client = ' checked';
		}

		if ($common->clean_from_db($_GET['start_date'])=='') {
			$start_date = date('Y-m-d', strtotime("-1 Year"));
			$start_date = $common->f_date($start_date);
		}
		if ($common->clean_from_db($_GET['end_date'])=='') {
			$end_date = date('Y-m-d', strtotime("+1 Day"));
			$end_date = $common->f_date($end_date);
		}

		echo '<form method="get" name="ts_search">';
		echo "<table><tr><td>Enter Range To Search:</td><td>";
		echo "<input type='date' name='start_date' size='10' value='{$start_date}' data-date-inline-picker='false' data-date-open-on-focus='true' >";
		echo " to ";
		echo "<input type='date' name='end_date' size='10' value='{$end_date}' data-date-inline-picker='false' data-date-open-on-focus='true' ></td></tr>";

		echo "<tr><td colspan='2'><input type='checkbox' name='include_me' value='checked' {$include_me}> Include My Timesheets</td></tr>";
		if ($approval->employee_approver_check('true') <> 0) {
			if (is_admin()) {
				$include_all='';
				if ($_GET['include_all']) {
					$include_all = 'checked';
				}
				echo "<tr><td colspan='2'><input type='checkbox' name='include_all' value='checked' {$include_all} onClick='disable_by_include_all()'> Include All Users</td></tr>";
			}
		}


		if ($approval->employee_approver_check()<> 0) {
			if ($_GET['include_all_team']) {
				$include_team = ' checked';
			}
			
			echo "<tr><td colspan='2'><input type='checkbox' name='include_all_team' value='checked' {$include_team} onClick='reset_team_member()'> Include All Team Members</td></tr>
			<tr><td>Select Team Member</td><td>";
			$users = $common->return_approvers_team_list($user_id, 1);
				echo "<select name='team_member'>
					<option value=''>--None</option>
			";
			foreach ($users as $user) {
				echo "<option value='{$user->a}'{$common->is_match($user->a, $_GET['team_member'], ' selected')}>{$user->display_name}</option>";
			}
			echo "</select></td></tr>";
		}
		echo "<tr><td colspan='2'><input type='checkbox' name='include_completed' value='checked' {$include_completed}> Include Completed Timesheets</td></tr>";
		echo "<tr><td colspan='2'><input type='checkbox' name='filter_by_client' value='checked' {$filter_by_client} onClick='disable_client_and_project()'> Filter by Client and Project</td></tr>";

		$js_projects = $common->draw_clients_and_projects(0, 0, 0, '', (object) $_GET, $clientList);

		echo "<tr><td colspan='2'><input type='submit' name='submit' value='Search' class='button-primary'>";
		echo "<input type='hidden' name='page' value='search_timesheet'></td></tr>";
		echo "</table></form>";
		if ($approval->employee_approver_check()<> 0) {
echo "
<script>
function reset_team_member() {
	if (ts_search.include_all_team.checked==true) {
		ts_search.team_member.disabled=true;
	} else {
		ts_search.team_member.disabled=false;
	}
}

reset_team_member();
</script>
";
		}
echo "<script>";

$common->client_and_projects_javascript($js_projects, 'ts_search', 0, (object) $_GET);

echo "

function disable_client_and_project() {
	if (ts_search.filter_by_client.checked==false) {
		ts_search.ClientId.disabled=true;
		ts_search.ProjectId.disabled=true;
	} else {
		ts_search.ClientId.disabled=false;
		ts_search.ProjectId.disabled=false;
	}
}

disable_client_and_project();
resetProject()
</script>";
		if (is_admin()) {
echo "<script>
	function disable_by_include_all() {
		if (ts_search.include_all.checked==true) {
			ts_search.include_all_team.disabled=true;
			ts_search.team_member.disabled=true;
			ts_search.include_me.disabled=true;
		} else {
			ts_search.include_all_team.disabled=false;
			ts_search.team_member.disabled=false;
			ts_search.include_me.disabled=false;
		}
	}
disable_by_include_all();
</script>";

		}
		$this->run_timesheet_search($start_date, $end_date, $include_me);
	}

	function run_timesheet_search($start_date, $end_date, $include_me) {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();
		$options = get_option('time_sheets');
		$common = new time_sheets_common();


		$sql = "select t.timesheet_id, t.approved, t.start_date, c.ClientName client_name, cp.ProjectName, t.marked_complete_by, u.display_name, t.total_hours
			from {$wpdb->prefix}timesheet t
			JOIN {$wpdb->prefix}timesheet_clients c ON t.ClientId = c.ClientId
			JOIN {$wpdb->prefix}timesheet_client_projects cp on t.ProjectId=cp.ProjectId
			join {$wpdb->users} u on t.user_id = u.ID
			where t.start_date between %s and %s";
			if (!$_GET['include_completed']) {
				$sql = "{$sql} and week_complete = 0";
			}
			if (!$_GET['include_all']) {
				if ($include_me != '') {
					$sql = $sql." and (t.user_id={$user_id} /*b1*/";
				}
			}else {
				$sql = $sql." and (1=1";
			}
				if ($_GET['include_all_team']) {
					IF ($include_me == '') {
						$sql = $sql." and (";
					} else {
						$sql = $sql." or ";
					}
					$sql = $sql." t.user_id in (select approvie_user_id from {$wpdb->prefix}timesheet_approvers_approvies where approver_user_id = {$user_id})  )";
				} elseif ($_GET['team_member']) {
					IF ($include_me == '') {
						$sql = $sql." and (";
					} else {
						$sql = $sql." or ";
					}
					$sql = $sql." t.user_id = {$common->intval($_GET['team_member'])} /*b2*/ )";
				} else {
					$sql = $sql." )";
				}
			

			if ($_GET['filter_by_client']) {
				if ($_GET['ClientId']) {
					$sql = $sql." and t.ClientId = {$common->intval($_GET['ClientId'])} ";
				}
				if ($_GET['ProjectId']) {
					$sql = $sql." and t.ProjectId = {$common->intval($_GET['ProjectId'])} ";
				}
			}

			$sql = "{$sql} 
			order by t.start_date desc, c.ClientName asc, cp.ProjectName asc";
		
		$params = array($common->mysql_date($start_date), $common->mysql_date($end_date));

		$timesheets = $db->get_results($sql, $params);

		if (strpos($_SERVER['REQUEST_URI'], 'admin.php') !== False) {
			$timesheet_url = "./admin.php?page=enter_timesheet&";
		} else {
			$timesheet_url = $options['rel_url_to_timesheet'] . '?';
		}

		if ($timesheets) {
			echo "<table border='1' cellspacing='0'><tr><td>Time Sheet</td><td>User</td><td>Approved</td><td>Week Completed</td><td>Start Date</td><td>Client Name</td><td>Project Name</td><td>Total Hours</td></tr>";
			foreach ($timesheets as $timesheet) {
				echo "<tr><td align='center'><a href='{$timesheet_url}timesheet_id={$timesheet->timesheet_id}'>{$timesheet->timesheet_id}</a></td><td>{$timesheet->display_name}</td><td align='center'>";
				if ($timesheet->approved==1) {
					echo "<img src='". plugins_url( 'check.png' , __FILE__) ."' height='15' width='15'>";
				} else {
					echo "&nbsp;";
				}
				echo "</td><td align='center'>";
				if ($timesheet->marked_complete_by) {
					echo "<img src='". plugins_url( 'check.png' , __FILE__) ."' height='15' width='15'>";
				} else {
					echo "&nbsp;";
				}
				echo "</td><td>{$common->f_date($timesheet->start_date)}</td><td>{$common->clean_from_db($timesheet->client_name)}</td><td>{$common->clean_from_db($timesheet->ProjectName)}</td><td>{$common->clean_from_db($timesheet->total_hours)}</td></tr>";
			}
			echo "</table>";
		} else {
			echo "<BR>No time sheets found with the above search criteria.";
		}
	}

}
