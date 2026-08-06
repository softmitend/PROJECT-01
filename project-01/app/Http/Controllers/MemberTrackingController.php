<?php

namespace App\Http\Controllers;

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

        $member = Member::query()
            ->where('is_active', true)
            ->where(function ($query) use ($lookup) {
                $query->where('member_code', $lookup)
                    ->orWhere('username', $lookup)
                    ->orWhere('access_code', $lookup);
            })
            ->first();

        if (! $member) {
            return back()->withErrors(['lookup' => 'Data member tidak ditemukan.'])->onlyInput('lookup');
        }

        return new RedirectResponse('/tracking/'.$member->member_code, 303);
    }

    public function member(string $memberCode)
    {
        $member = Member::where('member_code', $memberCode)
            ->where('is_active', true)
            ->with(['orders.batch.currentStatus', 'orders.overrideStatus', 'orders.items'])
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
            'overrideStatus',
            'items.overrideStatus',
            'statusHistories.oldStatus',
            'statusHistories.newStatus',
        ]);

        return view('tracking.order', ['member' => $member, 'order' => $memberOrder]);
    }
}
