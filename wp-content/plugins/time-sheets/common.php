<?php
class time_sheets_common {
	function draw_clients_and_projects ($client_id, $project_id, $timesheet_id, $disable_object, $timesheet, &$clientList) {
		//Draws the client and project drop downs. Returns JSON document
		//For use by the Javascript function.

		global $wpdb;
		$user_id = get_current_user_id();
		$db = new time_sheets_db();
		$queue_approval = new time_sheets_queue_approval();

		$clients = $db->get_results("select ClientName, tc.ClientId, r.BillOnProjectCompletion
					from {$wpdb->prefix}timesheet_clients tc
					join {$wpdb->prefix}timesheet_clients_users tcu ON tc.ClientId = tcu.ClientId
					join {$wpdb->prefix}timesheet_client_projects tcp on tc.ClientId = tcp.ClientId
						 and tcp.Active=1 
					left outer join {$wpdb->prefix}timesheet_recurring_invoices_monthly r on tc.ClientId = r.client_id
					WHERE tcu.user_id = $user_id
					union
					select ClientName, tc1.ClientId, r.BillOnProjectCompletion
					from {$wpdb->prefix}timesheet_clients tc1
					left outer join {$wpdb->prefix}timesheet_recurring_invoices_monthly r on tc1.ClientId = r.client_id
					where tc1.ClientId = $client_id
					union
					select ClientName, tc2.ClientId, r.BillOnProjectCompletion
					from {$wpdb->prefix}timesheet_clients tc2
					left outer join {$wpdb->prefix}timesheet_recurring_invoices_monthly r on tc2.ClientId = r.client_id
					join {$wpdb->prefix}timesheet t on tc2.ClientId = t.ClientId
					where t.timesheet_id = $timesheet_id
					order by ClientName");


		$sql = "select tcp.ClientId, ProjectId, ProjectName, BillOnProjectCompletion from {$wpdb->prefix}timesheet_client_projects tcp join {$wpdb->prefix}timesheet_clients_users tcu on tcp.ClientId = tcu.ClientId where tcu.user_id = $user_id and tcp.Active=1 
union
select ClientId, ProjectId, ProjectName, BillOnProjectCompletion from {$wpdb->prefix}timesheet_client_projects where ProjectId = {$project_id}
order by ProjectName
";

		$projects = $db->get_results($sql);

			foreach ($projects as $project) {
				$project->ProjectName = $this->clean_from_db($project->ProjectName);
				$clean_projects[] = $project;
			}


			$js_projects = json_encode($clean_projects);

		if ($options['hide_client_project'] && count($clients)==1 && count($projects)==1) {
			$client = $clients[0];
			$project = $projects[0];
			echo "<tr><td><input type='hidden' name='ClientId' value='{$client->ClientId}'>
			<input type='hidden' name='ProjectId' value='{$project->ProjectId}'></td></tr>";
			$clientList = "{$clientList}\"{$client->ClientId}\", ";
			$clientList = "[$clientList \"-1\"]";
		} else {

		echo "<tr><td>Client Name:</td>";

			if ($clients) {
				echo "<td><select name='ClientId' onChange='clientChange()'{$disable_object}><option value='-2'>Select Client</option>";
				$clientList = '';
				foreach ($clients as $client) {
					echo "<option value='{$client->ClientId}'";
					if ($client->ClientId==$timesheet->ClientId) {
					echo " selected";
						}
					echo ">{$this->clean_from_db($client->ClientName)}</option>";
					if ($client->BillOnProjectCompletion=="1") {
						$clientList = "{$clientList}\"{$client->ClientId}\", ";
					}
				}
				if ($clientList) {
					$clientList = "[$clientList \"-1\"]";
				} else {
					$clientList = "[\"-1\"]";
				}
				echo "</select>";
			} else {
				echo "<td>New Client must be added.";
			}
			if ($queue_approval->employee_approver_check()!=0 && $disable_object=="") {
				echo "<a href='admin.php?page=timesheet_manage_clients&menu=New+Client'>Add a Client</a>";
			}
			echo "</td></tr>";

			echo "<tr><td>Project Name:</td><td><Select name='ProjectId'{$disable_object} onChange='ProjectChange()'></select>";
			if ($queue_approval->employee_approver_check()!=0 && $disable_object=="") {
				echo "<a href='admin.php?page=timesheet_manage_clients&menu=New+Project'>Add a Project</a>";
			}

			echo "</td></tr>";
		}

		return $js_projects;

	}

	function client_and_projects_javascript($js_projects, $html_formname, $billing_on_form, $timesheet) {
echo "

function clientChange() {
	if ({$html_formname}.ClientId.value==-2) {
		alert ('Invalid Client Selected');
	}
	resetProject();
	";
	if ($billing_on_form==1) {
		echo "isProjectBilled();
		isClientProjectBilled();";
	}
	echo "
}


function resetProject(){
	var projectlist = {$js_projects};
	{$html_formname}.ProjectId.options.length = 0;

	var numberOfProjects = projectlist.length;
	//alert(numberOfProjects);
	for (var i = 0; i < numberOfProjects; i++) {
		project = projectlist[i];
		if (project['ClientId']=={$html_formname}.ClientId.value) {
			var opt = document.createElement('option');
			opt.value = project['ProjectId'];
			opt.innerHTML = project['ProjectName'];
			{$html_formname}.ProjectId.appendChild(opt);
		}
	  //alert(project['ProjectName']);
	}";

	if ($timesheet->ProjectId) {
		echo "
	{$html_formname}.ProjectId.value={$timesheet->ProjectId};";
	}
	echo "
}";
	}
	
	function return_employee_list() {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.user_login, u.id, u.display_name
		from {$wpdb->users} u 
		order by u.display_name";

		$users = $db->get_results($sql, $values);

		return $users;

	}

	function return_approvers_team_list($approver_id, $members_only=0) {
		global $wpdb;
		$db = new time_sheets_db();

		$sql = "select u.user_login, aa.approvie_user_id a, u.id, u.display_name
		from {$wpdb->users} u ";
		if ($members_only==1) {
			$sql=$sql."inner";
		} else {
			$sql=$sql."left outer";
		}
		$sql = $sql." join {$wpdb->prefix}timesheet_approvers_approvies aa ON u.ID = aa.approvie_user_id
			and aa.approver_user_id = %d
		order by u.display_name";

		$values = array($approver_id);

		$users = $db->get_results($sql, $values);

		return $users;
	}

	function intval($string) {
		return intval($string);
	}

	function esc_textarea($string) {
		$string = stripslashes($string);
		return esc_textarea($string);
	}

	function clean_from_db($string) {
		
		$string = str_replace("''", "'", $string);
		$string = esc_textarea($string);
		$string = stripslashes($string);

		return $string;
	}

	function add_days_to_date($idate, $days, $holidays) {
		$date = new DateTime($idate);
		date_add($date, date_interval_create_from_date_string("{$days} days"));
		$leading_color = "";
		$trailing_color = "";

		if ($holidays) {
			foreach ($holidays as $dt) {
				foreach ($dt as $daykey) {
					if (date('m-d',strtotime((string)$daykey))==date_format($date, 'm-d')) {
						$leading_color="<font color='red'>";
						$trailing_color="</font>";
					}
				}
			}
		}
		$date = date_format($date, 'm-d');
		$return = "{$leading_color} {$date} {$trailing_color}";
		return $return;
	}

	function show_clients_on_retainer($mine_only = 0) {
		global $wpdb;
		$db = new time_sheets_db();
		$user_id = get_current_user_id();

		$sql = "select ClientName, im.MonthlyHours, im.HourlyRate, im.Notes
			from {$wpdb->prefix}timesheet_clients tc
			inner join {$wpdb->prefix}timesheet_recurring_invoices_monthly im on tc.clientid = im.client_id";
		if ($mine_only == 1) {
			$sql = $sql." inner join {$wpdb->prefix}timesheet_clients_users tcu on tc.clientid = tcu.clientid and tcu.user_id = {$user_id}";
		}
			$sql = $sql." where im.MonthlyHours <> 0
			order by ClientName";

		$clients = $db->get_results($sql);

		if ($clients) {
			echo "<table border='1' cellpadding='0' cellspacing='0' width='50%'><tr><td>Client Name</td><td>Number of Hours</td><td>Hourly Rate on Retainer</td><td>Notes</td></tr>";
			foreach ($clients as $client) {
				echo "<tr><td>{$client->ClientName}</td><td align='center'>{$client->MonthlyHours}</td><td align='center'>$ {$client->HourlyRate}</td><td><textarea disabled>{$this->esc_textarea($client->Notes)}</textarea></td></tr>";
			}
			echo "</table>";
		}

		$sql = "select ClientName, im.MonthlyHours, im.HourlyRate, im.Notes
			from {$wpdb->prefix}timesheet_clients tc
			inner join {$wpdb->prefix}timesheet_recurring_invoices_monthly im on tc.clientid = im.client_id";
		if ($mine_only == 1) {
			$sql = $sql." inner join {$wpdb->prefix}timesheet_clients_users tcu on tc.clientid = tcu.clientid and tcu.user_id = {$user_id}";
		}
			$sql = $sql." 
			where im.MonthlyHours = 0
			order by ClientName";

		$clients = $db->get_results($sql);

		if ($clients) {
			echo "<BR><table border='1' cellpadding='0' cellspacing='0' width='50%'><tr><td>Client Name</td><td>Notes</td></tr>";
			foreach ($clients as $client) {
				echo "<tr><td>{$client->ClientName}</td><td><textarea disabled>{$this->esc_textarea($client->Notes)}</textarea></td></tr>";
			}
			echo "</table>";
		}

		$sql = "select ClientName, ProjectName, p.notes
			from {$wpdb->prefix}timesheet_client_projects p
			join {$wpdb->prefix}timesheet_clients c on p.ClientId = c.ClientId";
		if ($mine_only == 1) {
			$sql = $sql." inner join {$wpdb->prefix}timesheet_clients_users tcu on p.clientid = tcu.clientid and tcu.user_id = {$user_id}";
		}
			$sql = $sql." 
			where p.notes <> '' and p.Active = 1
			order by ClientName, ProjectName";

		$clients = $db->get_results($sql);

		if ($clients) {
			echo "<BR><table border='1' cellpadding='0' cellspacing='0' width='50%'><tr><td>Client Name</td><td>Project Name</td><td>Notes</td></tr>";
			foreach ($clients as $client) {
				echo "<tr><td>{$client->ClientName}</td><td>{$client->ProjectName}</td><td><textarea disabled>{$this->esc_textarea($client->notes)}</textarea></td></tr>";
			}
			echo "</table>";
		}
	}



	function is_match($v1, $v2, $return, $debug=0) {
		if ($debug==1) {
			var_dump($v1);
			var_dump($v2);
		}
		if (trim($v1)==trim($v2)) {
			return $return;
		} else {
			return "";
		}
	}


	function replace($search, $replace, $value) {
		return str_replace($search, $replace, $value);
	}

	function f_date($date) {
		$options = get_option('time_sheets');

		if ($options['override_date_format'] == 'system_defined') {
			$date_format = get_option('date_format');
		} else {
			$date_format = $options['new_date_format'];

			if ($options['user_specific_date_format']) {
				if (get_user_option('user_date_format', $user_id)) {
					$date_format = get_user_option('user_date_format', $user_id);
				}
			}
		}

		return date_i18n($date_format, strtotime($date));
	}

	function mysql_date($date) {
		return date("Y-m-d", strtotime($date));
	}

	function send_email ($to, $subject, $body) {
		global $wpdb;
		$options = get_option('time_sheets');
		$db = new time_sheets_db();

		if($options['enable_email']) {
			$sql = "insert into {$wpdb->prefix}timesheet_emailqueue 
				(send_to, send_from_email, send_from_name, subject, message_body, entered_on)
				values (%s, %s, %s, %s, %s, now())";
			$parms = array($to, $options['email_from'], $options['email_name'], $subject, $body);

			$db->query($sql, $parms);
			if ($options['show_email_notice']) {
				echo "<div if='message' class='updated'><p>Email to {$to} queued.</p></div>";
			}
		}
	}

	function remove_br($string) {
		$string = str_replace("\'", "'", $string);
		return nl2br($string);
	}

	function enqueue_js() {

		wp_enqueue_script ("jquery", '', array(), "1.11.0", false);
		wp_enqueue_script ("polyfiller", plugins_url( 'js/minified/polyfiller.js' , __FILE__), array(), false, false);
		#wp_enqueue_script ("polyfiller", "//afarkas.github.io/webshim/js-webshim/minified/polyfiller.js", array(), "");

	}

	function show_datestuff() {

		?>
  <style type="text/css">
    .hide-replaced.ws-inputreplace {
    display: none !important;
}
.input-picker .picker-list td > button.othermonth {
    color: #888888;
    background: #fff;
}
.ws-inline-picker.ws-size-2, .ws-inline-picker.ws-size-4 {
    width: 49.6154em;
}
.ws-size-4 .ws-index-0, .ws-size-4 .ws-index-1 {
    border-bottom: 0.07692em solid #eee;
    padding-bottom: 1em;
    margin-bottom: 0.5em;
}
.picker-list.ws-index-2, .picker-list.ws-index-3 {
    margin-top: 3.5em;
}
div.ws-invalid input {
    border-color: #c88;
}
.ws-invalid label {
    color: #933;
}
div.ws-success input {
    border-color: #8c8;
}
form {
    #margin: 10px auto;
    #width: 700px;
    #min-width: 49.6154em;
    #border: 1px solid #000;
    #padding: 10px;
}
.form-row {
    padding: 5px 10px;
    margin: 5px 0;
}
label {
    display: block;
    margin: 3px 0;
}
.form-row input {
    width: 220px;
    padding: 3px 1px;
    border: 1px solid #ccc;
    box-shadow: none;
}
.form-row input[type="checkbox"] {
    width: 15px;
}
.date-display {
    display: inline-block;
    min-width: 200px;
    padding: 5px;
    border: 1px solid #ccc;
    min-height: 1em;
}
.show-inputbtns .input-buttons {
    display: inline-block;
}
  </style>



<script type='text/javascript'>//<![CDATA[

webshim.setOptions('forms-ext', {
    replaceUI: 'auto',
    types: 'date',
    date: {
        startView: 2,
        inlinePicker: true,
        classes: 'hide-inputbtns'
    }
});
webshim.setOptions('forms', {
    lazyCustomMessages: true
});
//start polyfilling
webshim.polyfill('forms forms-ext');

//only last example using format display
$(function () {
    $('.format-date').each(function () {
        var $display = $('.date-display', this);
        $(this).on('change', function (e) {
            //webshim.format will automatically format date to according to webshim.activeLang or the browsers locale
            var localizedDate = webshim.format.date($.prop(e.target, 'value'));
            $display.html(localizedDate);
        });
    });
});
//]]> 

</script>

<?php

	}
}
