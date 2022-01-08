<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    // 경고메세지를 경고창으로
    function alert_log($lctn='', $msg='', $url='')
    {
        $CI =& get_instance();

        if (!$msg) $msg = '올바른 방법으로 이용해 주십시오.';

        echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
        echo "<script type='text/javascript'>alert('".$msg."');";

        $lvl = 'error';

        //log_message($level, $message, $php_error = FALSE)
        //$level: error, debug, info
        if (isset($_SESSION['usr_no']) && isset($_SESSION['db_no']))
        {
            log_message($lvl, "[usr_no=" . $_SESSION['usr_no'] . "][db_no=" . $_SESSION['db_no'] . "][" . $lctn . "] " . $msg);
        }
        else if (isset($_SESSION['usr_no']) && !isset($_SESSION['db_no']))
        {
            log_message($lvl, "[usr_no=" . $_SESSION['usr_no'] . "][" . $lctn . "] " . $msg);
        }
        else
        {
            log_message($lvl, "[" . $lctn . "] " . $msg);
        }

        if ($url)
        {
            echo "location.replace('".$url."');";
        }
        else
        {
            echo "history.go(-1);";
        }

        echo "</script>";
        exit;
    }


    function info_log($lctn='', $msg='', $log_lvl='')
    {
        $CI =& get_instance();

        $lvl = 'error';

        if (strcmp($log_lvl, "d") != 0)
        {
            if (isset($_SESSION['usr_no']) && isset($_SESSION['db_no']))
            {
                log_message($lvl, "[usr_no=" . $_SESSION['usr_no'] . "][db_no=" . $_SESSION['db_no'] . "][" . $lctn . "] " . $msg);
            }
            else if (isset($_SESSION['usr_no']))
            {
                log_message($lvl, "[usr_no=" . $_SESSION['usr_no'] . "][" . $lctn . "] " . $msg);
            }
            else
            {
                log_message($lvl, "[" . $lctn . "] " . $msg);
            }
        }
    }


    // 경고메세지 출력후 창을 닫음
    //function alert_close($msg)
    //{
    //    $CI =& get_instance();
    //
    //    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    //    echo "<script type='text/javascript'> alert('".$msg."'); window.close(); </script>";
    //    exit;
    //}
    //
    //// 경고메세지만 출력
    //function alert_only($msg)
    //{
    //    $CI =& get_instance();
    //
    //    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    //    echo "<script type='text/javascript'> alert('".$msg."'); </script>";
    //    exit;
    //}
    //
    //function alert_continue($msg)
    //{
    //    $CI =& get_instance();
    //
    //    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    //    echo "<script type='text/javascript'> alert('".$msg."'); </script>";
    //}
?>
