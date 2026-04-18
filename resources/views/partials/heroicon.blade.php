@php
    $classes = trim('sidebar-link__icon ' . ($class ?? ''));
@endphp

@if ($name === 'home')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 10.5L12 3l9 7.5" />
        <path d="M5 10v10h14V10" />
    </svg>
@elseif ($name === 'school')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 10.5 12 5l8 5.5" />
        <path d="M6 9.5V19h12V9.5" />
        <path d="M9 19v-5h6v5" />
    </svg>
@elseif ($name === 'calendar')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="5" width="18" height="16" rx="2" />
        <path d="M8 3v4M16 3v4M3 11h18" />
    </svg>
@elseif ($name === 'clipboard')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="8" y="3" width="8" height="4" rx="1.5" />
        <path d="M9 5h6" />
        <path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
        <path d="M8 12h8M8 16h8" />
    </svg>
@elseif ($name === 'book')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20v16H6.5A2.5 2.5 0 0 0 4 22V6.5Z" />
        <path d="M8 8h8M8 12h8M8 16h5" />
    </svg>
@elseif ($name === 'chart')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19.5h16" />
        <path d="M7 17V11" />
        <path d="M12 17V7" />
        <path d="M17 17v-4" />
    </svg>
@elseif ($name === 'bell')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M15 17H9a3 3 0 0 1-3-3V11a6 6 0 1 1 12 0v3a3 3 0 0 1-3 3Z" />
        <path d="M10 19a2 2 0 0 0 4 0" />
    </svg>
@elseif ($name === 'user')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21a8 8 0 0 0-16 0" />
        <circle cx="12" cy="8" r="4" />
    </svg>
@elseif ($name === 'logout')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 17l5-5-5-5" />
        <path d="M15 12H4" />
        <path d="M20 4v16" />
    </svg>
@elseif ($name === 'menu')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16M4 12h16M4 18h16" />
    </svg>
@elseif ($name === 'chevron-down')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6" />
    </svg>
@elseif ($name === 'drawer')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16" />
        <path d="M4 12h16" />
        <path d="M4 18h10" />
    </svg>
@elseif ($name === 'calculator')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="5" y="3" width="14" height="18" rx="2" />
        <path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M16 15h0M8 19h2M12 19h2M16 19h0" />
    </svg>
@elseif ($name === 'receipt')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 3h12v18l-2-1.5L14 21l-2-1.5L10 21l-2-1.5L6 21V3Z" />
        <path d="M9 8h6M9 12h6M9 16h3" />
    </svg>
@elseif ($name === 'wallet')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 7a2 2 0 0 1 2-2h14v4H5a2 2 0 0 1-2-2Z" />
        <path d="M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
        <circle cx="16" cy="12" r="1.5" />
    </svg>
@elseif ($name === 'building')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 21h16" />
        <path d="M6 21V5l6-2 6 2v16" />
        <path d="M10 9h4M10 13h4M10 17h4" />
    </svg>
@elseif ($name === 'document')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M7 3h7l5 5v13H7z" />
        <path d="M14 3v5h5" />
        <path d="M9 12h6M9 16h6" />
    </svg>
@elseif ($name === 'layers')
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="m12 4 8 4-8 4-8-4 8-4Z" />
        <path d="m4 12 8 4 8-4" />
        <path d="m4 16 8 4 8-4" />
    </svg>
@else
    <svg class="{{ $classes }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="9" />
    </svg>
@endif
