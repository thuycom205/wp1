<?php
$dir = plugin_dir_path(__FILE__);
class Payment extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'amount'    => 'required|numeric'
                ));
        
        $validatedData = $gump->run($post);
        GUMP::set_field_name("amount", "Amount");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>