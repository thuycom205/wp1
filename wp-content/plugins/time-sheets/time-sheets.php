<?php
/*
Plugin Name: Time Sheets
Version: 1.7.2
Plugin URI: http://dcac.co/go/time-sheets
Description: Time Sheets application
Author: Denny Cherry & Associates Consulting
Author URI: http://dcac.co/
*/

require_once dirname( __FILE__ ) .'/common.php';
require_once dirname( __FILE__ ) .'/setup.php';
require_once dirname( __FILE__ ) .'/entry.php';
require_once dirname( __FILE__ ) .'/settings.php';
require_once dirname( __FILE__ ) .'/db.php';
require_once dirname( __FILE__ ) .'/cron.php';
require_once dirname( __FILE__ ) .'/manage-projects.php';
require_once dirname( __FILE__ ) .'/mysettings.php';
require_once dirname( __FILE__ ) .'/manage-client-approvers.php';
require_once dirname( __FILE__ ) .'/my_dashboard.php';
require_once dirname( __FILE__ ) .'/docs.php';

require_once dirname( __FILE__ ) .'/queue-payroll.php';
require_once dirname( __FILE__ ) .'/queue-invoice.php';
require_once dirname( __FILE__ ) .'/queue-approval.php';

$plugins_url = plugins_url();
$base_url = get_option( 'siteurl' );
$plugins_dir = str_replace( $base_url, ABSPATH, $plugins_url );

$folder = $plugins_dir .'/time-sheets-modules' ;
$filename = "{$folder}/custom.php";

if (file_exists($filename)) {
	DEFINE('time_sheet_custom', 'yes');
	require_once $filename;
}

class time_sheets_main {

	function activation() {

		// Default options
		$options = array (
			'from_email' => '',
			'from_name' => '',
			'email_enabled' => '',
			'hide_dcac_ad' => '',
			'email_late_timesheets' => 'checked',
			'override_date_format' => 'system_defined'
		);

		// Add options
		add_option('time_sheets', $options);


		$setup = new time_sheets_setup();
		$setup->create_db_objects();

		$cron = new time_sheets_cron();
		$cron->add_cron();

	 }

	function deactivation() {
		//delete_option('time_sheets');
		$cron = new time_sheets_cron();
		$cron->remove_cron();
	}

	function upgrade() {
		$setup = new time_sheets_setup();
		$setup->create_db_objects();
		$options = get_option('time_sheets');

		$cron = new time_sheets_cron();
		$cron->remove_cron();
		$cron->add_cron();
	}

	function custom_menu() {
		$options = get_option('time_sheets');
		$time_sheets_main = new time_sheets_main();
		$time_sheets_entry = new time_sheets_entry();
		$time_sheets_settings = new time_sheets_settings();
		$docs = new time_sheets_docs();
		$time_sheets_manage_projects = new time_sheets_manage_projects();
		$mydashboard = new time_sheets_mydashboard();

		$queue_payroll = new time_sheets_queue_payroll();
		$queue_invoice = new time_sheets_queue_invoice();
		$queue_approval = new time_sheets_queue_approval();

		$client_managers = new time_sheets_client_managers();
		$user_id = get_current_user_id();
		$my = new time_sheets_my_settings();
		$cron = new time_sheets_cron();

		if ($queue_approval->employee_approver_check()!=0) {
			$vcount_pending_approval = $queue_approval->count_pending_approval();
		} else {
			$vcount_pending_approval = 0;
		}
		if ($queue_invoice->employee_invoicer_check()!=0) {
			$vcount_pending_invoice = $queue_invoice->count_pending_invoice();
		} else {
			$vcount_pending_invoice = 0;
		}
		if ($queue_payroll->employee_payroll_check()!=0) {
			$vcount_pending_payroll = $queue_payroll->count_pending_payroll();
		} else {
			$vcount_pending_payroll = 0;
		}

		$timesheets_not_complete = $queue_invoice->count_users_open_invoice();

		$pending_stuff = $vcount_pending_approval->NotRetainer + $vcount_pending_approval->Retainer + $vcount_pending_invoice->NotRetainer + $vcount_pending_invoice->Retainer + $vcount_pending_payroll+$timesheets_not_complete;

		$menuLocation = $options['menu_location'];
		if ($menuLocation == '') {
			$menuLocation = NULL;
		}

		$userMenuLocation = get_user_option('time_sheets_menu_location', $user_id);
		if ($options['users_override_location']) {
			if ($userMenuLocation != '') {
				$menuLocation = $userMenuLocation;
			}
		}

		if ($pending_stuff != 0) {
			$tag = "Time Sheets <span class='update-plugins count-1'><span class='plugin-count'>{$pending_stuff}</span></span>";
		} else {
			$tag = 'Time Sheets';
		}
		
		add_menu_page('Time Sheets', $tag, '', 'time_sheets_top', array($time_sheets_entry, 'enter_timesheet'), plugins_url( 'time-sheets/icon.png' ), $menuLocation);

		add_submenu_page('time_sheets_top', 'Enter Time Sheet', 'Enter Time Sheet', 'read', 'enter_timesheet', array($time_sheets_entry, 'enter_timesheet'));

		if ($timesheets_not_complete == 0) {
			$tag = "My Dashboard";
		} else {
			$tag = "My Dashboard<span class='update-plugins count-1'><span class='plugin-count'>{$timesheets_not_complete}</span></span>";
		}

		add_submenu_page('time_sheets_top', 'My Dashboard', $tag, 'read', 'search_timesheet', array($mydashboard, 'show_dashboard'));

		if ($client_managers->client_manager_check()==1) {
			add_submenu_page('time_sheets_top', 'Manage Clients & Projects', 'Manage Clients', 'read', 'timesheet_manage_clients', array($time_sheets_manage_projects, 'main'));
		}

		if ($queue_approval->employee_approver_check('true')!=0) {
			$vcount_pending_approval = $queue_approval->count_pending_approval();

			if ($vcount_pending_approval->Embargoed != 0) {
				$embargo_value = "/{$vcount_pending_approval->Embargoed}";
			} else {
				$embargo_value = '';
			}

			if ($vcount_pending_approval->NotRetainer+$vcount_pending_approval->Retainer != 0) {
				$tag = "Approval Queue<span class='update-plugins count-1'><span class='plugin-count'>{$vcount_pending_approval->NotRetainer}/{$vcount_pending_approval->Retainer}{$embargo_value}</span></span>";

			} else {
				$tag = "Approval Queue";
			}

			add_submenu_page('time_sheets_top', 'Approvel Queue', $tag, 'read', 'approve_timesheet', array($queue_approval, 'approve_timesheet'));
		}

		if ($queue_invoice->employee_invoicer_check()!=0) {
			$vcount_pending_invoicing = $queue_invoice->count_pending_invoice();

			if ($vcount_pending_invoicing->NotRetainer+$vcount_pending_invoicing->Retainer != 0) {
				$tag = "Invoice Queue<span class='update-plugins count-1'><span class='plugin-count'>{$vcount_pending_invoicing->NotRetainer}/{$vcount_pending_invoicing->Retainer}</span></span>";
			} else {
				$tag = "Invoice Queue";
			}


			add_submenu_page('time_sheets_top', 'Invoice Queue', $tag, 'read', 'invoice_timesheet', array($queue_invoice, 'invoice_timesheet'));

		}
		
		if ($queue_payroll->employee_payroll_check()!=0) {
			$vcount_pending_payroll = $queue_payroll->count_pending_payroll();

			if ($vcount_pending_payroll != 0) {
				$tag = "Payroll Queue<span class='update-plugins count-1'><span class='plugin-count'>{$vcount_pending_payroll}</span></span>";
			} else {
				$tag = "Payroll Queue";
			}


			add_submenu_page('time_sheets_top', 'Payroll Queue', $tag, 'read', 'payroll_timesheet', array($queue_payroll, 'payroll_timesheet'));
			add_submenu_page('time_sheets_top', 'Employees Who Always Are Sent to Payroll for Processing', 'Force Payroll Setup', 'read', 'employees_allways_to_payroll', array($time_sheets_settings, 'employees_allways_to_payroll'));
		}

		add_submenu_page('time_sheets_top', 'Manage Approvers', 'Manage Approvers', 'manage_options', 'time_sheets_manage_approvers', array($time_sheets_settings, 'manage_approvers'));

		add_submenu_page('time_sheets_top', 'Manage Employees Who Process Invoices', 'Manage Invoicers', 'manage_options', 'time_sheets_manage_invoicers', array($time_sheets_settings, 'manage_invoicers'));

		add_submenu_page('time_sheets_top', 'Manage Payroll Processors', 'Manage Payroll', 'manage_options', 'time_sheets_manage_payroll', array($time_sheets_settings, 'manage_payrollers'));

		add_submenu_page('time_sheets_top', 'Manage Client Managers', 'Manage Client Managers', 'manage_options', 'time_sheets_client_managers', array($client_managers, 'manage_client_managers'));

		if ($queue_approval->employee_approver_check('false')!=0) {
			add_submenu_page('time_sheets_top', 'Setup Approval Teams', 'Setup Approval Teams', 'read', 'setup_approval_teams', array($time_sheets_settings, 'setup_approval_teams'));
		}
		add_submenu_page('time_sheets_top', 'Time Sheet Global Settings', 'Global Settings', 'manage_options', 'time_sheets_settings', array($time_sheets_settings, 'show_settings_page'));
		add_submenu_page('time_sheets_top', 'My Settings', 'My Settings', 'read', 'my_settings', array($my, 'main'));

		//If the customer has requested custom menus they'll be shown here.
		if (DEFINED('time_sheet_custom')) {
			$custom = new time_sheets_custom();
			$custom->custom_menu();
		}

		add_submenu_page('time_sheets_top', 'Documentation', 'Documentation', 'manage_options', 'timesheet_docs', array($docs, 'main'));

		if ($_GET) {
			$page = $_GET['page'];
		} else {
			$page = '';
		}


		if (($page == "payroll_timesheet" || $page == "invoice_timesheet" || $page == "approve_timesheet" || $page == "enter_timesheet" || $page == "search_timesheet" || $page == "timesheet_manage_clients" || $page == "timesheet_settings" || $page == "my_settings" ) && (!$_GET['action'] && !$_POST['action'])) {
			$this->footer();
		}
	}

	function init_settings(){
		$settings = new time_sheets_settings();
		$settings->register_settings();
	}

	function footer() {
		$options=get_option('time_sheets');
		$time_sheets_main = new time_sheets_main();

		if (!$options['hide_dcac_ad']) {
			add_filter('admin_footer_text', array($time_sheets_main, 'show_footer'));
			add_filter( 'update_footer', array($time_sheets_main, 'show_footer_version'));
		} #else {
		#	remove_filter('admin_footer_text', array($time_sheets_main, 'show_footer'));
		#	remove_filter( 'update_footer', array($time_sheets_main, 'show_footer_version'));
		#}
	}

	function show_footer() {
		echo '<span id="footer-thankyou"><a href="https://www.dcac.co/applications/wordpress-plugins/time-sheets">Time Sheets</a> provided by <a href="http://www.dcac.co">Denny Cherry & Associates Consulting</a><p></span>';
	}

	function show_footer_version() {
		$folder = plugins_url();
		$info = get_plugin_data( __FILE__ );
		echo "Version {$info['Version']}";
	}

	function toolbar_open_invoices( $wp_admin_bar ) {
		$queue_invoice = new time_sheets_queue_invoice();
		$vcount_open_invoices = $queue_invoice->count_users_open_invoice();

		$title = "My Time Sheet Dashboard({$vcount_open_invoices})";

		if ($vcount_open_invoices != 0) {
			$args = array(
				'id'    => 'open_timesheets',
				'title' => $title,
				'href'  => admin_url('admin.php?page=search_timesheet'),
				'meta'  => array( 'class' => 'my-toolbar-page' )
			);
			$wp_admin_bar->add_node( $args );
		}
	}

	function toolbar_pending_approval( $wp_admin_bar ) {
		$queue_approval = new time_sheets_queue_approval();
		if ($queue_approval->employee_approver_check()!=0) {
			$vcount_pending_approval = $queue_approval->count_pending_approval();

			if ($vcount_pending_approval->Embargoed != 0) {
				$embargo_value = "/{$vcount_pending_approval->Embargoed}";
			} else {
				$embargo_value = '';
			}

			$title = "Approval Queue({$vcount_pending_approval->NotRetainer}/{$vcount_pending_approval->Retainer}{$embargo_value})";
			if ($vcount_pending_approval->NotRetainer+$vcount_pending_approval->Retainer != 0) {
				$args = array(
					'id'    => 'pending_approval',
					'title' => $title,
					'href'  => admin_url('admin.php?page=approve_timesheet'),
					'meta'  => array( 'class' => 'my-toolbar-page' )
				);
				$wp_admin_bar->add_node( $args );
			}
		}
	}

	function toolbar_pending_invoicing( $wp_admin_bar ) {
		$queue_invoice = new time_sheets_queue_invoice();
		if ($queue_invoice->employee_invoicer_check()!=0) {
			$vcount_pending_approval = $queue_invoice->count_pending_invoice();

			$title = "Invoicing Queue({$vcount_pending_approval->NotRetainer}/{$vcount_pending_approval->Retainer})";

			if ($vcount_pending_approval->NotRetainer+$vcount_pending_approval->Retainer != 0) {
				$args = array(
					'id'    => 'pending_invoicing',
					'title' => $title,
					'href'  => admin_url('admin.php?page=invoice_timesheet'),
					'meta'  => array( 'class' => 'my-toolbar-page' )
				);
				$wp_admin_bar->add_node( $args );
			}
		}
	}

	function toolbar_pending_payroll( $wp_admin_bar ) {
		$queue_payroll = new time_sheets_queue_payroll();
		if ($queue_payroll->employee_payroll_check()!=0) {
			$vcount_pending_payroll = $queue_payroll->count_pending_payroll();

			$title = "Payroll Queue({$vcount_pending_payroll})";

			if ($vcount_pending_payroll != 0) {
				$args = array(
					'id'    => 'pending_payroll',
					'title' => $title,
					'href'  => admin_url('admin.php?page=payroll_timesheet'),
					'meta'  => array( 'class' => 'my-toolbar-page' )
				);
				$wp_admin_bar->add_node( $args );
			}
		}
	}

	function add_new_intervals($schedules) 
	{
		// add weekly and monthly intervals
		$schedules['minutes_5'] = array(
			'interval' => 300,
			'display' => __('Once Every 5 Minutes')
		);

		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __('Once Weekly')
		);

		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => __('Once a month')
		);

		return $schedules;
	}


	function add_to_add_node( $wp_admin_bar ) {
		$args = array(
			'id'     => 'Time Sheet',     // id of the existing child node (New > Post)
			'title'  => 'Time Sheet', // alter the title of existing node
			'parent' => 'new-content',          // set parent to false to make it a top level (parent) node
			'href'  => admin_url('admin.php?page=enter_timesheet')
		);
		$wp_admin_bar->add_node( $args );
	}
} //End Class

$main = new time_sheets_main();
$cron = new time_sheets_cron();
$entry = new time_sheets_entry();
$common = new time_sheets_common();

$options = get_option('time_sheets');

register_activation_hook(__FILE__, array($main, 'activation'));
add_action('admin_menu', array($main, 'custom_menu'));
add_action('admin_init', array($main, 'init_settings'), 1);
register_deactivation_hook( __FILE__, array($main, 'deactivation' ));
add_filter('upgrader_post_install', array($main, 'upgrade'), 10, 2); //Deploy database proc on upgrade as needed.

add_filter( 'cron_schedules', array($main, 'add_new_intervals'));
add_action('time_sheets_monthly_cron' , array($cron, 'InsertMonthlyInvoices'));
add_action('time_sheets_email_check' , array($cron, 'process_email'));
add_action('time_sheets_email_late_timesheets' , array($cron, 'email_late_timesheets'));
add_action('email_retainers_due', array($cron, 'email_retainers_due'));

add_action('admin_enqueue_scripts', array($common, 'enqueue_js'));
add_action('wp_enqueue_scripts', array($common, 'enqueue_js'));

if ($_GET) {
	$page = $_GET['page'];
} else {
	$page = '';
}

if ($page == "payroll_timesheet" || $page == "invoice_timesheet" || $page == "approve_timesheet" ) {
	add_action( 'admin_bar_menu', array($entry, 'process_timesheets'), 996);
}

if (($options['override_date_format'] <> 'system_defined' && $options['override_date_format'] <> 'admin_defined') || $options['day_of_week_timesheet_reminders']=='' || $options['week_starts']=='' || $options['queue_order'] == '' || $options['sales_override'] == '') {
	echo '<div if="message" class="error"><p>Settings changes are needed for the time sheet application. Please verify settings.</p></div>';	
	}

add_action( 'admin_bar_menu', array($main, 'add_to_add_node'), 100);

if ($options['show_header_open_invoices']) {
	add_action( 'admin_bar_menu', array($main, 'toolbar_open_invoices'), 996 );
}

if ($options['show_header_queues']) {
	add_action( 'admin_bar_menu', array($main, 'toolbar_pending_approval'), 997 );
	add_action( 'admin_bar_menu', array($main, 'toolbar_pending_invoicing'), 998 );
	add_action( 'admin_bar_menu', array($main, 'toolbar_pending_payroll'), 999 );
}

add_shortcode('timesheet_entry', array($entry, 'enter_timesheet'));
add_shortcode('timesheet_search', array($entry, 'search_timesheet'));
