@props([
    'title' => 'Important: Assignment Submission',
    'body' => 'Please submit your assignment before the deadline. Check the instructions carefully and make sure your file is complete and readable. Late submissions may not be accepted.',
    'details' => [],
    'action' => 'Upload your assignment now and confirm the file opens correctly before submitting.',
    'deadline' => 'Deadline: 25 April 2026, 5:00 PM sharp',
    'icon' => 'bullhorn',
    'tone' => 'warning',
    'label' => 'Student Notice',
    'ctaLabel' => 'Send Announcement',
    'ctaHref' => route('admin.messaging.send-form'),
    'showCta' => true,
    'showAction' => true,
    'showDeadline' => true,
])

@php
    $toneStyles = [
        'warning' => [
            'wrapper' => 'background: linear-gradient(180deg, #0f172a 0%, #111827 100%); border: 1px solid rgba(245, 158, 11, 0.28); box-shadow: 0 20px 60px rgba(2, 6, 23, 0.34);',
            'eyebrow' => 'background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.24); color: #fde68a;',
            'accent' => '#fbbf24',
            'action' => 'background: rgba(245, 158, 11, 0.10); border: 1px solid rgba(245, 158, 11, 0.22); color: #fef3c7;',
            'deadline' => 'background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.26); color: #fef3c7;',
            'button' => 'background: #f59e0b; color: #0f172a; border: 1px solid #f59e0b;',
        ],
        'success' => [
            'wrapper' => 'background: linear-gradient(180deg, #0f172a 0%, #111827 100%); border: 1px solid rgba(34, 197, 94, 0.28); box-shadow: 0 20px 60px rgba(2, 6, 23, 0.34);',
            'eyebrow' => 'background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.24); color: #bbf7d0;',
            'accent' => '#86efac',
            'action' => 'background: rgba(34, 197, 94, 0.10); border: 1px solid rgba(34, 197, 94, 0.22); color: #dcfce7;',
            'deadline' => 'background: rgba(34, 197, 94, 0.14); border: 1px solid rgba(34, 197, 94, 0.26); color: #dcfce7;',
            'button' => 'background: #22c55e; color: #052e16; border: 1px solid #22c55e;',
        ],
        'info' => [
            'wrapper' => 'background: linear-gradient(180deg, #0f172a 0%, #111827 100%); border: 1px solid rgba(56, 189, 248, 0.28); box-shadow: 0 20px 60px rgba(2, 6, 23, 0.34);',
            'eyebrow' => 'background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.24); color: #bae6fd;',
            'accent' => '#7dd3fc',
            'action' => 'background: rgba(56, 189, 248, 0.10); border: 1px solid rgba(56, 189, 248, 0.22); color: #e0f2fe;',
            'deadline' => 'background: rgba(56, 189, 248, 0.14); border: 1px solid rgba(56, 189, 248, 0.26); color: #e0f2fe;',
            'button' => 'background: #38bdf8; color: #082f49; border: 1px solid #38bdf8;',
        ],
    ];

    $variant = $toneStyles[$tone] ?? $toneStyles['warning'];
@endphp

<section style="border-radius: 24px; padding: 24px; backdrop-filter: blur(18px); {{ $variant['wrapper'] }}">
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div style="display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;">
            <div style="flex:0 0 auto; width:56px; height:56px; display:flex; align-items:center; justify-content:center; border-radius:18px; {{ $variant['eyebrow'] }}">
                <i class="fa-solid fa-{{ $icon }}" style="font-size:20px; color: {{ $variant['accent'] }};"></i>
            </div>
            <div style="min-width:0; flex:1 1 280px;">
                <div style="display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:800; letter-spacing:0.12em; text-transform:uppercase; {{ $variant['eyebrow'] }}">
                    <i class="fa-solid fa-circle-info"></i>
                    {{ $label }}
                </div>
                <h3 style="margin-top:14px; color:#f8fafc; font-size:28px; line-height:1.15; font-weight:800;">{{ $title }}</h3>
                <p style="margin-top:10px; color:#cbd5e1; font-size:15px; line-height:1.7;">{{ $body }}</p>
            </div>
        </div>

        @if (! empty($details))
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
                @foreach ($details as $detail)
                    <div style="border-radius:20px; padding:16px; background:rgba(15, 23, 42, 0.92); border:1px solid rgba(51,65,85,0.95);">
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <div style="flex:0 0 auto; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:14px; background:rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-{{ $detail['icon'] ?? 'circle-info' }}" style="font-size:15px; color:#e2e8f0;"></i>
                            </div>
                            <div style="min-width:0;">
                                <p style="color:#94a3b8; font-size:11px; font-weight:800; letter-spacing:0.14em; text-transform:uppercase;">
                                    {{ $detail['label'] ?? 'Detail' }}
                                </p>
                                <p style="margin-top:4px; color:#f8fafc; font-size:14px; line-height:1.5; font-weight:600;">
                                    {{ $detail['value'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($showAction)
            <div style="border-radius:20px; padding:16px 18px; {{ $variant['action'] }}">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <i class="fa-solid fa-exclamation-triangle" style="margin-top:3px; color:#fbbf24;"></i>
                    <div style="min-width:0;">
                        <p style="color:#ffffff; font-size:15px; font-weight:800; line-height:1.4;">Action Required</p>
                        <p style="margin-top:4px; color:#e2e8f0; font-size:14px; line-height:1.7;">{{ $action }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($showDeadline || $showCta)
            <div style="display:flex; flex-direction:row; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
                @if ($showDeadline)
                    <div style="display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:10px 14px; font-size:14px; font-weight:700; {{ $variant['deadline'] }}">
                        <i class="fa-regular fa-clock"></i>
                        <span>{{ $deadline }}</span>
                    </div>
                @endif
                @if ($showCta)
                    <a href="{{ $ctaHref }}" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:14px; padding:12px 16px; font-size:14px; font-weight:800; text-decoration:none; {{ $variant['button'] }}">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ $ctaLabel }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
