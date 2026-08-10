@props(['title'])

<section {{ $attributes->merge(['class' => 'admin-form-section']) }}>
    <h2 class="admin-form-section-title">{{ $title }}</h2>
    {{ $slot }}
</section>
