=== Dynamic Time ===
Contributors: rermis
Tags: timesheet, time sheet, timecard, time card, clock in, punch in, overtime, time tracking, time tracker
Donate link: https://richardlerma.com/r1cm
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: 3.3.8
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple calendar-based timecard and timesheet solution. 

== Description ==
A simple calendar-based time card and time sheet solution. Record hours & notes on weekly, bi-weekly, monthly or bi-monthly schedules, including automatic overtime calculations. Dynamic Time is mobile compatible and integrates with existing WordPress users.

= Special Features =
* Automatic overtime calculations, configurable by user, even across pay periods
* Multiple punches per day, including notes
* Fully configurable pay periods
* Approval process between user, supervisor and payroll
* Automatic integration with existing WordPress users

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/dynamic-time` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Visit Dynamic Time from the WordPress admin menu to configure settings

== Screenshots ==
1. Timesheet set up for bi-weekly pay period
2. Settings Page with user list of entries

== Changelog ==
= 3.3.8 = * Supervisor permissions fixes to plugin admin page.
= 3.3.7 = * Improvements to supervisor permissions in entry list view.
= 3.3.6 = * Entry list access for supervisors with a minimum of editor role or list_users capability. Settings access for admins with manage_options capability. Addition of punch entry type option.
= 3.3.5 = * Improvements to setup instructions.
= 3.3.4 = * Bug fix: Allow all itemized entry deletion for pay period by setting entry to 0.0 on clear.
= 3.3.3 = * Bug fix: Duplicate display of more than 3 entries per day.
= 3.3.2 = * Developer extensions and compatibility improvements. WordPress dashboard entry for non-admins. Simplify setup process. Minor bug fixes.
= 3.2.9 = * Allow reverse/reset of pay period by admins. Lock submission from user until halfway thru period.
= 3.2.8 = * Bug fixes for js unset variables.
= 3.2.7 = * Allow notes on simple or itemized entry setting.
= 3.2.5 = * Bug fix for itemized setting dropdown.
= 3.2.4 = * Diagnostic checks for DB safe & strict mode.
= 3.2.2 = * Addition of pay period note and bonus fields. Bi-weekly navigation updates. Reverse all function improvements. Precautions to prevent deletion of period data.
= 3.2.1 = * Bug fix: users last active timeframe. Lock submit button on future pay periods to reduce incorrect submissions.
= 3.2.0 = * Refine users last active timeframe. Support for beta features.
= 3.1.19 = * Update Currency field to allow open text, Addition of User Account icon. Correction to split week overtime calculations.
= 3.1.18 = * Improvements to installation stability and upgrade option.
= 3.1.15 = * Minor improvements, additions to diagnostics.
= 3.1.12 = * JS overtime calculation bug resolved affecting bi-monthly or monthly pay periods when pay period begin day falls on week begin day. Overtime bug resolved when multiple in outs exist not matching subsequent hour type.
= 3.1.11 = * PHP warnings resolved in calendar view. Various minor improvements.
= 3.1.9 = * Multi-entry per Day calculation bug resolved.
= 3.1.8 = * Minor CSS improvements.
= 3.1.7 = * Addition of multisite and install loc to diagnostic module. Pay Period title on timecard. Mobile CSS optimizations.
= 3.1.5 = * User Entries default to active users within 3 months. Add time calculations to accommodate overnight shifts.
= 3.1.4 = * Compatibility for PHP shorthand=off. Improvement to punch in/out feature.
= 3.1.1 = * Bug fixes to overtime calculations on split weeks. Bug fixes to overtime calculations days with multiple in/outs.
= 3.0.9 = * Visual enhancements, stability updates.
= 3.0.8 = * Add close buttons to modules. Minor updates to inform users of new features.
= 3.0.6 = * Minor bug fixes and compatibility updates. Prep for future features.
= 3.0.4 = * Add conditions for error reporting, add functions for dynamic file paths and prepare db calls.
= 3.0.3 = * Revise some features to use separate add on plugin.
= 3.0.1 = * Upgrade options: Table-based time entry overviews. Pay period optionally configurable on user level. JS bug fix for simple time multiple entry.
= 2.8.2 = * Addition of Australian currency. Updated print CSS for front end. Hide 'Get Started' if setup is complete with at least one user. Smooth view transitions. Addition of title tips for all setup options. Live calculation for time during input or adjustment. Focus behavior refinements to time popup. New configuration option to keep data safe on uninstall. New database fields to store summary per pay period.
= 2.7.5 = * Bug fix for split-week OT and CA split-week carryforward exclusion for day OT. Addition of Canadian currency.
= 2.7.4 = * Added hard refresh to refresh button. Fixed PHP warning. Added supvs to diagnostics.
= 2.7.2 = * Bug fixes for screen switching JS function. Addition of currency options. 
= 2.6.9 = * Refinements to punch and navigation buttons. Confirmation for submission and approval actions. JS consolidation.
= 2.6.8 = * Compatibility updates for IE 11.
= 2.6.6 = * Minor bug fixes compatibility with 4.9.3.
= 2.6.3 = * Resolved email bug affecting some server configurations.
= 2.6.2 = * CSS updates to print media. Refresh button in admin view.
= 2.6.1 = * Allow admin reversal of submission, approval and processing timestamps.
= 2.6.0 = * Mobile improvements. Minor bug fix for non-admin supervisor approvals.
= 2.5.8 = * California overtime option to trigger overtime for hours worked in excess of 8 hours/day or 40 hours/week.
= 2.5.7 = * Accommodation for servers without PHP shorthand turned on. Bug fixes for setup, upgrades, and version syncing db versions.
= 2.5.5 = * Mods to database initial setup and future upgrades. Store table version indicator. Check version and trigger dbDelta.
= 2.5.3 = * Email and approval process addition. Improved navigation between pay periods. Hourly rate archival. Addition of diagnostics. General optimization and bug fixes.
= 2.1.5 = * Resolve overtime bug. CSS adjustments. Image compression.
= 2.1.3 = * Update css & js file revision parameters.
= 2.1.1 = * Punch in quick-click buttons. 15 minute increment arrows. General aesthetic improvements and bug fixes.
= 2.0.5 = * Resolved survey hide bug, added setup tips
= 2.0.2 = * Resolved JS prompt/time bug
= 2.0.1 = * Resolved overtime calculation bug
= 2.0.0 = * Addition of notes field, css refinements, stability updates
= 1.3.3 = * CSS adjustments
= 1.3.2 = * Added redirect/return logic
= 1.3.1 = * Force print compatibility with most WP themes
= 1.3.0 = * Tested up to 4.8.2
= 1.2.8 = * Define Acronyms in summary, Default initial users to Non-Exempt
= 1.2.7 = * Installation bug fixes
= 1.2.1 = * Visual enhancements to entry list
= 1.2.0 = * Added survey option
= 1.1.5 = * Compatibility verification with WP 4.8
= 1.1.3 = * Compatibility fixes with old PHP versions
= 1.1.2 = * Aesthetic improvements to work with parent themes, minor bug fixes
= 1.0.5 = * Resolved initial setup menu selections * Order user entries by last entry day, last modified date
= 1.0.9 = * Resolved svn folder paths
= 1.1.1 = * Minor bug fixes
= 1.0.2 = * Updated directory logo & expanded readme documentation
= 1.0.1 = * Added directory logo & alert icons for setup
= 1.0   = * Basic functionality created

== Frequently Asked Questions ==

= Does this plugin have a user limit? =
This plugin works with an unlimited number of users/employees.