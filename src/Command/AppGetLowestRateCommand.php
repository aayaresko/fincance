<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
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
         * @var RateRepository $rateRepository
         * @var CurrencyRepository $currencyRepository
         */
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $rateRepository     = $em->getRepository(Rate::class);
        $currencyRepository = $em->getRepository(Currency::class);
        $templating         = $this->getContainer()->get('twig');
        $mailer             = $this->getContainer()->get('mailer');
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
                $message = (new \Swift_Message('Lowest rate'));
                $message
                    ->setFrom('finance-application@disbalans.net')
                    ->setTo('aayaresko@gmail.com')
                    ->setBody(
                        $templating->render('email/rate/lowest.html.twig', compact('rate')),
                        'text/html'
                    );
                $mailer->send($message);
            }
        }
        $io->writeln('Done.');
    }
}
