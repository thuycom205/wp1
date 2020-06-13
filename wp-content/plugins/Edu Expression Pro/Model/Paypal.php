<?php
$dir = plugin_dir_path(__FILE__);
class Paypal extends ExamApps
{
   public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post);
        $gump->validation_rules(array(
                'username'    => 'required|alphaNumericCustom',
                'password'    => 'required|alphaNumericCustom',
                'signature'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'username' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("username", "username Invalid");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    } 
    
}
?>