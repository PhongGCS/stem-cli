<?php

namespace ILab\Stem\CommandLine\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectCommand extends Command
{
    protected function configure()
    {
        $this->setName('new-project')
             ->setDescription('Creates a new Stem project by installing trellis, bedrock, stem and a blank stem app.')
             ->addArgument('name', InputArgument::OPTIONAL, 'Name of project.')
             ->addOption('roots', null, InputOption::VALUE_NONE, "Use Root.io's trellis instead of the Interfacelab fork.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = null;
        if ($input->hasArgument('name')) {
            $name = $input->getArgument('name');
        }

        $useRoots = $input->getOption('roots');

        $command = $this->getApplication()->find('install-trellis');
        $trellisArgs = [
            'name'    => $name,
            '--roots' => $useRoots,
        ];
        if ($command->run(new ArrayInput($trellisArgs), $output) != 0) {
            $output->writeln('<error>Error installing trellis.  Aborting.</error>');

            return 1;
        }

        $command = $this->getApplication()->find('install-bedrock');
        $bedrockArgs = [
            'name' => $name,
        ];
        if ($command->run(new ArrayInput($bedrockArgs), $output) != 0) {
            $output->writeln('<error>Error installing bedrock.  Aborting.</error>');

            return 1;
        }

        $command = $this->getApplication()->find('install-stem');
        $stemArgs = [
        ];
        if ($command->run(new ArrayInput($stemArgs), $output) != 0) {
            $output->writeln('<error>Error installing Stem.  Aborting.</error>');

            return 1;
        }
    }
}
