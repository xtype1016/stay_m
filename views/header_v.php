<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />

    <title><?php if (strncmp("https://xsvr.duckdns.org", base_url(), 23) == 0) { echo 'StayM'; } else { echo '[DEV] StayM';} ?></title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="
    sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- Bootstrap -->


    <!-- datetimePicker -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">

    <style type="text/css"> 
        a:link {text-decoration:none; color: #6495ed;}
    </style> 

</head>

<body>
    <div class="container">
        <div class="row py-1 border-bottom fs-6">
            <div class="col text-start">
                <?php if (strncmp("https://xsvr.duckdns.org", base_url(), 23) == 0) { echo 'StayM'; } else { echo '[DEV] StayM'; } ?>
            </div>
            <div class="col-auto text-end">
                <?php if (isset($_SESSION['usr_id']) > 0) { echo $_SESSION['usr_id'] . ' | ' . '<a href=/auth/logout>로그아웃</a>'; } ?>
            </div>
        </div>
    </div>
