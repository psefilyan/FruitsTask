<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'get:fruits',
    description: 'Add a short description for your command',
)]
class GetFruitsCommand extends Command
{

	public  $client;


	public function __construct(HttpClientInterface $client,string $name = null) {
		parent::__construct($name);
		$this->client = $client;
	}

	protected function configure(): void
    {
        $this
			->setName('GetFruits')
			->setDescription('Parse Fruits from website')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
	public function  getFruits()
	{
		try {
			$response = $this->client->request(
				'GET',
				'https://fruityvice.com/api/fruit/all'
			);
			dump($response->toArray());
		}

		catch (\Exception $e)
		{
			return $e->getMessage();
		}
	}
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getFruits();
		return 0;
    }
}
