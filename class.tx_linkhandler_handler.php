<?php
if (!defined ('TYPO3_MODE'))
	die ('Access denied.');



class tx_linkhandler_handler {

	function main($linktxt, $conf, $linkHandlerKeyword, $linkHandlerValue, $link_param, &$pObj) {
		$this->pObj=&$pObj;
		$linkConfig=$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_linkhandler.'];

		if (!is_array($linkConfig)) {
			return $linktxt;
		}
		$linkHandlerData=t3lib_div::trimExplode(':',$linkHandlerValue);
		if (!isset($linkConfig[$linkHandlerData[0].'.'])) {
			return $linktxt;
		}

		$localcObj = t3lib_div::makeInstance('tslib_cObj');
		$recordRow=$this->getRecordRow($linkHandlerData[0],$linkHandlerData[1]);
		$localcObj->start($recordRow, '');

		$lconf = array();
		$lconf=$linkConfig[$linkHandlerData[0].'.'];

		return $localcObj->typoLink($linktxt, $lconf);

	}

	function getRecordRow($table,$uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, 'uid='.intval($uid).$this->pObj->enableFields($table), '', '');
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		return $row;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler.current/class.tx_linkhandler_handler.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler.current/class.tx_linkhandler_handler.php']);
}
?>