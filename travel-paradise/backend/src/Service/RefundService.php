<?php

namespace App\Service;

use App\Entity\Visit;
use App\Entity\Refund;
use App\Repository\RefundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class RefundService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefundRepository $refundRepository,
        private NotificationService $notificationService,
        private Security $security
    ) {}

    public function calculateRefundAmount(Visit $visit): float
    {
        $now = new \DateTime();
        $visitDate = $visit->getDate();
        $daysUntilVisit = $now->diff($visitDate)->days;

        // Remboursement à 100% si plus de 7 jours avant la visite
        if ($daysUntilVisit > 7) {
            return $visit->getPrice();
        }

        // Remboursement à 50% si entre 3 et 7 jours avant la visite
        if ($daysUntilVisit >= 3) {
            return $visit->getPrice() * 0.5;
        }

        // Pas de remboursement si moins de 3 jours avant la visite
        return 0;
    }

    public function processRefund(Visit $visit, string $reason): Refund
    {
        $refundAmount = $this->calculateRefundAmount($visit);
        
        $refund = new Refund();
        $refund->setVisit($visit);
        $refund->setAmount($refundAmount);
        $refund->setReason($reason);
        $refund->setStatus('pending');
        $refund->setRequestedBy($this->security->getUser());
        $refund->setRequestedAt(new \DateTime());

        $this->entityManager->persist($refund);
        $this->entityManager->flush();

        // Envoyer une notification
        $this->notificationService->sendRefundRequestNotification($refund);

        return $refund;
    }

    public function approveRefund(Refund $refund): void
    {
        $refund->setStatus('approved');
        $refund->setProcessedAt(new \DateTime());
        $refund->setProcessedBy($this->security->getUser());

        // Marquer la visite comme annulée
        $visit = $refund->getVisit();
        $visit->setIsCancelled(true);
        $visit->setCancellationReason($refund->getReason());

        $this->entityManager->flush();

        // Envoyer une notification
        $this->notificationService->sendRefundApprovalNotification($refund);
    }

    public function rejectRefund(Refund $refund, string $reason): void
    {
        $refund->setStatus('rejected');
        $refund->setRejectionReason($reason);
        $refund->setProcessedAt(new \DateTime());
        $refund->setProcessedBy($this->security->getUser());

        $this->entityManager->flush();

        // Envoyer une notification
        $this->notificationService->sendRefundRejectionNotification($refund);
    }

    public function getRefundHistory(Visit $visit): array
    {
        return $this->refundRepository->findBy(['visit' => $visit], ['requestedAt' => 'DESC']);
    }

    public function getPendingRefunds(): array
    {
        return $this->refundRepository->findBy(['status' => 'pending'], ['requestedAt' => 'ASC']);
    }
} 