<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />
    <!--
    <meta name="apple-moblie-web-app-capable" content="yes" />
    -->
    <?php if (strncmp("https://xsvr.duckdns.org", base_url(), 23) == 0) { ?>
    <title> StayM</title>
    <?php } else { ?>
    <title> [DEV] StayM</title>
    <?php } ?>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/htl5.js"></script>
    <![endif]-->
    


    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- datetimePicker -->
    <!--
    <script type="text/javascript" src="https://uxsolutions.github.io//bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://uxsolutions.github.io//bootstrap-datepicker/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
    -->

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">

    <style type="text/css">
        @media (max-width: 767px)
        {
            .form-inline .form-control
            {
                display: inline-block;
                width: auto;
                vertical-align: middle;
            }
        }
        @media (max-width: 767px)
        {
            .form-inline .form-group
            {
                display: inline-block;
                margin-bottom: 0;
                vertical-align: middle;
            }
        }
        .container {
            padding-left: 5px;
            padding-right: 5px;
            margin-top: 0px;
            margin-bottom: 0px;
        }
        .container > .navbar-header, .container-fluid > .navbar-header, .container > .navbar-collapse, .container-fluid > .navbar-collapse {
            margin-left: -10px;
            margin-right: -10px;
            margin-top: 0px;
            margin-bottom: 0px;
        }
        .row {
            margin-left: 0px;
            margin-right: 0px;
            margin-top: 3px;
            margin-bottom: 3px;
            padding-left: 5px;
            padding-right: 5px;
        }
        .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
            padding-left: 10px;
            padding-right: 10px;
        }
        .scrollable-menu {
            height: auto;
            max-height: 140px;
            overflow: auto;
           -webkit-overflow-scrolling: touch;
        }

        body { padding-bottom: 50px; }
    </style>

</head>

<body>
    <div class="container">
        <div class="pull-left">
            <h6>
                <?php if (strncmp("https://xsvr.duckdns.org", base_url(), 23) == 0) { echo 'StayM'; } else { echo '[DEV] StayM'; } ?>
            </h6>
        </div>

        <div class="pull-right">
            <h6>
                <?php if (isset($_SESSION['usr_id']) > 0) { echo $_SESSION['usr_id'] . ' | ' . '<a href=/auth/logout>로그아웃</a>'; } ?>
            </h6>
        </div>

        <hr color="gray" width=100%>
    </div>

    <?php $this->benchmark->mark('code_start'); ?>
