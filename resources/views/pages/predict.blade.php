<x-filament::page>
    <style>
        .text-custom-green {
            color: rgba(34, 197, 94, 1);
        }

        .text-custom-red {
            color: rgba(239, 68, 68, 1);
        }

        .text-custom-amber {
            color: rgba(251, 191, 36, 1);
        }

        .text-custom-blue-dark {
            color: rgba(59, 130, 246, 1);
        }

        .bg-custom-light {
            background-color: rgba(249, 250, 251, 1);
        }

        .border-custom-gray {
            border-color: rgba(156, 163, 175, 1);
        }

        .icon {
            width: 1rem;
            height: 1rem;
            margin-left: 0.5rem;
            vertical-align: middle;
            display: inline-block;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .icon-question {
            fill: currentColor;
        }

        .card {
            position: relative;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid rgba(156, 163, 175, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            background-color: white;
            overflow: hidden;
        }

        .card.ok {
            background-color: #fff;
            /*#ecfdf5*/
        }

        .card.warning {
            background-color: #fff7ed;
        }

        .card.critical {
            background-color: #fee2e2;
        }

        .status-overlay {
            position: absolute;
            bottom: 1rem;
            right: 1.5rem;
            font-size: 3rem;
            font-weight: 800;
            opacity: 0.08;
            color: #000;
            pointer-events: none;
            user-select: none;
        }

        .status-icon {
            position: absolute;
            top: 0.75rem;
            right: 1rem;
            font-size: 3rem;
            opacity: 0.1;
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }
    </style>

    @php
        $predictions = $data['prediction'] ?? [];

        function badgeClasses($class)
        {
            return match ($class) {
                'increase' => 'text-custom-green dark:text-custom-blue-dark bg-custom-light',
                'decrease' => 'text-custom-red dark:text-custom-blue-dark bg-custom-light',
                'same' => 'text-custom-amber dark:text-custom-blue-dark bg-custom-light',
                default => 'text-custom-blue-dark bg-custom-light',
            };
        }

        function trendIcon($class)
        {
            return match ($class) {
                'increase' => '<svg class="icon" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 6-8" /><path d="M14 7h6v6" /></svg>',
                'decrease' => '<svg class="icon" viewBox="0 0 24 24"><path d="M3 7l6 6 4-4 6 8" /><path d="M14 17h6v-6" /></svg>',
                'same' => '<svg class="icon" viewBox="0 0 24 24"><line x1="4" y1="12" x2="20" y2="12" /></svg>',
                default => '<svg class="icon icon-question" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="none" /><text x="12" y="16" font-size="12" text-anchor="middle" fill="currentColor" font-family="Arial" font-weight="bold">?</text></svg>',
            };
        }

        $allErrored = collect($predictions)->every(fn ($v) => isset($v['error']));
    @endphp

    <h2 class="text-2xl font-bold mb-6 text-custom-green dark:text-custom-blue-dark">Predict Filametrics Site</h2>
    <p class="text-lg font-semibold text-custom-green mb-8">{{ $domain }}/ <span
            class="text-custom-blue-dark">Prediction</span></p>

    @if ($predictions && $allErrored)
        <div class="p-6 rounded-2xl bg-yellow-100 text-yellow-900 border border-yellow-300">
            ⚠️ Predictor models not found for <strong>{{ $domain }}</strong>.<br>
            Please ensure this site is set up properly on the Predictor API.
        </div>
    @elseif ($predictions)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($predictions as $metric => $values)
                @if (! isset($values['error']))
                    @php
                        $status = strtoupper($values['status'] ?? 'OK');
                        $class = match ($status) {
                            'CRITICAL' => 'critical',
                            'WARNING' => 'warning',
                            default => 'ok',
                        };

                        $icon = match ($status) {
                            'CRITICAL' => '🚨',
                            'WARNING' => '⚠️',
                            default => '✔️',
                        };
                    @endphp

                    <div class="card {{ $class }}">
                        <div class="status-icon">{{ $icon }}</div>
                        <div class="status-overlay">{{ $status }}</div>

                        <h4 class="text-lg font-semibold capitalize mb-4 text-custom-green dark:text-custom-blue-dark">
                            {{ str_replace('_', ' ', $metric) }}
                        </h4>

                        <p class="text-gray-600 mb-2">
                            <span class="font-semibold">Previous:</span> {{ $values['prev'] }}
                            <span class="mx-2 font-bold">→</span>
                            <span class="font-semibold">Next:</span> {{ $values['next'] }}
                        </p>

                        <p class="text-gray-500 mb-2">
                            <span class="font-semibold">Prediction Date:</span> {{ $values['prediction_date'] }}
                        </p>

                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ badgeClasses($values['prediction_class']) }}">
                            {{ ucfirst($values['prediction_class']) }}
                            {!! trendIcon($values['prediction_class']) !!}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No prediction data found.</p>
    @endif
</x-filament::page>