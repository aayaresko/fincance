<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use App\Service\AverageRateService;
use App\Service\Subscriber;
use Doctrine\ORM\EntityManager;
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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $currenciesIdentifiers = $input->getArgument('currenciesIdentifiers');
        if (empty($currenciesIdentifiers)) {
            $message = 'Checking lowest rates for all currencies';
        } else {
            $message = sprintf('Checking lowest rates for currencies: %s', implode(',', $currenciesIdentifiers));
        }
        $io->comment($message);
        /**
         * @var EntityManager $em
         * @var AverageRateService $averageRateService
         * @var RateRepository $rateRepository
         * @var CurrencyRepository $currencyRepository
         */
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $rateRepository     = $em->getRepository(Rate::class);
        $averageRateService = $this->getContainer()->get(AverageRateService::class);
        $currencyRepository = $em->getRepository(Currency::class);
        if (!empty($currenciesIdentifiers)) {
            $qb         = $currencyRepository->createQueryBuilder('c');
            $currencies = $qb
                ->where(
                    $qb->expr()->in('c.id', $currenciesIdentifiers)
                )
                ->getQuery()
                ->execute();
        } else {
            $currencies = $currencyRepository->findAll();
        }
        $created = 0;
        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            $averageRate = $averageRateService->createFromRateIfNotExist($currency, AverageRateService::TYPE_SALE);
            if ($averageRate) {
                $rate = $rateRepository->getLowestSaleByCurrency($currency);
                $this->sendEmail($io, 'email/rate/sale/lowest.html.twig', $rate);
                $created++;
            }
            $averageRate = $averageRateService->createFromRateIfNotExist($currency, AverageRateService::TYPE_BUY);
            if ($averageRate) {
                $rate = $rateRepository->getHighestBuyByCurrency($currency);
                $this->sendEmail($io, 'email/rate/buy/highest.html.twig', $rate);
                $created++;
            }
        }
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

    private function sendEmail(SymfonyStyle $io, $template, $rate = null)
    {
        /**
         * @var Subscriber $subscriber
         */
        $subscriber = $this->getContainer()->get(Subscriber::class);
        $templating = $this->getContainer()->get('twig');
        if ($rate instanceof Rate) {
            $io->writeln(
                sprintf(
                    'The lowest rate for currency %s is %d.',
                    $rate->getCurrency()->getName(),
                    $rate->getId()
                )
            );
            $subscriber
                ->sendEmailToUsers(
                    $subscriber->getActiveUsers(),
                    'Lowest rate',
                    'finance-application@disbalans.net',
                    $templating->render($template, compact('rate')),
                    'text/html'
                );
        }
    }
}
