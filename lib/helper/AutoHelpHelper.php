<?php

/**
 * This helper tries to find a help page corresponding to the current module/action
 * and return a 
 */
function help_link ($label = 'help')
{
  $context = sfContext::getInstance ();

  // check if there is a doc file for this module/action
  $helpFinder = new uapvHelpFinder ($context);
  if (($url = $helpFinder->getHelpUrl()) !== null)
    return '<div id="help">'.link_to ($label, $url, array ('target' => '_blank')).'</div>';

  return '';
}

/**
 *
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
  return link_to ($name, url_for_help ($doc_uri), $options);
}

function url_for_help ($doc_uri)
{
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  return $helpFinder->generateUrl ($helpFinder->resolve($doc_uri));
}

function include_help_partial_if_exists ($templateName)
{
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  return ($helpFinder->fileExists ($helpFinder->resolve ($templateName, true)) !== false ? include_help_partial ($templateName) : '');
}

function include_help_partial ($templateName)
{
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  $templateName = $helpFinder->resolve ($templateName);
  $view = new uapvMarkdownPartialView (sfContext::getInstance (), 'uapvHelpPage', $templateName.'.mkd', '');
  $view->setDirectory ($helpFinder->getHelpRootDir ());
  return $view->render();
}
