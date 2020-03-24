<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Command;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnableMaintenanceCommand extends Command
{

    protected static $defaultName = 'jontsa:maintenance:enable';

    private $maintenance;

    public function __construct(Maintenance $maintenance, string $name = null)
    {
        parent::__construct($name);
        $this->maintenance = $maintenance;
    }

    public function configure()
    {
        $this
            ->setDescription('Activates maintenance mode for the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (true === $this->maintenance->isEnabled()) {
            $output->writeln('Application is already in maintenance mode.');
        } else {
            $this->maintenance->enable();
            $output->writeln('Application is in maintenance mode.');
        }
        return 0;
    }

}