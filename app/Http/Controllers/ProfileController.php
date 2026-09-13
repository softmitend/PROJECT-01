<?php

namespace App\Http\Controllers;

use App\Models\MemberOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $member = $user?->member;
        $orders = collect();

        if ($member) {
            $orders = $member->orders()
                ->with([
                    'batch.currentStatus',
                    'overrideStatus',
                    'paymentStatus',
                    'items.overrideStatus',
                ])
                ->latest()
                ->get();

            $orders->each(function ($order) use ($member): void {
                $order->setAttribute('profile_tracking_url', URL::temporarySignedRoute(
                    'tracking.order',
                    now()->addMinutes(30),
                    ['memberCode' => $member->member_code, 'memberOrder' => $order]
                ));
            });
        }

        $stats = [
            'unpaid' => $orders->filter(fn ($order) => in_array($order->paymentStatus?->code, ['menunggu-dp', 'menunggu-pelunasan'], true))->count(),
            'ems' => $orders->filter(fn ($order) => $order->ems_tax_is_published && $order->ems_tax_status === 'unpaid')->count(),
            'history' => $orders->filter(fn ($order) => $order->effective_status?->code === 'selesai')->count(),
            'shipping' => $orders->filter(fn ($order) => $order->effective_status?->code === 'dikirim-ke-customer')->count(),
            'refund' => $orders->filter(fn ($order) => $order->is_refunded)->count(),
        ];

        $summary = [
            'orders' => $orders->count(),
            'items' => $orders->sum(fn ($order) => $order->items->sum('quantity')),
            'order_value' => $orders->sum(fn ($order) => (float) ($order->total_amount
                ?? $order->items->sum('subtotal'))),
            'payment_submitted' => $orders->sum(fn ($order) => (float) ($order->payment_amount ?? 0)),
            'ems_outstanding' => $orders
                ->where('ems_tax_status', 'unpaid')
                ->sum(fn ($order) => (float) ($order->ems_tax_amount ?? 0)),
        ];

        return view('profile.show', [
            'user' => $user,
            'member' => $member,
            'orders' => $orders,
            'stats' => $stats,
            'summary' => $summary,
            'lineConfigured' => filled(config('services.line.channel_id')) && filled(config('services.line.channel_secret')),
        ]);
    }

    public function paymentProof(Request $request, MemberOrder $memberOrder)
    {
        abort_unless(
            $request->user()?->member_id
            && $memberOrder->member_id === $request->user()->member_id,
            403
        );

        abort_unless(
            $memberOrder->payment_proof_path
            && Storage::disk('local')->exists($memberOrder->payment_proof_path),
            404
        );

        return Storage::disk('local')->response($memberOrder->payment_proof_path);
    }
}
