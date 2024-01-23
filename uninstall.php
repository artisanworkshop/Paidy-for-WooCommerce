<?php
if( ! defined ('WP_UNINSTALL_PLUGIN') )
exit();
function wc_paidy_delete_plugin(){
	global $wpdb;

	//delete option settings
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'woocommerce\_paidy\_%';");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wc-paidy-%';");
}

wc_paidy_delete_plugin();
