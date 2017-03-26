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

    public function addSong(Request $request){
        $query = $request->input('query');
        return $this->add($query);
    }

    public function twilio(Request $request){
        var_dump($request);
    }

    public function add($query){
        $this->authenticateWithSpotify();
        $track = $this->api->search($query, 'track', [
            'limit' => 1,
        ])->tracks->items[0];
        $uri = $track->uri;
        $id = substr($uri, 14);
        $response = $this->api->addUserPlaylistTracks('zachpanz88', $this->playlist, [
            $id
        ]);
        return ($response) ? "Ok" : "Fail";
    }

    public function authenticateWithSpotify(){
        $this->playlist = '6O4HPYVqaiH239Y4fLWP50';
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

        $this->api = new SpotifyWebAPI\SpotifyWebAPI();
        $this->api->setAccessToken($this->accessToken);
    }
}
