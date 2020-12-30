<?php


use SP\Service\Supermetrics;
use SP\Stats\Report;

require_once __DIR__ . '/vendor/autoload.php';

$posts = (new Supermetrics())->getPosts();

$stats = (new Report($posts))->getStats();

header('Content-Type: application/json');

echo json_encode($stats);
