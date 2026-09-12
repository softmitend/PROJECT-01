@props(['name' => 'grid', 'size' => 20])

<svg {{ $attributes->merge(['class' => 'public-icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('home')<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/>@break
        @case('bag')
            @if(request()->routeIs('home'))
                <image href="{{ asset('img/ocean-paws-mascot.jpg') }}" x="3.5" y="1" width="17" height="22" preserveAspectRatio="xMidYMid meet" />
            @else
                <path d="M6 8h12l-1 12H7L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            @endif
            @break
        @case('card')<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/>@break
        @case('grid')<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>@break
        @case('user')<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>@break
        @case('arrow-left')<path d="m15 18-6-6 6-6"/>@break
        @case('arrow-right')<path d="m9 18 6-6-6-6"/>@break
        @case('chevron-right')<path d="m9 18 6-6-6-6"/>@break
        @case('settings')<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1V21h-4v-.09a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1-.4H3v-4h.09a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1V3h4v.09a1.7 1.7 0 0 0 1.1 1.5 1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.12.36.33.7.6 1 .28.27.62.48 1 .6h.09v4H21a1.7 1.7 0 0 0-1.6.4Z"/>@break
        @case('link')<path d="M10 13a5 5 0 0 0 7.07.07l2-2a5 5 0 0 0-7.07-7.07l-1.15 1.15"/><path d="M14 11a5 5 0 0 0-7.07-.07l-2 2A5 5 0 0 0 12 20l1.15-1.15"/>@break
        @case('wallet')<path d="M20 7V5a2 2 0 0 0-2-2H5a3 3 0 0 0 0 6h15v10a2 2 0 0 1-2 2H5a3 3 0 0 1-3-3V6"/><path d="M16 13h4"/>@break
        @case('history')<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>@break
        @case('truck')<path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>@break
        @case('swap')<path d="M7 7h12l-3-3"/><path d="m19 7-3 3"/><path d="M17 17H5l3 3"/><path d="m5 17 3-3"/>@break
        @case('gift')<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13M3 12h18"/><path d="M7.5 8C5.5 8 5 6.5 5.8 5.4 7 3.8 10 5 12 8c2-3 5-4.2 6.2-2.6C19 6.5 18.5 8 16.5 8"/>@break
        @case('sparkle')<path d="m12 3 1.3 4.2L17.5 8.5l-4.2 1.3L12 14l-1.3-4.2-4.2-1.3 4.2-1.3L12 3Z"/><path d="m19 14 .7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14Z"/><path d="m5 15 .7 2.3L8 18l-2.3.7L5 21l-.7-2.3L2 18l2.3-.7L5 15Z"/>@break
        @case('box')<path d="m21 8-9 5-9-5 9-5 9 5Z"/><path d="m3 8 9 5 9-5v8l-9 5-9-5V8Z"/><path d="M12 13v8"/>@break
        @case('calendar')<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>@break
        @case('document')<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>@break
        @case('check-circle')<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>@break
        @case('upload')<path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M5 14v5h14v-5"/>@break
        @case('plus')<path d="M12 5v14M5 12h14"/>@break
        @case('minus')<path d="M5 12h14"/>@break
        @case('close')<path d="m6 6 12 12M18 6 6 18"/>@break
        @case('message')<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/>@break
        @case('search')<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>@break
        @case('radio')<circle cx="6" cy="12" r="1.4"/><path d="M9 9.2a4 4 0 0 1 0 5.6M12 6.5a7.8 7.8 0 0 1 0 11"/>@break
        @case('send')<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>@break
        @default<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>
    @endswitch
</svg>
