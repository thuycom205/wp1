<?php
$dir = plugin_dir_path(__FILE__);
class Smssetting extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post);
        $gump->validation_rules(array(
                'api'    => 'required|alphaNumericCustom',
                'username'    => 'required|alphaNumericCustom',
                'password'    => 'required|alphaNumericCustom',
                'senderid'    => 'required|alphaNumericCustom',
                'husername'    => 'required|alphaNumericCustom',
                'hpassword'    => 'required|alphaNumericCustom',
                'hmobile'    => 'required|alphaNumericCustom',
                'hmessage'    => 'required|alphaNumericCustom',
                'hsenderid'    => 'required|alphaNumericCustom'
                ));
        
        $validatedData = $gump->run($post);
        GUMP::set_field_name("api", "Invalid Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }

}
?>