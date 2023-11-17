<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) { 
    exit();
}
// Delete the single option that contains all plugin settings
delete_option('sbfwl_settings');
//delete_option('your_plugin_option_name1'); //delete single option