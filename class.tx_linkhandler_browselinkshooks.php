<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Daniel P�tzinger 
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * hook to adjust linkwizard (linkbrowser)
 *
 * @author	Daniel Poetzinger (AOE media GmbH)
 * @package TYPO3
 * @subpackage linkhandler
 */
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


// include defined interface for hook
// (for TYPO3 4.x usage this interface is part of the patch)
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['linkhandler']);
if ($confArr['applyPatch']==1) {	
	require_once (t3lib_extMgm::extPath('linkhandler').'patch/interfaces/interface.t3lib_browselinkshook.php');
}
else {
	require_once (PATH_t3lib.'interfaces/interface.t3lib_browselinkshook.php');
}



require_once (t3lib_extMgm::extPath('linkhandler').'classes/class.tx_linkhandler_recordsTree.php');
require_once (t3lib_extMgm::extPath('linkhandler').'classes/class.TBE_browser_recordListRTE.php');




class tx_linkhandler_browselinkshooks implements t3lib_browseLinksHook {

	var $pObj;
	
	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	parent browse_links object
	 * @return	void
	 */
	function init($pObj,$params) {
		$this->pObj=&$pObj;		
		$this->_checkConfigAndGetDefault();	
		$tabs=$this->getTabsConfig();
		foreach ($tabs as $key=>$tabConfig) {
			if ($this->isRTE()) {
				$this->pObj->anchorTypes[] = $key; //for 4.3
			}
		}
		
	}
	
	/* checks if 
	*	$this->pObj->thisConfig['tx_linkhandler.'] is set, and if not it trys to load default from
	*	TSConfig key mod.tx_linkhandler.
	*	(in case the hook is called from a RTE, this configuration might exist because it is configured in RTE.defaul.tx_linkhandler)
	*		In mode RTE: the parameter RTEtsConfigParams have to exist 
	*		In mode WIzard: the parameter P[pid] have to exist 
	*/
	function _checkConfigAndGetDefault() {
		global $BE_USER;
		if ($this->pObj->mode=='rte') {
			$RTEtsConfigParts = explode(':',$this->pObj->RTEtsConfigParams);
			$RTEsetup = $BE_USER->getTSConfig('RTE',t3lib_BEfunc::getPagesTSconfig($RTEtsConfigParts[5]));
			$this->pObj->thisConfig = t3lib_BEfunc::RTEsetup($RTEsetup['properties'],$RTEtsConfigParts[0],$RTEtsConfigParts[2],$RTEtsConfigParts[4]);
		}
		
		elseif (!is_array($this->pObj->thisConfig['tx_linkhandler.'])) {
			$P=t3lib_div::_GP('P');
			$pid=$P['pid'];			
			$modTSconfig = $GLOBALS["BE_USER"]->getTSConfig("mod.tx_linkhandler",t3lib_BEfunc::getPagesTSconfig($pid));
			//print_r($modTSconfig);
			$this->pObj->thisConfig['tx_linkhandler.']=$modTSconfig['properties'];
		}
		
	}
	
	/**
	 * adds new items to the currently allowed ones and returns them
	 *
	 * @param	array	currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	function addAllowedItems($allowedItems) {		
		if (is_array($this->pObj->thisConfig['tx_linkhandler.'])) {
			foreach ($this->pObj->thisConfig['tx_linkhandler.'] as $name => $tabConfig) {				
				if (is_array($tabConfig)) {
					$key=substr($name,0,-1);
					$allowedItems[]=$key;
				}
			}
		}		
		return $allowedItems;
	}
	
	/**
	 * checks the current URL and returns a info array. This is used to
	 *	tell the link browser which is the current tab based on the current URL.
	 *	function should at least return the $info array.
	 *
	 * @param	string		$href
	 * @param	string		$siteUrl
	 * @param	array		$info		Current info array.
	 * @return	array 				$info		a infoarray for browser to tell them what is current active tab
	 */
	function parseCurrentUrl($href,$siteUrl,$info) {
		
			//depending on link and setup the href string can contain complete absolute link			
			if (substr($href,0,7)=='http://') {
				if ($_href=strstr($href,'?id=')) {
					$href=substr($_href,4);
				}
				else {				
					$href=substr (strrchr ($href, "/"),1);
				}
			}
				
			if (strtolower(substr($href,0,7))=='record:') {
					$parts=explode(":",$href);
					
					$info['act']='record';
					
					//check the linkhandler TSConfig and find out  which config is responsible for the current table:
					$tabs=$this->getTabsConfig();
					foreach ($tabs as $key=>$tabConfig) {					
						if ($parts[1]==$tabConfig['listTables']) {
							$info['act']=$key;
						}						
					}		
					
					$info['recordTable']=$parts[1];
					$info['recordUid']=$parts[2];
			}				
			return $info;
	}
	
	private  function getTabsConfig() {
		
		$tabs=array();
		if (is_array($this->pObj->thisConfig['tx_linkhandler.'])) {
			foreach ($this->pObj->thisConfig['tx_linkhandler.'] as $name => $tabConfig) {	
				if (is_array($tabConfig)) {
					$key=substr($name,0,-1);
					$tabs[$key]=$tabConfig;
				}
				
			}
		}
		return $tabs;
	}
	/**
	 * modifies the menu definition and returns it
	 *
	 * @param	array	menu definition
	 * @return	array	modified menu definition
	 */
	function modifyMenuDefinition($menuDef) {
		$tabs=$this->getTabsConfig();
		foreach ($tabs as $key=>$tabConfig) {
			$menuDef[$key]['isActive'] = $this->pObj->act==$key;
			$menuDef[$key]['label'] = $tabConfig['label']; // $LANG->getLL('records',1);
			$menuDef[$key]['url'] = '#';
			$addPassOnParams.=$this->getaddPassOnParams();
			
								
			$menuDef[$key]['addParams'] = 'onclick="jumpToUrl(\'?act='.$key.'&editorNo='.$this->pObj->editorNo.'&contentTypo3Language='.$this->pObj->contentTypo3Language.'&contentTypo3Charset='.$this->pObj->contentTypo3Charset.$addPassOnParams.'\');return false;"';					
			
		}
		
		return $menuDef;
	}
	
	/**
	* returns additional addonparamaters - required to keep several informations for the RTE linkwizard
	**/
	function getaddPassOnParams() {
		if (!$this->isRTE()) {
						$P2=t3lib_div::_GP('P');
						return t3lib_div::implodeArrayForUrl('P',$P2);
		}
	}
	private function isRTE() {
		if ($this->pObj->mode=='rte') {
			return true;
		}
		else {
			return false;
		}
		
	}
	
	/**
	 * returns a new tab for the browse links wizard
	 *
	 * @param	string		current link selector action
	 * @return	string		a tab for the selected link action
	 */
	function getTab($act) {
		
		global $LANG;
		if (!$this->_isOneOfLinkhandlerTabs($act))
		    return;
		    
		if ($this->isRTE()) {
			
			if (isset($this->pObj->classesAnchorJSOptions)) {
				$this->pObj->classesAnchorJSOptions[$act]=@$this->pObj->classesAnchorJSOptions['page']; //works for 4.1.x patch, in 4.2 they make this property protected! -> to enable classselector in 4.2 easoiest is to path rte. 
			}
			$content .=$this->pObj->addAttributesForm();
		}
			
		$pagetree = t3lib_div::makeInstance('tx_linkhandler_recordsTree');
		$pagetree->browselistObj=&$this->pObj;
		$tree=$pagetree->getBrowsableTree();
		$cElements = $this->expandPageRecords();
		$content.= '
		<!--
			Wrapper table for page tree / record list:
		-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-linkPages">
					<tr>
						<td class="c-wCell" valign="top">'.$this->pObj->barheader($LANG->getLL('pageTree').':').$tree.'</td>
						<td class="c-wCell" valign="top">'.$cElements.'</td>
					</tr>
				</table>
				';
		return $content;
	}
	
	/******************************************************************
	 *
	 * Record listing
	 *
	 ******************************************************************/
	/**
	 * For RTE: This displays all content elements on a page and lets you create a link to the element.
	 *
	 * @return	string		HTML output. Returns content only if the ->expandPage value is set (pointing to a page uid to show tt_content records from ...)
	 */
	function expandPageRecords()	{	
		
		
		global $TCA,$BE_USER, $BACK_PATH;

		$out='';
		if ($this->pObj->expandPage>=0 && t3lib_div::testInt($this->pObj->expandPage) && $BE_USER->isInWebMount($this->pObj->expandPage))	{
			
			$tables='*';
			
			
			if (isset($this->pObj->thisConfig['tx_linkhandler.'][$this->pObj->act.'.']['listTables'])) {
				$tables=$this->pObj->thisConfig['tx_linkhandler.'][$this->pObj->act.'.']['listTables'];
			}
				// Set array with table names to list:
			if (!strcmp(trim($tables),'*'))	{
				$tablesArr = array_keys($TCA);
			} else {
				$tablesArr = t3lib_div::trimExplode(',',$tables,1);
			}
			reset($tablesArr);

				// Headline for selecting records:
			$out.=$this->pObj->barheader($GLOBALS['LANG']->getLL('selectRecords').':');

				// Create the header, showing the current page for which the listing is. Includes link to the page itself, if pages are amount allowed tables.
			$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);
			$mainPageRec = t3lib_BEfunc::getRecordWSOL('pages',$this->pObj->expandPage);
			$ATag='';
			$ATag_e='';
			$ATag2='';
			if (in_array('pages',$tablesArr))	{
				$ficon=t3lib_iconWorks::getIcon('pages',$mainPageRec);
				$ATag="<a href=\"#\" onclick=\"return insertElement('pages', '".$mainPageRec['uid']."', 'db', ".t3lib_div::quoteJSvalue($mainPageRec['title']).", '', '', '".$ficon."','',1);\">";
				$ATag2="<a href=\"#\" onclick=\"return insertElement('pages', '".$mainPageRec['uid']."', 'db', ".t3lib_div::quoteJSvalue($mainPageRec['title']).", '', '', '".$ficon."','',0);\">";
				$ATag_alt=substr($ATag,0,-4).",'',1);\">";
				$ATag_e='</a>';
			}
			$picon=t3lib_iconWorks::getIconImage('pages',$mainPageRec,$BACK_PATH,'');
			$pBicon=$ATag2?'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/plusbullet2.gif','width="18" height="16"').' alt="" />':'';
			$pText=htmlspecialchars(t3lib_div::fixed_lgd_cs($mainPageRec['title'],$titleLen));
			$out.=$picon.$ATag2.$pBicon.$ATag_e.$ATag.$pText.$ATag_e.'<br />';

				// Initialize the record listing:
			$id = $this->pObj->expandPage;
			$pointer = t3lib_div::intInRange($this->pObj->pointer,0,100000);
			$perms_clause = $GLOBALS['BE_USER']->getPagePermsClause(1);
			$pageinfo = t3lib_BEfunc::readPageAccess($id,$perms_clause);
			$table='';

				// Generate the record list:
			$dblist = t3lib_div::makeInstance('TBE_browser_recordListRTE');
			$dblist->hookObj=&$this;
			$dblist->browselistObj=&$this->pObj;
			$dblist->this->pObjScript=$this->pObj->this->pObjScript;
			$dblist->backPath = $GLOBALS['BACK_PATH'];
			$dblist->thumbs = 0;
			$dblist->calcPerms = $GLOBALS['BE_USER']->calcPerms($pageinfo);
			$dblist->noControlPanels=1;
			$dblist->clickMenuEnabled=0;
			$dblist->tableList=implode(',',$tablesArr);

			$dblist->start($id,t3lib_div::_GP('table'),$pointer,
				t3lib_div::_GP('search_field'),
				t3lib_div::_GP('search_levels'),
				t3lib_div::_GP('showLimit')
			);

			$dblist->setDispFields();			
			$dblist->generateList();
			$dblist->writeBottom();

				//	Add the HTML for the record list to output variable:
			$out.=$dblist->HTMLcode;
			$out.=$dblist->getSearchBox();
		}

			// Return accumulated content:
		return $out;
		
		
		
		
		/*		
		global $BE_USER, $BACK_PATH;
		
		$out='';
		$expPageId = $this->pObj->expandPage;		// Set page id (if any) to expand

			// If there is an anchor value (content element reference) in the element reference, then force an ID to expand:
		if (!$this->pObj->expandPage && $this->pObj->curUrlInfo['cElement'])	{
			$expPageId = $this->pObj->curUrlInfo['pageid'];	// Set to the current link page id.
		}

			// Draw the record list IF there is a page id to expand:
		if ($expPageId && t3lib_div::testInt($expPageId) && $BE_USER->isInWebMount($expPageId))	{

				// Set header:
			$out.=$this->pObj->barheader($GLOBALS['LANG']->getLL('contentElements').':');

				// Create header for listing, showing the page title/icon:
			$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);
			$mainPageRec = t3lib_BEfunc::getRecordWSOL('pages',$expPageId);
			$picon=t3lib_iconWorks::getIconImage('pages',$mainPageRec,'','');
			$picon.= htmlspecialchars(t3lib_div::fixed_lgd_cs($mainPageRec['title'],$titleLen));
			$out.=$picon.'<br />';

				// Look up tt_content elements from the expanded page:
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid,header,hidden,starttime,endtime,fe_group,CType,colpos,bodytext',
							'tt_content',
							'pid='.intval($expPageId).
								t3lib_BEfunc::deleteClause('tt_content').
								t3lib_BEfunc::versioningPlaceholderClause('tt_content'),
							'',
							'colpos,sorting'
						);
			$cc = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

				// Traverse list of records:
			$c=0;
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$c++;
				$icon=t3lib_iconWorks::getIconImage('tt_content',$row,$BACK_PATH,'');
				if ($this->pObj->curUrlInfo['act']=='page' && $this->pObj->curUrlInfo['cElement']==$row['uid'])	{
					$arrCol='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/blinkarrow_left.gif','width="5" height="9"').' class="c-blinkArrowL" alt="" />';
				} else {
					$arrCol='';
				}
					// Putting list element HTML together:
				$out.='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/join'.($c==$cc?'bottom':'').'.gif','width="18" height="16"').' alt="" />'.
						$arrCol.
						'<a href="#" onclick="return link_typo3Page(\''.$expPageId.'\',\'#'.$row['uid'].'\');">'.
						$icon.
						htmlspecialchars(t3lib_div::fixed_lgd_cs($row['header'],$titleLen)).
						'</a><br />';

					// Finding internal anchor points:
				if (t3lib_div::inList('text,textpic', $row['CType']))	{
					$split = preg_split('/(<a[^>]+name=[\'"]?([^"\'>[:space:]]+)[\'"]?[^>]*>)/i', $row['bodytext'], -1, PREG_SPLIT_DELIM_CAPTURE);

					foreach($split as $skey => $sval)	{
						if (($skey%3)==2)	{
								// Putting list element HTML together:
							$sval = substr($sval,0,100);
							$out.='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/line.gif','width="18" height="16"').' alt="" />'.
									'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/join'.($skey+3>count($split)?'bottom':'').'.gif','width="18" height="16"').' alt="" />'.
									'<a href="#" onclick="return link_typo3Page(\''.$expPageId.'\',\'#'.rawurlencode($sval).'\');">'.
									htmlspecialchars(' <A> '.$sval).
									'</a><br />';
						}
					}
				}
			}
		}
		return $out;
		*/
		
		
		
	}

    function _isOneOfLinkhandlerTabs ($key)
    {
        foreach ($this->pObj->thisConfig['tx_linkhandler.'] as $name => $tabConfig) {
            if (is_array($tabConfig)) {
                $akey = substr($name, 0, - 1);
                if ($akey == $key)
                    return true;
            }
        }
        return false;
    }
}


?>