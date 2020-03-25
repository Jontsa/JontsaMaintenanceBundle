<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Tests\EventListener;

use Jontsa\Bundle\MaintenanceBundle\EventListener\MaintenanceListener;
use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class MaintenanceListenerTest extends TestCase
{

    /**
     * @var Maintenance|MockObject
     */
    private $maintenance;

    public function setUp(): void
    {
        parent::setUp();
        $this->maintenance = $this->createMock(Maintenance::class);
    }

    private function createListener(?array $ips = null) : MaintenanceListener
    {
        return new MaintenanceListener($this->maintenance, $ips);
    }

    /**
     * @return RequestEvent|MockObject
     */
    private function mockMasterRequestEvent() : RequestEvent
    {
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '1.1.1.1']);
        /** @var RequestEvent|MockObject $mock */
        $mock = $this->createMock(RequestEvent::class);
        $mock
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $mock
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        return $mock;
    }

    /**
     * @return RequestEvent|MockObject
     */
    private function mockNonMasterRequestEvent() : RequestEvent
    {
        /** @var RequestEvent|MockObject $mock */
        $mock = $this->createMock(RequestEvent::class);
        $mock
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(false);
        $mock
            ->expects($this->never())
            ->method('getRequest');
        return $mock;
    }

    /**
     * @test
     */
    public function nonMasterRequestsAreIgnored()
    {
        $event = $this->mockNonMasterRequestEvent();
        $listener = $this->createListener();
        $listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function requestsFromWhiteListedIpAddressesAreIgnored()
    {
        $this->maintenance
            ->expects($this->never())
            ->method('isEnabled');

        $event = $this->mockMasterRequestEvent();
        $listener = $this->createListener(['1.1.1.0/24']);
        $listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function nothingHappensIfMaintenanceIsNotEnabled()
    {
        $this->maintenance
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $event = $this->mockMasterRequestEvent();
        $listener = $this->createListener(['1.1.2.0/24']);
        $listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function requestThrowsExceptionDuringMaintenance()
    {
        $this->maintenance
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->expectException(ServiceUnavailableHttpException::class);

        $event = $this->mockMasterRequestEvent();
        $listener = $this->createListener();
        $listener->onKernelRequest($event);
    }

}
