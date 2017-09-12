<?php

use BLInc\Validator\Constraints\Unique;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

$app['schedule.validation_constraints'] = function (Silex\Application $app) {
    return new Assert\Collection(array(
      'fields' => array(
        'name' => new Assert\NotBlank(),
        'mon' => new Assert\Type(array('type' => 'boolean')),
        'tue' => new Assert\Type(array('type' => 'boolean')),
        'wed' => new Assert\Type(array('type' => 'boolean')),
        'thu' => new Assert\Type(array('type' => 'boolean')),
        'fri' => new Assert\Type(array('type' => 'boolean')),
        'sat' => new Assert\Type(array('type' => 'boolean')),
        'sun' => new Assert\Type(array('type' => 'boolean')),
        'startTime' => new Assert\Time(),
        'endTime' => new Assert\Time(),
      )
    ));
};

$app['card.validation_constraints'] = function (Silex\Application $app) {
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
        'schedules' => [new Assert\Count(['min' => 1]), new Assert\All([
          new Assert\Collection([
            'fields' => [
              'id' => [
                new Assert\NotBlank(),
                  // Valid Schedule Id
              ]
            ]
          ])
        ])]
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

    $app['card.manager']->update($id, $card);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_card', array('id' => $id)));

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

    return $app->json(['items' => $cards, 'count' => count($cards));
})->bind('get_cards');

$app->get('/api/logs', function(Silex\Application $app, Request $request) {
    $logs = $app['log.manager']->findAll();

    return $app->json(['items' => $logs, 'count' => count($logs));
})->bind('get_logs');

$app->match('/api/schedules', function() {
    $response = new JsonResponse();
    $response->headers->set('Access-Control-Allow-Methods', 'POST,GET,OPTIONS');

    return $response;
})->method('OPTIONS');

$app->match('/api/schedules/{id}', function() {
    $response = new JsonResponse();
    $response->headers->set('Access-Control-Allow-Methods', 'PUT,GET,OPTIONS');

    return $response;
})->method('OPTIONS');

$app->put('/api/schedules/{id}', function(Silex\Application $app, Request $request, $id) {
    $content = $request->getContent();

    $schedule = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($schedule)) {
        return $app->json(array(array('message' => 'Request must contain a hash or properties.')), 400);
    }

    $constraints = $app['schedule.validation_constraints'];
    $constraints->allowMissingFields = true;

    $violations = $app['validator']->validateValue($schedule, $constraints, 'edit');

    if (count($violations)) {
        return new Response(
          $app['serializer']->serialize($violations, 'json'),
          400,
          array('Content-Type' => 'application/json')
        );
    }

    $app['schedule.manager']->update($id, $schedule);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_schedule', array('id' => $id)));

    return $response;
})->bind('put_schedule');

$app->post('/api/schedules', function(Silex\Application $app, Request $request) {
    $content = $request->getContent();

    $schedule = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($schedule)) {
        return $app->json(array(array('message' => 'Request must contain a hash or properties.')), 400);
    }

    $violations = $app['validator']->validateValue($schedule, $app['schedule.validation_constraints'], 'new');

    if (count($violations)) {
        return new Response(
          $app['serializer']->serialize($violations, 'json'),
          400,
          array('Content-Type' => 'application/json')
        );
    }

    $schedule_id = $app['schedule.manager']->create($schedule);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_schedule', array('id' => $schedule_id)));

    return $response;
})->bind('post_schedule');

$app->get('/api/schedules/{id}', function(Silex\Application $app, Request $request, $id) {
    $schedule = $app['schedule.manager']->find($id);

    if (!is_array($schedule)) {
        throw new NotFoundHttpException();
    }

    return $app->json($schedule);
})->bind('get_schedule');

$app->get('/api/schedules', function(Silex\Application $app, Request $request) {
    $schedules = $app['schedule.manager']->findAll();

    return $app->json(['items' => $schedules, 'count' => count($schedules));
})->bind('get_schedules');
