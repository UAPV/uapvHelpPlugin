<?php

/**
 * uapvHelpPlugin configuration.
 * 
 * @package     uapvHelpPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class uapvHelpPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    sfToolkit::addIncludePath(array(realpath(dirname(__FILE__).'/../lib/vendor')));
  }
}
