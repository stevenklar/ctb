<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class AddProjectTimeCommand extends BaseCommand {

	protected $name = 'project:book';
	protected $description = 'Book project time.';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doAddProjectTime($phprojekt);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['project', InputArgument::REQUIRED, 'ProjectID'],
			['time', InputArgument::REQUIRED, 'Working on this project for XXXX (hours|minutes)'],
			['description', InputArgument::REQUIRED, 'Description what you did'],
		];
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doAddProjectTime($phprojekt)
	{
		$project = trim($this->argument('project'));
		$time = trim($this->argument('time'));
		$description = trim($this->argument('description'));

		if (strlen($time) != 4) {
			exit($this->error('[Response] Wrong format... Please use 0100 as example.'));
		}

		$hours = $time[0] . $time[1];
		$minutes = $time[2] . $time[3];

		try {
			$this->info('[Action] Book project time');

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logProjectHours(
				new \DateTime(),
				$project,
				$hours + $minutes / 60,
				$description
			);

			$this->call('p:list');
			$this->info('[Action] Done');

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
			$this->comment($e->getMessage());
		}
	}

}
