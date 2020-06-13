<?php
$dir = plugin_dir_path(__FILE__);
class Student extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'first_name'    => 'required|alphaNumericCustom',
                'last_name'    => 'alphaNumericCustom',
                'username'    => 'required|alphaNumericCustom',
                'email'    => 'required|valid_email',
                'enroll'    => 'alphaNumericCustom',
                'address'    => 'required|alphaNumericCustom',
                'phone'    => 'required|alphaNumericCustom',
                'alternate_number'    => 'alphaNumericCustom',
                'expiry_days'    => 'numeric'
                ));
        $gump->filter_rules(array(
                'first_name' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("first_name", "First Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>