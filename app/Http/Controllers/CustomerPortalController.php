<?php

namespace App\Http\Controllers;

use App\Models\MemberOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CustomerPortalController extends Controller
{
    public function billing(Request $request)
    {
        $member = $request->user()?->member;
        $orders = $member
            ? $member->orders()->with(['batch', 'paymentStatus'])->latest()->get()
            : collect();

        $unpaidCount = $orders
            ->filter(fn ($order) => in_array($order->paymentStatus?->code, ['menunggu-dp', 'menunggu-pelunasan'], true))
            ->count();
        $emsCount = $orders->where('ems_tax_status', 'unpaid')->count();

        return view('portal.overview', [
            'type' => 'billing',
            'title' => 'Tagihan & Pembayaran',
            'description' => 'Pantau tagihan pesanan, Pajak/EMS, dan riwayat pembayaranmu dari satu tempat.',
            'items' => [
                [
                    'icon' => 'card',
                    'title' => 'Tagihan Pesanan',
                    'description' => 'Lihat DP atau pelunasan batch yang masih menunggu pembayaran.',
                    'note' => $member ? $unpaidCount.' belum dibayar' : 'Login untuk melihat',
                    'url' => route('billing.orders'),
                ],
                [
                    'icon' => 'document',
                    'title' => 'Tagihan EMS & Pajak',
                    'description' => 'Periksa ongkir EMS dan pajak impor beserta rincian tiap pesanan.',
                    'note' => $member ? $emsCount.' tagihan aktif' : 'Rincian transparan',
                    'url' => route('billing.ems'),
                ],
            ],
        ]);
    }

    public function billingOrders(Request $request)
    {
        return $this->billingList($request, 'orders');
    }

    public function billingEms(Request $request)
    {
        return $this->billingList($request, 'ems');
    }

    public function services()
    {
        return view('portal.overview', [
            'type' => 'services',
            'title' => 'Layanan Pesanan',
            'description' => 'Pilih layanan yang kamu butuhkan untuk mulai jajan atau memantau pesanan.',
            'items' => [
                [
                    'icon' => 'search',
                    'title' => 'Lacak Pesanan',
                    'description' => 'Cari status terkini menggunakan kode pesanan atau username LINE.',
                    'note' => 'Data real-time',
                    'url' => route('tracking.index'),
                ],
                [
                    'icon' => 'history',
                    'title' => 'Riwayat Pesanan',
                    'description' => 'Lihat seluruh batch, item, pembayaran, dan pengiriman milikmu.',
                    'note' => 'Tersimpan di profil',
                    'url' => route('orders.history'),
                ],
            ],
        ]);
    }

    public function history(Request $request)
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
                $order->setAttribute('history_tracking_url', URL::temporarySignedRoute(
                    'tracking.order',
                    now()->addMinutes(30),
                    ['memberCode' => $member->member_code, 'memberOrder' => $order]
                ));
            });
        }

        $filter = $request->string('filter')->toString();
        $filter = in_array($filter, ['unpaid', 'active', 'history', 'shipping', 'refund'], true)
            ? $filter
            : 'all';

        $filteredOrders = $filter === 'all'
            ? $orders
            : $orders->filter(fn ($order) => in_array($filter, $this->orderGroups($order), true))->values();

        return view('portal.order-history', [
            'user' => $user,
            'member' => $member,
            'orders' => $filteredOrders,
            'allOrderCount' => $orders->count(),
            'filter' => $filter,
        ]);
    }

    private function orderGroups(MemberOrder $order): array
    {
        $trackingStatus = $order->tracking_status;

        return array_values(array_filter([
            in_array($order->paymentStatus?->code, ['menunggu-dp', 'menunggu-pelunasan'], true) ? 'unpaid' : null,
            ! $trackingStatus?->is_final && ! $order->is_refunded ? 'active' : null,
            $trackingStatus?->code === 'selesai' ? 'history' : null,
            $trackingStatus?->code === 'dikirim-ke-customer' ? 'shipping' : null,
            $order->is_refunded ? 'refund' : null,
        ]));
    }

    private function billingList(Request $request, string $scope)
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
                    'items',
                ])
                ->latest()
                ->get();

            $orders->each(function ($order) use ($member): void {
                $order->setAttribute('billing_tracking_url', URL::temporarySignedRoute(
                    'tracking.order',
                    now()->addMinutes(30),
                    ['memberCode' => $member->member_code, 'memberOrder' => $order]
                ));
            });
        }

        $tab = $request->string('tab')->toString() === 'history' ? 'history' : 'unpaid';

        if ($scope === 'ems') {
            $unpaidOrders = $orders->filter(fn ($order) => $order->ems_tax_is_published && $order->ems_tax_status === 'unpaid')->values();
            $historyOrders = $orders->filter(fn ($order) => $order->ems_tax_is_published && $order->ems_tax_status === 'paid')->values();
            $title = 'Tagihan EMS & Pajak';
            $description = 'Rincian ongkir internasional dan pajak impor dipisahkan dari riwayat pesananmu.';
        } else {
            $unpaidOrders = $orders
                ->filter(fn ($order) => in_array($order->paymentStatus?->code, ['menunggu-dp', 'menunggu-pelunasan'], true))
                ->values();
            $historyOrders = $orders->filter(fn ($order) => $order->payment_submitted_at !== null)->values();
            $title = 'Tagihan Pesanan';
            $description = 'Cek pembayaran yang masih perlu diselesaikan dan bukti pembayaran yang pernah kamu kirim.';
        }

        return view('portal.billing-list', [
            'user' => $user,
            'member' => $member,
            'scope' => $scope,
            'tab' => $tab,
            'title' => $title,
            'description' => $description,
            'orders' => $tab === 'history' ? $historyOrders : $unpaidOrders,
            'unpaidCount' => $unpaidOrders->count(),
            'historyCount' => $historyOrders->count(),
        ]);
    }
}
