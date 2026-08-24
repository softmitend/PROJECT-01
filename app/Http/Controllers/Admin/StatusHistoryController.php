<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusHistory;
use Illuminate\Http\Request;

class StatusHistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $histories = StatusHistory::query()
            ->with(['trackable', 'oldStatus', 'newStatus', 'changedBy'])
            ->when(request('status_id'), function ($query, $statusId) {
                $query->where('old_status_id', $statusId)->orWhere('new_status_id', $statusId);
            })
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.histories.index', compact('histories'));
    }
}
