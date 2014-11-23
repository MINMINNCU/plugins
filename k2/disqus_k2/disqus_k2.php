<?php
/**
 * @version		1.4
 * @package		DISQUS for K2 Plugin (K2 plugin)
 * @author    Marek Wojtaszem - http://www.nika-foto.pl
 * @copyright	Copyright (c) 2012 Marek Wojtaszek. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_k2'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'k2plugin.php');

class plgK2Disqus_K2 extends K2Plugin {

	var $pluginName = 'disqus_k2';
	var $pluginNameHumanReadable = 'DISQUS for K2 plugin';


	public function plgK2Disqus_K2( & $subject, $params) {
		parent::__construct($subject, $params);
		$mode = $this->params->def('mode', 1);
		JPlugin::loadLanguage('plg_k2_disqus_k2');	
	}


	function onK2CommentsCounter( &$item, &$params, $limitstart) {
		$plugin = JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = $this->params;

		$item_link = $item->link;
		$identifier = $item->id;

		$output = ''.JText::_('DISQUS_COMMENTS_COUNT').':<a href= "'.$item_link.'#disqus_thread" data-disqus-identifier="'.$identifier.'">'.JText::_('DISQUS_COMMENTS').'</a>
		';

		return $output;
	}

	function onK2CommentsBlock( &$item, &$params, $limitstart) {
		$plugin = JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = $this->params;

		$item_link = $item->link;
		$identifier = $item->id;

		$lang = JFactory::getLanguage();
		$lang_shortcode = explode('-',$lang->getTag());
		if ($pluginParams->get('multilingual') == 1) {
		$disqus_lang = $lang_shortcode[0];
		} else {
		$disqus_lang = '';
		}

		if ($this->isArticlePage()): 
		$output = '<a name="itemCommentsAnchor" id="itemCommentsAnchor"></a>

					<div id="disqus_thread" class="itemComments"></div>
					
					<script  type="text/javascript">
						var disqus_identifier = \''.$identifier.'\';
						var disqus_shortname = \''.$pluginParams->get('shortname').'\';
						var disqus_url = \''.$site_root.'\';
						var disqus_config = function () { 
							this.language = "'.$disqus_lang.'";
							};
							
							(function() {
							var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
							dsq.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/embed.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
							})();
							
							(function () {
							var s = document.createElement(\'script\'); s.type = \'text/javascript\'; s.async = true;
							s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(s);
							}());
							
					</script>					
					
					
					';
		else: 
		$output = '';
		endif;
		return $output;
		
	}
	
	function onK2AfterDispatch( &$item, &$params, $limitstart){
		$plugin = JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = $this->params;

		$site_root = JURI::current();
		$shortname = $pluginParams->get('shortname');
		$identifier = $item->id;
		$parsedInModule = $params->get('parsedInModule');

		$lang = JFactory::getLanguage();
		$lang_shortcode = explode('-',$lang->getTag());
		if ($pluginParams->get('multilingual') == 1) {
		$disqus_lang = $lang_shortcode[0];
		} else {
		$disqus_lang = '';
		}

		if ($parsedInModule != 1){
			if ($this->isArticlePage()): 
			$output = '
				<script  type="text/javascript">
						var disqus_identifier = \''.$identifier.'\';
						var disqus_shortname = \''.$pluginParams->get('shortname').'\';
						var disqus_url = \''.$site_root.'\';
						var disqus_config = function () { 
							this.language = "'.$disqus_lang.'";
							};
							
							(function () {
							var s = document.createElement(\'script\'); s.type = \'text/javascript\'; s.async = true;
							s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(s);
							}());
							
					</script>
			';
			else: 
			$output = '';
			endif;

			$document = JFactory::getDocument();
			$doctype  = $document->getType();

			$lang = JFactory::getLanguage();
			if ($pluginParams->get('multilingual') == 1) {
			$disqus_lang = str_replace("-","_",$lang->getTag());
			} else {
			$disqus_lang = '';
			}
			
			// Only render for HTML output
			if ( $doctype == 'html' ) {
				$document->addCustomTag($output); 
			}
		} elseif ($parsedInModule == 1){
			if ($this->isK2()): 
			$output = '';
			else: 
			$output = '
				<script  type="text/javascript">
						var disqus_identifier = \''.$identifier.'\';
						var disqus_shortname = \''.$pluginParams->get('shortname').'\';
						var disqus_url = \''.$site_root.'\';
						var disqus_config = function () { 
							this.language = "'.$disqus_lang.'";
							};
							
							(function () {
							var s = document.createElement(\'script\'); s.type = \'text/javascript\'; s.async = true;
							s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(s);
							}());
							
					</script>
			';
			endif;

			return $output;
		}
		
	}

	function onBeforeRender (){
		$plugin = JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = $this->params;

		$site_root = JURI::current();
		$shortname = $pluginParams->get('shortname');

		$lang = JFactory::getLanguage();
		$lang_shortcode = explode('-',$lang->getTag());
		if ($pluginParams->get('multilingual') == 1) {
		$disqus_lang = $lang_shortcode[0];
		} else {
		$disqus_lang = '';
		}
		
		if ($this->isArticlePage()){
			$output = '';
			} else {
			$output = '
				<script  type="text/javascript">
						var disqus_identifier = \''.$identifier.'\';
						var disqus_shortname = \''.$pluginParams->get('shortname').'\';
						var disqus_url = \''.$site_root.'\';
						var disqus_config = function () { 
							this.language = "'.$disqus_lang.'";
							};
							
							(function () {
							var s = document.createElement(\'script\'); s.type = \'text/javascript\'; s.async = true;
							s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(s);
							}());
							
					</script>
			';
			}

			$document = JFactory::getDocument();
			$doctype  = $document->getType();
			
			// Only render for HTML output
			if ( $doctype == 'html' ) {
				$document->addCustomTag($output); 
			}
	}

	public function isArticlePage()
	{
		$option 	 = JRequest::getVar('option');
		$view 		 = JRequest::getVar('view');
		if  ($option == 'com_k2' && $view == 'item'/*K2 Specific*/) {
			return true;
		} 
		return false;
	}
	public function isK2()
	{
		$option 	 = JRequest::getVar('option');
		if  ($option == 'com_k2') {
			return true;
		} 
		return false;
	}
} 
