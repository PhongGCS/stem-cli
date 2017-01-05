<?php
namespace ILab\Stem\CommandLine\Commands;

use ILab\Stem\CommandLine\Utilities\StemCLITools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallBedrockCommand extends Command {
	protected function configure() {
		$this->setName('install-bedrock')
		     ->setDescription('Installs bedrock.')
		     ->addArgument('name', InputArgument::OPTIONAL, "Name of project.");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->write('Installing bedrock ... ');

		$dir = DIRECTORY_SEPARATOR.trim(getcwd(),DIRECTORY_SEPARATOR);

		$name = '';
		if ($input->hasArgument('name'))
			$name = $input->getArgument('name');

		if (!file_exists($dir.DIRECTORY_SEPARATOR.$name)) {
			mkdir($dir.DIRECTORY_SEPARATOR.$name, 0777, true);
		}

		$bedrockDir = $dir.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'site';

		$process=new Process("git clone https://github.com/roots/bedrock.git  $bedrockDir", getcwd());
		$process->run();

		(new Filesystem())->remove($bedrockDir.DIRECTORY_SEPARATOR.'.git');

		$output->writeln('Done.');

		$output->write('Updating composer ... ');

		$process=new Process("composer update", $bedrockDir);
		$process->setTimeout(null);
		$process->run();

		$output->writeln('Done.');

		StemCLITools::setWordpressPath($bedrockDir.DIRECTORY_SEPARATOR.'web');

		return 0;
	}
}