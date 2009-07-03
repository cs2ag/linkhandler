<?php
/***************************************************************
 *  Copyright notice
 *
 *  Copyright (c) 2008, Daniel P�tzinger <daniel.poetzinger@aoemedia.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined ('TYPO3_MODE'))
	die ('Access denied.');

/**
 * Linkhandler to process custom linking to any kind of configured record.
 *
 * @author	Daniel Poetzinger <daniel.poetzinger@aoemedia.de>
 * @author	Michael Klapper <michael.klapper@aoemedia.de>
 * @version $Id: $
 * @date 08.04.2009 - 15:06:25
 * @package TYPO3
 * @subpackage tx_linkhandler
 * @access public
 */
class tx_linkhandler_handler {

	/**
	 * @var boolean
	 */
	protected $returnLastURL = false;

	/**
	 * Setting to retrieve just the URL string instead of the full A-tag
	 *
	 * @access public
	 * @return void
	 * @author Michael Klapper <michael.klapper@aoemedia.de>
	 */
	public function returnOnlyURL() {
		$this->returnLastURL = true;
	}

	/**
	 * Process the link generation
	 *
	 * @param string $linktxt
	 * @param array $conf
	 * @param string $linkHandlerKeyword Define the identifier that an record is given
	 * @param string $linkHandlerValue Table and uid of the requested record like "tt_news:2"
	 * @param string $linkParams Full link params like "record:tt_news:2"
	 * @param tslib_cObj $pObj
	 * @return string
	 */
	public function main($linktxt, $conf, $linkHandlerKeyword, $linkHandlerValue, $linkParams, $pObj) {
		$this->pObj        = $pObj;
		$linkConfigArray   = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_linkhandler.'];
		$generatedLink     = '';
		$furtherLinkParams = str_replace('record:' . $linkHandlerValue, '', $linkParams); // extract link params like "target", "css-class" or "title"
		list ($recordTableName, $recordUid) = t3lib_div::trimExplode(':', $linkHandlerValue);

			// get the record from $linkhandlerValue
		$recordRow = $GLOBALS['TSFE']->sys_page->checkRecord($recordTableName, $recordUid);

			// build the typolink when the requested record and the nessesary cofiguration are available
		if (
				( is_array($linkConfigArray) && array_key_exists($recordTableName . '.', $linkConfigArray) ) // record type link configuration available
			&&
				(
					( is_array($recordRow) && !empty($recordRow) ) // recored available
				||
					( (int)$linkConfigArray[$recordTableName . '.']['forceLink'] === 1 ) // if the record are hidden ore someting else, force link generation
				)
			) {
			$localcObj = t3lib_div::makeInstance('tslib_cObj'); /* @var $localcObj tslib_cObj */
			$localcObj->start($recordRow, '');
			$linkConfigArray[$recordTableName . '.']['parameter'] .= $furtherLinkParams;

				// build the full link to the record
			$generatedLink = $localcObj->typoLink($linktxt, $linkConfigArray[$recordTableName . '.']);

			if ($this->returnLastURL)
				$generatedLink = $localcObj->lastTypoLinkUrl;
		} else {
			$generatedLink = $linktxt;
		}

		return $generatedLink;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler/class.tx_linkhandler_handler.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler/class.tx_linkhandler_handler.php']);
}

?>