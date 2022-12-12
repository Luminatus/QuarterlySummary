<?php

namespace Lumie\QuarterlySummary;

use Lumie\QuarterlySummary\Controller\ControllerInterface;
use Lumie\QuarterlySummary\Request\RequestHandler;
use Lumie\QuarterlySummary\Routing\Route;
use Lumie\QuarterlySummary\Service\QuarterlySummaryService;
use PDO;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

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

    /** @var array $services */
    protected $discounts;

    /** @var bool $init */
    protected bool $init = false;

    /** @var Environment $twig */
    protected Environment $twig;

    public function init()
    {
        if (!$this->init) {
            $this->loadEnv();
            $this->loadRoutes();
            $this->loadControllers();
            $this->loadHandler();
            $this->loadDbConnection();
            $this->loadDiscounts();
            $this->loadServices();
            $this->loadTwig();

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

    public function getService(string $name)
    {
        return $this->services[$name] ?? null;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    private function loadEnv()
    {
        $dotenv = new Dotenv();
        $dotenv->usePutenv(true);
        $dotenv->load('../.env');
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
    }

    private function loadControllers()
    {
        $yaml = Yaml::parse(file_get_contents('../config/controllers.yaml'));

        foreach ($yaml['controllers'] as $key => $controllerClass) {
            if (in_array(\Lumie\QuarterlySummary\Controller\ControllerInterface::class, class_implements($controllerClass))) {
                $controller = new $controllerClass;
                $controller->setKernel($this);
                $this->controllers[$key] = $controller;
            } else {
                throw new \Exception("Controller class does not implement ControllerInterface");
            }
        }
    }

    private function loadDbConnection()
    {
        $dsn = sprintf('%s:host=%s;dbname=%s', getenv('DB_DRIVER'), getenv('DB_HOST'), getenv('DB_DATABASE'));
        $username = getenv('DB_USERNAME');
        $pw = getenv('DB_PASSWORD');

        $this->db = new PDO($dsn, $username, $pw);
    }

    private function loadServices()
    {
        $this->services['quarterlySummary'] = new QuarterlySummaryService($this->db, $this->discounts);
    }

    private function loadDiscounts()
    {
        $yaml = Yaml::parse(file_get_contents('../config/discounts.yaml'));

        foreach ($yaml['discounts'] as $discountClass) {
            if (in_array(\Lumie\QuarterlySummary\Discount\DiscountInterface::class, class_implements($discountClass))) {
                $this->discounts[] = new $discountClass;
            } else {
                throw new \Exception("Discount class does not implement DiscountInterface");
            }
        }
    }

    private function loadTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => '../var/cache',
        ]);
    }
}
