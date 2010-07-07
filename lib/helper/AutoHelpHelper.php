<?php

/**
 *
 */
function help_link ($label = 'help')
{
  global $sf_context;

  $context = sfContext::getInstance ();
  $module  = $context->getModuleName ();
  $action  = $context->getActionName ();

  // check if there is a doc file for this module/action
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  if (($url = $helpFinder->resolve ($module.'/'.$action)) !== null)
   return '<div id="help">'.link_to_help ($label, $url).'</div>';
 
  return '';
}

/**
 *
 * @param  string $name     name of the link, i.e. string to appear between the <a> tags
 * @param  string $doc_uri  'module/action' or '@rule' of the action
 * @param  array  $options  additional HTML compliant <a> tag parameters
 *
 * @return string XHTML compliant <a href> tag
 *
 * @see    link_to for $option
 */
function link_to_help ($name, $doc_uri, $options = array ())
{
  return link_to ($name, '@uapvHelpShowPage?file='.$doc_uri, $options);
}
