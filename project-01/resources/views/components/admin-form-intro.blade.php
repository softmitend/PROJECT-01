@props(['title', 'description'])

<div class="admin-form-intro">
    <span class="admin-form-intro-icon">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
    </span>
    <div>
        <h2>{{ $title }}</h2>
        <p>{{ $description }}</p>
    </div>
</div>
