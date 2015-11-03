
<div class="page-content">
    <div class="flex-grid no-responsive-future" style="height: 100%;">
        <div class="row" style="height: 100%">
            <div class="cell auto-size padding20 bg-white" id="cell-content">
                <h1 class="text-light"><span class='fg-yellow'>Create</span> Base<span class="mif-pencil fg-red place-right mif-ani-ring mif-ani-slow"></span></h1>
                <hr class="thin bg-grayLighter">
               <!-- content -->
               <div class="row">

				<?php 
				$errorMsg = validation_errors(); 

				if($errorMsg) {
					echo "<span class='notify warning'>".$errorMsg."</span>";
				}
				?>

               <form action="" method="POST" enctype="multipart/form-data">
               	
	                <div class="input-control modern text full-size">
					    <input type="text" name="base_name" value="<?= set_value('base_name'); ?>">
					    <span class="label">Base Name</span>
					    <span class="informer fg-darkOrange">Enter base name minimum 5 character</span>
					    <span class="placeholder fg-green">Enter base name</span>
					</div>
					<br>
					<br>
				    <div class="input-control select full-size">
					    <span class="label fg-green">Towh Hall</span>

				        <select name="base_town_hall">
				        <?php for($i = 1; $i<=10; $i++): ?>
				            <option value='<?= $i; ?>'>TH-<?= $i; ?></option>
				        <?php endfor; ?>
				        </select>
				    </div>

					<div class="input-control textarea full-size" requi data-role="input" data-text-auto-resize="true" data-text-max-height="200" data-text-min-height="50">
	                    <textarea value='text' placeholder='Enter desc.' name="base_desc" value="<?= set_value('base_desc'); ?>"></textarea>
	                </div>

	                <br>
	                <br>
				    <div class="input-control file full-size" data-role="input">
					    <span class="label fg-green">Base Image</span>
				        <input type="file" name="base_image">
				        <button class="button"><span class="mif-folder"></span></button>
				    </div>



	                <button class="button bg-orange fg-white"><span class='mif-share '></span> Share Now !!</button>
               </form>

                </div>
               <!-- end content -->
        </div>
    </div>
</div>