<?php
/***************************************************************
 *  Copyright notice
 *
 *  Copyright (c) 2009, AOE media GmbH <dev@aoemedia.de>
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

/**
 * TCEmain hook
 *
 * class.tx_linkhandler_tcemain.php
 *
 * @author Michael Klapper <klapper@aoemedia.de>
 * @copyright Copyright (c) 2009, AOE media GmbH <dev@aoemedia.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 * @date $Date$
 * @since 22.05.2009 - 23:03:18
 * @package TYPO3
 * @subpackage tx_linkhandler
 * @access public
 */
class tx_linkhandler_tcemain {

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain) when a record is saved.
	 *
	 * We use the tx_linkhandler for backend "save & show" button to display records on the configured detail view page
	 *
	 * @param	array		$fieldArray: The field names and their values to be processed (passed by reference)
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	object		$pObj: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access public
	 * @author Michael Klapper <michael.klapper@aoemedia.de>
	 */
	public function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, $pObj) {

			// direct preview
		if (isset($GLOBALS['_POST']['_savedokview_x']) && !$GLOBALS['BE_USER']->workspace) {
			$settingFound   = false;
			$currentPageID  = t3lib_div::intval_positive($GLOBALS['_POST']['popViewId']);
			$rootLineStruct = t3lib_BEfunc::BEgetRootLine($currentPageID);
			$defaultPageID  = (isset($rootLineStruct[0]) && array_key_exists('uid', $rootLineStruct[0])) ? $rootLineStruct[0]['uid'] : $currentPageID ;

				// if "savedokview" has been pressed and the beUser works in the LIVE workspace open current record in single view
			$pagesTSC = t3lib_BEfunc::getPagesTSconfig($currentPageID, $rootLineStruct); // get page TSconfig
			$handlerConfigurationStruct = $pagesTSC['mod.']['tx_linkhandler.'];

				// search for the current setting for given table
			foreach ($pagesTSC['mod.']['tx_linkhandler.'] as $key => $handler) {
				if ( (is_array($handler)) && ($handler['listTables'] === $table) ) {
					$settingFound = true;
					$selectedConfiguration = $key;
					break;
				}
			}

			if ($settingFound) {

				if ( array_key_exists('previewPageId', $handlerConfigurationStruct[$selectedConfiguration]) && (t3lib_div::intval_positive($handlerConfigurationStruct[$selectedConfiguration]['previewPageId']) > 0) ) {
					$previewPageId = t3lib_div::intval_positive($handlerConfigurationStruct[$selectedConfiguration]['previewPageId']);
				} else {
					$previewPageId = t3lib_div::intval_positive($defaultPageID);
				}

				$queryString = '&eID=linkhandlerPreview&linkParams=record:' . $table . ':' . $id . '&authCode=' . t3lib_div::stdAuthCode($table . ':' . $id, '', 32) . ($fieldArray['sys_language_uid'] > 0 ? '&L=' . $fieldArray['sys_language_uid'] : '');
				$GLOBALS['_POST']['viewUrl'] = $this->buildViewUrl($previewPageId) . '?id=' . $previewPageId . $queryString;
			}
		}
	}

	/**
	 * Build the preview url and take care to the domain
	 *
	 * @param integer $previewPageId
	 * @access protected
	 * @return string
	 * @author Michael Klapper <michael.klapper@aoemedia.de>
	 */
	protected function buildViewUrl($previewPageId) {
		$rootLineStruct = t3lib_BEfunc::BEgetRootLine($previewPageId);
			// check alternate Domains
		if ($rootLineStruct) {
			$parts = parse_url(t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
			if ( t3lib_BEfunc::getDomainStartPage($parts['host'], $parts['path']) ) {
				$viewUrl = t3lib_BEfunc::firstDomainRecord($rootLineStruct);
			}
		}

		return $viewUrl ? (t3lib_div::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://') . $viewUrl : t3lib_div::getIndpEnv('TYPO3_SITE_URL');
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler/service/class.tx_linkhandler_tcemain.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/linkhandler/service/class.tx_linkhandler_tcemain.php']);
}
?>