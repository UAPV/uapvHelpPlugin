<?php

/**
 * Description of uapvMarkdownView
 *
 * @author didrya
 */
class uapvMarkdownView extends sfPHPView
{

  public function configure ()
  {
    $this->setExtension ('');
    parent::configure ();
  }

  /**
   * Renders the presentation.
   *
   * @param  string $_sfFile  Filename
   *
   * @return string File content
   */
  protected function renderFile($_sfFile)
  {
    require_once 'PhpMarkdown/markdown.php';
    return Markdown (parent::renderFile($_sfFile));
  }
}
