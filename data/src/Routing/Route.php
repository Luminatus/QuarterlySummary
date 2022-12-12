<?php

namespace Lumie\QuarterlySummary\Routing;

class Route
{
    protected const DEFAULT_PARAMETER_FORMAT = '[a-zA-Z0-9_\-]+';

    protected string $pattern;

    protected $processedUri;

    protected $controllerMethod;

    protected $parameters;

    public function __construct(string $pattern, string $controllerMethod, array $parameters = [])
    {
        $this->pattern = $pattern;

        $this->controllerMethod = $controllerMethod;

        $this->parameters = $parameters;
    }

    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    public function match(string $uri): array|false
    {
        $requestParameters = [];

        $uri = strtok($uri, '?');

        $uriSegments = explode('/', $uri);
        $patternSegments = explode('/', $this->pattern);

        if (count($uriSegments) !== count($patternSegments)) {
            return false;
        }

        foreach ($patternSegments as $idx => $segment) {
            if ($this->isParameter($segment)) {
                $segment = trim($segment, '{}');
                $regex = $this->getParameterRegex($segment);
                if (!preg_match($regex, $uriSegments[$idx])) {
                    return false;
                }
                $requestParameters[$segment] = $uriSegments[$idx];
            } elseif ($uriSegments[$idx] != $segment) {
                return false;
            }
        }

        return $requestParameters;
    }

    protected function isParameter($pattern): bool
    {
        return preg_match('/{[a-zA-Z0-9_]+}/', $pattern);
    }

    protected function getParameterRegex($param): string
    {
        if (array_key_exists($param, $this->parameters)) {
            return '/^' . $this->parameters[$param] . '$/';
        } else {
            return '/^' . static::DEFAULT_PARAMETER_FORMAT . '$/';
        }
    }
}
