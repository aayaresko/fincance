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
        $io->comment('Fetching currencies.');
        /**
         * @var EntityManager $em
         * @var FinanceService $service
         * @var CurrencyRepository $currencyRepository
         */
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $service            = $this->getContainer()->get(FinanceService::class);
        $currencyRepository = $em->getRepository(Currency::class);
        $currencies         = $currencyRepository->findAll();
        $codes              = array_map(
            function (Currency $currency) {
                return $currency->getCode();
            },
            $currencies
        );
        $flushNeeded = false;
        $data        = $service->getAvailableCurrencies();
        foreach ($data as $item) {
            /** @var CurrencyContainer $item */
            if (!in_array($item->code, $codes)) {
                $io->writeln(sprintf('New currency will be added with code %s.', $item->code));
                $entity = new Currency();
                $entity->setName($item->name);
                $entity->setCode($item->code);
                $flushNeeded = true;
                $em->persist($entity);
            }
        }
        if ($flushNeeded) {
            $io->writeln('Persisting data.');
            $em->flush();
        } else {
            $io->writeln('Nothing to do...');
        }
        $io->writeln('Done.');
    }
}
