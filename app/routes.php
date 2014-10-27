<?php

use BLInc\Validator\Constraints\Unique;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

$app['card.validation_constraints'] = function(Silex\Application $app) {
    return new Assert\Collection(array(
        'fields' => array(
            'name' => new Assert\NotBlank(),
            'code' => array(
                new Assert\NotBlank(),
                new Unique(array('table' => 'cards', 'column' => 'code')),
            ),
            'pin' => array(
                new Assert\Type('digit'),
                new Assert\Length(array('min' => 3)),
            ),
            'isActive' => new Assert\Type(array('type' => 'boolean')),
        )
    ));
};

$app->match('/api/cards', function() {
    $response = new JsonResponse();
    $response->headers->set('Access-Control-Allow-Methods', 'POST,GET,OPTIONS');

    return $response;
})->method('OPTIONS');

$app->match('/api/cards/{id}', function() {
    $response = new JsonResponse();
    $response->headers->set('Access-Control-Allow-Methods', 'PUT,GET,OPTIONS');

    return $response;
})->method('OPTIONS');

$app->put('/api/cards/{id}', function(Silex\Application $app, Request $request, $id) {
    $content = $request->getContent();

    $card = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($card)) {
        return $app->json(array(array('message' => 'Request must contain a hash or properties.')), 400);
    }

    $constraints = $app['card.validation_constraints'];
    $constraints->allowMissingFields = true;

    $violations = $app['validator']->validateValue($card, $constraints, 'edit');

    if (count($violations)) {
        return new Response(
            $app['serializer']->serialize($violations, 'json'),
            400,
            array('Content-Type' => 'application/json')
        );
    }

    $card_id = $app['card.manager']->update($id, $card);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_card', array('id' => $card_id)));

    return $response;
})->bind('put_card');

$app->post('/api/cards', function(Silex\Application $app, Request $request) {
    $content = $request->getContent();

    $card = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($card)) {
        return $app->json(array(array('message' => 'Request must contain a hash or properties.')), 400);
    }

    $violations = $app['validator']->validateValue($card, $app['card.validation_constraints'], 'new');

    if (count($violations)) {
        return new Response(
            $app['serializer']->serialize($violations, 'json'),
            400,
            array('Content-Type' => 'application/json')
        );
    }

    $card_id = $app['card.manager']->create($card);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_card', array('id' => $card_id)));

    return $response;
})->bind('post_card');

$app->get('/api/cards/{id}', function(Silex\Application $app, Request $request, $id) {
    $card = $app['card.manager']->find($id);

    if (!is_array($card)) {
        throw new NotFoundHttpException();
    }

    return $app->json($card);
})->bind('get_card');

$app->get('/api/cards', function(Silex\Application $app, Request $request) {
    $cards = $app['card.manager']->findAll();

    return $app->json($cards);
})->bind('get_cards');

$app->get('/api/logs', function(Silex\Application $app, Request $request) {
    $logs = $app['log.manager']->findAll();

    return $app->json($logs);
})->bind('get_logs');
