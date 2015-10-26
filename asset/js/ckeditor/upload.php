<?php
	//process $_FILES['upload']
	//store uploaded images URL in $uploadedImageURL
	//print_r($_FILES);
	
	$uploaddir = './uploads/';
	$uploadfile = $uploaddir . basename($_FILES['upload']['name']);

	if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile)) {
	   echo "File is valid, and was successfully uploaded.\n";
	} else {
	   echo "Possible file upload attack!\n";
	}

	
?>
<script type="text/javascript">
//window.parent.CKEDITOR.tools.callFunction( <?php echo $_GET['CKEditorFuncNum']?>, '<?php echo "$uploadfile"?>' );
</script>