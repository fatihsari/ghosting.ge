<?php 
namespace WHMCS\Route\Middleware;


class RoutePathMatch implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use Strategy\AssumingMiddlewareTrait;

    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $dispatch = \DI::make("Route\\Dispatch");
        $route = $dispatch->dispatch($request->getMethod(), $request->getUri()->getPath());
        if( $route[0] == $dispatch::FOUND ) 
        {
            if( !empty($route[2]) ) 
            {
                foreach( $route[2] as $attribute => $value ) 
                {
                    $request = $request->withAttribute($attribute, $value);
                }
            }

            $request = $request->withAttribute("matchedRouteHandle", $route[1]);
        }

        return $delegate->process($request);
    }

}