<x-layouts.app title="Kelola Group">
    <x-page-heading title="Kelola Group" description="Label customer yang tersedia pada registrasi. Group tidak menentukan hak akses akun.">
        <x-slot:action><a class="admin-primary-action" href="{{ route('admin.customer-groups.create') }}">+ Tambah group</a></x-slot:action>
    </x-page-heading>
    <div class="order-table-card">
        @include('admin.groups.partials.table')
        <div class="order-table-footer">{{ $groups->links() }}</div>
    </div>
</x-layouts.app>
