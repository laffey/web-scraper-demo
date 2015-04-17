<?php
namespace EIdeas\OpenPayments\ScraperBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use \EIdeas\OpenPayments\ScraperBundle\Document\Query\DoctorPaymentQuery;

class OpenPaymentsController extends Controller {

    /**
     * Paginate an unfiltered list of payments
     *
     * @param int $page
     * @return JsonResponse
     */
    public function listAction($page)
    {
        /**
         * @var DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->get('doctor_payment_query');
        $payments = $doctorPaymentQuery->all($page);
        $grandTotalCount = $doctorPaymentQuery->count();
        $paymentsFormatted = [];
        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\DoctorPayment $payment
         */
        foreach ($payments as $payment) {
            $paymentsFormatted[$payment->getGeneralTransactionId()] = $payment->toArray();
        }

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\OpColumnQuery $opColumnQuery
         */
        $opColumnQuery = $this->get('op_column_query');
        $columnHeaders = $opColumnQuery->getColumnsForTable();

        $response = new JsonResponse();
        $response->setData(array(
            'payments'          => $paymentsFormatted,
            'column_headers'    => $columnHeaders,
            'page'              => $page,
            'count'             => count($paymentsFormatted),
            'total_records'     => $grandTotalCount,
            'per_page'          => DoctorPaymentQuery::RECORDS_PER_PAGE
        ));
        return $response;
    }

    /**
     * look up matching results for a filter
     *
     * @param string $filterValue
     * @param string $filterCategory
     * @param int $page
     * @return JsonResponse
     */
    public function filterAction($filterValue, $filterCategory, $page)
    {
        //1. validate the category
        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\OpColumnQuery $opColumnQuery
         */
        $opColumnQuery = $this->get('op_column_query');
        try {
            $propertyName = $opColumnQuery->getModelPropertyName($filterCategory);
            $dataType = $opColumnQuery->getColumnDataType($filterCategory);
        } catch (\InvalidArgumentException $e) {
            //return with allowed search categories
            $response = new JsonResponse();
            $response->setStatusCode(406);
            $allowedCategories = $opColumnQuery->getAggregateColumns();
            $response->setData(array(
                'allowed_categories' => $allowedCategories,
            ));
            return $response;
        }

        //2. run the search
        /**
         * @var DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->get('doctor_payment_query');
        if ($dataType == 'number') {
            $filterValue = (int)$filterValue;
        }
        $payments = $doctorPaymentQuery->filter($propertyName, $filterValue, $page);
        $grandTotalCount = $doctorPaymentQuery->count($propertyName, $filterValue);

        $paymentsFormatted = [];
        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\DoctorPayment $payment
         */
        $numOfRecords = 0;
        foreach ($payments as $payment) {
            $paymentsFormatted[$payment->getGeneralTransactionId()] = $payment->toArray();
            $numOfRecords++;
        }
        if (!$numOfRecords) {
            //return with not found response
            $response = new JsonResponse();
            $response->setStatusCode(404);
            $response->setData(array(
                'result' => 'No matches found',
            ));
            return $response;
        }

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\OpColumnQuery $opColumnQuery
         */
        $opColumnQuery = $this->get('op_column_query');
        $columnHeaders = $opColumnQuery->getColumnsForTable();

        $response = new JsonResponse();
        $response->setData(array(
            'payments'          => $paymentsFormatted,
            'column_headers'    => $columnHeaders,
            'page'              => $page,
            'count'             => $numOfRecords,
            'total_records'     => $grandTotalCount,
            'per_page'          => DoctorPaymentQuery::RECORDS_PER_PAGE
        ));
        return $response;
    }

}