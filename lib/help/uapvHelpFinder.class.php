<?php

/**
 * uapvHelpFinder allows to easily find the documentation files corresponding
 * to a specific module and/or action.
 *
 * @author didrya
 */
class uapvHelpFinder {

  /* @var $user array Intersection between user prefered languages and the available ones */
  protected $languages = array ();

  /* @var $user array Available language for the documentation */
  protected $Availablelanguages = array ();

  /* @var $baseDocDir string */
  protected $baseDocDir = null;

  
  /**
   * Constructor
   * 
   * @param sfContext $request
   */
  public function __construct (sfContext $context)
  {
    $this->baseDocDir = sfconfig::get('sf_root_dir').'/apps/'
                          .$context->getConfiguration()->getApplication().'/doc/';
    foreach (glob ($this->baseDocDir.'*', GLOB_ONLYDIR) as $dir)
      $this->availableLanguages [] = basename ($dir);

    $userLanguages = array_unique ($context->getRequest ()->getLanguages ()
                                + array (sfConfig::get ('sf_default_culture')));

    // Removing unavailable languages
    foreach ($userLanguages as $lang)
    {
      $langDetail = explode ('_', $lang);
      foreach ($this->availableLanguages as $lang2)
      {
        $langDetail2 = explode ('_', $lang2);
        if ($lang == $lang2 && !in_array ($lang, $this->languages))
        {
          $this->languages [] = $lang; // adding full notation, ex: fr_FR
          continue 2;
        }
        if (count ($langDetail2) > 1 && $langDetail[0] == $langDetail2[0]
            && !in_array ($langDetail[0], $this->languages))
        {
          $this->languages [] = $langDetail[0]; // adding short notation, ex: fr
          continue 2;
        }
      }
    }
  }

  /**
   * Returns the culture configured at the project level
   *
   * @return string
   */
  public function getDefaultLanguage ()
  {
    return ;
  }

  /**
   * Returns the user culture based
   *
   * @return string
   */
  public function getPreferedLanguage ()
  {
    return $user->getCulture();
  }

  /**
   * Find the documentation file corresponding to a module and(?) action
   *
   * @param string $file    By convention : 'module_name/action_name' (without .mkd)
   *
   * @return string or null if not found
   */
  public function resolve ($file)
  {
    $file = str_replace('/../', '/', $file); // TODO improve

    // Let's find the files by traversing toward documentation root dir
    $pos = strlen($file);
    while ($file != '')
    {
      if ($this->fileExists ($file))
        return $file;

      if ($this->fileExists ($file.'/index'))
        return $file.'/index';

      $pos = strrpos ($file, '/');
      $file = substr ($file, 0, $pos);
    }
    
    if ($this->fileExists ('index')) // last solution ?
      return 'index';

    // nothing was found
    return null;
  }

  /**
   * Checks if a file exists in any language
   * 
   * @param string $file
   * @return string or false if not found
   */
  public function fileExists ($file)
  {
    foreach ($this->languages as $lang)
    {
      $filename = $lang.'/'.$file.'.mkd';
      if (file_exists ($this->baseDocDir.$filename))
        return $filename;
    }

    return false;
  }

  /**
   * Returns the directory where the application documentation is stored
   *
   * @return string
   */
  public function getHelpRootDir ()
  {
    return $this->baseDocDir;
  }

}
