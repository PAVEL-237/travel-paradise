<?php

namespace App\Command;

use App\Entity\Visit;
use App\Repository\VisitRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-visit-reminders',
    description: 'Envoie des rappels pour les visites à venir'
)]
class SendVisitRemindersCommand extends Command
{
    public function __construct(
        private VisitRepository $visitRepository,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Envoi des rappels de visite');

        // Récupérer les visites dans les prochaines 24h
        $tomorrow = new \DateTime('+24 hours');
        $visits = $this->visitRepository->findUpcomingVisits($tomorrow);

        $io->progressStart(count($visits));

        foreach ($visits as $visit) {
            try {
                $this->notificationService->sendVisitReminder($visit);
                $io->progressAdvance();
            } catch (\Exception $e) {
                $io->error(sprintf(
                    'Erreur lors de l\'envoi du rappel pour la visite %d : %s',
                    $visit->getId(),
                    $e->getMessage()
                ));
            }
        }

        $io->progressFinish();
        $io->success('Rappels envoyés avec succès');

        return Command::SUCCESS;
    }
} 