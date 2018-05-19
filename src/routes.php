<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client;

/**
 * @var Slim\App $app
 */
$app = new \Slim\App;

$app->get('/go/{title}', function (Request $request, Response $response, array $args) {
    $title = $args['title'];

    $client = new Client([
        'base_uri' => 'https://en.wikipedia.org/'
    ]);

    $out = '';

    // loop api calls to pull in data you need unless there is a way to chain concatenate them?

    ## Thumbnail
    $apiResponse = $client->request('GET', '/w/api.php', [
        'query' => [
            'action' => 'query',
            'format' => 'json',
            'prop' => 'pageimages',
            'pithumbsize' => 100,
            'titles' => $title
        ]
    ]);

    if ($apiResponse->getStatusCode() == 200) {
        $decoded_response = json_decode($apiResponse->getBody(), true);
        if (count($decoded_response["query"]["pages"] == 1) && key($decoded_response["query"]["pages"]) != '-1') {
            $response_page = $decoded_response["query"]["pages"];
            $page_thumbnail = $response_page[key($response_page)]['thumbnail']['source'];
            $page_image_filename = $response_page[key($response_page)]['pageimage'];
            $out .= "<img src='$page_thumbnail' /><br />";
            $out .= "<a href='https://commons.wikimedia.org/wiki/File:$page_image_filename'>Image Licensing information here</a><br />";
        } else {
            throw new Exception("Something bad happened");
        }
    }

    ## Extract
    $apiResponse = $client->request('GET', '/w/api.php', [
        // put this param config in config file
        'query' => [
            'action' => 'query',
            'format' => 'json',
            'prop' => 'extracts',
            'exlimit' => 1,
            'exintro' => 1,
            'explaintext' => 1,
            'titles' => $title
        ]
    ]);

    if ($apiResponse->getStatusCode() == 200) {
        $decoded_response = json_decode($apiResponse->getBody(), true);
        if (count($decoded_response["query"]["pages"] == 1) && key($decoded_response["query"]["pages"]) != '-1') {
            $response_page = $decoded_response["query"]["pages"];
            $page_extract = $response_page[key($response_page)]['extract'];
            $out .= $page_extract;
        } else {
            throw new Exception("Something bad happened");
        }
    }


//    $output = $res->getStatusCode(). PHP_EOL;
//    $output .= $res->getHeader('content-type'). PHP_EOL;
//    $output .= $res->getBody();

    $response->getBody()->write($out);
    return $response;
});
