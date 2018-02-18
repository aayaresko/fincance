<?php

namespace App\Command;

use App\Container\Finance\CurrencyContainer;
use App\Entity\Currency;
use App\Entity\Organization;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use App\Service\FinanceService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppRatesFetchCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:rates:fetch';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
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
            $message = 'Fetching rates for all currencies';
        } else {
            $message = sprintf('Fetching rates for currencies: %s', implode(',', $currenciesIdentifiers));
        }
        $io->comment($message);
        /**
         * @var EntityManager $em
         * @var FinanceService $service
         * @var OrganizationRepository $organizationRepository
         * @var CurrencyRepository $currencyRepository
         */
        $em                     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $service                = $this->getContainer()->get(FinanceService::class);
        $currencyRepository     = $em->getRepository(Currency::class);
        $organizationRepository = $em->getRepository(Organization::class);
        $qb                     = $currencyRepository
            ->createQueryBuilder('currency')
            ->select('c')
            ->from(Currency::class, 'c', 'c.code');
        if (!empty($currenciesIdentifiers)) {
            $qb->where(
                $qb->expr()->in('c.id', $currenciesIdentifiers)
            );
        }
        $data          = [];
        $organizations = $organizationRepository->findAll();
        foreach ($organizations as $organization) {
            /** @var Organization $organization */
            $data[$organization->getExternalIdentifier()] = $organization;
        }
        $organizations = $data;
        $currencies    = $qb->getQuery()->execute();
        $codes         = array_map(
            function (Currency $currency) {
                return $currency->getCode();
            },
            $currencies
        );
        $flushNeeded   = false;
        $rates         = $service->getRates();
        foreach ($rates as $organizationIdentifier => $item) {
            /** @var CurrencyContainer $item */
            if (!isset($organizations[$organizationIdentifier])) {
                $io->writeln(
                    sprintf('Organization with external identifier %s does not exist!', $organizationIdentifier)
                );
                continue;
            }
            if (in_array($item->code, $codes)) {
                $io->writeln(
                    sprintf(
                        'New rate will be added: sale %s, by %s, currency %s',
                        $item->saleValue,
                        $item->buyValue,
                        $item->code
                    )
                );
                $entity = new Rate();
                $entity->setBuyValue($item->buyValue);
                $entity->setSaleValue($item->saleValue);
                $currency     = $currencies[$item->code];
                $organization = $organizations[$organizationIdentifier];
                $entity->setCurrency($currency);
                $entity->setOrganization($organization);
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
