<?php

namespace App\Http\Controllers;

use App\Services\AdminService;

class AdminController extends Controller
{
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function generateProductSalesReports()
    {
        $this->adminService->generateProductSalesReports();
        return response()->json(['message' => 'Product sales report generation initiated.']);
    }
}
