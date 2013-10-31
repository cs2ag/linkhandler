<?php
$extensionPath = t3lib_extMgm::extPath('linkhandler');
return array(
	'tx_linkhandler_handler' => $extensionPath . 'class.tx_linkhandler_handler.php',
	'tx_linkhandler_tcemain' => $extensionPath . 'service/class.tx_linkhandler_tcemain.php',
	'tx_linkhandler_recordTab' => $extensionPath . 'classes/class.tx_linkhandler_recordTab.php',
	'tx_linkhandler_recordsTree' => $extensionPath . 'classes/record/class.tx_linkhandler_recordsTree.php',
	'TBE_browser_recordListRTE' => $extensionPath . 'classes/record/class.TBE_browser_recordListRTE.php',
	'tx_linkhandler_tabHandler' => $extensionPath . 'classes/interface.tx_linkhandler_tabHandler.php',
);
?>