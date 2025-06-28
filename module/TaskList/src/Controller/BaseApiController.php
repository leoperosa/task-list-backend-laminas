<?php

namespace TaskList\Controller;

use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\Mvc\MvcEvent;

class BaseApiController extends AbstractRestfulController
{
    protected string $authUser = 'user';
    protected string $authPassword = '1234';

    public function onDispatch(MvcEvent $e)
    {

        $routeMatch = $e->getRouteMatch();
        $routeName = $routeMatch ? $routeMatch->getMatchedRouteName() : '';

        if ($routeName === 'api-login') {
            return parent::onDispatch($e);
        }

        $request = $this->getRequest();
        $auth = $request->getHeader('Authorization');

        if (!$auth || strpos($auth->getFieldValue(), 'Basic ') !== 0) {
            return $this->unauthorizedResponse();
        }

        $decoded = base64_decode(substr($auth->getFieldValue(), 6));
        [$user, $pass] = explode(':', $decoded, 2);

        if ($user !== $this->authUser || $pass !== $this->authPassword) {
            return $this->forbiddenResponse();
        }

        return parent::onDispatch($e);
    }

    protected function unauthorizedResponse()
    {
        $response = new Response();
        $response->setStatusCode(401);
        $response->getHeaders()->addHeaderLine('WWW-Authenticate', 'Basic realm="API"');
        return $response;
    }

    protected function forbiddenResponse()
    {
        $response = new Response();
        $response->setStatusCode(403);
        return $response;
    }
}
