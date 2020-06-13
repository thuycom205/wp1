<?php
$dir = plugin_dir_path(__FILE__);
class Currency extends ExamApps
{
    public function validate($post)
    {
        $gump = new GUMP();
        $post = $gump->sanitize($post); // You don't have to sanitize, but it's safest to do so.
        $gump->validation_rules(array(
                'name'    => 'required|alpha_space'
                ));
        $gump->filter_rules(array(
                'name' => 'trim|sanitize_string'
                ));
        $validatedData = $gump->run($post);
        GUMP::set_field_name("name", "Currency Name");
        return array('validatedData'=>$validatedData,'error'=>$gump->get_readable_errors(true));
    }
}
?>