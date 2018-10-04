<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use App\Service\AverageRateService;
use App\Service\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppGetAverageRateCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:rates:get-average';

    protected function configure()
    {
        $this
            ->setDescription('Get missing organizations.')
            ->addArgument(
                'currenciesIdentifiers',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List currencies identifiers (separate multiple names with a space)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io          = new SymfonyStyle($input, $output);
        $identifiers = $input->getArgument('currenciesIdentifiers');

        if (empty($identifiers)) {
            $message = 'Checking lowest rates for all currencies';
        } else {
            $message = sprintf('Checking lowest rates for currencies: %s', implode(',', $identifiers));
        }

        $io->comment($message);

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var RateRepository $rateRepository */
        $rateRepository = $em->getRepository(Rate::class);
        /** @var AverageRateService $averageRateService */
        $averageRateService = $this->getContainer()->get(AverageRateService::class);
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $em->getRepository(Currency::class);

        if (!empty($currenciesIdentifiers)) {
            $builder    = $currencyRepository->createQueryBuilder('c');
            $currencies = $builder
                ->where(
                    $builder->expr()->in('c.id', $currenciesIdentifiers)
                )
                ->getQuery()
                ->execute();
        } else {
            $currencies = $currencyRepository->findAll();
        }

        $week         = new \DateTime('this sunday');
        $created      = 0;
        $lowestRates  = [];
        $highestRates = [];

        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            $averageRate = $averageRateService->createFromRateIfNotExist($currency, $week, AverageRateService::TYPE_SALE);

            if ($averageRate) {
                $lowestRates[] = $rateRepository->getLowestSaleByCurrency($currency, $week);
                $created++;
            }

            $averageRate = $averageRateService->createFromRateIfNotExist($currency, $week, AverageRateService::TYPE_BUY);

            if ($averageRate) {
                $highestRates[] = $rateRepository->getHighestBuyByCurrency($currency, $week);
                $created++;
            }
        }

        $this->sendEmail($io, $lowestRates, $highestRates);

        if ($created) {
            $io->writeln([
                sprintf('%d average rates created.', $created),
                'Persisting data.'
            ]);

            $em->flush();
        } else {
            $io->writeln('Nothing to do...');
        }

        $io->writeln('Done.');
    }

    private function sendEmail(SymfonyStyle $io, array $lowestRates = [], array $highestRates = [])
    {
        if (empty($lowestRates) || empty($highestRates)) {
            return;
        }

        /** @var Subscriber $subscriber */
        $subscriber = $this->getContainer()->get(Subscriber::class);

        foreach ($lowestRates as $rate) {
            /** @var Rate $rate */
            $io->writeln(
                sprintf(
                    'The lowest rate for currency %s is %d.',
                    $rate->getCurrency()->getName(),
                    $rate->getId()
                )
            );
        }

        foreach ($highestRates as $rate) {
            /** @var Rate $rate */
            $io->writeln(
                sprintf(
                    'The highest rate for currency %s is %d.',
                    $rate->getCurrency()->getName(),
                    $rate->getId()
                )
            );
        }

        $subscriber->sendRatesUpdatesEmailToActiveUsers($lowestRates, $highestRates);
    }
}
