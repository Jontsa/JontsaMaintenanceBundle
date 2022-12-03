<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\EventListener;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Listener to decide if user can access to the site.
 */
class MaintenanceListener
{

    protected Maintenance $maintenance;

    /**
     * @var string[]|null
     */
    protected ?array $ips;

    /**
     * @param string[]|null $ips
     */
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
        if(false === $event->isMainRequest()){
            return;
        }

        $request = $event->getRequest();

        if (true === $this->checkIps($request->getClientIp(), $this->ips)) {
            return;
        }

        if (true === $this->inMaintenance()) {
            throw new ServiceUnavailableHttpException();
        }
    }

    /**
     * Check if maintenance mode is active.
     */
    protected function inMaintenance() : bool
    {
        return $this->maintenance->isEnabled();
    }

    /**
     * Checks if the requested ip is valid.
     *
     * @param string|null $requestedIp
     * @param string|string[]|null $ips
     * @return bool
     */
    protected function checkIps(?string $requestedIp, $ips) : bool
    {
        if (!$requestedIp) {
            return false;
        }

        $ips = array_filter((array) $ips);

        foreach ($ips as $ip) {
            if (true === IpUtils::checkIp($requestedIp, $ip)) {
                return true;
            }
        }
        return false;
    }

}
