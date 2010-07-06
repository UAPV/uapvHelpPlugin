<?php

/**
 *
 */
function help_link ()
{
  $context = sfContext::getInstance ();
  $module  = $context->getModuleName ();
  $action  = $context->getActionName ();

  // check if there is a doc file for this module/action
  if (false /* TODO */)
    return '';

  $html = '<div id="help">'.link_to_help ('help', $module.'/'.$action).'</div>';
 
  return $html;
}

/**
 *
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag
 * @see    link_to
 */
function link_to_help ($name, $internal_uri, $options = array ())
{
  $url = '@uapvHelpShowPage';
  if (preg_match ('/^(([^#\/]*)(\/([^#]*))?)?(#(.*))?$/', $internal_uri, $exploded_url))
  {
    if (array_key_exists ('2', $exploded_url))
      $url .= '?help_module='.$exploded_url[2];
    if (array_key_exists ('4', $exploded_url))
      $url .= '&help_action='.$exploded_url[4];
    if (array_key_exists ('6', $exploded_url))
      $options ['anchor'] = $exploded_url[6];
  }

  return link_to ($name, $url, $options);
}
