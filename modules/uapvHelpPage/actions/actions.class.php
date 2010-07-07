<?php

/**
 * authentication actions.
 */
class uapvHelpPageActions extends sfActions
{
  public function executeShow (sfWebRequest $request)
  {
    $helpFinder = new uapvHelpFinder ($this->getContext ());

    if (($filename = $helpFinder->fileExists($request->getParameter ('file'))) !== false)
    {
      require_once 'PhpMarkdown/markdown.php';

      ob_start ();
      include $helpFinder->getHelpRootDir ().$filename;
      $this->htmlDoc = Markdown (ob_get_clean ());
    }
    else
        return $this->redirect404 ();
  }
}
 
