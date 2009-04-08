<?php

########################################################################
# Extension Manager/Repository config file for ext: "linkhandler"
#
# Auto generated 03-07-2008 14:20
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'AOE link handler',
	'description' => 'Enables userfriendly links to records like tt_news etc... Configure new Tabs to the link-wizard. (by AOE media GmbH)',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.2.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Daniel Poetzinger',
	'author_email' => 'mylastname@aoemedia.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.1.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:21:{s:9:"ChangeLog";s:4:"ce95";s:10:"README.txt";s:4:"ee2d";s:41:"class.tx_linkhandler_browselinkshooks.php";s:4:"ca14";s:32:"class.tx_linkhandler_handler.php";s:4:"a9d8";s:21:"ext_conf_template.txt";s:4:"6e68";s:12:"ext_icon.gif";s:4:"f19a";s:17:"ext_localconf.php";s:4:"538d";s:14:"ext_tables.php";s:4:"cde0";s:43:"classes/class.TBE_browser_recordListRTE.php";s:4:"5e42";s:44:"classes/class.tx_linkhandler_recordsTree.php";s:4:"b7b1";s:14:"doc/manual.sxw";s:4:"0d61";s:19:"doc/wizard_form.dat";s:4:"cfc2";s:20:"doc/wizard_form.html";s:4:"c70c";s:35:"patch/4.1/class.ux_browse_links.php";s:4:"6c5a";s:43:"patch/4.1/class.ux_t3lib_parsehtml_proc.php";s:4:"d0de";s:36:"patch/4.1/class.ux_tslib_content.php";s:4:"c23e";s:50:"patch/4.1/class.ux_tx_rtehtmlarea_browse_links.php";s:4:"4328";s:39:"patch/4.1/class.ux_ux_tslib_content.php";s:4:"df20";s:52:"patch/interfaces/interface.t3lib_browselinkshook.php";s:4:"258f";s:33:"static/link_handler/constants.txt";s:4:"ab63";s:29:"static/link_handler/setup.txt";s:4:"889c";}',
	'suggests' => array(
	),
);

?>