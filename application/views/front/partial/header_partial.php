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

    <link href="<?= asset('metro-ui/build/css/metro.css'); ?>" rel="stylesheet">
    <link href="<?= asset('metro-ui/build/css/metro-icons.css'); ?>" rel="stylesheet">
    <link href="<?= asset('metro-ui/build/css/metro-responsive.css'); ?>" rel="stylesheet">
    <link href="<?= asset('front/css/custom.css'); ?>" rel="stylesheet">
    
    <script src="<?= asset('metro-ui/build/js/jquery-2.1.3.min.js'); ?>"></script>
    <script src="<?= asset('metro-ui/build/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?= asset('metro-ui/build/js/metro.js'); ?>"></script>
    <script src="<?= asset('front/js/app.js'); ?>"></script>
        
</head>
<body class="bg-steel">
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
                   <li><a href="<?= site_url('base/create'); ?>"> Create New Base</a></li>
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
            <h2 class="text-light">Quick settings</h2>
            <ul class="unstyled-list fg-dark">
                <li><a href="<?= site_url('user/profile'); ?>" class="fg-white1 fg-hover-yellow">Profile</a></li>
                <li><a href="<?= site_url('user/setting'); ?>" class="fg-white3 fg-hover-yellow">Exit</a></li>
            </ul>
        </div>
    </div>
</div>