<script>
	$(document).ready(function() {		
			$(".datepicker").inputmask("d-m-y");
		
			$('.popup').popupWindow({ 
				centerBrowser:1 
			}); 
	
			$('.editor').ckeditor({  
				enterMode : CKEDITOR.ENTER_BR,
				shiftEnterMode: CKEDITOR.ENTER_P,
				toolbar:[ 	[ 'Bold','Italic','-','RemoveFormat' ],		    
							[ 'NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
							[ 'Link','Unlink'],
							[ 'TextColor','BGColor' ],
							[ 'Image','Table','Source']
						],
						//filebrowserUploadUrl : '<?php echo base_url()?>media/ck_upload',						
						filebrowserUploadUrl : null, //disable upload tab
						filebrowserImageUploadUrl : null, //disable upload tab
						filebrowserFlashUploadUrl : null, //disable upload tab
						filebrowserLinkUrl   : null,
						filebrowserBrowseUrl : '<?php echo base_url()?>media/browseck'

			});
	});
</script>