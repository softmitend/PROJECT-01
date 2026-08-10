<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusHistory;

class StatusHistoryController extends Controller
{
    public function __invoke()
    {
        $histories = StatusHistory::query()
            ->with(['trackable', 'oldStatus', 'newStatus', 'changedBy'])
            ->when(request('status_id'), function ($query, $statusId) {
                $query->where('old_status_id', $statusId)->orWhere('new_status_id', $statusId);
            })
            ->when(request('date_from'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when(request('date_to'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.histories.index', compact('histories'));
    }
}
