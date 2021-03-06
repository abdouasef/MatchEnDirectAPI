<?php
/**
 * Created by PhpStorm.
 * User: maxime
 * Date: 16/12/2016
 * Time: 13:44
 */
require 'vendor/autoload.php';
require_once "config.php";
use Controller\indexController;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;


$app->get('/', function ($request, $response, $args) {

});

//tout les match d'une équipe
$app->get('/last-score/equipe/{name}', function ($request, $response, $args) {

    // var declaration
    $name = $args['name'];
    $client = new Client();
    $crawler = $client->request('GET', 'http://www.matchendirect.fr/equipe/' . $name . '.html');
    $finalTab = [];

    // tableau contenant le nom des équipe à domicile
    $array_domicile = $crawler->filter('.lm3_eq1')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant les scores
    $array_score = $crawler->filter('.lm3_score')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant le nom des équipe à l'exterieur
    $array_exterieur = $crawler->filter('.lm3_eq2')->each(function ($node) {
        return $node->text();
    });

    // on crée le tableau de réponse
    foreach ($array_domicile as $key => $item) {
        $finalTab[$key] = array(
            'id' => ($key + 1),
            'equipe_domicile' => $item,
            'score' => $array_score[$key],
            'equipe_exterieur' => $array_exterieur[$key]
        );
    }

    // return results in JSON
    return json_encode($finalTab);
});

// dernière journée de championnat
$app->get('/pays/{name}/championnat/{name-champ}', function ($request, $response, $args) {

    // var declaration
    $name = $args['name'];
    $champ = $args['name-champ'];
    $client = new Client();
    $crawler = $client->request('GET', 'http://www.matchendirect.fr/' . $name . '/' . $champ . '/');
    $finalTab = [];

    // tableau contenant le nom des équipe à domicile
    $array_domicile = $crawler->filter('.lm3_eq1')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant les scores
    $array_score = $crawler->filter('.lm3_score')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant le nom des équipe à l'exterieur
    $array_exterieur = $crawler->filter('.lm3_eq2')->each(function ($node) {
        return $node->text();
    });

    // on crée le tableau de réponse
    foreach ($array_domicile as $key => $item) {
        $finalTab[$key] = array(
            'id' => ($key + 1),
            'equipe_domicile' => $item,
            'score' => $array_score[$key],
            'equipe_exterieur' => $array_exterieur[$key]
        );
    }

    // return results in JSON
    return json_encode($finalTab);
});

// journée de championnat
$app->get('/pays/{name}/championnat/{name-champ}/date/{annee-jour}', function ($request, $response, $args) {

    // var declaration
    $id = 0;
    $name = $args['name'];
    $champ = $args['name-champ'];
    $date = $args['annee-jour'];
    $tab = [];
    $client = new Client();
    $crawler = $client->request('GET', 'http://www.matchendirect.fr/' . $name . '/' . $champ . '/' . $date . '/');

    $finalTab = [];

    // tableau contenant le nom des équipe à domicile
    $array_domicile = $crawler->filter('.lm3_eq1')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant les scores
    $array_score = $crawler->filter('.lm3_score')->each(function ($node) {
        return $node->text();
    });

    // tableau contenant le nom des équipe à l'exterieur
    $array_exterieur = $crawler->filter('.lm3_eq2')->each(function ($node) {
        return $node->text();
    });

    // on crée le tableau de réponse
    foreach ($array_domicile as $key => $item) {
        $finalTab[$key] = array(
            'id' => ($key + 1),
            'equipe_domicile' => $item,
            'score' => $array_score[$key],
            'equipe_exterieur' => $array_exterieur[$key]
        );
    }

    // return results in JSON
    return json_encode($finalTab);

});

//score d'un match
$app->get('/live-score/equipe/{name-dom}-{name-ext}', function ($request, $response, $args) {

    // var declaration
    $domicile = $args['name-dom'];
    $exterieur = $args['name-ext'];
    $client = new Client();
    $replace = ["\t","\n"];
    $crawler = $client->request('GET',
        'http://www.matchendirect.fr/live-score/' . $domicile . '-' . $exterieur . '.html');
    $finalTab = [];

    // get information from page
    $equipe = $crawler->filter('.team')->each(function ($node) {
        return $node->text();
    });
    $score = $crawler->filter('.score')->each(function ($node) {
        return $node->text();
    });
    $cote = $crawler->filter('.tableau_comparateur td')->each(function ($node) {
        return $node->text();
    });

    // construct tab results
    $finalTab[] = array('equipe_domicile' => str_replace($replace, '', $equipe[0]), 'score_domicile' => $score[0], 'score_exterieur' => $score[1], 'equipe_exterieur' => str_replace($replace, '', $equipe[1]), 'cote_1' => $cote[3], 'cote_n' => $cote[4], 'cote_2' => $cote[5]);

    // return results in JSON
    return json_encode($finalTab);
});

// classement
$app->get('/classement/pays/{name}/championnat/{name-champ}', function ($request, $response, $args) {

    // var declaration
    $name = $args['name'];
    $champ = $args['name-champ'];
    $tabEquipe = [];
    $tabPts = [];
    $client = new Client();
    $crawler = $client->request('GET',
        'http://www.matchendirect.fr/classement-foot/' . $name . '/classement-' . $champ . '.html');
    $finalTab = [];

    // get information from page
    $tabEquipe = $crawler->filter('.equipe')->each(function ($node) {
        return $node->text();
    });
    $tabPts = $crawler->filter('.tab-content td strong')->each(function ($node) {
        return $node->text();
    });

    // construct tab results
    foreach ($tabEquipe as $key => $item) {
        $finalTab[$key] = array('place' => ($key + 1), 'equipe' => substr($item, 1), 'points' => (int)$tabPts[$key]);
    }

    // return results in JSON
    return json_encode($finalTab);
});

$app->run();