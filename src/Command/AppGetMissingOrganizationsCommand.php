<?php

namespace App\Command;

use App\Container\Finance\OrganizationContainer;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Service\FinanceService;
use App\Service\OrganizationService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppGetMissingOrganizationsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:organizations:get-missing';

    protected function configure()
    {
        $this
            ->setDescription('Get missing organizations.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('Fetching organizations.');
        /**
         * @var EntityManager $em
         * @var FinanceService $financeService
         * @var OrganizationService $organizationService
         * @var OrganizationRepository $organizationRepository
         */
        $em                     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $financeService         = $this->getContainer()->get(FinanceService::class);
        $organizationService    = $this->getContainer()->get(OrganizationService::class);
        $organizationRepository = $em->getRepository(Organization::class);
        $organizations          = $organizationRepository->findAll();
        $identifiers            = array_map(
            function (Organization $organization) {
                return $organization->getExternalIdentifier();
            },
            $organizations
        );
        $flushNeeded = false;
        $data        = $financeService->getAvailableOrganizations();
        foreach ($data as $item) {
            /** @var OrganizationContainer $item */
            if (!in_array($item->id, $identifiers)) {
                if (!in_array($item->oldId, $identifiers)) {
                    $io->writeln(sprintf('New organization will be added with identifier %s.', $item->id));
                    $organizationService->create($item, $item->getType(), false);
                    $flushNeeded = true;
                } else {
                    $io->writeln(
                        sprintf(
                            'Organization identifier has been changed from %d to %d.',
                            $item->oldId,
                            $item->id
                        )
                    );
                }
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
