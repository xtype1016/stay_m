<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Todo_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function chk_usr_email($usr_email)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('tbd001i00  a');
        $this->db->where('a.usr_email = ', $usr_email);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            print_r($this->db->error());
            if ($query->num_rows() == 0) {
                info_log("chk_usr_email", "No Data Found!(usr_email=" . $usr_email . ")");
            } else {
                //info_log("chk_usr_email", "SQL ERROR! [" . $this->db->error() . "]");
                info_log("chk_usr_email", "last_query  = [" . $this->db->last_query() . "]");
            }
        }

        print_r($this->db->error());

        return $result;
    }

}