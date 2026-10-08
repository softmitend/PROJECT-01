<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterCustomerRequest;
use App\Models\CustomerGroup;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisteredCustomerController extends Controller
{
    public function create()
    {
        return view('auth.register', [
            'groups' => CustomerGroup::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(RegisterCustomerRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data): void {
            $group = CustomerGroup::whereKey($data['customer_group_id'])->lockForUpdate()->first();
            if (! $group?->is_active) {
                throw ValidationException::withMessages(['customer_group_id' => 'Group tidak tersedia. Pilih group aktif.']);
            }
            $member = Member::create([
                'member_code' => 'CUS-'.Str::upper(Str::random(12)),
                'display_name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'customer_group_id' => $data['customer_group_id'],
                'is_active' => true,
            ]);
            $user = new User([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'member_id' => $member->id,
            ]);
            $user->role = 'customer';
            $user->password_set_at = now();
            $user->save();
        });

        return redirect()->route('login')->with('status', 'Akun berhasil dibuat. Silakan login menggunakan username dan password.');
    }
}
