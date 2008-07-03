<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 * Class which generates the page tree for records, specific version for linkhandler extension 
 *  -> shows records on the selected page and makes them clickable to get the link
 * 
 * 
 * 
 */
class tx_linkhandler_recordsTree extends localPageTree {
	var $browselistObj;
	/**
	 * Create the page navigation tree in HTML
	 *
	 * @param	array		Tree array
	 * @return	string		HTML output.
	 */
	function printTree($treeArr='')	{
		global $BACK_PATH;
		$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);
		if (!is_array($treeArr))	$treeArr=$this->tree;

		$out='';
		$c=0;
		$dofiltering=False; //should the pagetree be filter to show only $onlyPids
		$onlyPids=array();
		if (isset($this->browselistObj->thisConfig['tx_linkhandler.'][$this->browselistObj->act.'.']['onlyPids'])) {
			$configured_onlyPids=t3lib_div::trimExplode(',',$this->browselistObj->thisConfig['tx_linkhandler.'][$this->browselistObj->act.'.']['onlyPids']);
			foreach ($configured_onlyPids as $actualPid) {
				$onlyPids=array_merge($onlyPids,$this->_getRootLinePids($actualPid));
			}			
			$dofiltering=TRUE;
		}
		foreach($treeArr as $k => $v)	{			
			$c++;
			$bgColorClass = ($c+1)%2 ? 'bgColor' : 'bgColor-10';
			if ($GLOBALS['SOBE']->browser->curUrlInfo['act']=='page' && $GLOBALS['SOBE']->browser->curUrlInfo['pageid']==$v['row']['uid'] && $GLOBALS['SOBE']->browser->curUrlInfo['pageid'])	{
				$arrCol='<td><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" /></td>';
				$bgColorClass='bgColor4';
			} else {
				$arrCol='<td></td>';
			}
			$addPassOnParams=$this->_getaddPassOnParams();
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->browser->act.'&editorNo='.$GLOBALS['SOBE']->browser->editorNo.'&contentTypo3Language='.$GLOBALS['SOBE']->browser->contentTypo3Language.'&contentTypo3Charset='.$GLOBALS['SOBE']->browser->contentTypo3Charset.'&mode='.$GLOBALS['SOBE']->browser->mode.'&expandPage='.$v['row']['uid'].$addPassOnParams.'\');';
			$cEbullet = !$this->ext_isLinkable($v['row']['doktype'],$v['row']['uid']) ?
						'<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' :
						'';
			if ($dofiltering && (!in_array($v['row']['uid'],$onlyPids) )) {
				continue;
			}
			else {
				$out.='
					<tr class="'.$bgColorClass.'">
						<td nowrap="nowrap"'.($v['row']['_CSSCLASS'] ? ' class="'.$v['row']['_CSSCLASS'].'"' : '').'>'.
						$v['HTML'].
						'<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$this->getTitleStr($v['row'],$titleLen).'</a>'.
						'</td>'.
						$arrCol.
						'<td>'.$cEbullet.'</td>
					</tr>';
			}
		}
		
		$out='


			<!--
				Navigation Page Tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-tree">
				'.$out.'
			</table>';
		return $out;
	}
	
	function _getRootLinePids($pid) {
			$pids=array();
			$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
			$sys_page->init(true);						
			$rootLine = $sys_page->getRootLine($pid, $mpvar);
			foreach ($rootLine as $v) {
				$pids[]=$v['uid'];
			}			
			return $pids;
	}
	function _getaddPassOnParams() {
		if ($this->pObj->mode!='rte') {
				if ($this->cachedParams!='') {
				
				}else {
						$P_GET=t3lib_div::_GP('P');
						$P3=array();
						if (is_array($P_GET)) {
							foreach ($P_GET as $k=>$v) {
								if (!is_array($v) && $k != 'returnUrl' && $k != 'md5ID' && $v != '')
									$P3[$k]=$v;
							}												
						}						
						$this->cachedParams= t3lib_div::implodeArrayForUrl('P',$P3);
				}
				return $this->cachedParams;
		}
	}
	
	
	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		$aOnClick = "return jumpToUrl('".$this->thisScript.'?PM='.$cmd.$this->_getaddPassOnParams()."','".$anchor."');";

		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}
}


?>