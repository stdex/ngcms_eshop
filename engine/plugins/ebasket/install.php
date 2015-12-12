<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
loadPluginLang('ebasket', 'config', '', '', ':');

$db_update = array(
 array(
  'table'  => 'eshop_ebasket',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
	array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int', 'params' => 'default 0'),
	array('action' => 'cmodify', 'name' => 'cookie', 'type' => 'char(50)', 'params' => 'default ""'),
	array('action' => 'cmodify', 'name' => 'linked_ds', 'type' => 'int', 'params' => 'default 0'),
	array('action' => 'cmodify', 'name' => 'linked_id', 'type' => 'int', 'params' => 'default 0'),
	array('action' => 'cmodify', 'name' => 'title', 'type' => 'char(120)', 'params' => 'default ""'),
  	array('action' => 'cmodify', 'name' => 'linked_fld', 'type' => 'text'),
  	array('action' => 'cmodify', 'name' => 'price', 'type' => 'decimal(12,2)', 'params' => 'default 0'),
	array('action' => 'cmodify', 'name' => 'count', 'type' => 'int', 'params' => 'default 0'),
  )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('ebasket', $db_update)) {
		plugin_mark_installed('ebasket');
	}
} else {
	$text = $lang['ebasket:desc_install'];
	generate_install_page('ebasket', $text);
}

?>
