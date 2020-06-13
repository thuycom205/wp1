<?php
//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN'))
exit();
global $wpdb;

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_configurations`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_currencies`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_emailtemplates`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_warns`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_stats`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_feedbacks`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_results`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_questions`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_preps`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_orders`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_maxquestions`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exam_groups`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_exams`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_helpcontents`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_paypal_configs`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_smssettings`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_smstemplates`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_wallets`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_user_groups`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_student_groups`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_payments`");

$wpdb->query("DELETE `User` FROM `".$wpdb->prefix."users` AS `User` INNER JOIN `".$wpdb->prefix."emp_students` AS `Student` ON(`User`.`ID`=`Student`.`student_id`);");

$wpdb->query("DELETE `Usermeta` FROM `".$wpdb->prefix."usermeta` AS `Usermeta` LEFT JOIN `".$wpdb->prefix."users` AS `User` ON(`User`.`ID`=`Usermeta`.`user_id`) WHERE `User`.`ID` IS NULL;");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_students`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_question_groups`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_questions`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_subject_groups`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_subjects`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_diffs`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_qtypes`");

$wpdb->query("DROP TABLE `".$wpdb->prefix."emp_groups`");


?>