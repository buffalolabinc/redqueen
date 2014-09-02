<?php

use BLInc\Validator\Constraints\Unique;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Validator\Constraints as Assert;

$app['card.validation_constraints'] = function(Silex\Application $app) {
    return new Assert\Collection(array(
        'fields' => array(
            'code' => new Assert\NotBlank(),
            'pin' => new Assert\NotBlank(),
        )
    ));
};

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

$app->match('/api/cards/{id}', function(Silex\Application $app, Request $request, $id) {
    $links = $request->attributes->get('_links', array());

    /**
     * @var $url_matcher UrlMatcher
     */
    $url_matcher = new UrlMatcher($app['routes'], $app['request_context']);
    $url_matcher->getContext()->setMethod('GET');

    foreach ($links as $link) {
        $route = $url_matcher->match($link['url']);

        $app['card.manager']->update($id, array('user_id' => $route['id']));
    }

    $card = $app['card.manager']->find($id);

    if (!is_array($card)) {
        throw new NotFoundHttpException();
    }

    return $app->json($card);
})->method('LINK');

$app['user.validation_constraints'] = function(Silex\Application $app) {
    return new Assert\Collection(array(
        'fields' => array(
            'first_name' => new Assert\NotBlank(),
            'last_name' => new Assert\NotBlank(),
            'email' => array(new Assert\Email(), new Unique(array('table' => 'users', 'column' => 'email'))),
        )
    ));
};

$app->post('/api/users', function(Silex\Application $app, Request $request) {
    $content = $request->getContent();

    $user = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($user)) {
        return $app->json(array(array('message' => 'Request must contain a hash of properties.')), 400);
    }

    $violations = $app['validator']->validateValue($user, $app['user.validation_constraints'], 'new');

    if (count($violations)) {
        return new Response(
            $app['serializer']->serialize($violations, 'json'),
            400,
            array('Content-Type' => 'application/json')
        );
    }

    $user_id = $app['user.manager']->create($user);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_user', array('id' => $user_id)));

    return $response;
})->bind('post_user');

$app->patch('/api/users/{id}', function(Silex\Application $app, Request $request, $id) {
    $content = $request->getContent();

    $user = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $app->json(array(array('message' => 'Failed to parse request.')), 400);
    }

    if (!is_array($user)) {
        return $app->json(array(array('message' => 'Request must contain a hash of properties.')), 400);
    }

    /**
     * @var $constraint \Symfony\Component\Validator\Constraint
     */
    $constraint = $app['user.validation_constraints'];
    $constraint->allowMissingFields = true;

    $violations = $app['validator']->validateValue($user, $constraint, 'edit');

    if (count($violations)) {
        return new Response(
            $app['serializer']->serialize($violations, 'json'),
            400,
            array('Content-Type' => 'application/json')
        );
    }

    $app['user.manager']->update($id, $user);

    $user = $app['user.manager']->find($id);

    return $app->json($user);
})->bind('patch_user');

$app->get('/api/users/{id}', function(Silex\Application $app, Request $request, $id) {
    $user = $app['user.manager']->find($id);

    if (!is_array($user)) {
        throw new NotFoundHttpException();
    }

    return $app->json($user);
})->bind('get_user');

$app->get('/api/users', function(Silex\Application $app, Request $request) {
    $users = $app['user.manager']->findAll();

    return $app->json($users);
})->bind('get_users');