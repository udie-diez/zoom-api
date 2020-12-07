<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zoom_meetings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // enable CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        // load zoom config
        $this->config->load("zoom_meetings");
    }

    public function index() {
        $zoom_client_id = $this->config->item("ZOOM_CLIENT_ID");
        $zoom_redirect  = $this->config->item("ZOOM_REDIRECT_CALLBACK");

        // create url zoom authorization
        $zoom_base_url = "https://zoom.us/oauth/authorize";
        $zoom_queries = [
            "response_type" => "code",
            "redirect_uri"  => $zoom_redirect,
            "client_id"     => $zoom_client_id,
        ];
        // set output data for views
        $data["zoom_authorize"] = $zoom_base_url . "?" . http_build_query($zoom_queries);
        // load views with passed data
        $this->load->view("zoom/dashboard", $data);
    }

    public function get_token($authorize_code) {
        if( !$authorize_code && empty($authorize_code) ) die("You are not been authorized");

        $zoom_client_id     = $this->config->item("ZOOM_CLIENT_ID");
        $zoom_client_secret = $this->config->item("ZOOM_CLIENT_SECRET");
        $zoom_redirect      = $this->config->item("ZOOM_REDIRECT_CALLBACK");

        // start access zoom API
        $authorized = base64_encode( $zoom_client_id . ":" . $zoom_client_secret );
        $client = new \GuzzleHttp\Client([
            "base_uri" => "https://zoom.us/oauth/"
        ]);
        $response = $client->request("POST", "token", [
            "headers" => [
                "Authorization" => "Basic " . $authorized
            ],
            "query" => [
                "grant_type"   => "authorization_code",
                "code"         => $authorize_code,
                "redirect_uri" => $zoom_redirect,
            ]
        ]);

        return $this->output
            ->set_status_header( $response->getStatusCode() )
            ->set_content_type( $response->getHeaderLine('content-type') )
            ->set_output( $response->getBody() );
    }

    public function refresh_token($token) {
        if( !$token && empty($token) ) die("Your token is missing");

        $zoom_client_id     = $this->config->item("ZOOM_CLIENT_ID");
        $zoom_client_secret = $this->config->item("ZOOM_CLIENT_SECRET");

        // start access zoom API
        $authorized = base64_encode( $zoom_client_id . ":" . $zoom_client_secret );
        $client = new \GuzzleHttp\Client([
            "base_uri" => "https://zoom.us/oauth/"
        ]);
        $response = $client->request("POST", "token", [
            "headers" => [
                "Authorization" => "Basic " . $authorized
            ],
            "query" => [
                "grant_type"    => "refresh_token",
                "refresh_token" => $token,
            ]
        ]);

        return $this->output
            ->set_status_header( $response->getStatusCode() )
            ->set_content_type( $response->getHeaderLine('content-type') )
            ->set_output( $response->getBody() );
    }

    public function users_info($token) {
        if( !$token && empty($token) ) die("Your token is missing");

        // start access zoom API
        $client = new \GuzzleHttp\Client([
            "base_uri" => "https://api.zoom.us/v2/"
        ]);
        $response = $client->request("GET", "users/me", [
            "headers" => [
                "Authorization" => "Bearer " . $token
            ]
        ]);

        return $this->output
            ->set_status_header( $response->getStatusCode() )
            ->set_content_type( $response->getHeaderLine('content-type') )
            ->set_output( $response->getBody() );
    }

    public function user_meeting_info($token) {
        if( !$token && empty($token) ) die("Your token is missing");

        // start access zoom API
        $client = new \GuzzleHttp\Client([
            "base_uri" => "https://api.zoom.us/v2/"
        ]);
        $response = $client->request("GET", "users/me/meetings", [
            "headers" => [
                "Authorization" => "Bearer " . $token
            ],
            "query" => [
                "type"            => "live", // life, upcoming, scheduled
                "page_size"       => "30",
                "next_page_token" => "",
                "page_number"     => "",
            ]
        ]);

        return $this->output
            ->set_status_header( $response->getStatusCode() )
            ->set_content_type( $response->getHeaderLine('content-type') )
            ->set_output( $response->getBody() );
    }

    public function user_webinar_info($token) {
        if( !$token && empty($token) ) die("Your token is missing");

        // start access zoom API
        $client = new \GuzzleHttp\Client([
            "base_uri" => "https://api.zoom.us/v2/"
        ]);
        $response = $client->request("GET", "users/me/webinars", [
            "headers" => [
                "Authorization" => "Bearer " . $token
            ],
            "query" => [
                "page_size"   => "30",
                "page_number" => "1",
            ]
        ]);

        return $this->output
            ->set_status_header( $response->getStatusCode() )
            ->set_content_type( $response->getHeaderLine('content-type') )
            ->set_output( $response->getBody() );
    }

}