<?php
$dir = plugin_dir_path(__FILE__);
class Smstemplate extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'name'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'name' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("name", "Sms Template Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>