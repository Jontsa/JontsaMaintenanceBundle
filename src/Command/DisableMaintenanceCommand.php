<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Command;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisableMaintenanceCommand extends Command
{

    protected static $defaultName = 'jontsa:maintenance:disable';

    private $maintenance;

    public function __construct(Maintenance $maintenance, string $name = null)
    {
        parent::__construct($name);
        $this->maintenance = $maintenance;
    }

    public function configure()
    {
        $this
            ->setDescription('Disables maintenance mode for the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (false === $this->maintenance->isEnabled()) {
            $output->writeln('Application is not in maintenance mode.');
        } else {
            $this->maintenance->disable();
            $output->writeln('Application is now available.');
        }
        return 0;
    }

}