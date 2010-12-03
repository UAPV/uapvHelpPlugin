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

/**
 * Include a markdown partial if it exists
 *
 * @param  string $templateName     ex: user/delete
 * @return string
 */
function include_help_partial_if_exists ($templateName)
{
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  return ($helpFinder->fileExists ($helpFinder->resolve ($templateName, true)) !== false ? include_help_partial ($templateName) : '');
}

/**
 * Include a partial in the mardown format
 *
 * @param  string $templateName     ex: user/delete
 * @return string
 */
function include_help_partial ($templateName)
{
  $helpFinder = new uapvHelpFinder (sfContext::getInstance ());
  $templateName = $helpFinder->resolve ($templateName, true);
  $view = new uapvMarkdownPartialView (sfContext::getInstance (), 'uapvHelpPage', $templateName.'.mkd', '');
  $view->setDirectory ($helpFinder->getHelpRootDir ());
  return $view->render();
}

/**
 * Return an url to access documentation static files
 *
 * @param  $uri
 * @return string
 */
function help_public_path ($uri)
{
  $context = sfContext::getInstance ();
  $rootDir = $context->getConfiguration ()->getApplication ().'/';

  if (empty ($uri))
    return;

  if ($uri[0] != '/') // We've got an relative URL
    $rootDir .= dirname ($context->getRequest()->getParameter ('file')).'/';

  return public_path ('/doc_assets/'.$rootDir.$uri);
}

/**
 * Return an image tag in the markdown syntax
 *
 * @param string $uri  relative or absolute uri (from the language document root)
 * @param string $title
 *
 * @return string Markdown image tag
 */
function help_image_tag ($uri, $title = '')
{

  return ' !['.$title.']('.help_public_path($uri).') ';
}