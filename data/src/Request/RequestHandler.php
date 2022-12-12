<?php

namespace Lumie\QuarterlySummary\Request;

use Lumie\QuarterlySummary\Kernel;
use Lumie\QuarterlySummary\Routing\Route;

class RequestHandler
{
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function createRequest()
    {
        $get = $_GET;
        $post = $_POST;

        return new Request($_SERVER['REQUEST_URI'], $get, $post);
    }

    public function handle(Request $request)
    {
        $route = $this->findRouteForRequest($request);
        if (!$route) {
            throw new \Exception("Request does not fit any route");
        }
        $this->callController($request, $route);
    }

    protected function callController(Request $request, Route $route)
    {
        $controllerMethod = $route->getControllerMethod();
        $controllerName = strtok($controllerMethod, ':');
        $methodName = strtok('');

        $controller = $this->kernel->getControllers()[$controllerName];

        if (method_exists($controller, $methodName)) {
            call_user_func([$controller, $methodName], $request);
        } else {
            throw new \Exception("Undefined controller method");
        }
    }

    protected function findRouteForRequest(Request $request)
    {
        $uri = $request->uri();
        foreach ($this->kernel->getRoutes() as $route) {
            if ($match = $route->match($uri)) {
                $request->setParameters($match);
                return $route;
            }
        }

        return null;
    }
}
