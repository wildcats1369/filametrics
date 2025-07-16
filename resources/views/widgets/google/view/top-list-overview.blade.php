@php
    $color = $this->getColor();
    $filters = $this->getFilters();
    $heading = $this->getHeading();
@endphp

<div
    class="fi-wi-stats-overview-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-4">
        <div class="relative space-y-2 max-auto">
            <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ---
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Count
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Changes
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($this->getCachedData() as $record)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($record['field'], 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $record['value'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex items-center gap-x-1">
                                                    <span @class([
                                                        'fi-wi-stats-overview-card-description text-sm font-medium',
                                                        match ($record['color']) {
                                                            'gray' => 'text-gray-500 dark:text-gray-400',
                                                            default => 'text-custom-600 dark:text-custom-500',
                                                        },
                                                    ]) @style([
                                                        \Filament\Support\get_color_css_variables($record['color'], shades: [500, 600]) => $record['color'] !== 'gray',
                                                    ])>
                                                        {{ $record['description'] }}
                                                    </span>
                                                    @php
                                                        $descriptionIconClasses = \Illuminate\Support\Arr::toCssClasses([
                                                            'fi-wi-stats-overview-card-description-icon h-5 w-5',
                                                            match ($record['color']) {
                                                                'gray' => 'text-gray-400 dark:text-gray-500',
                                                                default => 'text-custom-600 dark:text-custom-500',
                                                            },
                                                        ]);

                                                        $descriptionIconStyles = \Illuminate\Support\Arr::toCssStyles([
                                                            \Filament\Support\get_color_css_variables($record['color'], shades: [500, 600]) => $record['color'] !== 'gray',
                                                        ]);
                                                    @endphp
                                                    <x-filament::icon :icon="$record['icon']" :class="$descriptionIconClasses"
                                                        :style="$descriptionIconStyles" />
                                                </div>
                                            </td>
                                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>