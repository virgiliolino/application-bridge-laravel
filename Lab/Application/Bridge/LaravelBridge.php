<?php
namespace Lab\Application\Bridge;

//use Bridge\ApplicationBridge\CommandHandler;
//use Bridge\ApplicationBridge\Route;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bridge between a LaravelApplication and a Standard Application defined in ApplicationBrdige
 */
class LaravelBridge
    extends ApplicationBridge {

    const ATTRIBUTE_PREFIX = 'prefix';
    const ATTRIBUTE_MIDDLEWARE = 'middleware';
    const ATTRIBUTE_NAMESPACE = 'namespace';

    private $app;
    
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $method
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function addRoute(Route $route) {
        if (!$route->isValid()) {
            //perhaps some log?
            return;
        }
        /** @var \Illuminate\Routing\Route $router */
        $router = $this->app['router'];

        $router->group(
            $this->createAttributesFromRoute($route),
            function () use ($router, $route) {
                $router->match(
                    //if merged laravel pull request remove ->getValue()
                    $route->getMethod()->getValue(), $route->getUri()->getValue(), $route->getAction()->getValue());
            }
        );
    }

    private function createAttributesFromRoute(Route $route) {
        $customAttributes = [];
        if (!$route->getPrefix()->isNull()) {
            $customAttributes[self::ATTRIBUTE_PREFIX] =
                $route->getPrefix()->getValue();
        }

        if (!$route->getNamespace()->isNull()) {
            $customAttributes[self::ATTRIBUTE_NAMESPACE] =
                $route->getNamespace()->getValue();
        }

        if (!$route->getMiddleware()->isNull()) {
            $customAttributes[self::ATTRIBUTE_MIDDLEWARE] =
                $route->getMiddleware()->getValue();
        }
        return $customAttributes;
    }
}