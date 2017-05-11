<?php
/**
 * @package    DD_Google_Analytics
 *
 * @author     HR IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2017 - 2017 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die();

/**
 * Class PlgSystemDD_Google_AnalyticsInstallerScript
 *
 * @since  Version  1.0.0.0
 */

class PlgSystemDD_Google_AnalyticsInstallerScript
{
	protected $name = 'DD Google Analytics';

	protected $extensionsToEnable = array(

		array(  'name'  => 'dd_google_analytics',
				'type'  => 'plugin',
				'group' => 'system')

	);

	/**
	 * Enable extensions
	 *
	 * @since Version 1.1.0.2
	 *
	 * @return void
	 */
	private function enableExtensions()
	{
		foreach ($this->extensionsToEnable as $extension)
		{
			$db  = JFactory::getDbo();
			$query = $db->getQuery(true)
					->update('#__extensions')
					->set($db->qn('enabled') . ' = ' . $db->q(1))
					->where('type = ' . $db->q($extension['type']))
					->where('element = ' . $db->q($extension['name']));

			if ($extension['type'] === 'plugin')
			{
				$query->where('folder = ' . $db->q($extension['group']));
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * JInstaller
	 *
	 * @param   object  $parent  \JInstallerAdapterPackageParent
	 *
	 * @return  boolean
	 *
	 * @since Version 1.1.0.2
	 */
	public function install($parent)
	{
		$this->enableExtensions();

		return true;
	}
}
