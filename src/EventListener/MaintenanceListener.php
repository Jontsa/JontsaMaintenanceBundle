<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\EventListener;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Listener to decide if user can access to the site.
 */
class MaintenanceListener
{

    /**
     * @var Maintenance
     */
    protected $maintenance;

    /**
     * @var array|null
     */
    protected $ips;

    /**
     * @var bool
     */
    protected $handleResponse = false;

    public function __construct(Maintenance $maintenance, ?array $ips = null)
    {
        $this->ips = $ips;
        $this->maintenance = $maintenance;
    }

    /**
     * @param RequestEvent $event
     * @throws ServiceUnavailableHttpException
     */
    public function onKernelRequest(RequestEvent $event) : void
    {
        if(false === $event->isMasterRequest()){
            return;
        }

        $request = $event->getRequest();

        if (true === $this->checkIps($request->getClientIp(), $this->ips)) {
            return;
        }

        if (true === $this->inMaintenance()) {
            $this->handleResponse = true;
            throw new ServiceUnavailableHttpException();
        }
    }

    /**
     * If request was JSON, make sure we respond in kind.
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event) : void
    {
        if (true === $this->handleResponse) {
            $request = $event->getRequest();
            if ('json' === $request->getContentType()) {
                $response = new JsonResponse([], Response::HTTP_SERVICE_UNAVAILABLE);
                $response->headers->set('Content-Type', 'application/problem+json');
                $event->setResponse($response);
            }
        }
    }

    /**
     * Check if maintenance mode is active.
     *
     * @return bool
     */
    public function inMaintenance() : bool
    {
        return $this->maintenance->isEnabled();
    }

    /**
     * Checks if the requested ip is valid.
     *
     * @param string       $requestedIp
     * @param string|array|null $ips
     * @return bool
     */
    protected function checkIps(string $requestedIp, $ips) : bool
    {
        $ips = array_filter((array) $ips);

        foreach ((array) $ips as $ip) {
            if (true === IpUtils::checkIp($requestedIp, $ip)) {
                return true;
            }
        }
        return false;
    }

}
