<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Tests;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use PHPUnit\Framework\TestCase;

class MaintenanceTest extends TestCase
{

    private string $lockFile;

    public function setUp(): void
    {
        parent::setUp();
        $this->lockFile = '/tmp/maintenance_test.' . \bin2hex(\random_bytes(10));
        $this->resetTempFile();
    }

    public function tearDown(): void
    {
        $this->resetTempFile();
    }

    /**
     * @test
     */
    public function maintenanceIsNotEnabledByDefault()
    {
        $maintenance = $this->createClass();
        $this->assertFalse($maintenance->isEnabled());
    }

    /**
     * @test
     * @depends maintenanceIsNotEnabledByDefault
     */
    public function doesNothingWhenDisablingMaintenanceWhileNotInMaintenance()
    {
        $maintenance = $this->createClass();
        $maintenance->disable();
        $this->assertFalse($maintenance->isEnabled());
    }

    /**
     * @test
     */
    public function enablesMaintenance()
    {
        $maintenance = $this->createClass();
        $maintenance->enable();
        $this->assertFileExists($this->lockFile);
        $this->assertTrue($maintenance->isEnabled());
    }

    /**
     * @test
     */
    public function disablesMaintenance()
    {
        $maintenance = $this->createClass();
        $maintenance->enable();
        $this->assertTrue($maintenance->isEnabled());
        $maintenance->disable();
        $this->assertFalse($maintenance->isEnabled());
    }

    private function createClass() : Maintenance
    {
        return new Maintenance($this->lockFile);
    }

    private function resetTempFile() : void
    {
        if (true === \file_exists($this->lockFile)) {
            \unlink($this->lockFile);
        }
    }

}
