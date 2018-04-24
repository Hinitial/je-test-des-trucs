<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 24/04/18
 * Time: 09:28
 */

namespace AppBundle\EventListener;

use AppBundle\Exceptions\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Router;

class ExceptionListener
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        $response = new Response();
        $response->setContent($message);

        if ($exception instanceof SessionNotFoundException) {

            $route = 'homepage_billetterie';

            $url = $this->router->generate($route);
            $response = new RedirectResponse($url);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}