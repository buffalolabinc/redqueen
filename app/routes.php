<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;

$app['user.validation_constraints'] = function(Silex\Application $app) {
    return new Assert\Collection(array(
        'fields' => array(
            'first_name' => new Assert\NotBlank(),
            'last_name' => new Assert\NotBlank(),
            'email' => new Assert\Email(),
        )
    ));
};

$app->post('/api/users', function(Silex\Application $app, Request $request) {
    $content = $request->getContent();

    $user = json_decode($content, true);

    $violations = $app['validator']->validateValue($user, $app['user.validation_constraints'], 'new');

    if (count($violations)) {
        throw new BadRequestHttpException();
    }

    /**
     * @var $dbal Doctrine\DBAL\Connection
     */
    $dbal = $app['db'];

    $user = array_merge($user, array(
        'created_at' => date_create()->format('Y-m-d H:i:s'),
        'updated_at' => date_create()->format('Y-m-d H:i:s'),
    ));

    $dbal->insert('users', $user);

    $response = new JsonResponse();
    $response->setStatusCode(201);
    $response->headers->set('Location', $app['url_generator']->generate('get_user', array('id' => $dbal->lastInsertId())));

    return $response;
})->bind('post_user');

$app->patch('/api/users/{id}', function(Silex\Application $app, Request $request, $id) {
    $content = $request->getContent();

    $user = json_decode($content, true);

    /**
     * @var $constraint \Symfony\Component\Validator\Constraint
     */
    $constraint = $app['user.validation_constraints'];
    $constraint->allowMissingFields = true;

    $violations = $app['validator']->validateValue($user, $constraint, 'edit');

    if (count($violations)) {
        throw new BadRequestHttpException();
    }

    /**
     * @var $dbal Doctrine\DBAL\Connection
     */
    $dbal = $app['db'];

    $user = array_merge($user, array(
        'updated_at' => date_create()->format('Y-m-d H:i:s'),
    ));

    $dbal->update('users', $user, array('id' => $id));

    $query = 'SELECT u.id, u.email, u.first_name, u.last_name FROM users u WHERE id = :id';
    $user = $dbal->fetchAssoc($query, array('id' => $id));

    return $app->json($user);
})->bind('patch_user');

$app->get('/api/users/{id}', function(Silex\Application $app, Request $request, $id) {
    $query = 'SELECT u.id, u.email, u.first_name, u.last_name FROM users u WHERE id = :id';

    /**
     * @var $dbal Doctrine\DBAL\Connection
     */
    $dbal = $app['db'];

    $user = $dbal->fetchAssoc($query, array('id' => $id));

    if (!is_array($user)) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    return $app->json($user);
})->bind('get_user');