<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Tests\EventListener;

use Jontsa\Bundle\MaintenanceBundle\EventListener\MaintenanceListener;
use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
     * @param Request $request
     * @return ResponseEvent|MockObject
     */
    private function createResponseEvent(Request $request) : ResponseEvent
    {
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        return new ResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
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

    /**
     * @test
     */
    public function nothingHappensInKernelResponseWhenNotInMaintenance()
    {
        $request = $this->createMock(Request::class);
        $request
            ->expects($this->never())
            ->method('getContentType');
        $event = $this->createResponseEvent($request);
        $listener = $this->createListener();
        $listener->onKernelResponse($event);
    }

    /**
     * @test
     */
    public function nothingHappensInKernelResponseWhenNotUsingJsonRequest()
    {
        $request = new Request();

        $this->maintenance
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $requestEvent = $this->mockMasterRequestEvent();
        $listener = $this->createListener();

        try {
            $listener->onKernelRequest($requestEvent);
        } catch(ServiceUnavailableHttpException $e) {
            // do nothing
        }

        $event = $this->createResponseEvent($request);
        $response = $event->getResponse();
        $listener->onKernelResponse($event);

        $this->assertSame($response, $event->getResponse(), 'Response object should not have changed.');
    }

    /**
     * @test
     */
    public function responseIsConvertedToJson()
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'application/json']);

        $this->maintenance
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $requestEvent = $this->mockMasterRequestEvent();
        $listener = $this->createListener();

        try {
            $listener->onKernelRequest($requestEvent);
        } catch(ServiceUnavailableHttpException $e) {
            // do nothing
        }

        $event = $this->createResponseEvent($request);
        $listener->onKernelResponse($event);

        $this->assertInstanceOf(JsonResponse::class, $event->getResponse(), 'Response object should be JsonResponse when content type is json.');
    }

}
