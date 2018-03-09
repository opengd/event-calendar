<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
//use App\Repositories\UserRepository;
use Illuminate\Database\QueryException;

use React\EventLoop\Factory;
use React\ChildProcess\Process;

use React\HttpClient\Client;
use React\HttpClient\Response;

use Clue\React\Buzz\Browser;

class EventsComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $cords;

    /**
     * Create a new profile composer.
     *
     * @return void
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        //$this->events = $events;
        $this->cords = [ 'long' => 55.605833, 'lat' => 13.0025];
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

        //$places = DB::table('places')->get();

        $all_places = DB::table('places')
                ->selectRaw('place_id id, name, latitude, longitude')
                ->get();

        $all_places_other = DB::table('places')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('events')
                  ->whereRaw('events.place_id = places.place_id');
        })
        ->get();

        //$all_places = array();
        echo count($all_places) . ' ';
        echo count($all_places_other) . ' ';
        if(count($all_places) < 1) {

            $url = ''; //facebook graph place search url

            $all_places = array();
            $all_places = $this->get_all_places($url, $all_places);

            echo count($all_places) . ' ';

            foreach ($all_places as $key => $place) {
                if(isset($place['id'])) {
                    DB::table('places')->insert([
                        'place_id' => $place['id'], 
                        'name' => $place['name'], 
                        'latitude' => $place['location']['latitude'],
                        'longitude' => $place['location']['longitude']  
                        ]
                    );
                }
            }

            echo count($all_places) . ' ';

            $all_places = DB::table('places')
            ->selectRaw('place_id id, name, latitude, longitude')
            ->get();
        }

        //echo "count all_places: " . count($all_places) . "<br>";
        //return json_encode($all_places);

        echo count($all_places) . ' ';

        $all_events = DB::table('events')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('places')
                  ->whereRaw('places.place_id = events.place_id');
        })
        ->get();

        
        $events_and_places = DB::table('events')
            ->join('places', 'events.place_id', '=', 'places.place_id')
            ->select('places.place_id as place_id', 
            'places.name as place_name', 
            'places.latitude', 
            'places.longitude',
            'events.name as event_name',
            'events.event_id')
            ->get();
        
        echo 'events_and_places: ' . count($events_and_places) . ' ';
        
        echo count($all_events) . ' ';

        if(count($all_events) < 1) {
            $all_events = $this->get_all_events($all_places);

            foreach ($all_events as $key => $events) {
                foreach ($events['events'] as $key => $event) {
                    try {
                    DB::table('events')->insert([
                        'event_id' => $event['id'],
                        'place_id' => $events['place']->id, 
                        'name' => $event['name'], 
                        ]
                    );
                    } catch(\Illuminate\Database\QueryException $e) {

                    }
                }
            }
        } else {

        }

        $view->with( ['long' => $this->cords['long'], 
            'lat' => $this->cords['lat'], 
            'cords' => [$this->cords['long'], $this->cords['lat']],
            'places' => $all_places,
            'events' => $all_events,
            'events_and_places' => $events_and_places,
        ]);
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

    function get_all_events($places) {

        $loop = Factory::create();
        $events = array();

        $temp_places = array();

        foreach ($places as $key => $value2) {
            //echo gettype($value2);
            $temp_events = array();

            if(gettype($value2) == 'array' || gettype($value2) == 'object') {
                if(isset($value2->name)) {
                    //echo('name: ' . $value2->name . '<br>');
                }

                if(isset($value2->id)) {
                    //echo('id: ' . $value2['id'] . '<br>');
                    $url = ''; //facebook graph events url
                    
                    $events[$value2->id] = [
                        'browser' => new Browser($loop),
                        'events' => array(), 
                        'place' => $value2,
                    ];

                    //$events[$value2['id']]['browser'] = new Browser($loop); 
                    
                    //$events[$value2['id']]['data'] = array();

                    $p = &$events[$value2->id]['events'];

                    $events[$value2->id]['browser']->get($url)
                        ->then(function(\Psr\Http\Message\ResponseInterface $response) use ( &$p ) {
                            $json_response = json_decode($response->getBody(), true);

                            $p = array_merge($p, $json_response["data"]);

                            if(isset($json_response["paging"]) && isset($json_response["paging"]["next"]))
                                $p = $this->get_all_places($json_response["paging"]["next"], $p);
                            
                            //echo "count data: " . count($p) . "<br>";
                            
                            //echo count($p);
                            //var_dump($json_response);
                    });

                    //echo count($p);

                    /*
                    if( count($events[$value2['id']]['data']) > 0 ) {
                        $temp_places[$value2['id']] = [
                            'place' => $value2,
                            'events' => $events[$value2['id']]['data']
                        ];
                        echo "ass";
                    }
                    */
                    //$events = $this->get_data($url, array());

                    //echo 'events: ' . count($events) . '<br>';                   
                }
            }
        }

        $loop->run();
        
        foreach ($events as $key => $value) {
            //echo $key . ' count events: ' . count($value['events']) .  '<br>';
            //var_dump($value['data']);
            //echo '<br>';

            if( count($value['events']) > 0) {
                $temp_places[count($temp_places)] = [
                    'place' => $value['place'],
                    'events' => $value['events']
                ];
            }
        }
        
        //var_dump($events);

        return $temp_places;
    }
}