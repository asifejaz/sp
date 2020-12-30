<?php


namespace SP\Stats;

use Illuminate\Support\Collection;

class Report
{

    private $collection;


    function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }


    public function getStats()
    {
        return [
            'Average_character_length_posts_per_month' => $this->averageByMonth(),
            'Longest_post_character_length_per_month' => $this->longestByMonth(),
            'Posts_count_per_week' => $this->postsByWeek(),
            'Posts_average_per_user_per_month' => $this->postsByUser()
        ];

    }

    private function groupByMonth(): object
    {
        return $this->collection->sortBy(function ($post) {
            return $post->created_time->month;
        })->groupBy(function ($post) {
            return $post->created_time->month;
        });
    }

    private function groupByWeek(): object
    {
        return $this->collection->sortBy(function ($post) {
            return $post->created_time->week;
        })->groupBy(function ($post) {
            return $post->created_time->week;
        });
    }

    private function averageByMonth(): object
    {
        return $this->groupByMonth()->map(function ($items) {
            return round($items->average(function ($item) {
                return strlen($item->message);
            }), 0);
        });
    }


    private function longestByMonth(): object
    {

        return $this->groupByMonth()->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                $item->content_length = strlen($item->message);
                if (!$carry) {
                    return $item;
                }
                if (strlen($carry->message) > strlen($item->message)) {
                    $carry->content_length = strlen($carry->message);
                    return $carry;
                }
                return $item;

            });
        });
    }

    private function postsByWeek(): object
    {

        return $this->groupByWeek()->map(function ($items) {
            return $items->count();
        });
    }

    private function postsByUser(): object
    {
        return $this->groupByMonth()->map(function ($items) {
            return round($items->count() / $items->groupBy(function ($item) {
                    return $item->from_id;
                })->count(), 0);
        });
    }

}