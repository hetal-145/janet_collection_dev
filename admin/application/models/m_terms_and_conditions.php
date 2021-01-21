<?php

class M_terms_and_conditions extends CI_Model {

    public function get_term_condition_data() {

        $tc = $this->db->select('value')
                ->where('key', 'terms_and_conditions')
                ->get('setting')
                ->row_array();
        return $tc;
    }

    public function update_term_condition_content($post = '') {

        $update_ary = array();
        if (!empty($post['terms_and_condition'])) {
            $update_ary = [
                'value' => $post['terms_and_condition'],
                'key'=> 'terms_and_conditions'
            ];
        }
        $this->db->set($update_ary)
                ->where('key', 'terms_and_conditions')
                ->update('setting');

        return 'success';
    }

}
