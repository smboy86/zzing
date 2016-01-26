<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CIBOARD INSTALL</title>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css" />
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
<link href="<?php echo base_url(VIEW_DIR . 'install/css/common.css'); ?>" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/earlyaccess/nanumgothic.css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="wrapper">
    <!-- header start -->
    <div class="header">
        <h1>CIBOARD</h1>
        <span class="version">Version <?php echo CB_VERSION; ?> CIBoard Installation</span>
    </div>
    <!-- header end -->
    <!-- main start -->
    <div class="main">
        <ul class="menu">
            <li <?php echo ($install_step === 1) ? 'class="active"' : '';?>>약관동의</li>
            <li <?php echo ($install_step === 2) ? 'class="active"' : '';?>>기본환경체크</li>
            <li <?php echo ($install_step === 3) ? 'class="active"' : '';?>>CONFIG 설정파일체크</li>
            <li <?php echo ($install_step === 4) ? 'class="active"' : '';?>>디비설정체크</li>
            <li <?php echo ($install_step === 5) ? 'class="active"' : '';?>>관리자정보입력</li>
            <li <?php echo ($install_step === 6) ? 'class="active"' : '';?>>설치</li>
        </ul>
