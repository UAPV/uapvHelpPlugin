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
  protected $availableLanguages = array ();

  /* @var $baseDocDir string */
  protected $baseDocDir = null;

  /* @var $context sfContext */
  protected $context = null;


  /**
   * Constructor
   * 
   * @param sfContext $request
   */
  public function __construct (sfContext $context)
  {
    $this->context = $context;

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
   * Find the documentation file corresponding to a module and(?) action
   *
   * @param string $file    By convention : 'module_name/action_name' (without .mkd)
   *
   * @return string or null if not found
   */
  public function resolve ($file)
  {
    if ($this->fileExists ($file) !== false)
      return $file;

    if ($file == 'index')
      return null;

    if ($this->fileExists ($file.'/index') !== false)
      return $file.'/index';

    return $this->resolve ($this->getParentDirectory ($file));
  }

  /**
   * Checks if a file exists in any language
   * 
   * @param string $file
   * @return string or false if not found
   */
  public function fileExists ($file)
  {
    return file_exists ($this->getAbsolutePath ($file));
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

  /**
   * Return the absolute path of the help page after cleaning it from possible
   * path hack...
   *
   * @param  $filename  Name of the help page (ex: fr/user/edit)
   * @return string
   */
  public function getAbsolutePath ($filename)
  {
    $filename = str_replace('/../', '/', $filename); // TODO improve
    return $this->getHelpRootDir ().$filename.'.mkd';
  }

  /**
   * Generate the URL associated to the page $filename (ex: fr/admin/edit)
   *
   * @param  $filename    
   * @return string
   */
  public function generateUrl ($filename)
  {
    return $this->context->getController()->genUrl('@uapvHelpShowPage?file=').$filename;
  }

  /**
   * @param  $page
   * @return string
   */
  public function getPageTitle ($page)
  {
    $pageContent = file_get_contents($this->getAbsolutePath ($page));

    $matches = array ();
    if (preg_match('{^(.+?)[ ]*\n=+[ ]*}mx', $pageContent, $matches)     == 1 || // Setext style (undescored with "=")
        preg_match ('{^\#[ ]*(.+?)[ ]*\#$\n+}xm', $pageContent, $matches) == 1 ) // atx style ("# title")
    {
      return $matches[1];
    }
    else
      return ucfirst (basename ($page));
  }

  public function getBreadcrumb ($file)
  {
    $breadcrumb = array ();

    if (dirname ($file) != '.')
    {
      $breadcrumb = $breadcrumb + $this->getBreadcrumb ($this->getParentDirectory ($file.'/index'));
    }

    $breadcrumb [] = array (
      'label' => $this->getPageTitle($file),
      'path'  => $file,
    );

    return $breadcrumb;
  }

  protected function getParentDirectory ($path)
  {
    print_r( substr ($path, 0, strrpos ($path, '/')));die;
    return substr ($path, 0, strrpos ($path, '/'));
  }

}
