<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Metro, a sleek, intuitive, and powerful framework for faster and easier web development for Windows Metro Style.">
    <meta name="keywords" content="HTML, CSS, JS, JavaScript, framework, metro, front-end, frontend, web development">
    <meta name="author" content="Sergey Pimenov and Metro UI CSS contributors">

    <link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />

    <title><?= isset($title) ? $title : APP_NAME; ?></title>

    <script>
        var baseUrl = '<?= base_url(); ?>';
        var baseAsset = '<?= BASE_ASSET; ?>';
        var sessionUser = <?= json_encode(userdata('session_user')); ?>;
    </script>
    
    <link href="<?= asset('metro-ui/build/css/metro.css'); ?>" rel="stylesheet">
    <link href="<?= asset('metro-ui/build/css/metro-icons.css'); ?>" rel="stylesheet">
    <link href="<?= asset('metro-ui/build/css/metro-responsive.css'); ?>" rel="stylesheet">
    <link href="<?= asset('front/css/custom.css'); ?>" rel="stylesheet">
    
    <script src="<?= asset('metro-ui/build/js/jquery-2.1.3.min.js'); ?>"></script>
    <script src="<?= asset('metro-ui/build/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?= asset('metro-ui/build/js/metro.min.js'); ?>"></script>
    <script src="<?= asset('front/js/app.js'); ?>"></script>
    <script type="text/javascript">
    $.fn.inputEnabled = function(opsi){
        $(this).removeAttr('disabled');
    };
    </script>

        
</head>
<body class="bg-white">
    <div class="app-bar fixed-top darcula" data-role="appbar">
        <a class="app-bar-element branding" href="<?= site_url('home'); ?>"><?= APP_NAME; ?></a>
        <span class="app-bar-divider"></span>
        <ul class="app-bar-menu">
            <li>
                <a href="" class="dropdown-toggle">Base</a>
                <ul class="d-menu" data-role="dropdown">
                   <li><a href="<?= site_url('base/home'); ?>">Home Village</a></li>
                   <li><a href="<?= site_url('base/war'); ?>">War Base</a></li>
                   <li class="divider"></li>
                   <li><a href="<?= site_url('base/create'); ?>" onclick='return cekLogin()'> Create New Base</a></li>
               </ul>
           </li>
           <li><a href="">MY Clan</a></li>
           <li>
            <a href="" class="dropdown-toggle">Help</a>
            <ul class="d-menu" data-role="dropdown">
                <li><a href="<?= site_url('page/about'); ?>">About</a></li>
            </ul>
        </li>       
    </ul>

    <div class="app-bar-element place-right">
        <span class="dropdown-toggle"><span class="mif-cog"></span></span>
        <div class="app-bar-drop-container padding10 place-right no-margin-top block-shadow fg-dark" data-role="dropdown" data-no-close="true" style="width: 220px">
            <h2 class="text-light">Howdy..</h2>
            <ul class="unstyled-list fg-dark">

                <?php if($this->auth->login_scurity(FALSE)): ?>
                <li><a href="<?= site_url('user/my_profile'); ?>" class="fg-white3 fg-hover-yellow"><span><?= user_member('user_name'); ?></span> Profile</a></li>
                <?php endif; ?>

                <?php if(!$this->auth->login_scurity(FALSE)): ?>
                <li><a href="#" onclick="showDialog('dialogLogin');" class="fg-white1 fg-hover-yellow">Login</a></li>
                <?php else: ?>
                <li><a href="<?= site_url('user/logout'); ?>" class="fg-white1 fg-hover-yellow">Logout</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>


<!-- dialog login -->
<div style="" data-role="dialog" id="dialogLogin" class="padding20" data-close-button="true" data-overlay="true" data-overlay-color="op-dark" data-windows-style="true" data-overlay-click-close="true">
    <h1>Login</h1>
    <form action="<?= site_url('user/login'); ?>" id='formLogin'>
       <div class="cell">
           <div class="input-control full-size text">
                <span class="mif-user prepend-icon"></span>
                <input type="text" name="email" placeholder='email'>
            </div>
        </div>
        <div class="cell">
            <div class="input-control full-size text">
                <span class="mif-lock prepend-icon"></span>
                <input type="password" name="password" placeholder='password'>
            </div>
        </div>
        <br>
        <input type="submit" class="button" value="login"> Don't have accont ? <a class="fg-orange" href='#' onclick="showDialog('dialogRegister'); hideDialog('dialogLogin');">register</a>
    </form>
</div>

<!-- dialog login -->
<div style="" data-role="dialog" id="dialogRegister" class="padding20" data-close-button="true" data-overlay="true" data-overlay-color="op-dark" data-windows-style="true" data-overlay-click-close="true">
    <h1>Register</h1>
    <form action="<?= site_url('user/register'); ?>" id='formRegister'>
       <div class="cell">
           <div class="input-control full-size text">
                <span class="mif-user prepend-icon"></span>
                <input type="text" name="name" placeholder='name'>
            </div>
        </div>
       <div class="cell">
           <div class="input-control full-size text">
                <span class="mif-contacts-mail prepend-icon"></span>
                <input type="text" name="email" placeholder='email'>
            </div>
        </div>
        <div class="cell">
            <div class="input-control full-size text">
                <span class="mif-lock prepend-icon"></span>
                <input type="password" name="password" placeholder='password'>
            </div>
        </div>
        <br>
        <input type="submit" class="button" value="register"> have accont ? <a class="fg-orange" href='#' onclick="showDialog('dialogLogin'); hideDialog('dialogRegister');">login</a>
    </form>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        //notif
        var notif = "<?= userdata('notif'); ?>";

        if(notif) {
            console.log(notif);
            $.Notify({
                caption: '',
                content: notif,
                type: 'success'
            });
        }

        $('#formLogin').submit(function() {
            var url = $(this).attr('action');
            var dataForm = $(this).serialize();
            var formLogin = $(this);

            $(formLogin).find('button').addClass('loading-cube');

            $.ajax({
                url : url,
                data : dataForm ,
                type : 'POST',
                dataType : 'JSON',
                success : function(res) {
                    if(res.flag) {
                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'success'
                        });

                      window.location = '<?= site_url(); ?>';
                    } else {
                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'warning'
                        });
                    }

                    $(formLogin).find('button').removeClass('loading-cube');
                    console.log(res);
                    return false;
                }
            });

            $(formLogin).find('textarea').inputEnabled();
            $(formLogin).find('textarea').html('');

            return false;
        });

        //register
        $('#formRegister').submit(function() {
            var url = $(this).attr('action');
            var dataForm = $(this).serialize();
            var formRegister = $(this);

            $(formRegister).find('button').addClass('loading-cube');

            $.ajax({
                url : url,
                data : dataForm ,
                type : 'POST',
                dataType : 'JSON',
                success : function(res) {
                    if(res.flag) {
                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'success'
                        });

                      window.location = '<?= site_url(); ?>';
                    } else {
                        $.Notify({
                            caption: '',
                            content: res.msg,
                            type: 'warning'
                        });
                    }

                    $(formRegister).find('button').removeClass('loading-cube');
                    console.log(res);
                    return false;
                }
            });

            $(formRegister).find('textarea').inputEnabled();
            $(formRegister).find('textarea').html('');

            return false;
        });
    });
</script>
