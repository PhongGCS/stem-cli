<?php
namespace ILab\Stem\CommandLine\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class AddAppCommand extends Command {
	protected function configure() {
		$this->setName('add-app')
		     ->setDescription('Creates a new Stem app in your current WordPress/Bedrock site.')
		     ->addArgument('app-name', InputArgument::REQUIRED, "Name of app.");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
	}
}