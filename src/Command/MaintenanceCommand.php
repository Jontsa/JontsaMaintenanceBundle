<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\Command;

use Jontsa\Bundle\MaintenanceBundle\Maintenance;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('jontsa:maintenance')]
class MaintenanceCommand extends Command
{

    private Maintenance $maintenance;

    public function __construct(Maintenance $maintenance, string $name = null)
    {
        parent::__construct($name);
        $this->maintenance = $maintenance;
    }

    public function configure() : void
    {
        $this
            ->setDescription('Activates maintenance mode for the application.')
            ->addArgument('action', InputArgument::REQUIRED, '"enable" or "disable"');
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string $action */
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
        return Command::SUCCESS;
    }

    /**
     * Enables maintenance mode if not yet enabled.
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
