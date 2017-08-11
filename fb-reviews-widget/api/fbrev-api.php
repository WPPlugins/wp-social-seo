<?php

require_once(dirname(__FILE__) . '/fbrev-url.php');

if (!extension_loaded('json')) {
    require_once(dirname(__FILE__) . '/json.php');
    function fbrev_json_decode($data) {
        $json = new JSON;
        return $json->unserialize($data);
    }
} else {
    function fbrev_json_decode($data) {
        return json_decode($data);
    }
}

class FacebookReviewsAPI {

    function __construct() {
        $this->last_error = null;
    }

    function reviews($page_id, $params=array()) {
        $reviews = $this->call($page_id, 'ratings', $params);
        if (!$reviews) {
            $error = $this->last_error;
            return compact('error');
        }
        return $reviews->data;
    }

    function call($page_id, $method, $args=array(), $post=false) {
        $url = FBREV_GRAPH_API . $page_id . '/' . $method;

        foreach ($args as $key=>$value) {
            if (empty($value)) unset($args[$key]);
        }

        if (!$post) {
            $url .= '?' . fbrev_get_query_string($args);
            $args = null;
        }

        if (!($response = fbrev_urlopen($url, $args)) || !$response['code']) {
            $this->last_error = 'FACEBOOK_COULDNT_CONNECT';
            return false;
        }

        if ($response['code'] != 200) {
            if ($response['code'] == 500) {
                if (!empty($response['headers']['X-Error-ID'])) {
                    $this->last_error = 'Returned a bad response (HTTP '.$response['code'].', ReferenceID: '.$response['headers']['X-Error-ID'].')';
                    return false;
                }
            } elseif ($response['code'] == 400) {
                $data = fbrev_json_decode($response['data']);
                if ($data && $data->message) {
                    $this->last_error = $data->message;
                } else {
                    $this->last_error = "Returned a bad response (HTTP ".$response['code'].")";
                }
                return false;
            }
            $this->last_error = "Returned a bad response (HTTP ".$response['code'].")";
            return false;
        }

        $data = fbrev_json_decode($response['data']);

        if (!$data) {
            $this->last_error = 'No valid JSON content returned from Facebook';
            return false;
        }
        $this->last_error = null;
        return $data;
    }
}

?>
