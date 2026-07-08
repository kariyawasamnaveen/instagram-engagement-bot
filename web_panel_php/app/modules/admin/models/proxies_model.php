<?php
defined('BASEPATH') or exit('No direct script access allowed');

class proxies_model extends MY_Model
{
    private $tb_main = 'general_proxies';

    public function __construct()
    {
        parent::__construct();
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $this->db->select('*');
            $this->db->from($this->tb_main);
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->db->get_where($this->tb_main, ['ids' => $params['ids']])->row_array();
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        $data = array(
            "proxy"   => post('proxy'),
            "type"    => post('type'),
            "status"  => post('status'),
            "changed" => now(),
        );
        if ($option['task'] == 'add-item') {
            $data['ids']     = ids();
            $data['created'] = now();
            $this->db->insert($this->tb_main, $data);
            return array("status" => "success", "message" => "Added successfully");
        }
        if ($option['task'] == 'edit-item') {
            $this->db->update($this->tb_main, $data, array('ids' => post('ids')));
            return array("status" => "success", "message" => "Updated successfully");
        }
    }
}
