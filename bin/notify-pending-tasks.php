<?php

use Laminas\Mvc\Application;
use TaskList\Service\PendingTaskNotifier;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$appConfig = require 'config/application.config.php';
$application = Application::init($appConfig);

$notifier = $application->getServiceManager()->get(PendingTaskNotifier::class);
$notifier->notify();