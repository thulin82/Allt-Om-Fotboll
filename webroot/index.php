<?php 
/**
 * This is a Anax frontcontroller.
 *
 */

// Get environment & autoloader.
require __DIR__.'/config_with_app.php';

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
$app->theme->configure(ANAX_APP_PATH . 'config/theme_project.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_project.php');

$di->set('CommentsController', function () use ($di) {
    $controller = new Anax\Questions\CommentsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function () use ($di) {
    $controller = new Anax\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('AnswersController', function () use ($di) {
    $controller = new Anax\Questions\AnswersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function () use ($di) {
    $controller = new Anax\Questions\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function () use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('db', function () {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_sqlite.php');
    $db->connect();
    return $db;
});

$di->setShared('form', function () {
    $form = new \Mos\HTMLForm\CForm();
    return $form;
});


$app->router->add('', function () use ($app) {
    $app->theme->setTitle("Home");
    
    //Displayed logged in user in 'status' div
    $user = $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
    ]);
    if ($user[0] != null) {
        $app->views->addString('Logged in as: ' . $user[1], 'status');
    }

    //Get the 5 latest questions
    $latest_questions = $app->dispatcher->forward([
            'controller' => 'questions',
            'action'     => 'getLatestQuestions',
    ]);
    $app->views->add('questions/questions-list', [
            'list' => $latest_questions,
            'title' => "5 latest questions (topics)",
        ]);

    //Get the 5 most popular tags
    $popular_tags = $app->dispatcher->forward([
            'controller' => 'tags',
            'action'     => 'popularTags',
    ]);
    $app->views->add('questions/tags-list', [
            'tags' => $popular_tags,
            'title' => "Most popular tags",
        ]);

    //Get the 5 most active users
    $most_active = $app->dispatcher->forward([
            'controller' => 'users',
            'action'      => 'mostActiveUsers',
    ]);

    $app->views->add('users/users-list', [
            'users' => $most_active,
            'title' => "Most active users",
    ]);
});


$app->router->add('users', function () use ($app) {
    $app->dispatcher->forward([
    'controller' => 'users',
    'action'     => 'list',
    ], 'main');

    //Displayed logged in user in 'status' div
    $user = $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
    ]);
    if ($user[0] != null) {
        $app->views->addString('Logged in as: ' . $user[1], 'status');
    }
});


$app->router->add('questions', function () use ($app) {
    $app->theme->setTitle("Questions");
    $app->dispatcher->forward([
    'controller' => 'questions',
    'action'     => 'show',
    ], 'main');

    //Displayed logged in user in 'status' div
    $user = $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
    ]);
    if ($user[0] != null) {
        $app->views->addString('Logged in as: ' . $user[1], 'status');
    }

});

$app->router->add('about', function () use ($app) {
    $app->theme->setTitle("About");
    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown, clickable');
    
    $byline  = $app->fileContent->get('byline.md');
    $byline = $app->textFilter->doFilter($byline, 'shortcode, markdown');

    $app->views->add('me/page', [
        'content' => $content,
        'bylineName' => 'Markus Thulin',
        'bylineInfo' => $byline,
    ]);

    //Displayed logged in user in 'status' div
    $user = $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
    ]);
    if ($user[0] != null) {
        $app->views->addString('Logged in as: ' . $user[1], 'status');
    }

});

$app->router->add('tags', function () use ($app) {
    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'show',
        ], 'main');

    //Displayed logged in user in 'status' div
    $user = $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
    ]);
    if ($user[0] != null) {
        $app->views->addString('Logged in as: ' . $user[1], 'status');
    }
});


$app->router->handle();
$app->theme->render();
