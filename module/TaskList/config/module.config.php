<?php

namespace TaskList;

use Laminas\Router\Http\Segment;
use TaskList\Controller\ApiController;
use TaskList\Service\TaskRulesService;
use TaskList\Service\PendingTaskNotifier;
use TaskList\Service\PendingTaskNotifierFactory;
use TaskList\Controller\NotifyController;
use TaskList\Service\NotifierInterface;

return [
    'router' => [
        'routes' => [
            'api-task' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/task[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => ApiController::class,
                    ],
                ],
            ],
            'api-notify' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/api/notify',
                    'defaults' => [
                        'controller' => NotifyController::class,
                        'action'     => 'send',
                    ],
                ],
            ],     
            'api-task-status' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/task/:id/status',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => ApiController::class,
                        'action'     => 'updateStatus',
                    ],
                ],
            ],                   
            'api-login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/login',
                    'defaults' => [
                        'controller' => ApiController::class,
                        'action'     => 'login',
                    ],
                ],
            ],                   
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ApiController::class => function ($container) {
                return new Controller\ApiController(
                    $container->get(Model\TaskTable::class),
                    $container->get(TaskRulesService::class)
                );
            },
            NotifyController::class => function ($container) {
                return new NotifyController(
                    $container->get(NotifierInterface::class)
                );
            },
        ],
    ],
    'service_manager' => [
        'aliases' => [
            \TaskList\Service\NotifierInterface::class => \TaskList\Service\PendingTaskNotifier::class,
        ],
        'factories' => [
            Model\TaskTable::class => Model\TaskTableFactory::class,
            TaskRulesService::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            PendingTaskNotifier::class => PendingTaskNotifierFactory::class,
            Middleware\BasicAuthMiddleware::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
            TaskList\Middleware\CorsMiddleware::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'strategies' => ['ViewJsonStrategy'],
    ],
    'middleware_pipeline' => [
        'before' => [
            'middleware' => [
                \TaskList\Middleware\CorsMiddleware::class,
                Middleware\BasicAuthMiddleware::class,
            ],
            'priority' => 10000,
        ],
    ],
];
