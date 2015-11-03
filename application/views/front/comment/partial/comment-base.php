 <div class="box-comment padding10 text-default popover marker-on-left ">
 	<div class="cell">
 	<span class='mif-user padding10 fg-blue '><a href=""> <?= ucwords($row->user_name); ?></a></span> 
 		<span class=' padding10 place-'> <small><i><?= time_ago($row->comment_created_date); ?></i></small></span> 
 		<div class="rating small score-hide place-right" data-role="rating" data-static='true' data-stars='5' data-value='<?= $row->comment_rating_count; ?>'></div>
 	</div>
 	<hr class="thin bg-grayLighter">
 	<?= $row->comment_text; ?>
 </div>