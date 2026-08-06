<x-layouts.app title="Tracking Pesanan">
    <div class="mx-auto max-w-2xl">
        <x-page-heading title="Tracking Pesanan Jajanan" description="Masukkan kode member, username, atau access code untuk melihat batch dan status pesanan." />
        <form method="POST" action="/tracking" class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
            @csrf
            <x-text-input label="Kode member, username, atau access code" name="lookup" required />
            <button class="mt-4 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white" type="submit">Cari Pesanan</button>
        </form>
    </div>
</x-layouts.app>
