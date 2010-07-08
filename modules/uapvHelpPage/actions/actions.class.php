<?php

/**
 * authentication actions.
 */
class uapvHelpPageActions extends sfActions
{
  public function executeShow (sfWebRequest $request)
  {
    //sfConfig::set ('mod_uapvHelpPage_view_class', 'uapvPHPMarkdown');
    $this->helpFinder = new uapvHelpFinder ($this->getContext ());
    $docFile = $request->getParameter ('file');
    $filename = $this->helpFinder->fileExists ($docFile);

    if ($filename === false)
    {
      // The file wasn't found. Let's search higher or in another language
      if (null !== ($filename = $this->helpFinder->resolve ($docFile)))
      {
        $baseUrl = $this->getController()->genUrl('@uapvHelpShowPage?file=');
        return $this->redirect ($baseUrl.$filename); // prevent symfo from url_encoding 'file'
      }
      else
        return $this->forward404 ();

    }

    // TODO : refactor markdown parsing !
    //$this->setTemplate ($this->helpFinder->getHelpRootDir ().$filename);
    require_once 'PhpMarkdown/markdown.php';
    ob_start ();
    include $this->helpFinder->getHelpRootDir ().$filename;
    $this->htmlDoc = Markdown (ob_get_clean ());
  }
}
