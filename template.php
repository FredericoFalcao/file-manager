<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
window.MAX_UPLOAD_SIZE = <?php echo $MAX_UPLOAD_SIZE; ?>;
window.ALLOW_DIRECT_LINK = <?php echo $allow_direct_link ? 'true' : 'false'; ?>;
</script>
<script src="script.js"></script>
</head><body>
<div id="top">
   <?php if($allow_create_folder): ?>
        <form action="?" method="post" id="mkdir" />
                <label for=dirname>Create New Folder</label><input id=dirname type=text name=name value="" />
                <input type="submit" value="create" />
        </form>

   <?php endif; ?>

   <?php if($allow_upload): ?>

        <div id="file_drop_target">
                Drag Files Here To Upload
                <b>or</b>
                <input type="file" multiple />
        </div>
        <form method="POST" action="">
          Youtube URL:
          <input type="text" name="youtube_url" value="" />
          <input type="submit" />
        </form>
   <?php endif; ?>
        <div id="breadcrumb">&nbsp;</div>
</div>

<div id="upload_progress"></div>
<table id="table"><thead><tr>
        <th>Name</th>
        <th>Size</th>
        <th>Modified</th>
        <th>Permissions</th>
        <th>Actions</th>
</tr></thead><tbody id="list">

</tbody></table>
<footer>simple php filemanager by <a href="https://github.com/jcampbell1">jcampbell1</a></footer>
</body></html>
