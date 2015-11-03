
<div class="page-content">
    <div class="flex-grid no-responsive-future" style="height: 100%;">
        <div class="row" style="height: 100%">
            <div class="cell auto-size padding20 bg-white" id="cell-content">
                <h1 class="text-light"><span class='fg-yellow'><?= ucwords($row->base_name); ?></span></span></h1>
                <hr class="thin bg-grayLighter">
                <!-- content -->
                <div class="row">
                    <div class="cell6 padding20 ">

                        <div class="row">
                            <div class="image-container">
                                <div class="frame"><img src="<?= asset('base-image/'.$row->base_image); ?>"></div>
                                <div class="image-overlay">

                                </div>
                            </div>
                        </div>
                        <div class="cell">
                            <span class='mif-bell fg-grayLight padding10 place-right'> <?= time_ago($row->base_created_date); ?></span> 
                            <a class="button" href="<?= site_url('base/download_base_image/' .  $row->idbase); ?>">
                                <span class="icon mif-download bg-white fg-orange"></span> 
                                Save
                            </a>
                        </div>
                        <hr class="thin bg-grayLighter">
                        <?php $total_rating = 0;
                        $rating = 0;
                        $total_comment = count($result_comment);

                        foreach($result_comment as $com) {$total_rating += (int) $com->comment_rating_count;}
                        $rating = round($total_rating * $total_comment) / 5;
                        ?>
                        <p class="text-default no-padding no-margin"><?= $row->base_desc; ?></p>
                            <br>
                        <div class="cell no-margin">
                            <span class='mif-user  '> Uploaded by <a href="<?= site_url('user/detail/'.$row->iduser); ?>"><?= ucwords($row->user_name); ?></a></span> 
                            <span class='mif-home  '> <a href="#">TH <?= $row->base_town_hall; ?></a></span> 
                        </div>
                        <br>
                        <div class="rating" data-role="rating" data-size="large" data-static='true' data-stars='5' data-value='<?= $rating; ?>'></div>
                    </div>
                    <div class="divider-base"></div>
                </div>
                <!-- end content -->
                <hr class="thin bg-grayLighter">

                <!-- comment -->
                <div class="message"></div>
                <span class=' mif-bubbles mif-ani-ring mif-ani-slow fg-taupe mif-2x'></span> <u><span class='count-total-comment'><?= $total_comment; ?></span> Peopple Commented</u>
                <br>
                <br>
                <?php 
                if(!$user_comment): ?>
                <form action="<?= site_url('comment/base_comment/'.$row->idbase); ?>" id='form-komentar'>
                    <div class="input-control textarea full-size" data-role="input" data-text-auto-resize="true" data-text-max-height="200" data-text-min-height="50">
                        <textarea value='text' placeholder='Enter your comment.' name="comment" ></textarea>
                    </div>
                    <input value="<?= $row->idbase; ?>" name='idbase' type='hidden'>
                    <div class="rating comment-rating place-right score-hide" data-role="rating" data-stars='5' data-value=''></div>
                    <button class="button">Send</button>
                </form>
                <?php endif; ?>
                <br>

                <!-- list comment -->
                <div class="container-result-comment">
                    <?php foreach($result_comment as $com): ?>
                    <div class="box-comment padding10 text-default popover marker-on-left ">
                        <div class="cell">
                            <span class='mif-user padding10 fg-blue '><a href=""> <?= ucwords($com->user_name); ?></a></span> 
                            <span class=' padding10 place-'> <small><i><?= time_ago($com->comment_created_date); ?></i></small></span> 
                            <div class="rating small score-hide place-right" data-role="rating" data-static='true' data-stars='5' data-value='<?= $com->comment_rating_count; ?>'></div>
                        </div>
                        <hr class="thin bg-grayLighter">
                        <?= $com->comment_text; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                </div>
            </div>
        </div>


<script type="text/javascript">
    $(document).ready(function(){
        $('#form-komentar').submit(function() {
            if(!cekLogin()) {
                return false;
            }

            var url = $(this).attr('action');
            var dataForm = $(this).serialize();
            var rating = $(this).find('.comment-rating').rating('value');
            var formKomentar = $(this);

            $(formKomentar).find('textarea').inputDisabled();
            $(formKomentar).find('button').addClass('loading-cube');

            $.ajax({
                url : url,
                data : dataForm + '&rating=' + rating ,
                type : 'POST',
                dataType : 'JSON',
                success : function(res) {
                    if(res.flag) {
                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'success'
                        });

                        $('.container-result-comment').prepend(res.html);

                        var totalComment = $('.count-total-comment').html();
                        $('.count-total-comment').html(parseInt(totalComment) + 1);

                        $('#form-komentar').fadeOut();
                    } else {

                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'warning'
                        });
                    }

                    $(formKomentar).find('button').removeClass('loading-cube');
                }
            });

            $(formKomentar).find('textarea').inputEnabled();
            $(formKomentar).find('textarea').html('');

            return false;
        });
    });
</script>