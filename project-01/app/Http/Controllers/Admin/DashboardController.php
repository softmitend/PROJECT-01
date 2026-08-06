<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\StatusHistory;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'activeMembers' => Member::where('is_active', true)->count(),
            'activeBatches' => Batch::where('is_archived', false)->count(),
            'ordersCount' => MemberOrder::count(),
            'completedOrders' => MemberOrder::whereHas('overrideStatus', fn ($query) => $query->where('status_type', 'success'))->count(),
            'processingOrders' => MemberOrder::whereHas('batch.currentStatus', fn ($query) => $query->where('status_type', 'process'))->count(),
            'problemOrders' => MemberOrder::whereHas('overrideStatus', fn ($query) => $query->whereIn('status_type', ['failed', 'cancelled']))->count(),
            'latestBatches' => Batch::with('currentStatus')->latest()->limit(5)->get(),
            'latestHistories' => StatusHistory::with(['trackable', 'oldStatus', 'newStatus', 'changedBy'])->latest()->limit(8)->get(),
        ]);
    }
}
