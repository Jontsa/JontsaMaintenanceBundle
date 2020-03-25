<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle;

/**
 * Enables or disables maintenance mode by creating a lock file.
 */
class Maintenance
{

    protected $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Check if maintenance mode is active.
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (true === \file_exists($this->filePath));
    }

    /**
     * Activates maintenance mode.
     */
    public function enable() : void
    {
        if (false === $this->isEnabled()) {
            $this->createLock();
        }
    }

    /**
     * De-activates maintenance mode.
     */
    public function disable() : void
    {
        if (true === $this->isEnabled()) {
            $this->createUnlock();
        }
    }

    protected function createLock() : void
    {
        \touch($this->filePath);
    }

    protected function createUnlock() : void
    {
        @\unlink($this->filePath);
    }

}
