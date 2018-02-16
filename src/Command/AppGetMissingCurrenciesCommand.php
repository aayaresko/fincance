<?php

namespace App\Command;

use App\Container\Finance\CurrencyContainer;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Service\FinanceService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppGetMissingCurrenciesCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:currencies:get-missing';

    protected function configure()
    {
        $this
            ->setDescription('Get missing currencies.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('Fetching new currencies.');
        /**
         * @var EntityManager $em
         * @var FinanceService $service
         * @var CurrencyRepository $currencyRepository
         */
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $service            = $this->getContainer()->get(FinanceService::class);
        $currencyRepository = $em->getRepository(Currency::class);
        $currentEntities    = $currencyRepository->findAll();
        $codes              = array_map(
            function (Currency $currency) {
                return $currency->getCode();
            },
            $currentEntities
        );
        $flushNeeded = false;
        $data        = $service->getAvailableCurrencies();
        foreach ($data as $item) {
            /** @var CurrencyContainer $item */
            $entity = new Currency();
            $entity->setName($item->name);
            $entity->setCode($item->code);
            if (!in_array($entity->getCode(), $codes)) {
                $io->writeln(sprintf('New currency will be added with code %s.', $entity->getCode()));
                $flushNeeded = true;
                $em->persist($entity);
            }
        }
        if ($flushNeeded) {
            $io->writeln('Persisting data.');
            $em->flush();
        }
        $io->writeln('Done.');
    }
}
