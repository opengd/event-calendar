<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use React\EventLoop\Factory;
use React\ChildProcess\Process;

use React\HttpClient\Client;
use React\HttpClient\Response;

use Clue\React\Buzz\Browser;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $url = 'https://graph.facebook.com/v2.12/search?type=place&center=55.605833,13.0025&distance=10000&fields=name,location&limit=150&access_token=130067932002|PlUgA7siv62V3l2lofPGZWKtJGE';

        $all_places = array();
        $all_places = $this->get_all_places($url, $all_places);
        //echo "count all_places: " . count($all_places) . "<br>";

        return json_encode($all_places);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function get_all_places($url, $data) {
        $loop = Factory::create();

        $client = new Browser($loop);

        $client->get($url)
            ->then(function(\Psr\Http\Message\ResponseInterface $response) use (&$data) {
                $json_response = json_decode($response->getBody(), true);

                //echo "count data: " . count($json_response["data"]) . "<br>";
                $data = array_merge($data, $json_response["data"]);

                if(isset($json_response["paging"]) && isset($json_response["paging"]["next"]))
                    $data = $this->get_all_places($json_response["paging"]["next"], $data);
            });
                
        $loop->run();
        return $data;
    }
}
