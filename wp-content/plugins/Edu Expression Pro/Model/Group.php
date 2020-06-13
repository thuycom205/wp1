<?php
$dir = plugin_dir_path(__FILE__);
class Group extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post=$this->globalSanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'group_name'    => 'required|alphaNumericCustom'
                ));
        $gump->filter_rules(array(
                'group_name' => 'trim'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("group_name", "Group Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>