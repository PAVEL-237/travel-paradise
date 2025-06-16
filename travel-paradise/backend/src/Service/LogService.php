<?php

namespace App\Service;

use App\Entity\Log;
use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class LogService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LogRepository $logRepository,
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function log(string $action, string $entity, ?int $entityId = null, array $data = []): void
    {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $log = new Log();
        $log->setAction($action);
        $log->setEntity($entity);
        $log->setEntityId($entityId);
        $log->setData($data);
        $log->setUser($user);
        $log->setIpAddress($request?->getClientIp());
        $log->setUserAgent($request?->headers->get('User-Agent'));
        $log->setCreatedAt(new \DateTime());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function getLogs(array $filters = [], int $page = 1, int $limit = 50): array
    {
        $qb = $this->logRepository->createQueryBuilder('l')
            ->orderBy('l.createdAt', 'DESC');

        // Filtres
        if (!empty($filters['action'])) {
            $qb->andWhere('l.action = :action')
               ->setParameter('action', $filters['action']);
        }

        if (!empty($filters['entity'])) {
            $qb->andWhere('l.entity = :entity')
               ->setParameter('entity', $filters['entity']);
        }

        if (!empty($filters['entityId'])) {
            $qb->andWhere('l.entityId = :entityId')
               ->setParameter('entityId', $filters['entityId']);
        }

        if (!empty($filters['user'])) {
            $qb->andWhere('l.user = :user')
               ->setParameter('user', $filters['user']);
        }

        if (!empty($filters['startDate'])) {
            $qb->andWhere('l.createdAt >= :startDate')
               ->setParameter('startDate', new \DateTime($filters['startDate']));
        }

        if (!empty($filters['endDate'])) {
            $qb->andWhere('l.createdAt <= :endDate')
               ->setParameter('endDate', new \DateTime($filters['endDate']));
        }

        // Pagination
        $total = $this->getTotalResults($qb);
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return [
            'logs' => $qb->getQuery()->getResult(),
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ];
    }

    private function getTotalResults($qb): int
    {
        $countQb = clone $qb;
        $countQb->select('COUNT(l.id)');
        return (int) $countQb->getQuery()->getSingleScalarResult();
    }

    public function getLogStats(): array
    {
        return [
            'by_action' => $this->logRepository->getLogsByAction(),
            'by_entity' => $this->logRepository->getLogsByEntity(),
            'by_user' => $this->logRepository->getLogsByUser(),
            'by_date' => $this->logRepository->getLogsByDate()
        ];
    }

    public function clearOldLogs(int $days = 90): int
    {
        $date = new \DateTime("-$days days");
        return $this->logRepository->deleteLogsOlderThan($date);
    }

    public function exportLogs(array $filters = []): array
    {
        $logs = $this->getLogs($filters, 1, PHP_INT_MAX)['logs'];
        $export = [];

        foreach ($logs as $log) {
            $export[] = [
                'id' => $log->getId(),
                'action' => $log->getAction(),
                'entity' => $log->getEntity(),
                'entityId' => $log->getEntityId(),
                'data' => $log->getData(),
                'user' => $log->getUser() ? $log->getUser()->getEmail() : null,
                'ipAddress' => $log->getIpAddress(),
                'userAgent' => $log->getUserAgent(),
                'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $export;
    }
} 