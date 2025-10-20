<?php
include_once(__DIR__ . '/../models/staff_dashboardModel.php');

if (isset($_GET['action']) && $_GET['action'] === 'get_dashboard_data_ajax') {
    header('Content-Type: application/json');
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
    $controller = new DashboardController();
    $data = $controller->getDashboardData($filter);
    echo json_encode($data);
    exit;
}

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
    }

    public function getDashboardData($filter = 'today') {
        $allowedFilters = ['today', 'yesterday', 'this_week', 'this_month', 'this_year', 'all_time'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'today';
        }

        $productSales = $this->dashboardModel->getTotalProductSalesAmount($filter);
        $serviceRevenue = $this->dashboardModel->getTotalServiceRevenue($filter);
        $totalSales = $productSales + $serviceRevenue;

        $pendingAppointmentsTodayCount = $this->dashboardModel->getPendingAppointmentsCount('today');

        return [
            'completedAppointments' => $this->dashboardModel->getCompletedAppointmentsCount($filter),
            'pendingAppointments' => $this->dashboardModel->getPendingAppointmentsCount($filter),
            'totalSales' => $totalSales,
            'totalRegisteredPets' => $this->dashboardModel->getTotalRegisteredPetsCount(),
            'productSalesForChart' => $this->dashboardModel->getProductSalesForChart($filter),
            'serviceSalesForChart' => $this->dashboardModel->getServiceSalesForChart($filter),
            'topProducts' => $this->dashboardModel->getTopProducts($filter),
            'topServices' => $this->dashboardModel->getTopServices($filter),
            'pendingAppointmentsDetails' => $this->dashboardModel->getPendingAppointmentsDetails($filter),
            'pendingAppointmentsTodayCount' => $pendingAppointmentsTodayCount,
            'filterDisplayName' => $this->getFilterDisplayName($filter)
        ];
    }
    
    private function getFilterDisplayName($filterValue) {
        switch ($filterValue) {
            case 'today': return 'Today';
            case 'yesterday': return 'Yesterday';
            case 'this_week': return 'This Week';
            case 'this_month': return 'This Month';
            case 'this_year': return 'This Year';
            case 'all_time': return 'All Time';
            default: return 'Today';
        }
    }
}