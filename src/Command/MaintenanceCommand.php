<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Command;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceCommand extends Command
{

    protected static $defaultName = 'jontsa:maintenance';

    private $maintenance;

    public function __construct(Maintenance $maintenance, string $name = null)
    {
        parent::__construct($name);
        $this->maintenance = $maintenance;
    }

    public function configure()
    {
        $this
            ->setDescription('Activates maintenance mode for the application.')
            ->addArgument('action', InputArgument::REQUIRED, '"enable" or "disable"');
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $action = $input->getArgument('action');

        switch($action) {
            case 'enable':
                $this->enable($output);
                break;
            case 'disable':
                $this->disable($output);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported action "%s".', $action));
        }
        return 0;
    }

    /**
     * Enables maintenance mode if not yet enabled.
     *
     * @param OutputInterface $output
     */
    private function enable(OutputInterface $output) : void
    {
        if (true === $this->maintenance->isEnabled()) {
            $output->writeln('Application is already in maintenance mode.');
        } else {
            $this->maintenance->enable();
            $output->writeln('Application is in maintenance mode.');
        }
    }

    /**
     * Disables maintenance mode if enabled.
     *
     * @param OutputInterface $output
     */
    private function disable(OutputInterface $output) : void
    {
        if (false === $this->maintenance->isEnabled()) {
            $output->writeln('Application is not in maintenance mode.');
        } else {
            $this->maintenance->disable();
            $output->writeln('Application is now available.');
        }
    }

}