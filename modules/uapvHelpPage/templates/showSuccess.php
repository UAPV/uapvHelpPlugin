
  <div id="doc_navigation">
    <?php $first = true; foreach ($breadcrumb as $crumb): ?>
      <?php echo ($first ? '' : ' &gt; ') . link_to_help ($crumb['label'], $crumb['path']); $first = false; ?>  
    <?php endforeach ?>
  </div>

  <div id="doc_content">
    <div id="doc_toc"></div>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#doc_toc').toc({
          context: '#page',
          autoId: true
        });
        if ($('#doc_toc ul').children().length == 0)
          $('#doc_toc').remove ();
      });
    </script>

    <?php echo $htmlDoc ?>
      
  </div>
