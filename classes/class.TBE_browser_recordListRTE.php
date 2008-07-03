<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


/**
* class TBE_browser_recordListRTE extends TBE_browser_recordList
* to return correct linkWraps for RTE link browser
**/

class TBE_browser_recordListRTE extends TBE_browser_recordList {	
	
	var $hookObj;
	
	/**
	 * Returns the title (based on $code) of a record (from table $table) with the proper link around (that is for "pages"-records a link to the level of that record...)
	 *
	 * @param	string		Table name
	 * @param	integer		UID (not used here)
	 * @param	string		Title string
	 * @param	array		Records array (from table name)
	 * @return	string
	 */
	function linkWrapItems($table,$uid,$code,$row)	{
		global $TCA, $BACK_PATH;

		if (!$code) {
			$code = '<i>['.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.no_title',1).']</i>';
		} else {
			$code = htmlspecialchars(t3lib_div::fixed_lgd_cs($code,$this->fixedL));
		}
		
		
		if ($this->browselistObj->curUrlInfo['recordTable']==$table && $this->browselistObj->curUrlInfo['recordUid']==$uid)	{
			$curImg='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowL" alt="" />';
		} else {
			$curImg='';
		}
				
		$title = t3lib_BEfunc::getRecordTitle($table,$row,FALSE,TRUE);
		$ficon = t3lib_iconWorks::getIcon($table,$row);
		
		if (@$this->browselistObj->mode=='rte') {
			//used in RTE mode:
			$aOnClick='return link_spec(\'record:'.$table.':'.$row['uid'].'\');"';			
		}
		else {
			//used in wizard mode
			$aOnClick='return link_folder(\'record:'.$table.':'.$row['uid'].'\');"';
		}
		//$aOnClick = "return insertElement('".$table."', '".$row['uid']."', 'db', ".t3lib_div::quoteJSvalue($title).", '', '', '".$ficon."');";
		$ATag = '<a href="#" onclick="'.$aOnClick.'">';		
		$ATag_e = '</a>';

		return 
				$ATag.
				$code.$curImg.
				$ATag_e;
	}
	
	
	/**
	 * Returns additional, local GET parameters to include in the links of the record list.
	 *
	 * @return	string
	 */
	function ext_addP()	{	
		
		$str = '&act='.$GLOBALS['SOBE']->browser->act.
				'&editorNo='.$this->browselistObj->editorNo.
				'&contentTypo3Language='.$this->browselistObj->contentTypo3Language.
				'&contentTypo3Charset='.$this->browselistObj->contentTypo3Charset.
				'&mode='.$GLOBALS['SOBE']->browser->mode.
				'&expandPage='.$GLOBALS['SOBE']->browser->expandPage.
				'&RTEtsConfigParams='.t3lib_div::_GP('RTEtsConfigParams').					
				'&bparams='.rawurlencode($GLOBALS['SOBE']->browser->bparams).
				$this->hookObj->getaddPassOnParams();
		return $str;
	}
	
	
}




?>