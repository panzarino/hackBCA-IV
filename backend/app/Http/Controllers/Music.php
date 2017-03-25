<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SpotifyWebAPI;
use DB;
use Audeio\Spotify\Oauth2\Client\Provider\Spotify;
use League\OAuth2\Client\Grant\RefreshToken;


class Music extends Controller
{
    public function __construct()
    {
/*
        $this->session = new SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );
        $this->api = new SpotifyWebAPI\SpotifyWebAPI();
        $this->playlist = env('SPOTIFY_PLAYLIST');

        // set up authentication
        $this->authenticate();
        $this->updateToken();*/

    }

    protected function addSong(Request $request){

    }

    protected function updateToken()
    {
        $refreshToken = $this->session->getRefreshToken();
        $this->session->refreshAccessToken($refreshToken);
        $token = $this->session->getAccessToken();
        $newRefreshToken = $this->session->getRefreshToken();
        DB::table('Music')
            ->truncate();
        DB::table('Music')
            ->insert([
                'accessToken' => $token,
                'refreshToken' => $newRefreshToken
            ]);
    }

    protected function getToken(){
        return DB::table('Music')
            ->select('accessToken')
            ->first();
    }

    protected function authenticate(){
        $token = $this->getToken();
        $this->session->requestAccessToken($token);
        $accessToken = $this->session->getAccessToken();
        $this->api->setAccessToken($accessToken);

        return $this->getToken();
    }

    public function authenticateWithSpotify(){
        $oauthProvider = new Spotify(array(
            'clientId' => getenv('CLIENT_ID'),
            'clientSecret' => getenv('CLIENT_SECRET'),
            'redirectUri' => 'http://localhost:8000'
        ));

        $this->accessToken = $oauthProvider->getAccessToken(new RefreshToken(), array(
            'refresh_token' => getenv('REFRESH')
        ))->accessToken;

        $api = new \Audeio\Spotify\API();
        $api->setAccessToken($this->accessToken);

        return $api->getCurrentUser();

    }
}
