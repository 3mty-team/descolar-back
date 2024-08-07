<?php

namespace Descolar\Endpoints\Search;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\App;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class SearchHistoryEndpoint extends AbstractEndpoint
{
    #[Get('/search/history', name: 'getSearchHistory', auth: true)]
    #[OA\Get(path: "/search/history", summary: "getSearchHistory", tags: ["Search"], responses: [new OA\Response(response: 200, description: "History retrieved")])]
    private function getSearchHistory(): void
    {
        $this->reply(function ($response) {

            /** @var SearchHistoryUser[] $searches */
            $searches = OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->getSearchHistory();

            $data = [];
            foreach ($searches as $search) {
                $data[] = OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->toJson($search);
            }

            $response->addData('searches', $data);
        });
    }

    #[Delete('/search/history/:id', variables: ["id" => RouteParam::NUMBER], name: 'removeSearchHistory', auth: true)]
    #[OA\Delete(path: "/search/history/{id}", summary: "removeSearchHistory", tags: ["Search"], parameters: [new PathParameter("id", "id", "Search History ID", required: true)], responses: [new OA\Response(response: 200, description: "History removed")])]
    private function removeSearchHistory(int $id): void
    {
        $this->reply(function ($response) use ($id){
            $searchHistoryId = OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->removeSearchHistoryById($id);

            $response->addData('id', $searchHistoryId);
        });
    }

    #[Delete('/search/history/clear', name: 'clearSearchHistory', auth: true)]
    #[OA\Delete(path: "/search/history/clear", summary: "clearSearchHistory", tags: ["Search"], responses: [new OA\Response(response: 200, description: "History cleared")])]
    private function clearSearchHistory(): void
    {
        $this->reply(function ($response){
            OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->clearSearchHistory();

            $response->addData('clearSearchHistory', true);
        });
    }
}