<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberHistoryLookupRequest;
use App\Http\Requests\TrackingLookupRequest;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class MemberTrackingController extends Controller
{
    public function home()
    {
        $testimonialSlides = Testimonial::query()
            ->where('is_published', true)
            ->with(['member', 'orderItem.order.batch'])
            ->latest()
            ->take(9)
            ->get()
            ->map(fn (Testimonial $testimonial) => [
                'image' => $testimonial->photo_url
                    ?: $testimonial->orderItem?->order?->batch?->catalog_image_url
                    ?: '/assets/testimonial-1.png',
                'content' => $testimonial->content,
                'name' => $testimonial->member?->display_name ?: 'Customer Ocean Paws',
                'rating' => $testimonial->rating,
            ]);

        if ($testimonialSlides->isEmpty()) {
            $testimonialSlides = collect([
                [
                    'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=80',
                    'content' => 'Album datang dengan aman dan packing-nya rapi banget. Update selama proses GO juga jelas, jadi nggak perlu khawatir menunggu.',
                    'name' => 'Alya',
                    'rating' => 5,
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
                    'content' => 'Photocard sampai tanpa lecet dan kondisinya sesuai deskripsi. Bakal ikut group order di Ocean Paws lagi!',
                    'name' => 'Nadira',
                    'rating' => 5,
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
                    'content' => 'Prosesnya transparan dari pembayaran sampai pengiriman. Barang juga tiba lebih cepat dari perkiraanku.',
                    'name' => 'Keisha',
                    'rating' => 5,
                ],
            ]);
        }

        return view('tracking.index', compact('testimonialSlides'));
    }

    public function index()
    {
        return view('tracking.search');
    }

    public function smartLookup(Request $request)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ], [
            'query.required' => 'Masukkan kode tracking atau username LINE pelanggan.',
        ]);

        $rawQuery = trim($validated['query']);
        $orderCode = mb_strtoupper($rawQuery);

        // Coba sebagai kode tracking terlebih dahulu. Ini menghindari tebakan format
        // karena kode tracking dan username LINE sama-sama berupa teks.
        $order = MemberOrder::query()
            ->where('order_code', $orderCode)
            ->whereHas('member', fn ($member) => $member->where('is_active', true))
            ->with([
                'member',
                'batch.currentStatus',
                'batch.statusHistories.oldStatus',
                'batch.statusHistories.newStatus',
                'overrideStatus',
                'paymentStatus',
                'items.overrideStatus',
                'statusHistories.oldStatus',
                'statusHistories.newStatus',
            ])
            ->first();

        if ($order) {
            return view('tracking.search', [
                'searchType' => 'tracking',
                'searchQuery' => $orderCode,
                'orderResult' => $order,
                'timeline' => $this->timelineFor($order),
            ]);
        }

        $username = mb_strtolower(ltrim($rawQuery, '@'));
        $member = Member::query()
            ->where('username', $username)
            ->where('is_active', true)
            ->with([
                'orders' => fn ($orders) => $orders->latest(),
                'orders.batch.currentStatus',
                'orders.overrideStatus',
                'orders.paymentStatus',
                'orders.items.overrideStatus',
            ])
            ->first();

        if ($member) {
            return view('tracking.search', [
                'searchType' => 'username',
                'searchQuery' => $username,
                'memberResult' => $member,
            ]);
        }

        return back()
            ->withErrors(['query' => 'Kode tracking atau username LINE tidak ditemukan. Periksa kembali data dari admin.'])
            ->onlyInput('query');
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

        return new RedirectResponse(URL::temporarySignedRoute(
            'tracking.progress',
            now()->addMinutes(15),
            ['orderCode' => $order->order_code]
        ), 303);
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
                'paymentStatus',
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
            ->where('username', $request->validated('username'))
            ->where('is_active', true)
            ->first();

        if (! $member) {
            return back()
                ->withErrors(['username' => 'Username LINE tidak ditemukan pada data pelanggan.'])
                ->onlyInput('username');
        }

        return new RedirectResponse(URL::temporarySignedRoute(
            'tracking.member',
            now()->addMinutes(15),
            ['memberCode' => $member->member_code]
        ), 303);
    }

    public function member(string $memberCode)
    {
        $member = Member::where('member_code', $memberCode)
            ->where('is_active', true)
            ->with(['orders' => fn ($query) => $query->latest(), 'orders.batch.currentStatus', 'orders.overrideStatus', 'orders.paymentStatus', 'orders.items'])
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
            'paymentStatus',
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
        $refundHistory = $order->statusHistories
            ->first(fn ($history) => $history->newStatus?->code === 'refunded');

        $batchHistories = $order->batch->statusHistories;
        if ($refundHistory) {
            $batchHistories = $batchHistories
                ->filter(fn ($history) => $history->created_at->lt($refundHistory->created_at)
                    || ($history->created_at->equalTo($refundHistory->created_at) && $history->id < $refundHistory->id));
        }

        $timeline = $batchHistories
            ->concat($order->statusHistories)
            ->sortByDesc('created_at')
            ->values();

        if ($refundHistory) {
            $refundHistory->setRelation('newStatus', $order->tracking_status);
        }

        return $timeline;
    }
}
