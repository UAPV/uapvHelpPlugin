<?php

/**
 * authentication actions.
 */
class uapvHelpPageActions extends sfActions
{
  public function executeShow (sfWebRequest $request)
  {
    $docFile = $request->getParameter ('file');
    $this->returnPage($docFile.'.mkd');
  }

  protected function returnPage ($filename)
  {
    $this->helpFinder = new uapvHelpFinder ($this->getContext ());
    if ($this->helpFinder->fileExists ($filename));
        return $this->forward404 ();

    $this->breadcrumb = $this->helpFinder->getBreadcrumb ($docFile);

    // TODO : refactor markdown parsing !
    require_once 'PhpMarkdown/markdown.php';
    ob_start ();
    include $this->helpFinder->getAbsolutePath ($filename);
    $this->htmlDoc = Markdown (ob_get_clean ());
  }
}
