<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;
use Exception;

class SearchHistoryUserRepository extends EntityRepository
{
    public function addToSearchHistory(string $search, string $user_uuid): void
    {
        try {
            $user = $this->getEntityManager()->getRepository(User::class)->find($user_uuid);

            $searchHistory = new SearchHistoryUser();
            $searchHistory->setUser($user);
            $searchHistory->setSearch($search);
            $searchHistory->setDate(new DateTime("now", new DateTimeZone('Europe/Paris')));
            $searchHistory->setIsActive(true);

            $this->getEntityManager()->persist($searchHistory);
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new EndpointException("Error adding search history: " . $e->getMessage(), 500);
        }
    }

    public function getSearchHistory(string $user_uuid): array
    {
        try {
            $user = $this->getEntityManager()->getRepository(User::class)->find($user_uuid);

            return $this->createQueryBuilder('sh')
                ->select('sh')
                ->where('sh.user = :user')
                ->andWhere('sh.isActive = 1')
                ->setParameter('user', $user)
                ->orderBy('sh.date', 'DESC')
                ->getQuery()
                ->getResult();

        } catch (Exception $e) {
            throw new EndpointException("Error getting search history: " . $e->getMessage(), 500);
        }
    }

    public function clearSearchHistory(string $user_uuid): void
    {
        try {
            $user = $this->getEntityManager()->getRepository(User::class)->find($user_uuid);

            $searchHistory = $this->findBy(['user' => $user]);

            foreach ($searchHistory as $history) {
                $history->setIsActive(false);
                $this->getEntityManager()->persist($history);
            }

            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new EndpointException("Error deleting search history: " . $e->getMessage(), 500);
        }
    }

    public function removeSearchHistoryById(string $user_uuid, int $searchHistoryId,): void
    {
        try {
            $user = $this->getEntityManager()->getRepository(User::class)->find($user_uuid);
            $searchHistory = $this->findOneBy(['id' => $searchHistoryId, 'user' => $user]);

            if ($searchHistory === null) {
                throw new EndpointException("Search history not found", 404);
            }

            $searchHistory->setIsActive(false);
            $this->getEntityManager()->persist($searchHistory);
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new EndpointException("Error deleting search history: " . $e->getMessage(), 500);
        }
    }

    public function toJson(SearchHistoryUser $searchHistory): array
    {
        return [
            'id' => $searchHistory->getId(),
            'search' => $searchHistory->getSearch(),
            'date' => $searchHistory->getDate()->getTimestamp()
        ];
    }
}