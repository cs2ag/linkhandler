<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['linkhandler']);
if ($confArr['applyPatch']==1) {
	//register XCLASSES (adds hooks)
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']=t3lib_extMgm::extPath($_EXTKEY) . 'patch/4.1/class.ux_tx_rtehtmlarea_browse_links.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/class.browse_links.php']=t3lib_extMgm::extPath($_EXTKEY) . 'patch/4.1/class.ux_browse_links.php';
	//patch because of yellow box arround rte links:
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_parsehtml_proc.php']=t3lib_extMgm::extPath($_EXTKEY) . 'patch/4.1/class.ux_t3lib_parsehtml_proc.php';

	if ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.tslib_content.php'] !='') {
		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.ux_tslib_content.php']=t3lib_extMgm::extPath($_EXTKEY) . 'patch/4.1/class.ux_ux_tslib_content.php';
	} else {
	    $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.tslib_content.php']=t3lib_extMgm::extPath($_EXTKEY) . 'patch/4.1/class.ux_tslib_content.php';
	}	
}

//add linkhandler for "record"
//require_once(t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_linkhandler_handler.php');
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['record'] = 'EXT:linkhandler/class.tx_linkhandler_handler.php:&tx_linkhandler_handler';

//register hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][]='EXT:linkhandler/class.tx_linkhandler_browselinkshooks.php:tx_linkhandler_browselinkshooks';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.browse_links.php']['browseLinksHook'][]='EXT:linkhandler/class.tx_linkhandler_browselinkshooks.php:tx_linkhandler_browselinkshooks';

?>