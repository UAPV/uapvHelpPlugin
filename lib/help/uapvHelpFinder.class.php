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
   * Return the help url associated to the current context.
   * Return null if not found.
   *
   * @return string or null
   */
  public function getHelpUrl ()
  {
    $module  = $this->context->getModuleName ();
    $action  = $this->context->getActionName ();

    $requestUrl = $this->context->getRequest()->getPathInfo ();
    foreach (sfConfig::get ('app_help_external', array ()) as $pattern => $url)
    {
      if (preg_match ($pattern, $requestUrl) === 1)
        return $url;
    }

    // check if there is a doc file for this module/action
    $url = $this->resolve ($module.'/'.$action);

    return ($url !== null ? $this->generateUrl ($url) : null);
  }

  /**
   * Find the documentation file corresponding to a module and(?) action in *any* language
   *
   * @param string $file    By convention : 'module_name/action_name' (without .mkd)
   *
   * @return string or null if not found
   */
  public function resolve ($file, $exactMatch = false)
  {
    foreach ($this->languages as $lang)
    {
      $filename = "$lang/$file";

      // if $file is a directory, we try find an index file inside
      if (!$exactMatch && $this->directoryExists ($filename) && $this->fileExists ($filename.'/index'))
        return $filename.'/index';

      // if $file is a file we've got what we were searching for !
      else if ($this->fileExists ($filename))
        return $filename;
    }

    // If we want an exact match there is no need to continue
    if ($exactMatch)
      return null;

    // if we reached the documentation root dir it means that there is no documentation
    if (in_array (dirname ($file), array('.','/')) && basename ($file) == 'index')
      return null;

    // if we are here we didn't find a file in the current directory, let's go up !
    return $this->resolve (dirname ($this->getParentDirectory ($file)).'/index');
  }

  /**
   * Checks if a file exists in any language
   *
   * @param string $file
   * @return string or false if not found
   */
  public function fileExists ($file)
  {
    return is_file ($this->getAbsolutePath ($file));
  }

  /**
   * Checks if a file exists in any language
   *
   * @param string $file
   * @return string or false if not found
   */
  public function directoryExists ($file)
  {
    return is_dir ($this->getAbsolutePath ($file));
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
   * Extract the page title from the markdown file
   *
   * @param  $page
   * @return string
   */
  public function getPageTitle ($page)
  {
    $pageContent = file_get_contents($this->getAbsolutePath ($page));

    $matches = array ();
    if (preg_match('{^(.+?)[ ]*\n=+[ ]*}mx', $pageContent, $matches)     == 1 || // Setext style (undescored with "=")
        preg_match ('{^\#[ ]*(.+?)[ ]*\#*\n+}xm', $pageContent, $matches) == 1 ) // atx style ("# title")
    {
      return $matches[1];
    }
    else
      return ucfirst (basename ($page));
  }

  /**
   * Return a breadcrumb from the file $file to the documentation root dirs
   *
   * @param  $file
   * @return array
   */
  public function getBreadcrumb ($file)
  {
    if (dirname ($file) != '.' && dirname ($file) != '/')
    {
      if (basename ($file) == 'index')
        $breadcrumb = $this->getBreadcrumb (dirname ($this->getParentDirectory ($file)).'/index');
      else
        $breadcrumb = $this->getBreadcrumb (dirname ($file).'/index');
    }
    else
      $breadcrumb = array ();

    if ($this->fileExists($file))
      $breadcrumb [] = array (
        'label' => $this->getPageTitle($file),
        'path'  => substr ($file, strpos ($file, '/') + 1), // remove the first directory (lang)
      );

    return $breadcrumb;
  }

  /**
   * Return the parent directory of the current directory
   *
   * @param  $path
   * @return string
   */
  protected function getParentDirectory ($path)
  {
    return substr ($path, 0, strrpos ($path, '/'));
  }

}
