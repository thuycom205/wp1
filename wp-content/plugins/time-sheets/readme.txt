=== Time Sheets ===
Contributors: mrdenny
Donate Link: http://dcac.co/go/time-sheets
Tags: ticketing system, time sheets, business management, consulting, workflow, invoicing, payroll, time tracking
Requires at least: 4.7.0
Tested up to: 4.9.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A fully configurable time sheet system which allows for employee time tracking, workflows, time sheet approvals, invoicing and payroll processes.

== Description ==

A fully configurable time sheet system which allows for employee time tracking, workflows, time sheet approvals, invoicing and payroll processes.

The system is pretty straight forward to configure.  It supports a basic workflow of employees submitting timesheets to their supervisor, who then approves or denies the time sheet. Once approved the time sheets go over to the accounts receivable queue (we call it the Invoicing Queue) so that the customer can be invoiced.  From there if needed it goes to the payroll queue so that expenses can be paid back to the employee.  There’s even a setting for making all invoices that someone submits go to the invoicing queue in case you have hourly employees that you need to handle.

When clients are entered into the system, there is security setup on the clients so that only employees who are working with those clients can see them in their drop down.  This makes the drop down smaller for employees and keeps any third party contractors that are working for you from seeing your entire client list.

The system is configured to allow for retainer projects that get billed automatically and it drops in reminder time sheets to the invoicing queue so that those invoices are invoiced at the beginning of each month.  Reminders are also sent to employees automatically if they have overdue time sheets or if they are working on retainers and they need to get their time sheets in at the end of the month.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the contents of the zip file to the `/wp-content/plugins/time-sheets` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings through the settings page.
4. Begin documenting customers, projects and employee access through the various settings pages.


== Frequently Asked Questions ==

= Can I add more workflows? =
No, not at this time.

= Is the Payroll workflow mandatory? =
No, if you don't configure any expense types, and you don't configure any employees to force their time sheets to payroll then the payroll queue will not be shown.

= Is the Invoicing workflow mandatory? =
Depends how you look at things.  If you don't approve anyone for the invoicing queue, but then configure the system for parallel queues for Invoicing and Payroll then the payroll queue will still work but the invoicing queue won't be used.

= Is the Approval Queue mandatory? =
Yes, there's no way to get invoices into the invoicing and/or payroll queues without someone approving them.

= Why is there a fraction shown after the approval and invoicing queues? =
The first number shown is the number of non-retainer time sheets which are pending.  The second number is then number of time sheets on projects which are marked as retainer projects.  The approval may have a third number up there. That is the number of time sheets which are under embargo.  The payroll queue doesn't have a different between retainer and non-retainer time sheets so there's just a single number showing that queue.

= Is there a way to turn off all the retainer settings as we don't need them? = 
Not at this time. If we get some requests to make that an option we'll look into it.

= Can I change the headings on the expenses section? =
No you can't. If you would like this feature added contact us and we'll add it to our backlog.

= When do retainer reminders get sent out? =
They are sent out on the last day of the month.

= When do reminders for late time sheets get sent out? =
They are sent out on Monday mornings.

= What is a work week defined as? =
You can configure it to start on whatever day you'd like. By default it'll configure itself for the week starting on Monday, but you can change it.  It wouldn't be recommneded to change it after starting to use it, as the old timesheets won't be updated.

= How do employees start getting reminders about retainer time sheets being do? =
As soon as they create a time sheet for a retainer project, they'll start getting the reminders.

= How do employees stop getting reminders about retainer time sheets being do? =
If they stop submitting time sheets for 60 days or more, then they will no longer receive the retainer reminders.

= If there's multiple emails of the same time which need to be sent, will they be sent one at a time? =
No, the emails are combined before they are sent out.  This helps minimize the number of emails so the employees don't get a flood of emails.  If the same email subject is queued for an employee within 5 minutes the employee won't get the email until another 5 minutes has passed.

= Can time sheets which have been sent to the invoicing queue be rejected? =
Yes. If an employee has the approval queue and the invoicing queue then they will be able to reject time sheets from the invoicing queue.

= Can time sheets which have been sent to the payroll queue be rejected? =
Yes they can.  Depending on how you have your queue workflows being done this could cause odd things to happen to the timesheet. So be careful.

= How many clients does the system support? =
As many as you need.

= How many employees does the system support? =
As many users as WordPress supports in the system.

= Where are the settings located? =
The settings are located under the Time Sheets parent menu. This includes the Global settings which are only available to people with the "manage_settings" WordPress settings (admins for example) as well as the "My Settings" page which is available for all users.

= Is there are specific system requirements? =
No. We run this for our business on a database with a single CPU core and a web server with a single CPU core (two machines) and the performance is exactly as expected (your mileage may vary).  If you see performance problems please let us know.

== Screenshots ==

1. Employee time sheet entry form
2. List of employees time sheets
3. Adding a new client to the system
4. Employees granted access to a client
5. "Retainer" settings and client notes for clients
6. New Project settings
7. Supervisor time sheet approval screen
8. Invoicing time sheet recording screen
9. Adding users to Approval, Invoicing and Payroll workflow
10. Global application settings
11. Admin menu header showing various queues with pending invoices
12. Available options on the per employee "My Settings" menu.

== Changelog ==

= 1.7.2 =
* Fixed bug to approve imbargoed time sheets

= 1.7.1 =
* Fixed updated code

= 1.7.0 =
* Added Sales Person dropdown to Client and Project
* Added Sales Person to invoicing queue
* Added Sales Person priority to settings page
* Added check for sales person setting to main screen


= 1.6.18 =
* Removed test from the entry form.
* Fixed monthy retainer emails and resetting of clients.

= 1.6.17 =
* Fixes the approvals menu so embargoes time sheets can be updated correctly.

= 1.6.16 =
* In the approval menu, clicking approve, reject or hold will set all time sheets to that settings.

= 1.6.15 =
* Fixed review screen so it works from all approval menus.

= 1.6.14 =
* Week Complete field on My Dashboard is now centered.

= 1.6.13 =
* Fixed permissions on dashboard so regular users couldn't see other people time sheets.
* Fixed dashboard so search worked properly.
* Change "Setup Approval Teams" so it doesn't show debug information when adding a team member.


= 1.6.12 =
* Fixed permissions on Manage Clients screen so the Manage Clients permissions actually work.
* Added PO Number to new project screen.

= 1.6.11.1 =
* No code change, but some updated gaphics to push down and a new readme.

= 1.6.11 =
* Added po number as a project field and display it on the invoicing screen.

= 1.6.10 =
* Added Hours to My Dashboard and to Payroll Queue.

= 1.6.9 =
* Fixing My Settings so it works for people who are only Invoing Queue users.

= 1.6.8.1 =
* Fixing 1.6.8 so it actually works.

= 1.6.8 =
* Allow invocing users to decide to put the "Record Invoicing" button at the top of the list all the time.

= 1.6.7 =
* Documentation!!!!!!!
* Make monthly retainers not show up in the payroll queue at the top of the month.
* Removed comments from time_sheets_cron.email_retainers_due in a vauge attempt to make it work correctly each month.

= 1.6.6 =
* Changed size of currency fields to support larger amounts.

= 1.6.5 =
* Fixed bug where timesheet queues weren't being loaded in parallel correctly, and timesheets could be sent to the payroll queue twice.

= 1.6.4 =
* Fixed bug where the submit button wasn't visible on new time sheets
* Fixed bug where all timesheets were being sent to payroll queue


= 1.6.3 =
* Changed label in the admin bar to My Time Sheet Dashboard
* Removed Submit button if viewing another users time sheet
* Added logic to prevent updating another users time sheet


= 1.6.2 =
* Fixed bug where the Project drop down doesn't work in the time sheet screen.

= 1.6.1 =
* Fixed bugs in query of My Dashboard where it was showing all employees at initial load instead of just that persons.
* Changed text in My Dashboard if not matches are found.


= 1.6 =
* Display project name when project is over hours
* Rename Old Timesheets to My Dashboard
* Add additional filters to My Dashboard (all employees, my time sheets, specific team member, all team members, filter by client and project)
* Display employees client notes and project names on My Dashboard
* Updated screenshot of My Dashboard (Screenshot 2)

= 1.5.17 =
* Fixed bug where the payrol queue is showing more invoices then it should if you are using parallel workflows.

= 1.5.16 =
* Added the ability to split the queue process so that Payroll can be processed before invoicing.

= 1.5.15 =
* Hack to make the dates on the entry page correct on matter what timezone it is. This isn't a perfect fix, but it'll do for now.

= 1.5.14 =
* Made the week start date configurable.
* Made the dates for the work days in the timesheet change on the fly based on the date selected (I hate JavaScript).
* Added checks for the new setting on startup.
* Resetting the cron so that the cron jobs fire at the correct time after the 1.5.13 upgrade.

= 1.5.13 =
* Added clarity to the screens granting access to parts of the system.
* Fixed the crons so that weekly reminders go out on Monday instead of being based on the time that the plugin was last updated.
* Corrected minimum required version of WordPress to 4.7.0.

= 1.5.12 =
* Fixed issue with employees who need to always need to go to payrol queue, and the project was set to skip the invoicing queue didn't make it into the payrol queue.

= 1.5.11 =
* Added Hold option to approver menu.

= 1.5.10 =
* Disable save new project button disabled after saving, to prevent duplicate projects from being saved.

= 1.5.9 =
* Better cleaning of single quotes in text fields to get rid of the escape chatacter that wordpress and PHP like to stick in there.
* Changed label on filter button for invoincing menu.
* Added submit buttons above the approval and invoicing lists when the list is long.
* Fixed sorting issues on various employee lists.
* Added Display Name to client permissions page and sorted by Display Name.

= 1.5.8 =
* Adding option to My Settings page for people who have access to the Invoicing queue to allow custom sorting of the output for easier invoicing based on user needs.

= 1.5.7 =
* Fixed problem with invoicing menu not saving changes. 
* Changed label on filter button for invoincing menu.
* Removed un-needed/un-used functions from primary file.

= 1.5.6 =
* Remove clients with no active project from time sheet client list.

= 1.5.5 =
* Fixed label on submit button on approval queue.

= 1.5.4 =
* Added ability to set project as flat rate billing do time sheets bypass the invoicing queue.
* Updated FAQ

= 1.5.3 =
* Added ability to create a new time sheet from the "New" menu in the admin bar (who can see that menu).
* Made the javascript a little more dynamic to account for features being turned on or off.

= 1.5.2 =
* Resolved additional potential cross site scripting vunerabilities.
* Removed some un-needed code (the calls had already been moved to common, but the old functions were still sitting there).
* Added a setting to remove the embargo feature if it isn't needed.
* Little cleanup in the client list code for the client management screens.

= 1.5.1 =
* Resovled issue with monthly cron job not updating retainer hours.

= 1.5.0 =
* Fixed possible cross site scripting in old timesheet list.
* Added feature to allow for hiding client and project dropdown if only one client and project are available.
* Included pollyfil.js and all needed files.
* Changed to internal jquery not external.

= 1.4.2 =
* Set the date picker for all date fields.
* Moved date picker CSS and Javascript calls to common class.

= 1.4.1 =
* Fixed formatting issue in settings
* Converted monthly notes to text box to minimize space being used by them while allowing for basically as much text as is desired.

= 1.4.0 =
* Clean up errors to make them more visible.
* Add data cleanup to date fields when searching and viewing client list.
* Fixed split time sheet from the approval menu.
* Added calendar popup to time sheet entry.
* Added warning when time sheet isn't starting on a Monday.
* Allow admin to customize date format.
* Allow admin to allow users to specify their own date format.
* Changed client and project notes to text boxes to minimize space being used by them while allowing for basically as much text as is desired.
* Updated FAQ

= 1.3.1 =
* Fixed issue where time sheets weren't saving or throwing an error message. They save now.

= 1.3 =
* Fixed Pier Diem days field converting to an integer. Now accepts decimals.
* Added option "Open Time Sheets" to header.
* Added ability to put new time sheet within a page using shortcut code timesheet_entry.
* Added ability to put new time sheet within a page using shortcut code timesheet_search.
* Increased security on the new and search time sheet pages due to the public short codes being available for use.
* Added setting to support the link redirection that needs to happen when using the timesheet_search shortcut code.


= 1.2.1 =
* Fixed menu for editing client not working correctly.

= 1.2 =
* Changed Edit Client Permissions to Edit Client and added the ability to change a client's name.

= 1.1.1 =
* Max hours on retainer projects now adjust monthly based on the hours used plus the available hours per month so the project alerts are accurate for retainer projects.
* Max hours on retainer projects not editable anymore.

= 1.1 =
* Major code refactoring.
* Cleaning up display names and user names.

= 1.0.2 =
* Fixed bug in invoicing where if time sheets were given an invoice number, but not marked as processed they could loose their invoice number when working on other invoices.
* Added ability to turn off notes and expenses sections for teams.
* Added ability to add a backup approver for teams (perfect for approvers who take vacations, or have an assistant).
* Added screen to manage those who can add customers and projects.

= 1.0.1 =
* Added per diem city to time sheets.

= 1.0 =
* First release

== Upgrade Notice ==


