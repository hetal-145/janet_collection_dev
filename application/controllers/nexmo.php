<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Nexmo extends CI_Controller {

    public $api_key = '';
    public $api_secret = '';
    public $from = '';

    public function __construct($config = array()) {
        $ci = & get_instance();
        echo "hii2"; exit;
        $global_config = $ci->load->config('nexmo', true);

        $this->api_key = $global_config['api_key'];
        $this->api_secret = $global_config['api_secret'];
        $this->from = $global_config['from'];

        if ($config) {
            if (isset($config['api_key']) && $config['api_key']) {
                $this->api_key = $config['api_key'];
            }
            if (isset($config['api_secret']) && $config['api_secret']) {
                $this->api_secret = $config['api_secret'];
            }
            if (isset($config['from']) && $config['from']) {
                $this->from = $config['from'];
            }
        }


        if (!$this->api_key) {
            show_error('NEXMO: Needed API Key');
        } else if (!$this->api_secret) {
            show_error('NEXMO: Needed API Secret');
        }
    }

    public function sms($to, $sms) {
        
        if (!$this->from) {
            show_error('NEXMO: Needed From');
        }
        $ci = & get_instance();
        //log_message('error','sms----->'.$sms);
        //$to = '035045340503405340503405';
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "http://ultramsg.com/api.php?send_sms&username=" . SMS_USER . "&password=" . SMS_PASSWORD . "&numbers=$to&sender=" . SMS_SENDER . "&message=".rawurlencode($sms));
        curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json");
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api_key=" . $this->api_key . "&api_secret=" . $this->api_secret . "&from=" . $this->from . "&to=$to&text=$sms&type=unicode");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp) {
            $resp = json_decode($resp, true);
            //print_r($resp); exit;
            //log_message('error', $this->uri->uri_string() . ' /// sms ---> ' . json_encode($resp));
            $delivery_status = [
                'message_id' => $resp,
                'delivery_status' => 'success',
                'phone' => $to
            ];
            if (strpos($sms, 'Your verification code is') !== FALSE) {
                $delivery_status['otp'] = (int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $sms);
            }
            /*$ci->db
                    ->insert('delivery_receipt', $delivery_status);
            $id = $ci->db->insert_id();
            return $ci->db->insert_id();*/
            //print_r($resp);

            if ($resp && $resp['messages'][0]['status'] == '0') {
                $delivery_status = [
                    'message_id' => $resp['messages'][0]['message-id'],
                    'delivery_status' => 'pending',
                    'phone' => $to
                ];
                if (strpos($sms, 'verification code is') !== FALSE) {
                    $delivery_status['otp'] = (int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $sms);
                }
                $ci->db
                        ->insert('delivery_receipt', $delivery_status);
                return $ci->db->insert_id();
            } else {
                $delivery_status = [
                    'message_id' => '',
                    'delivery_status' => 'pending',
                    'phone' => $to
                ];
                if (strpos($sms, 'verification code is') !== FALSE) {
                    $delivery_status['otp'] = (int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $sms);
                }
                $ci->db
                        ->insert('delivery_receipt', $delivery_status);
                return $ci->db->insert_id();
            }
        }
    }
    
    public function send_password($to, $sms) {
        echo "hi"; exit;
        if (!$this->from) {
            show_error('NEXMO: Needed From');
        }
        $ci = & get_instance();
        //log_message('error','sms----->'.$sms);
        //$to = '035045340503405340503405';
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "http://ultramsg.com/api.php?send_sms&username=" . SMS_USER . "&password=" . SMS_PASSWORD . "&numbers=$to&sender=" . SMS_SENDER . "&message=".rawurlencode($sms));
        curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json");
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api_key=" . $this->api_key . "&api_secret=" . $this->api_secret . "&from=" . $this->from . "&to=$to&text=$sms&type=unicode");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp) {
            $resp = json_decode($resp, true);
            //print_r($resp); exit;
            //log_message('error', json_encode($resp));
            return 'success';
        }
    }

    public function call($to, $otp) {
        $ci = & get_instance();


        $voice_txt = '<break time="1s"/>Your verification code is ';
        $len = strlen($otp);
        for ($i = 0; $i < $len; $i++) {
            $voice_txt .= '<break time="500ms"/>' . substr($otp, $i, 1);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.nexmo.com/tts/json");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api_key=" . $this->api_key . "&api_secret=" . $this->api_secret . "&to=$to&text=$voice_txt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp) {
            log_message('error', 'error----->' . $resp);
            $resp = json_decode($resp, TRUE);
            if (isset($resp['status']) && $resp['status'] == '0') {
                $delivery_status = [
                    'message_id' => $resp['call_id'],
                    'delivery_status' => $resp['error_text'],
                    'phone' => $resp['to'],
                    'otp' => $otp
                ];
                $ci->db
                        ->insert('delivery_receipt', $delivery_status);
                return $ci->db->insert_id();
            } else {
                $delivery_status = [
                    'message_id' => '',
                    'delivery_status' => 'pending',
                    'phone' => $to,
                    'otp' => $otp
                ];
                $ci->db
                        ->insert('delivery_receipt', $delivery_status);
                return $ci->db->insert_id();
            }
        }
    }

    public function confirm_otp($delivery_receipt_id, $otp) {
        $ci = & get_instance();
        $valid = $ci->db
                        ->where('delivery_receipt_id', $delivery_receipt_id)
                        ->where('otp', trim($otp))
                        ->get('delivery_receipt')->row_array();
        if ($valid) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delivery($msisdn, $network, $messageId) {
        
        if (!$this->from) {
            show_error('NEXMO: Needed From');
        }
        $ci = & get_instance();
        //log_message('error','sms----->'.$sms);
        //$to = '035045340503405340503405';
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "http://ultramsg.com/api.php?send_sms&username=" . SMS_USER . "&password=" . SMS_PASSWORD . "&numbers=$to&sender=" . SMS_SENDER . "&message=".rawurlencode($sms));
        curl_setopt($ch, CURLOPT_URL, "http://predrinkdelivery.com/webhooks/delivery-receipt ");
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "msisdn=" . $msisdn . "&to=" . $this->from . "&network-code=$network&messageId=$messageId");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp) {
            $resp = json_decode($resp, true);
            print_r($resp); exit;
            //log_message('error', $this->uri->uri_string() . ' /// sms ---> ' . json_encode($resp));
            
        }
    }

}
