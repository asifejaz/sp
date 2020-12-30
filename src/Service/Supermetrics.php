<?php


namespace SP\Service;

use Ixudra\Curl\CurlService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Supermetrics
{


    private $sl_token = false;
    private $client_id = 'ju16a6m81mhid5ue1z3v2g0uh';
    private $name = 'Asif Ejaz';
    private $email = 'asifejaz@msn.com';
    private $endpoint = 'https://api.supermetrics.com/assignment';
    private $maxPages = 10;
    private $collection;
    private $posts = [];

    public function getPosts($page = 1):object
    {

        if (!$this->sl_token) {
            $this->getToken();
        }

        $request = $this->initCurl();

        try {
            $response = $request->to($this->endpoint . '/posts')->withData(['page' => $page, 'sl_token' => $this->sl_token])->asJsonResponse()->get();

            if (isset($response->data->posts)) {

                $this->posts = array_merge($this->posts, $response->data->posts);

                if($page+1<=$this->maxPages) {
                    $this->getPosts($page+1);
                }

            }

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


        $this->collection = collect($this->posts)->each(function ($post) {
            $post->created_time = Carbon::parse($post->created_time);
        });

        return $this->collection;

    }

    public function getToken():bool
    {

        $request = $this->initCurl();
        $response = $request->to($this->endpoint . '/register')->withData(['client_id' => $this->client_id, 'name' => $this->name, 'email' => $this->email])->asJsonResponse()->post();

        if (isset($response->data->sl_token)) {
            $this->sl_token = $response->data->sl_token;
            return true;
        }
        return false;

    }

    private function initCurl()
    {
        return new CurlService();
    }


}

