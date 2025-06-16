<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Visit;
use App\Entity\Refund;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly string $appUrl
    ) {}

    public function sendVisitConfirmation(Visit $visit): void
    {
        $user = $visit->getTourists()->first();
        if (!$user) {
            return;
        }

        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.visit.confirmation.subject'))
            ->html($this->renderVisitConfirmationTemplate($visit));

        $this->mailer->send($email);
    }

    public function sendVisitReminder(Visit $visit): void
    {
        $user = $visit->getTourists()->first();
        if (!$user) {
            return;
        }

        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.visit.reminder.subject'))
            ->html($this->renderVisitReminderTemplate($visit));

        $this->mailer->send($email);
    }

    public function sendVisitCancellation(Visit $visit, ?string $reason = null): void
    {
        $user = $visit->getTourists()->first();
        if (!$user) {
            return;
        }

        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.visit.cancellation.subject'))
            ->html($this->renderVisitCancellationTemplate($visit, $reason));

        $this->mailer->send($email);
    }

    public function sendScheduleChange(Visit $visit, \DateTimeInterface $oldDate): void
    {
        $user = $visit->getTourists()->first();
        if (!$user) {
            return;
        }

        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.visit.schedule_change.subject'))
            ->html($this->renderScheduleChangeTemplate($visit, $oldDate));

        $this->mailer->send($email);
    }

    public function sendRefundRequestNotification(Refund $refund): void
    {
        $user = $refund->getRequestedBy();
        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.refund.request.subject'))
            ->html($this->renderRefundRequestTemplate($refund));

        $this->mailer->send($email);
    }

    public function sendRefundApprovalNotification(Refund $refund): void
    {
        $user = $refund->getRequestedBy();
        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.refund.approval.subject'))
            ->html($this->renderRefundApprovalTemplate($refund));

        $this->mailer->send($email);
    }

    public function sendRefundRejectionNotification(Refund $refund): void
    {
        $user = $refund->getRequestedBy();
        $email = (new Email())
            ->from('noreply@travel-paradise.com')
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.refund.rejection.subject'))
            ->html($this->renderRefundRejectionTemplate($refund));

        $this->mailer->send($email);
    }

    private function renderVisitConfirmationTemplate(Visit $visit): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $visit->getId()]);
        
        return $this->translator->trans('email.visit.confirmation.body', [
            '%visit_date%' => $visit->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $visit->getPlace()->getName(),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderVisitReminderTemplate(Visit $visit): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $visit->getId()]);
        
        return $this->translator->trans('email.visit.reminder.body', [
            '%visit_date%' => $visit->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $visit->getPlace()->getName(),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderVisitCancellationTemplate(Visit $visit, ?string $reason): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $visit->getId()]);
        
        return $this->translator->trans('email.visit.cancellation.body', [
            '%visit_date%' => $visit->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $visit->getPlace()->getName(),
            '%reason%' => $reason ?? $this->translator->trans('email.visit.cancellation.no_reason'),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderScheduleChangeTemplate(Visit $visit, \DateTimeInterface $oldDate): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $visit->getId()]);
        
        return $this->translator->trans('email.visit.schedule_change.body', [
            '%old_date%' => $oldDate->format('d/m/Y H:i'),
            '%new_date%' => $visit->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $visit->getPlace()->getName(),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderRefundRequestTemplate(Refund $refund): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $refund->getVisit()->getId()]);
        
        return $this->translator->trans('email.refund.request.body', [
            '%visit_date%' => $refund->getVisit()->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $refund->getVisit()->getPlace()->getName(),
            '%amount%' => number_format($refund->getAmount(), 2),
            '%reason%' => $refund->getReason(),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderRefundApprovalTemplate(Refund $refund): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $refund->getVisit()->getId()]);
        
        return $this->translator->trans('email.refund.approval.body', [
            '%visit_date%' => $refund->getVisit()->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $refund->getVisit()->getPlace()->getName(),
            '%amount%' => number_format($refund->getAmount(), 2),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function renderRefundRejectionTemplate(Refund $refund): string
    {
        $visitUrl = $this->generateAbsoluteUrl('visit_show', ['id' => $refund->getVisit()->getId()]);
        
        return $this->translator->trans('email.refund.rejection.body', [
            '%visit_date%' => $refund->getVisit()->getDate()->format('d/m/Y H:i'),
            '%place_name%' => $refund->getVisit()->getPlace()->getName(),
            '%reason%' => $refund->getRejectionReason(),
            '%visit_url%' => $visitUrl
        ]);
    }

    private function generateAbsoluteUrl(string $route, array $parameters = []): string
    {
        return $this->appUrl.$this->urlGenerator->generate($route, $parameters);
    }
}