<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Flysystem\Exception;
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
        $response = $this->add($request->input('Body'));
        if ($response == "Fail") {
            $output = $this->error();
        }
        else {
            $output = '<?xml version="1.0" encoding="UTF-8"?>
            <Response>
                <Message>Successfully added '.htmlentities($response).' to playlist.</Message>
            </Response>';
        }
        return response($output, 200)->header('Content-Type', 'application/xml');
    }

    public function getList(){
        $this->authenticateWithSpotify();
        $tracks = $this->api->getUserPlaylistTracks('zachpanz88', $this->playlist);
        $json = json_encode($tracks->items);
        return response($json, 200)->header('Content-Type', 'application/json');
    }

    public function upvote(Request $request){
        $this->authenticateWithSpotify();
        $track = intval($request->input('track'));
        $new = ($track>0)? $track-1: 0;
        $this->api->reorderUserPlaylistTracks('zachpanz88', $this->playlist, [
            'range_start' => $track,
            'range_length' => 1,
            'insert_before' => $new,
        ]);
    }

    public function downvote(Request $request){
        $this->authenticateWithSpotify();
        $track = intval($request->input('track'));
        $new = $track+1;
        $this->api->reorderUserPlaylistTracks('zachpanz88', $this->playlist, [
            'range_start' => $track,
            'range_length' => 1,
            'insert_before' => $new,
        ]);
    }

    public function add($query){
        $this->authenticateWithSpotify();
        try {
            $track = $this->api->search($query, 'track', [
                'limit' => 1,
            ])->tracks->items[0];
            $uri = $track->uri;
            $id = substr($uri, 14);
            $name = $track->name;
            $artist = $track->artists[0]->name;
        } catch (\Exception $e){
            return "Fail";
        }
        $response = $this->api->addUserPlaylistTracks('zachpanz88', $this->playlist, [
            $id
        ]);
        return ($response) ? $name." by ".$artist : "Fail";
    }

    public function error(){
        return '<?xml version="1.0" encoding="UTF-8"?>
            <Response>
                <Message>Could not add song!</Message>
            </Response>';
    }

    public function authenticateWithSpotify(){
        $this->playlist = '7ovSBQWZ0FHcKxsH3HXDqX';
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
