<?php
class Tinymce
{
    public $_script = false; 
    public function input($fieldName,$fieldValue,$attributeArr,$tinyAttribute,$type)
    {
        if(!$this->_script){ 
            // We don't want to add this every time, it's only needed once 
            $this->_script = true;
            $script.='<script type="text/javascript" src="'.plugin_dir_url(__FILE__).'js/tinymce/tinymce.min.js"></script>';      
        }         
        $script.=$this->tinyBuild($type,$fieldName,$tinyAttribute);
        $attribute=$this->inputAttribute($attributeArr);
        $textarea='<textarea name="'.$fieldName.'" '.$attribute.'>'.$fieldValue.'</textarea>
        <br/> <button type="button" class="btn btn-primary tinybtn" onclick="javascript:setup();">'. __('Load Editor').'</button>'.$script;
        return$textarea;
    }
    public function inputAttribute($attributeArr)
    {
        foreach($attributeArr as $key=>$value)
        {
            $attribute.=$key.'="'.$value.'" ';
        }
        return$attribute;
    }
    public function tinyBuild($name,$fieldName,$tinyAttribute)
    {
        $tinyoptions=$this->preset($name);
        $tinyoptions=array_merge($tinyoptions,$tinyAttribute);
        $tinyoptions['mode'] = 'exact'; 
        $tinyoptions['elements'] = $fieldName;
        
        // List the keys having a function 
        $value_arr = array(); 
        $replace_keys = array(); 
        foreach($tinyoptions as $key => &$value){ 
            // Checks if the value starts with 'function (' 
            if(strpos($value, 'function(') === 0){ 
                $value_arr[] = $value; 
                $value = '%' . $key . '%';                
                $replace_keys[] = '"' . $value . '"'; 
            } 
        }
        $json=json_encode($tinyoptions);
        $json=str_replace('"filemanager"','{ "filemanager" : "../../filemanager/plugin.min.js"}',$json);
        $script='<script type="text/javascript">
        //<![CDATA[
        function setup() {tinyMCE.init(' . $json . ');$(".tinybtn").hide();};
        //]]>
        </script>';
        return$script;
    }
    public function preset($name){ 
        // Full Feature
        if($name == 'full'){ 
            return array( 
                'selector' => 'textarea',
                'theme' => 'modern',
                'plugins' => 'advlist,autolink,lists,link,image,charmap,print,preview,hr,anchor,pagebreak,
                            searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,
                            insertdatetime,media,nonbreaking,save,table,contextmenu,directionality,
                            emoticons,template,paste,textcolor,youtube,colorpicker',
                'relative_urls' => false,
                'browser_spellcheck' => true,
                'toolbar1' => 'insertfile, undo, redo, |, styleselect, fontselect, |, fontsizeselect, |, bold, italic, |, alignleft, aligncenter, alignright, alignjustify, |, bullist, numlist, outdent, indent',
                'toolbar2' => 'link, image, media, youtube, emoticons, |, colorpicker, forecolor, backcolor, |, preview, print, code',
                'image_advtab' => true,
                'filemanager_title' => __('Filemanager'),
                'external_filemanager_path'=> '../wp-content/plugins/examapp/filemanager/',
                'external_plugins'=> 'filemanager',
                'filemanager_access_key'=>'59ebb11c0eec830732a7b877b8a1c5fb59ebb11c0eec830732a7b877b8a1c5fb',
                
            ); 
        } 

        // Basic 
        if($name == 'basic'){ 
            return array( 
                'theme' => 'advanced', 
                'plugins' => 'safari,advlink,paste', 
                'theme_advanced_buttons1' => 'code,|,copy,pastetext,|,bold,italic,underline,|,link,unlink,|,bullist,numlist',
                'theme_advanced_buttons2' => '', 
                'theme_advanced_buttons3' => '', 
                'theme_advanced_toolbar_location' => 'top', 
                'theme_advanced_toolbar_align' => 'center', 
                'theme_advanced_statusbar_location' => 'none', 
                'theme_advanced_resizing' => false, 
                'theme_advanced_resize_horizontal' => false, 
                'convert_fonts_to_spans' => false
            ); 
        } 

        // Simple 
        if($name == 'simple'){ 
            return array( 
                'theme' => 'modern',
            ); 
        }
        
        // Math
        if($name == 'math'){ 
            return array( 
                'selector' => 'textarea',
                'theme' => 'modern',
                'plugins' => 'advlist,autolink,lists,link,image,charmap,print,preview,hr,anchor,pagebreak,
                            searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,
                            insertdatetime,media,nonbreaking,save,table,contextmenu,directionality,
                            emoticons,template,paste,textcolor,youtube,colorpicker,mathslate',
                'relative_urls' => false,
                'browser_spellcheck' => true,
                'toolbar1' => 'insertfile, undo, redo, |, styleselect, fontselect, |, fontsizeselect, |, bold, italic, |, alignleft, aligncenter, alignright, alignjustify, |, bullist, numlist, outdent, indent',
                'toolbar2' => 'link, image, media, youtube, emoticons, |, colorpicker, forecolor, backcolor, |, mathslate |, preview, print, code',
                'image_advtab' => true,
                'filemanager_title' => __('Filemanager'),
                'external_filemanager_path'=> '../wp-content/plugins/examapp/filemanager/',
                'external_plugins'=> 'filemanager',
                'filemanager_access_key'=>'59ebb11c0eec830732a7b877b8a1c5fb59ebb11c0eec830732a7b877b8a1c5fb',
                
            ); 
        } 

        // BBCode 
        if($name == 'bbcode'){ 
            return array( 
                'theme' => 'advanced', 
                'plugins' => 'bbcode', 
                'theme_advanced_buttons1' => 'bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code',
                'theme_advanced_buttons2' => '', 
                'theme_advanced_buttons3' => '', 
                'theme_advanced_toolbar_location' => 'top', 
                'theme_advanced_toolbar_align' => 'left', 
                'theme_advanced_styles' => 'Code=codeStyle;Quote=quoteStyle', 
                'theme_advanced_statusbar_location' => 'bottom', 
                'theme_advanced_resizing' => true, 
                'theme_advanced_resize_horizontal' => false, 
                'entity_encoding' => 'raw', 
                'add_unload_trigger' => false, 
                'remove_linebreaks' => false, 
                'inline_styles' => false 
            ); 
        }
        // Absolute Url
        if($name == 'absolute'){ 
            return array( 
                'selector' => 'textarea',
                'theme' => 'modern',
                'plugins' => 'advlist,autolink,lists,link,image,charmap,print,preview,hr,anchor,pagebreak,
                            searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,
                            insertdatetime,media,nonbreaking,save,table,contextmenu,directionality,
                            emoticons,template,paste,textcolor,youtube,colorpicker',
                'relative_urls' => false,
                'remove_script_host'=> false,
                'browser_spellcheck'=> true,
                'toolbar1' => 'insertfile, undo, redo, |, styleselect, fontselect, |, fontsizeselect, |, bold, italic, |, alignleft, aligncenter, alignright, alignjustify, |, bullist, numlist, outdent, indent',
                'toolbar2' => 'link, image, media, youtube, emoticons, |, colorpicker, forecolor, backcolor, |, preview, print, code',
                'image_advtab' => true,
                'filemanager_title' => __('Filemanager'),
                'external_filemanager_path'=> '../wp-content/plugins/examapp/filemanager/',
                'external_plugins'=> 'filemanager',
                'filemanager_access_key'=>'59ebb11c0eec830732a7b877b8a1c5fb59ebb11c0eec830732a7b877b8a1c5fb',
                
            ); 
        } 
        return null; 
    } 
}