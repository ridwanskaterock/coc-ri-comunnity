
<div class="page-content">
    <div class="flex-grid no-responsive-future" style="height: 100%;">
        <div class="row" style="height: 100%">
            <div class="cell auto-size padding20 bg-white" id="cell-content">
                <h1 class="text-light"><span class='fg-yellow'><?= ucwords($type); ?></span> Base<span class="mif-map place-right"></span></h1>
                <hr class="thin bg-grayLighter">
               <!-- content -->
               <div class="row">
                <?php foreach($result as $row): ?>
                    <div class="cell6 padding20 container-base">
                       
                        <h3 class="base-title"><a href="<?= site_url('base/detail/'.$row->idbase); ?>"><?= ucwords($row->base_name); ?></a></h3>
                        <div class="row">
                        <div class="image-container">
                            <div class="frame"><img src="<?= asset('base-image/'.$row->base_image); ?>"></div>
                            <div class="image-overlay">
                                <h2><?= ucwords($row->base_name); ?></h2>
                                <p><?= $row->base_desc; ?></p>
                            </div>
                        </div>
                        </div>
                        <div class="cell">
                                <span class='mif-user padding10 '> <a href="<?= site_url('user/detail/'.$row->iduser); ?>"><?= ucwords($row->user_name); ?></a></span> 
                                <span class='mif-home padding10 '> <a href="#">TH <?= $row->base_town_hall; ?></a></span> 
                        </div>
                                <div class="rating" data-role="rating" data-size="large" data-static='true' data-stars='5' data-value='1'></div>
                    </div>
                    <div class="divider-base"></div>
                <?php endforeach; ?>

                </div>
               <!-- end content -->
        </div>
    </div>
</div>
