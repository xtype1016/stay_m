<?php

class Googleapi_ver extends CI_Controller
{

    public function index()
    {
        $this->load->library('googleapi');
        echo $this->googleapi->getLibraryVersion();
    }
    
}