<?php

include_once(__DIR__ . '/../models/admin_salesReportModel.php');
include_once(__DIR__ . '/../models/admin_dashboardModel.php');

class SalesReportController {
    private $salesReportModel;
    private $dashboardModel;

    public function __construct() {
        $this->salesReportModel = new SalesReportModel();
        $this->dashboardModel = new DashboardModel();
    }

     private function validateFilter($filter, &$startDate, &$endDate) {
         $allowedFilters = ['today', 'yesterday', 'last_7_days', 'last_15_days', 'last_30_days', 'this_week', 'this_month', 'this_year', 'all_time', 'custom'];
         if (!in_array($filter, $allowedFilters)) {
             $filter = 'all_time';
         }
         
         if ($filter === 'custom') {
             if (!strtotime($startDate) || !strtotime($endDate)) {
                 $filter = 'all_time';
                 $startDate = null;
                 $endDate = null;
             }
         } else {
             $startDate = null;
             $endDate = null;
         }
         return $filter;
     }

    public function getSalesReportData($filter = 'all_time', $startDate = null, $endDate = null) {
        $filter = $this->validateFilter($filter, $startDate, $endDate);

        $totalSales = $this->salesReportModel->getTotalSalesAmount($filter, $startDate, $endDate);
        $averageSales = $this->salesReportModel->getAverageSalesAmount($filter, $startDate, $endDate);
        $completedAppointments = $this->dashboardModel->getCompletedAppointmentsCount($filter, $startDate, $endDate);
        $totalProductsSold = $this->salesReportModel->getTotalProductsSoldCount($filter, $startDate, $endDate);

        $productSalesForChart = $this->salesReportModel->getProductSalesForChart($filter, $startDate, $endDate);
        $serviceSalesForChart = $this->salesReportModel->getServiceSalesForChart($filter, $startDate, $endDate);

        $salesHistoryTransactions = $this->salesReportModel->getSalesHistoryTransactions($filter, $startDate, $endDate);

        return [
            'filter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'averageSales' => $averageSales,
            'completedAppointments' => $completedAppointments,
            'totalProductsSold' => $totalProductsSold,
            'productSalesForChart' => $productSalesForChart,
            'serviceSalesForChart' => $serviceSalesForChart,
            'salesHistoryTransactions' => $salesHistoryTransactions,
        ];
    }

     public function getProductSaleDetails($saleId) {
         return $this->salesReportModel->getProductSaleDetails($saleId);
     }

      public function getServiceSaleDetails($transactionId) {
          return $this->salesReportModel->getServiceSaleDetails($transactionId);
      }

     public function handleRequest() {
         if (isset($_GET['action']) && ($_GET['action'] === 'getProductSaleDetails' || $_GET['action'] === 'getServiceSaleDetails')) {
             header('Content-Type: application/json');
             switch ($_GET['action']) {
                 case 'getProductSaleDetails':
                     $saleId = $_GET['saleId'] ?? null;
                     if ($saleId) {
                         $details = $this->getProductSaleDetails($saleId);
                         echo json_encode($details);
                     } else {
                         http_response_code(400);
                         echo json_encode(['error' => 'Sale ID not provided.']);
                     }
                     break;
                  case 'getServiceSaleDetails':
                      $transactionId = $_GET['transactionId'] ?? null;
                      if ($transactionId) {
                          $details = $this->getServiceSaleDetails($transactionId);
                          echo json_encode($details);
                      } else {
                          http_response_code(400);
                          echo json_encode(['error' => 'Transaction ID is missing.']);
                      }
                      break;
             }
             exit; 
         }
     }
}

if (isset($_GET['action']) && ($_GET['action'] === 'getProductSaleDetails' || $_GET['action'] === 'getServiceSaleDetails')) {
    $controller = new SalesReportController();
    $controller->handleRequest();
}