<?php
/**
 * @package    DD_Google_Analytics
 *
 * @author     HR IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2017 - 2017 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

/**
 * Class plgSystemDD_Google_Analytics
 *
 * @since  Version  1.0.0.0
 */
class PlgSystemDD_Google_Analytics extends JPlugin
{
	protected $app;

	protected $propertyid;

	protected $euprivacy;

	protected $autoloadLanguage = true;

	/**
	 * onBeforeCompileHead
	 *
	 * @return void
	 *
	 * @since Version 1.0.0.0
	 */
	public function onBeforeCompileHead()
	{
		// Front end
		if ($this->app->isSite())
		{
			// Get plugin parameter
			$this->propertyid = htmlspecialchars($this->params->get('propertyid'));
			$this->euprivacy  = (int) $this->params->get('euprivacy');

			if ($this->checkPropertyID())
			{
				// Set tracking snipped
				JFactory::getDocument()->addScriptDeclaration($this->getTrackingSnipped());
			}
		}
	}

	/**
	 * checkPropertyID
	 *
	 * @return boolean true on successfull checks or throw system message with notes
	 *
	 * @since Version 1.0.0.0
	 */
	private function checkPropertyID()
	{
		if (empty($this->propertyid))
		{
			$this->app->enqueueMessage(JText::_('PLG_SYSTEM_DD_GOOGLE_ANALYTICS_ALERT_PROPERTYID_MISSING'), 'warning');

			return false;
		}
		elseif (!preg_match('/^ua-\d{4,10}-\d{1,4}/i', strval($this->propertyid)))
		{
			$this->app->enqueueMessage(JText::_('PLG_SYSTEM_DD_GOOGLE_ANALYTICS_ALERT_PROPERTYID_INVALID'), 'warning');

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * getTrackingSnipped ALYTICSSCRIPT
	 *
	 * @return string tracking snipped
	 *
	 * @since Version 1.0.0.0
	 */
	private function getTrackingSnipped()
	{
		// Anonymice IP setup
		if ($this->euprivacy)
		{
			$anonymiceIP = 'true';
		}
		else
		{
			$anonymiceIP = 'false';
		}

		return <<<ALYTICSSCRIPT
/**
 * Creates a temporary global ga object and loads analytics.js.
 * Parameters o, a, and m are all used internally.  They could have been declared using 'var',
 * instead they are declared as parameters to save 4 bytes ('var ').
 *
 * @param {Window}      i The global context object.
 * @param {Document}    s The DOM document object.
 * @param {string}      o Must be 'script'.
 * @param {string}      g URL of the analytics.js script. Inherits protocol from page.
 * @param {string}      r Global name of analytics object.  Defaults to 'ga'.
 * @param {DOMElement?} a Async script tag.
 * @param {DOMElement?} m First script tag in document.
 */
(function (i, s, o, g, r, a, m) {
	i['GoogleAnalyticsObject'] = r; // Acts as a pointer to support renaming.

	// Creates an initial ga() function.  The queued commands will be executed once analytics.js loads.
	i[r] = i[r] || function () {
		(i[r].q = i[r].q || []).push(arguments)
            },

            // Sets the time (as an integer) this tag was executed.  Used for timing hits.
            i[r].l = 1 * new Date();

        // Insert the script tag asynchronously.  Inserts above current tag to prevent blocking in
        // addition to using the async attribute.
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    ga('create', '{$this->propertyid}', 'auto'); // Creates the tracker with default parameters.
    ga('set', 'anonymizeIp', $anonymiceIP);
    ga('send', 'pageview'); // Sends a pageview hit.
ALYTICSSCRIPT;
	}
}
