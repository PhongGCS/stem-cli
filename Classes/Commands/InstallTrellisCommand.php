<?php
namespace ILab\Stem\CommandLine\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallTrellisCommand extends Command {
	protected function configure() {
		$this->setName('install-trellis')
			->setDescription('Installs trellis.')
			->addArgument('name', InputArgument::OPTIONAL, "Name of project.")
			->addOption('roots',null, InputOption::VALUE_OPTIONAL,"Use Root.io's trellis instead of the Interfacelab fork.",  false);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->write('Installing trellis ... ');

		$useRoots = $input->getOption('roots');

		$dir = rtrim(getcwd(),DIRECTORY_SEPARATOR);

		$name = '';
		if ($input->hasArgument('name'))
			$name = $input->getArgument('name');

		if (!file_exists($dir.DIRECTORY_SEPARATOR.$name)) {
			mkdir($dir.DIRECTORY_SEPARATOR.$name, 0777, true);
		}

		$trellisDir = $dir.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'trellis';

		if ($useRoots)
			$process=new Process("git clone https://github.com/roots/trellis.git  $trellisDir", getcwd());
		else
			$process=new Process("git clone https://github.com/jawngee/trellis.git  $trellisDir", getcwd());

		$process->run();

		(new Filesystem())->remove($trellisDir.DIRECTORY_SEPARATOR.'.git');

		$output->writeln('Done.');

		return 0;
	}
}