<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::query()
            ->withCount('orders')
            ->when(request('q'), function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('display_name', 'like', "%{$q}%")
                        ->orWhere('member_code', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.members.form', ['member' => new Member]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        Member::create($request->validated() + [
            'member_code' => $this->generateMemberCode(),
            'is_active' => true,
        ]);

        session()->flash('status', 'Pelanggan berhasil ditambahkan.');

        return new RedirectResponse('/admin/members', 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['orders.batch.currentStatus', 'orders.overrideStatus', 'orders.items']);

        return view('admin.members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        return view('admin.members.form', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMemberRequest $request, Member $member)
    {
        $member->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        session()->flash('status', 'Pelanggan berhasil diperbarui.');

        return new RedirectResponse('/admin/members/'.$member->id, 303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->update(['is_active' => false]);

        return back()->with('status', 'Pelanggan dinonaktifkan.');
    }

    private function generateMemberCode(): string
    {
        do {
            $code = 'CUS-'.Str::upper(Str::random(8));
        } while (Member::where('member_code', $code)->exists());

        return $code;
    }
}
