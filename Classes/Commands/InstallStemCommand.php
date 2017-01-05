<?php
namespace ILab\Stem\CommandLine\Commands;

use ILab\Stem\CommandLine\Utilities\BladeView;
use ILab\Stem\CommandLine\Utilities\StemCLITools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallStemCommand extends Command {
	protected function configure() {
		$this->setName('install-stem')
		     ->setDescription('Installs stem.')
		     ->addArgument('plugin-path', InputArgument::OPTIONAL, "Path to your WordPress plugin");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		if (!$input->hasArgument('plugin-path')) {
			$pluginPath = StemCLITools::MUPluginPath();
		} else {
			$pluginPath = $input->getArgument('plugin-path');
			if (!$pluginPath) {
				$pluginPath = StemCLITools::MUPluginPath();
			} else {
				if (!file_exists($pluginPath)) {
					$output->writeln('<error>Error: The `plugin-path` argument specifies a non-existent directory.</error>');
					return 1;
				}
			}
		}

		if (!$pluginPath) {
			$output->writeln('<error>Error: Missing `plugin-path` argument.</error>');
			return 1;
		}

		if (!file_exists($pluginPath))
			mkdir($pluginPath, 0777, true);

		$output->write('Installing Stem ... ');

		if (!StemCLITools::isBedrock()) {
			$stemDIR = $pluginPath.DIRECTORY_SEPARATOR.'Stem';

			$process=new Process("git clone https://github.com/jawngee/stem.git  $stemDIR", getcwd());
			$process->run();

			BladeView::renderViewToFile('stem/stem-mu', $pluginPath . DIRECTORY_SEPARATOR . 'stem.php');

			(new Filesystem())->remove($stemDIR.DIRECTORY_SEPARATOR.'.git');
		} else {
			$bedrockDir = str_replace(DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'mu-plugins','',$pluginPath);
			$bedrockDir = rtrim($bedrockDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

			$process = new Process('composer config repositories.blade vcs https://github.com/jawngee/blade', $bedrockDir);
			if ($process->run() != 0)
				return $this->bailWithError("There was a problem updating the composer.json file.  Maybe composer isn't installed?", $output);

			$process = new Process('composer config repositories.stem vcs https://github.com/jawngee/stem', $bedrockDir);
			if ($process->run() != 0)
				return $this->bailWithError("There was a problem updating the composer.json file.  Maybe composer isn't installed?", $output);

			$process = new Process('composer require jawngee/blade:dev-master', $bedrockDir);
			if ($process->run() != 0)
				return $this->bailWithError("There was a problem updating the composer.json file.  Maybe composer isn't installed?", $output);

			$process = new Process('composer require ilab/stem:dev-master', $bedrockDir);
			if ($process->run() != 0)
				return $this->bailWithError("There was a problem updating the composer.json file.  Maybe composer isn't installed?", $output);
		}

		$output->writeln('Done.');

		return 0;
	}

	private function bailWithError($message, $output) {
		$output->writeln('error');
		$output->writeln("<error>$message</error>");
		return 1;
	}
}