<?php
include_once(__DIR__ . '/../models/admin_dashboardModel.php');

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
            'pendingAppointmentsTodayCount' => $pendingAppointmentsTodayCount
        ];
    }
}