<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberHistoryLookupRequest;
use App\Http\Requests\TrackingLookupRequest;
use App\Models\Member;
use App\Models\MemberOrder;
use Illuminate\Http\RedirectResponse;

class MemberTrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function lookup(TrackingLookupRequest $request)
    {
        $lookup = $request->validated('lookup');

        $order = MemberOrder::query()
            ->where('order_code', $lookup)
            ->whereHas('member', fn ($query) => $query->where('is_active', true))
            ->first();

        if (! $order) {
            return back()->withErrors(['lookup' => 'Kode pesanan tidak ditemukan. Periksa kembali kode dari admin.'])->onlyInput('lookup');
        }

        return new RedirectResponse(route('tracking.progress', $order->order_code), 303);
    }

    public function progress(string $orderCode)
    {
        $order = MemberOrder::query()
            ->where('order_code', $orderCode)
            ->whereHas('member', fn ($query) => $query->where('is_active', true))
            ->with([
                'member',
                'batch.currentStatus',
                'batch.statusHistories.oldStatus',
                'batch.statusHistories.newStatus',
                'overrideStatus',
                'items.overrideStatus',
                'statusHistories.oldStatus',
                'statusHistories.newStatus',
            ])
            ->firstOrFail();

        return view('tracking.progress', [
            'order' => $order,
            'timeline' => $this->timelineFor($order),
        ]);
    }

    public function historyLookup(MemberHistoryLookupRequest $request)
    {
        $member = Member::query()
            ->where('email', $request->validated('email'))
            ->where('is_active', true)
            ->first();

        if (! $member) {
            return back()
                ->withErrors(['email' => 'Email tidak ditemukan pada data pelanggan.'])
                ->onlyInput('email');
        }

        return new RedirectResponse(route('tracking.member', $member->member_code), 303);
    }

    public function member(string $memberCode)
    {
        $member = Member::where('member_code', $memberCode)
            ->where('is_active', true)
            ->with(['orders' => fn ($query) => $query->latest(), 'orders.batch.currentStatus', 'orders.overrideStatus', 'orders.items'])
            ->firstOrFail();

        return view('tracking.member', compact('member'));
    }

    public function order(string $memberCode, MemberOrder $memberOrder)
    {
        $member = Member::where('member_code', $memberCode)->where('is_active', true)->firstOrFail();

        abort_unless($memberOrder->member_id === $member->id, 403);

        $memberOrder->load([
            'member',
            'batch.currentStatus',
            'batch.statusHistories.oldStatus',
            'batch.statusHistories.newStatus',
            'overrideStatus',
            'items.overrideStatus',
            'statusHistories.oldStatus',
            'statusHistories.newStatus',
        ]);

        return view('tracking.order', [
            'member' => $member,
            'order' => $memberOrder,
            'timeline' => $this->timelineFor($memberOrder),
        ]);
    }

    private function timelineFor(MemberOrder $order)
    {
        return $order->batch->statusHistories
            ->concat($order->statusHistories)
            ->sortByDesc('created_at')
            ->values();
    }
}
