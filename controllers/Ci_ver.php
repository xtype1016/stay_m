<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    ������� ��� ��Ʈ�ѷ�
*/
class Ci_ver extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo CI_VERSION;
    }
}