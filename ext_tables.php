<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/link_handler/', 'link handler');



t3lib_extMgm::addPageTSConfig('RTE.default.tx_linkhandler {
 
	tt_news {		
		label=News
		listTables=tt_news
	}
} 

mod.tx_linkhandler {	
	tt_news {		
		label=News
		listTables=tt_news		
	}	
}
');


?>