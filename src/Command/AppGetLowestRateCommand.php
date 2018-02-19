<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use App\Service\Subscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppGetLowestRateCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:rates:get-lowest';

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
         * @var Subscriber $subscriber
         * @var RateRepository $rateRepository
         * @var CurrencyRepository $currencyRepository
         */
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $subscriber         = $this->getContainer()->get(Subscriber::class);
        $templating         = $this->getContainer()->get('twig');
        $rateRepository     = $em->getRepository(Rate::class);
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
        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            $rate = $rateRepository->getLowestByCurrency($currency, 'saleValue');
            if ($rate) {
                $io->writeln(
                    sprintf('The lowest rate for currency %s is %d.', $currency->getName(), $rate->getId())
                );
                $subscriber
                    ->sendEmailToUsers(
                        $subscriber->getActiveUsers(),
                        'Lowest rate',
                        'finance-application@disbalans.net',
                        $templating->render('email/rate/lowest.html.twig', compact('rate')),
                        'text/html'
                    );
            }
        }
        /*exit();
        if ($flushNeeded) {
            $io->writeln('Persisting data.');
            $em->flush();
        } else {
            $io->writeln('Nothing to do...');
        }*/
        $io->writeln('Done.');
    }
}
