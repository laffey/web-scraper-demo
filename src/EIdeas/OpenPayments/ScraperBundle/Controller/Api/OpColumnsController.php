<?php
namespace EIdeas\OpenPayments\ScraperBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OpColumnsController extends Controller {

    /**
     * Aggregate search column data for typeahead input
     *
     * @return JsonResponse
     */
    public function hintAction()
    {
        $opColumnQuery = $this->get('op_column_query');
        $columns = $opColumnQuery->getAggregateColumns();
        $searchHints = [];
        foreach ($columns as $columnName => $columnLabel) {
            $resultSet = $opColumnQuery->aggregate($columnName);
            if (!empty($resultSet)) {
                foreach ($resultSet as $aggregateInfo) {
                    $searchHints[] = array(
                        'val'       => $aggregateInfo['value'] . '',
                        'count'     => $aggregateInfo['count'],
                        'category'  => $columnName
                    );
                }
            }
        }


        $response = new JsonResponse();
        $response->setData(array(
            'search_hints' => $searchHints
        ));
        return $response;
    }

}