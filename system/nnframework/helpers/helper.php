<?php
/**
 * NoNumber Framework Helper File: Helper
 *
 * @package         NoNumber Framework
 * @version         14.8.5
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class NNFrameworkHelper
{
	static function getPluginHelper(&$plugin, $params = null)
	{
		if (!$params)
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
			$params = NNParameters::getInstance()->getPluginParams($plugin->get('_name'));
		}

		require_once JPATH_PLUGINS . '/' . $plugin->get('_type') . '/' . $plugin->get('_name') . '/helper.php';
		$class = get_class($plugin) . 'Helper';

		return new $class($params);
	}

	static function processArticle(&$article, &$context, &$helper, $method, $params = array())
	{
		if (isset($article->text)
			&& !(
				$context == 'com_content.category'
				&& JFactory::getApplication()->input->get('view') == 'category'
				&& !JFactory::getApplication()->input->get('layout')
			)
		)
		{
			call_user_func_array(array($helper, $method), array_merge(array(&$article->text), $params));
		}

		if (isset($article->description))
		{
			call_user_func_array(array($helper, $method), array_merge(array(&$article->description), $params));
		}

		if (isset($article->title))
		{
			call_user_func_array(array($helper, $method), array_merge(array(&$article->title), $params));
		}

		if (isset($article->created_by_alias))
		{
			call_user_func_array(array($helper, $method), array_merge(array(&$article->created_by_alias), $params));
		}
	}
}
