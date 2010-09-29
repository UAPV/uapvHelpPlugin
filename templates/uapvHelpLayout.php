<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php use_stylesheet('/uapvHelpPlugin/css/elastic.css') ?>
<?php use_stylesheet('/uapvHelpPlugin/css/elastic.print.css') ?>
<?php use_stylesheet('/uapvHelpPlugin/css/main.css') ?>

<?php use_javascript('/uapvHelpPlugin/js/jquery.js') ?>
<?php use_javascript('/uapvHelpPlugin/js/jquery.toc-1.1.0.js') ?>

<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_javascripts() ?>
<?php include_stylesheets() ?>
<?php include_title() ?>

<link rel="shortcut icon" href="/favicon.ico" />

</head>
<body id="uapvHelpPlugin">


<div id="page">


    <div id="doc_header">
      <?php echo include_help_partial_if_exists ('_header'); ?>
    </div>

    <?php echo $sf_content ?>

    <div id="doc_footer">
      <?php echo include_help_partial_if_exists ('_footer'); ?>
    </div>

</div>

</body>
</html>
