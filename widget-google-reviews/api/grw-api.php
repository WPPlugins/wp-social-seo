<?php

require_once(dirname(__FILE__) . '/grw-url.php');

if (!extension_loaded('json')) {
    require_once(dirname(__FILE__) . '/json.php');
    function grw_json_decode($data) {
        $json = new JSON;
        return $json->unserialize($data);
    }
} else {
    function grw_json_decode($data) {
        return json_decode($data);
    }
}

class GoogleReviewsWidgetAPI {

    function __construct() {
        $this->last_error = null;
    }

    function textsearch($params=array()) {
        $date = $this->call('textsearch', $params);
        if (!$date) {
            $error = $this->last_error;
            return compact('error');
        }

        $places = array();
        if ($date->results) {
            $google_places = $date->results;
            foreach ($google_places as $place) {
                array_push($places, array(
                    'icon' => $place->icon,
                    'name' => $place->name,
                    'rating' => isset($place->rating) ? $place->rating : null,
                    'formatted_address' => $place->formatted_address,
                    'place_id' => $place->place_id,
                    'reference' => $place->reference,
                    'url' => isset($place->url) ? $place->url : null,
                    'website' => isset($place->website) ? $place->website : null
                ));
            }
        }
        return compact('places');
    }

    function details($params=array()) {
        $date = $this->call('details', $params);
        if (!$date) {
            $error = $this->last_error;
            return compact('error');
        }

        $reviews = array();
        if ($date->result) {
            $result = $date->result;
            if ($result->reviews) {
                $google_reviews = $result->reviews;
                foreach ($google_reviews as $review) {
                    array_push($reviews, array(
                        'author_name' => $review->author_name,
                        'author_url' => $review->author_url,
                        'profile_photo_url' => isset($review->profile_photo_url) ? $review->profile_photo_url : null,
                        'rating' => $review->rating,
                        'text' => $review->text,
                        'time' => $review->time,
                        'language' => $review->language
                    ));
                }
            }
            $place = array(
                'icon' => $result->icon,
                'name' => $result->name,
                'rating' => isset($result->rating) ? $result->rating : null,
                'formatted_address' => $result->formatted_address,
                'place_id' => $result->place_id,
                'reference' => $result->reference,
                'url' => $result->url,
                'website' => isset($result->website) ? $result->website : null,
                'reviews' => $reviews
            );
        }
        return compact('place');
    }

    function call($method, $args=array(), $post=false) {
        $url = GRW_GOOGLE_PLACE_API . $method . '/json';

        foreach ($args as $key=>$value) {
            if (empty($value)) unset($args[$key]);
        }

        if (!$post) {
            $url .= '?' . grw_get_query_string($args);
            $args = null;
        }

        if (!($response = grw_urlopen($url, $args)) || !$response['code']) {
            $this->last_error = 'GOOGLE_COULDNT_CONNECT';
            return false;
        }

        if ($response['code'] != 200) {
            if ($response['code'] == 500) {
                if (!empty($response['headers']['X-Error-ID'])) {
                    $this->last_error = 'Returned a bad response (HTTP '.$response['code'].', ReferenceID: '.$response['headers']['X-Error-ID'].')';
                    return false;
                }
            } elseif ($response['code'] == 400) {
                $data = grw_json_decode($response['data']);
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

        $data = grw_json_decode($response['data']);

        if (!$data) {
            $this->last_error = 'No valid JSON content returned from Google';
            return false;
        }
        if ($data->status == 'OVER_QUERY_LIMIT') {
            $this->last_error = $data->status;
            return false;
        }
        $this->last_error = null;
        return $data;
    }
}

?>
