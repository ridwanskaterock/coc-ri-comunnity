<div class="page-content">
    <div class="flex-grid no-responsive-future" style="height: 100%;">
        <div class="row" style="height: 100%">
            <div class="cell auto-size padding20 bg-white" id="cell-content">
                <h1 class="text-light">New Base<span class="mif-map place-right"></span></h1>
                <hr class="thin bg-grayLighter">
                <div class="tile-large ol-transparent" data-role="tile">
                    <div class="tile-content">
                        <div class="carousel" data-role="carousel" data-height="100%" data-width="100%" data-controls="false">
                            <div class="slide">
                                <img src="<?= asset('base-image/skull-base.jpg'); ?>" data-role="fitImage" data-format="fill">
                            </div>
                            <div class="slide tile-content">
                                <img src="<?= asset('base-image/kitty-base.jpg'); ?>"  data-role="fitImage" data-format="fill">
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?= site_url('base?type=favourite'); ?>">
                <div class="tile bg-yellow fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon mif-star-full"></span>
                        <span class='tile-label padding10'>Favourite Base</span>
                    </div>
                </div>
                </a>
                <a href="<?= site_url('base'); ?>">
                <div class="tile bg-orange fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon  mif-video-camera"></span>
                        <span class='tile-label padding10'>Watch Attack</span>
                    </div>
                </div>
                </a>

                <a href="<?= site_url('base/create'); ?>" onclick='return cekLogin()' >
                <div class="tile bg-teal fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon mif-pencil"></span>
                        <span class='tile-label padding10'>Create Base</span>
                    </div>
                </div>
                </a>
                <?php if(!$this->auth->login_scurity(FALSE)): ?>
                <a href="#" onclick="showDialog('dialogLogin')">
                <div class="tile bg-lightOrange fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon mif-lock"></span>
                        <span class='tile-label padding10'>Login</span>
                    </div>
                </div>
                <?php endif; ?>
                </a>
                <a href="<?= site_url('base'); ?>">
                <div class="tile bg-blue fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon mif-envelop"></span>
                        <span class='tile-label padding10'>Message</span>
                    </div>
                </div>
                </a>
                <a href="<?= site_url('base'); ?>">
                <div class="tile bg-green fg-white" data-role="tile">
                    <div class="tile-content iconic">
                        <span class="icon mif-info"></span>
                        <span class='tile-label padding10'>About</span>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>
</div>