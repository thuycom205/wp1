<?php
$charset_collate=$wpdb->get_charset_collate();
$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_configurations`(
    `id` int(11) NOT NULL,
    `sms_notification` tinyint(1) DEFAULT '0',
    `email_notification` tinyint(1) DEFAULT '0',
    `manual_verification` tinyint(1) DEFAULT NULL,
    `paid_exam` tinyint(4) DEFAULT '0',
    `math_editor` tinyint(1) DEFAULT '0',
    `certificate` tinyint(1) DEFAULT '0',
    `currency` int(11) DEFAULT NULL,
    `signature` varchar(100) DEFAULT NULL,
    `exam_expiry` int(11) NOT NULL DEFAULT '1',
    `student_expiry` int(11) NOT NULL DEFAULT '1',
    `exam_feedback` tinyint(1) NOT NULL DEFAULT '0',
    `tolrance_count` int(1) DEFAULT NULL,
    `min_limit` int(11) DEFAULT NULL,
    `max_limit` int(11) DEFAULT NULL
) ENGINE=InnoDB $charset_collate;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_configurations`(`id`, `sms_notification`, `email_notification`, `manual_verification`, `paid_exam`, `math_editor`, `certificate`, `currency`, `signature`, `exam_expiry`, `student_expiry`, `exam_feedback`, `tolrance_count`, `min_limit`, `max_limit`) VALUES
(1, 1, 1, NULL, 1, NULL, 1, 21, '22579-18416.jpg', 0, 0, 1, 5, 20, 500);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_currencies`(
    `id` int(11) NOT NULL,
    `name` varchar(100) DEFAULT NULL,
    `short` varchar(3) DEFAULT NULL,
    `photo` varchar(100) DEFAULT NULL
    ) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=23 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_currencies` (`id`, `name`, `short`, `photo`) VALUES
(1, 'Australia Dollar AUD', 'AUD', '64238c6d767ab034b04c4681295567a0.gif'),
(2, 'Brunei Darussalam Dollar BND', 'BND', '53e34059e7bfe4db945404e901c4f396.gif'),
(3, 'Cambodia Riel KHR', 'KHR', 'aaa57dd0012641cdee2c8d6484db8238.gif'),
(4, 'China Yuan Renminbi CNY ', 'CNY', '5586a267c542d0f49b6c22c5c978bf23.gif'),
(5, 'Hong Kong Dollar HKD', 'HKD', '200ec0145292d85b380d8c4f570f9aa9.gif'),
(6, 'India Rupee INR', 'INR', '537f17a76864d11438d25ff5af7641a5.gif'),
(7, 'Indonesia Rupiah IDR', 'IDR', '6d27b2f196ce9d74b10d12111d9838b0.gif'),
(8, 'Japan Yen JPY', 'JPY', '3a7f86a61af62ddab4737f3df6db4807.gif'),
(9, 'Korea (North) Won KPW', 'KPW', 'cc0ad4a7ba48bedd9cf57bc4125fc2c9.gif'),
(10, 'Korea (South) Won KRW', 'KRW', '28fdcdac33f7429afe6bce2e08dd47c2.gif'),
(11, 'Laos Kip LAK', 'LAK', 'f72da580f617ee32683202aeee564df0.gif'),
(12, 'Malaysia Ringgit MYR', 'MYR', 'e86af0a98bf7398c27a5ad30319d82ad.gif'),
(13, 'Nigeria Naira NGN', 'NGN', '2cdb9ceeae309e948c6bd0a90e30ffec.gif'),
(14, 'Pakistan Rupee PKR', 'PKR', 'bac3525bb97f15f806a74d248f71d6b2.gif'),
(15, 'Philippines Peso PHP', 'PHP', 'c46c38e2701d3c3bd6ee442c93befd04.gif'),
(16, 'Singapore Dollar SGD', 'SGD', '2c1e20836f56700b13a08477216a61fb.gif'),
(17, 'Sri Lanka Rupee LKR', 'LKR', '38bb6c10813d0a1eb9c878bcea2b7570.gif'),
(18, 'Taiwan New Dollar TWD', 'TWD', 'a558976f34bf485cb72f61656595536c.gif'),
(19, 'Thailand Baht THB', 'THB', '3c3bcc74de1fd038ec2d7e0dfe2965bf.gif'),
(20, 'United Kingdom Pound GBP', 'GBP', 'df773c6ce35993089139c888ec5a3210.gif'),
(21, 'United States Dollar USD', 'USD', 'ef1e801ee13715b41e55c16886597878.gif'),
(22, 'Viet Nam Dong VND', 'VND', '5a5b143e1685239abd85f0b367d4669b.gif');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_diffs` (
  `id` int(11) NOT NULL,
  `diff_level` varchar(255) DEFAULT NULL,
  `type` char(1) DEFAULT NULL
) ENGINE=InnoDB $charset_collate;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_diffs` (`id`, `diff_level`, `type`) VALUES
(1, 'Easy', 'E'),
(2, 'Medium', 'M'),
(3, 'Hard', 'D');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_emailtemplates` (
`id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `status` varchar(11) DEFAULT 'Published',
  `type` varchar(3) DEFAULT NULL
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=5 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_emailtemplates` (`id`, `name`, `description`, `status`, `type`) VALUES
(1, 'Student Login Credentials', '<p>Dear".' $studentName'.",</p><p>Congratulations! Your".' $siteName'." account is now active.</p><p>Email Address : ".'$email'."</p><p>Username : ".'$userName'."</p><p>Password: ".'$password'."</p><p>If you need, you can reset your password at any time.</p><p>To get started, log on:<a href=".'"$url"'." target=\"_blank\">".'$url'."</a></p><p>If you have any questions or need assistance, please contact us.</p><p> </p><p>Best Regards,</p><p>".'$siteName'."</p>', 'Published', 'SLC'),
(2, 'Exam Activation', '<p>Dear ".'$studentName'.",</p><p>Exam Name ".'$examName Type'." ".'$type'." is active and start on ".'$startDate'." end on ".'$endDate'."</p><p>Sincerely,</p><p>".'$siteName'."</p>', 'Published', 'EAN'),
(3, 'Exam Finalized', '<p>Dear ".'$studentName'.",</p><p>Name: ".'$examName'."</p><p>Result: ".'$result'."</p><p>Rank: ".'$rank'."</p><p>Obtained Marks: ".'$obtainedMarks'."</p><p>Question Attempt: ".'$questionAttempt'."</p><p>Time Taken: ".'$timeTaken'."</p><p>Percentage: ".'$percent'."</p><p> </p><p>Sincerely,</p><p>".'$siteName'."</p>', 'Published', 'EFD'),
(4, 'Exam Result', '<p>Dear ".'$studentName'.",</p><p>Name: ".'$examName'."</p><p>Result: ".'$result'."</p><p>Obtained Marks: ".'$obtainedMarks'."</p><p>Question Attempt: ".'$questionAttempt'."</p><p>Time Taken: ".'$timeTaken'."</p><p>Percentage: ".'$percent'." %</p><p> </p><p>Sincerely,</p><p>".'$siteName'."</p>', 'Published', 'ERT');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exams` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `instruction` text,
  `duration` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `passing_percent` int(11) DEFAULT NULL,
  `negative_marking` varchar(3) DEFAULT NULL,
  `attempt_count` int(11) DEFAULT NULL,
  `declare_result` varchar(3) DEFAULT 'Yes',
  `finish_result` char(1) DEFAULT '0',
  `ques_random` char(1) DEFAULT '0',
  `paid_exam` char(1) DEFAULT '0',
  `browser_tolrance` char(1) DEFAULT '1',
  `instant_result` char(1) DEFAULT '0',
  `option_shuffle` char(1) DEFAULT '1',
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'Inactive',
  `type` varchar(10) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `expiry` int(11) DEFAULT '0',
  `finalized_time` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_feedbacks` (
`id` int(11) NOT NULL,
  `exam_result_id` int(11) NOT NULL,
  `comments` mediumtext,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_groups` (
`id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_maxquestions` (
`id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `max_question` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_orders` (
`id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `exam_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_preps` (
`id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `ques_no` int(11) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_questions` (
`id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_results` (
`id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_question` int(11) DEFAULT NULL,
  `total_attempt` int(11) DEFAULT NULL,
  `total_answered` int(11) DEFAULT NULL,
  `total_marks` decimal(5,2) DEFAULT NULL,
  `obtained_marks` decimal(5,2) DEFAULT NULL,
  `result` varchar(10) DEFAULT NULL,
  `percent` decimal(5,2) DEFAULT NULL,
  `finalized_time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_stats` (
`id` int(11) NOT NULL,
  `exam_result_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `question_id` int(11) NOT NULL,
  `ques_no` int(11) DEFAULT NULL,
  `options` varchar(30) DEFAULT NULL,
  `attempt_time` datetime DEFAULT NULL,
  `opened` char(1) DEFAULT '0',
  `answered` char(1) DEFAULT '0',
  `review` char(1) DEFAULT '0',
  `option_selected` varchar(15) DEFAULT NULL,
  `answer` text,
  `true_false` varchar(5) DEFAULT NULL,
  `fill_blank` text,
  `correct_answer` text,
  `marks` decimal(5,2) DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `ques_status` char(1) DEFAULT NULL,
  `closed` char(1) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `checking_time` datetime DEFAULT NULL,
  `bookmark` char(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_exam_warns` (
`id` int(11) NOT NULL,
  `exam_result_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_groups` (
`id` int(11) NOT NULL,
  `group_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_helpcontents` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `status` varchar(11) DEFAULT 'Published'
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=3 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_helpcontents` (`id`, `name`, `description`, `status`) VALUES
(1, 'Help 1', '<p>Suspendisse mattis magna augue, sed pretium lacus pellentesque nec. Nullam tincidunt lacinia urna sit amet tincidunt. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras consequat justo ac diam aliquet adipiscing. Ut orci nibh, viverra quis luctus id, lacinia quis purus. Vestibulum pharetra diam non nulla pretium scelerisque. Fusce posuere tellus vel mollis auctor.</p>', 'Published'),
(2, 'Help2', '<p>Aenean pretium nunc lectus, quis viverra metus accumsan vestibulum. Mauris vulputate urna nec leo viverra, at dictum nulla suscipit. Sed id pretium lectus, vitae egestas turpis. Quisque metus tortor, tristique in diam sit amet, suscipit facilisis augue. Nunc vel leo vitae ligula auctor tristique ut nec tortor. Aliquam nibh ligula, tristique non pharetra in, congue ac sem. Donec odio nulla, lobortis vitae risus in, porttitor pretium mauris. Nullam fringilla tortor eu quam luctus, eget bibendum lectus eleifend. Nam facilisis libero tempor rhoncus consequat.</p>', 'Published');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_payments` (
`id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(20) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_paypal_configs` (
`id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `sandbox_mode` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=2 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_paypal_configs` (`id`, `username`, `password`, `signature`, `sandbox_mode`) VALUES
(1, '', '', '', 1);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_qtypes` (
`id` int(11) NOT NULL,
  `question_type` varchar(255) DEFAULT NULL,
  `type` char(1) DEFAULT NULL
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=5 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_qtypes` (`id`, `question_type`, `type`) VALUES
(1, 'Objective', 'M'),
(2, 'True / False', 'T'),
(3, 'Fill in the blanks', 'F'),
(4, 'Subjective', 'S');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_questions` (
`id` int(11) NOT NULL,
  `qtype_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `diff_id` int(11) NOT NULL,
  `question` text,
  `option1` text,
  `option2` text,
  `option3` text,
  `option4` text,
  `option5` text,
  `option6` text,
  `marks` decimal(5,2) DEFAULT NULL,
  `negative_marks` decimal(5,2) DEFAULT NULL,
  `hint` text,
  `explanation` text,
  `answer` varchar(15) DEFAULT NULL,
  `true_false` varchar(5) DEFAULT NULL,
  `fill_blank` text,
  `status` varchar(3) DEFAULT 'Yes',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_question_groups` (
`id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_smssettings` (
`id` int(11) NOT NULL,
  `api` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `senderid` varchar(10) DEFAULT NULL,
  `husername` varchar(100) DEFAULT NULL,
  `hpassword` varchar(100) DEFAULT NULL,
  `hsenderid` varchar(100) DEFAULT NULL,
  `hmobile` varchar(100) DEFAULT NULL,
  `hmessage` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=2 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_smssettings` (`id`, `api`, `username`, `password`, `senderid`, `husername`, `hpassword`, `hsenderid`, `hmobile`, `hmessage`) VALUES
(1, '', '', '', '', '', '', '', '', '');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_smstemplates` (
`id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `status` varchar(11) DEFAULT 'Published',
  `type` varchar(3) DEFAULT NULL
) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=5 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="INSERT INTO `".$wpdb->prefix."emp_smstemplates` (`id`, `name`, `description`, `status`, `type`) VALUES
(1, 'Student Login Credentials', 'Dear ".'$studentName'.", Your ".'$siteName'." account is now active. Email: ".'$email'." Username: ".'$userName'." Password: ".'$password'." Website:".'$url'." Best Regards, ".'$siteName'."', 'Published', 'SLC'),
(2, 'Exam Activation', 'Dear ".'$studentName'.", Exam Name ".'$examName'." Type ".'$type'." is active and start on ".'$startDate'." end on ".'$endDate'." Sincerely, ".'$siteName'."', 'Published', 'EAN'),
(3, 'Exam Finalized', 'Dear ".'$studentName'.", Name: ".'$examName'." Result: ".'$result'." Rank: ".'$rank'." Obtained Marks: ".'$obtainedMarks'." Question Attempt: ".'$questionAttempt'." Time Taken: ".'$timeTaken'." Percentage: ".'$percent'." % Sincerely, ".'$siteName'."', 'Published', 'EFD'),
(4, 'Exam Result', 'Dear ".'$studentName'.", Name: ".'$examName'." Result: ".'$result'." Obtained Marks: ".'$obtainedMarks'." Question Attempt: ".'$questionAttempt'." Time Taken: ".'$timeTaken'." Percentage: ".'$percent'." % Sincerely, ".'$siteName'."', 'Published', 'ERT');";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_students` (
`id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `status` varchar(10) DEFAULT 'Unverified'
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_student_groups` (
`id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_subjects` (
`id` int(11) NOT NULL,
  `subject_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_subject_groups` (
`id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_user_groups` (
`id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."emp_wallets` (
`id` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `in_amount` decimal(18,2) DEFAULT NULL,
  `out_amount` decimal(18,2) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `type` varchar(2) DEFAULT NULL,
  `remarks` tinytext,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1 ;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_configurations` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_currencies` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_diffs` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_emailtemplates` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exams` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_feedbacks` ADD PRIMARY KEY (`id`), ADD KEY `exam_result_id` (`exam_result_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_groups` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `group_id` (`group_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_maxquestions` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `subject_id` (`subject_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_orders` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `student_id` (`student_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_preps` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `subject_id` (`subject_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_questions` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `question_id` (`question_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_results` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `student_id` (`student_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_stats` ADD PRIMARY KEY (`id`), ADD KEY `exam_id` (`exam_id`), ADD KEY `student_id` (`student_id`), ADD KEY `question_id` (`question_id`), ADD KEY `exam_result_id` (`exam_result_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_warns` ADD PRIMARY KEY (`id`), ADD KEY `exam_result_id` (`exam_result_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_groups` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `group_name` (`group_name`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_helpcontents` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_payments` ADD PRIMARY KEY (`id`), ADD KEY `student_id` (`student_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_paypal_configs` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_qtypes` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_questions` ADD PRIMARY KEY (`id`), ADD KEY `qtype_id` (`qtype_id`), ADD KEY `subject_id` (`subject_id`), ADD KEY `diff_id` (`diff_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_question_groups` ADD PRIMARY KEY (`id`), ADD KEY `question_id` (`question_id`), ADD KEY `group_id` (`group_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_smssettings` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_smstemplates` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_students` ADD PRIMARY KEY (`id`), ADD KEY `student_id` (`student_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_student_groups` ADD PRIMARY KEY (`id`), ADD KEY `student_id` (`student_id`), ADD KEY `group_id` (`group_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_subjects` ADD PRIMARY KEY (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_subject_groups` ADD PRIMARY KEY (`id`), ADD KEY `subject_id` (`subject_id`), ADD KEY `group_id` (`group_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_user_groups` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `group_id` (`group_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_wallets` ADD PRIMARY KEY (`id`), ADD KEY `student_id` (`student_id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_configurations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_currencies` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_diffs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_emailtemplates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exams` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_feedbacks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_maxquestions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_preps` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_questions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_results` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_stats` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_warns` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_helpcontents` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_payments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_paypal_configs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_qtypes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_questions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_question_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_smssettings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_smstemplates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_students` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_student_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_subjects` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_subject_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_user_groups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_wallets` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_feedbacks` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_feedbacks_ibfk_1` FOREIGN KEY (`exam_result_id`) REFERENCES `".$wpdb->prefix."emp_exam_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_groups` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_groups_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `".$wpdb->prefix."emp_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_maxquestions` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_maxquestions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_maxquestions_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `".$wpdb->prefix."emp_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_orders` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_orders_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_orders_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_preps` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_preps_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_preps_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `".$wpdb->prefix."emp_subjects` (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_questions` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `".$wpdb->prefix."emp_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_results` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_stats` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_stats_ibfk_1` FOREIGN KEY (`exam_result_id`) REFERENCES `".$wpdb->prefix."emp_exam_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_stats_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `".$wpdb->prefix."emp_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_stats_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_exam_stats_ibfk_4` FOREIGN KEY (`question_id`) REFERENCES `".$wpdb->prefix."emp_questions` (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_exam_warns` ADD CONSTRAINT `".$wpdb->prefix."emp_exam_warns_ibfk_1` FOREIGN KEY (`exam_result_id`) REFERENCES `".$wpdb->prefix."emp_exam_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_payments` ADD CONSTRAINT `".$wpdb->prefix."emp_payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_questions` ADD CONSTRAINT `".$wpdb->prefix."emp_questions_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `".$wpdb->prefix."emp_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_questions_ibfk_3` FOREIGN KEY (`qtype_id`) REFERENCES `".$wpdb->prefix."emp_qtypes` (`id`),
ADD CONSTRAINT `".$wpdb->prefix."emp_questions_ibfk_4` FOREIGN KEY (`diff_id`) REFERENCES `".$wpdb->prefix."emp_diffs` (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_question_groups` ADD CONSTRAINT `".$wpdb->prefix."emp_question_groups_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `".$wpdb->prefix."emp_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_question_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `".$wpdb->prefix."emp_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_students` ADD CONSTRAINT `".$wpdb->prefix."emp_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_student_groups` ADD CONSTRAINT `".$wpdb->prefix."emp_student_groups_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_student_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `".$wpdb->prefix."emp_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_subject_groups` ADD CONSTRAINT `".$wpdb->prefix."emp_subject_groups_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `".$wpdb->prefix."emp_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_subject_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `".$wpdb->prefix."emp_groups` (`id`);";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_user_groups` ADD CONSTRAINT `".$wpdb->prefix."emp_user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `".$wpdb->prefix."emp_user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `".$wpdb->prefix."emp_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);

$sql="ALTER TABLE `".$wpdb->prefix."emp_wallets` ADD CONSTRAINT `".$wpdb->prefix."emp_wallets_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `".$wpdb->prefix."users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;";
$wpdb->query($sql);
?>