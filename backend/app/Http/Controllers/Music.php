<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SpotifyWebAPI;
use DB;

class Music extends Controller
{
    public function __construct()
    {
        $this->session = new SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );
        $this->api = new SpotifyWebAPI\SpotifyWebAPI();
        $this->playlist = env('SPOTIFY_PLAYLIST');

        // set up authentication
        $this->authenticate();
        $this->updateToken();
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
            ->first()
            ->accessToken;
    }

    protected function authenticate(){
        $token = $this->getToken();
        $this->session->requestAccessToken($token);
        $accessToken = $this->session->getAccessToken();
        $this->api->setAccessToken($accessToken);
    }
}
