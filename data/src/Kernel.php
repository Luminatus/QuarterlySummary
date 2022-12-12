<?php

namespace Lumie\QuarterlySummary;

use Lumie\QuarterlySummary\Controller\ControllerInterface;
use Lumie\QuarterlySummary\Request\RequestHandler;
use Lumie\QuarterlySummary\Routing\Route;
use PDO;
use Symfony\Component\Yaml\Yaml;

class Kernel
{
    /** @var Route[] $routes */
    protected array $routes;

    /** @var ControllerInterface[] $controllers */
    protected array $controllers;

    /** @var RequestHandler */
    protected $handler;

    /** @var PDO */
    protected $db;

    /** @var array $services */
    protected $services;

    /** @var bool $init */
    protected bool $init = false;

    public function init()
    {
        if (!$this->init) {
            $this->loadRoutes();
            $this->loadControllers();
            $this->loadHandler();
            $this->loadDbConnection();
            $this->loadServices();

            $this->init = true;
        }
    }

    public function run()
    {
        $request = $this->handler->createRequest();
        $this->handler->handle($request);
    }


    /**
     * @return \Lumie\QuarterlySummary\Routing\Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return \Lumie\QuarterlySummary\Controller\ControllerInterface[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    private function loadHandler()
    {
        $this->handler = new RequestHandler($this);
    }

    private function loadRoutes()
    {
        $yaml = Yaml::parse(file_get_contents('../config/routes.yaml'));

        foreach ($yaml['routes'] as $pattern => $routeConfig) {
            if (!isset($routeConfig['controller'])) {
                throw new \Exception("Controller method must be set for route config");
            }

            if (!preg_match('/^[A-Za-z0-9_]+:[A-Za-z0-9_]+$/', $routeConfig['controller'])) {
                throw new \Exception('Invalid controller method definition in route config');
            }

            $this->routes[$pattern] = new \Lumie\QuarterlySummary\Routing\Route($pattern, $routeConfig['controller'], $routeConfig['parameters'] ?? []);
        }

        dump($this->routes);
    }

    private function loadControllers()
    {
        $yaml = Yaml::parse(file_get_contents('../config/controllers.yaml'));

        foreach ($yaml['controllers'] as $key => $controller) {
            if (in_array(\Lumie\QuarterlySummary\Controller\ControllerInterface::class, class_implements($controller))) {
                $this->controllers[$key] = new $controller;
            } else {
                throw new \Exception("Controller class does not implement ControllerInterface");
            }
        }
    }

    private function loadDbConnection()
    {
        $dsn = sprintf('%s:%s;dbname=%s', env('DB_DRIVER'), env('DB_HOST'), env('DB_DATABASE'));
        $username = env('DB_USERNAME');
        $pw = env('DB_PASSWORD');

        $this->db = new PDO($dsn, $username, $pw);
    }

    private function loadServices()
    {
    }
}
