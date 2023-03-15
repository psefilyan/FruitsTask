<?php

	namespace App\Command;

	use App\Entity\Fruit;
	use DateTime;
	use Doctrine\ORM\EntityManagerInterface;
	use Exception;
	use Symfony\Component\Console\Attribute\AsCommand;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Mailer\MailerInterface;
	use Symfony\Component\Mime\Email;
	use Symfony\Contracts\HttpClient\HttpClientInterface;

	#[AsCommand(name: 'get:fruits', description: 'Add a short description for your command',)]
	class GetFruitsCommand extends Command {

		public $client;
		public EntityManagerInterface $em;
		public MailerInterface $mailer;


		public function __construct(HttpClientInterface $client, EntityManagerInterface $em, MailerInterface $mailer, string $name = null) {
			parent::__construct($name);
			$this->client = $client;
			$this->em = $em;
			$this->mailer = $mailer;
		}

		protected function configure(): void {
			$this->setName('GetFruits')->setDescription('Parse Fruits from website')->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
		}

		public function getFruits() {
			try {
				$response = $this->client->request(
					'GET', 'https://fruityvice.com/api/fruit/all'
				);
				$response = $response->toArray();
				$data['result'] = 1;
				$data['data'] = $response;

				return $data;
			}

			catch (Exception $e) {
				return [ "result" => 0, "message" => $e->getMessage() ];
			}
		}

		public function sendEmail(MailerInterface $mailer, $fruitName) {
			$email = new Email();

			$email->from('psafalian@gmail.com')->to('hr@internetprojects.com')->text('Dear , the ' . $fruitName . '  just added in fruits data');


			$mailer->send($email);
		}

		protected function execute(InputInterface $input, OutputInterface $output): int {
			$fruits = $this->getFruits();
			$date = new DateTime('@' . strtotime('now'));

			if($fruits['result']){
				foreach ($fruits['data'] as $fruit) {
					$fruitEntity = new Fruit();
					if(empty($this->em->getRepository(Fruit::class)->findByFruitId($fruit['id']))){
						$fruitEntity->setGenus($fruit['genus']);
						$fruitEntity->setName($fruit['name']);
						$fruitEntity->setFruitId($fruit['id']);
						$fruitEntity->setFamily($fruit['family']);
						$fruitEntity->setOrderName($fruit['order']);
						$fruitEntity->setIsFavorite(0);
						$fruitEntity->setNutritions($fruit['nutritions']);
						$fruitEntity->setCreatedDate($date);
						$this->em->persist($fruitEntity);
						$this->em->flush();
						$this->sendEmail($this->mailer, $fruit['name']);
					}
				}
			}
			return 0;
		}
	}
