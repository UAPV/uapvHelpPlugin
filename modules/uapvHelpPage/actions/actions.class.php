<?php

/**
 * authentication actions.
 */
class uapvHelpPageActions extends sfActions
{
  public function executeShow (sfWebRequest $request)
  {
    $this->help_module = $request->getParameter ('help_module');
    $this->help_action = $request->getParameter ('help_action');
  }
}
 
