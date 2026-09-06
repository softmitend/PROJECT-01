<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Member;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $member = $this->member($request);
        $eligibleItems = collect();
        $reviewedItems = collect();

        if ($member) {
            $items = OrderItem::query()
                ->whereHas('order', fn ($query) => $query->where('member_id', $member->id))
                ->with([
                    'order.batch.currentStatus',
                    'order.overrideStatus',
                    'order.paymentStatus',
                    'testimonial',
                ])
                ->latest()
                ->get()
                ->reject(fn (OrderItem $item) => $item->order->is_refunded)
                ->values();

            $eligibleItems = $items->filter(fn (OrderItem $item) => ! $item->testimonial)->values();
            $reviewedItems = $items->filter(fn (OrderItem $item) => (bool) $item->testimonial)->values();
        }

        return view('testimonials.index', [
            'member' => $member,
            'eligibleItems' => $eligibleItems,
            'reviewedItems' => $reviewedItems,
            'lineConfigured' => filled(config('services.line.channel_id')) && filled(config('services.line.channel_secret')),
        ]);
    }

    public function create(Request $request, OrderItem $orderItem): View|RedirectResponse
    {
        $member = $this->member($request);

        if (! $member) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Masuk dengan LINE dulu untuk menulis testimoni.']);
        }

        $orderItem->load(['order.batch', 'order.overrideStatus', 'order.paymentStatus', 'testimonial']);
        $this->ensureOwnership($member, $orderItem);

        if ($orderItem->order->is_refunded) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Pesanan yang sudah direfund tidak dapat diberi testimoni.']);
        }

        if ($orderItem->testimonial) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Jajanan ini sudah pernah kamu beri testimoni.']);
        }

        return view('testimonials.create', compact('member', 'orderItem'));
    }

    public function store(StoreTestimonialRequest $request, OrderItem $orderItem): RedirectResponse
    {
        $member = $this->member($request);

        if (! $member) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Masuk dengan LINE dulu untuk menulis testimoni.']);
        }

        $orderItem->load(['order.overrideStatus', 'order.paymentStatus', 'testimonial']);
        $this->ensureOwnership($member, $orderItem);

        if ($orderItem->order->is_refunded) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Pesanan yang sudah direfund tidak dapat diberi testimoni.']);
        }

        if ($orderItem->testimonial) {
            return redirect()->route('testimonials.index')
                ->withErrors(['testimonial' => 'Jajanan ini sudah pernah kamu beri testimoni.']);
        }

        $validated = $request->validated();
        $photoPath = $request->file('photo')?->store('testimonials', 'public');

        $orderItem->testimonial()->create([
            'member_id' => $member->id,
            'rating' => $validated['rating'],
            'content' => trim($validated['content']),
            'photo_path' => $photoPath,
            'photo_disk' => 'public',
            'is_published' => true,
        ]);

        return redirect()->route('testimonials.index')
            ->with('status', 'Testimonimu sudah tersimpan. Terima kasih sudah berbagi cerita!');
    }

    private function member(Request $request): ?Member
    {
        return $request->user()?->member;
    }

    private function ensureOwnership(Member $member, OrderItem $orderItem): void
    {
        abort_unless($orderItem->order && $orderItem->order->member_id === $member->id, 403);
    }
}
