<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;
use Exception;

class SearchHistoryUserRepository extends EntityRepository
{

    public function findById(int $searchHistoryId): SearchHistoryUser
    {
        $searchHistory = $this->find($searchHistoryId);

        if ($searchHistory === null || !$searchHistory->isActive()) {
            throw new EndpointException('Search history not found', 404);
        }

        return $searchHistory;
    }

    public function findByIdAndUser(int $searchHistoryId, User $user): SearchHistoryUser
    {
        $searchHistory = $this->findById($searchHistoryId);

        if ($searchHistory->getUser() !== $user) {
            throw new EndpointException('Search history not found', 404);
        }

        return $searchHistory;
    }

    public function findByUser(User $user): array
    {
        $searchHistory = $this->findBy(['user' => $user]);

        if (empty($searchHistory)) {
            throw new EndpointException('Search history not found', 404);
        }

        return $searchHistory;
    }

    public function addToSearchHistory(string $search): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->getLoggedUser();

        $searchHistory = new SearchHistoryUser();
        $searchHistory->setUser($user);
        $searchHistory->setSearch($search);
        $searchHistory->setDate(new DateTime("now", new DateTimeZone('Europe/Paris')));
        $searchHistory->setIsActive(true);

        Validator::getInstance($searchHistory)->check();

        OrmConnector::getInstance()->persist($searchHistory);
        OrmConnector::getInstance()->flush();
    }

    public function getSearchHistory(): array
    {
        $user = $this->getEntityManager()->getRepository(User::class)->getLoggedUser();

        return $this->createQueryBuilder('sh')
            ->select('sh')
            ->where('sh.user = :user')
            ->andWhere('sh.isActive = 1')
            ->setParameter('user', $user)
            ->orderBy('sh.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function clearSearchHistory(): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->getLoggedUser();

        $searchHistory = $this->findByUser($user);

        foreach ($searchHistory as $history) {
            $history->setIsActive(false);

            Validator::getInstance($history)->check();

            $this->getEntityManager()->persist($history);
        }

        $this->getEntityManager()->flush();
    }

    public function removeSearchHistoryById(int $searchHistoryId): int
    {
        $user = $this->getEntityManager()->getRepository(User::class)->getLoggedUser();
        $searchHistory = $this->findByIdAndUser($searchHistoryId, $user);

        $searchHistory->setIsActive(false);

        Validator::getInstance($searchHistory)->check();
        $this->getEntityManager()->persist($searchHistory);
        $this->getEntityManager()->flush();

        return $searchHistoryId;
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