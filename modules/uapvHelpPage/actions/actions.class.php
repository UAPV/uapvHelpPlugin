<?php

/**
 * authentication actions.
 */
class uapvHelpPageActions extends sfActions
{
  public function executeShow (sfWebRequest $request)
  {
    $this->returnPage($request->getParameter ('file'));
  }

  protected function returnPage ($filename)
  {
    $this->helpFinder = new uapvHelpFinder ($this->getContext ());

    $filename = $this->helpFinder->fileExists ($filename) ? $filename : (
                $this->helpFinder->fileExists ($filename.'/index') ? $filename.'/index' : null );

    $this->forward404If ($filename === null);

    $this->breadcrumb = $this->helpFinder->getBreadcrumb ($filename);

    // TODO : refactor markdown parsing !
    require_once 'PhpMarkdown/markdown.php';
    ob_start ();
    include $this->helpFinder->getAbsolutePath ($filename);
    $this->htmlDoc = Markdown (ob_get_clean ());
  }
}
