<x-filament-panels::page>
    {{-- Date Range Filter --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    @php
        $stats = $this->getStats();
        $ticketsByDepartment = $this->getTicketsByDepartment();
        $ticketsOverTime = $this->getTicketsOverTime();
    @endphp

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_tickets'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Tickets</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['resolved_tickets'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Resolved</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['resolution_rate'] }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Resolution Rate</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['avg_response_time'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Response</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['avg_resolution_time'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Resolution</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['csat_average'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">CSAT Average</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Tickets by Department --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <x-filament::section>
            <x-slot name="heading">Tickets by Department</x-slot>
            @if(count($ticketsByDepartment) > 0)
                <div class="space-y-3">
                    @foreach($ticketsByDepartment as $dept)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $dept['name'] }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    @php
                                        $maxCount = collect($ticketsByDepartment)->max('count');
                                        $pct = $maxCount > 0 ? ($dept['count'] / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 w-10 text-right">{{ $dept['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No data for selected period.</p>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Tickets Over Time</x-slot>
            @if(count($ticketsOverTime) > 0)
                <div class="space-y-2">
                    @foreach($ticketsOverTime as $entry)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($entry['date'])->format('M j') }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    @php
                                        $maxDaily = collect($ticketsOverTime)->max('count');
                                        $pctDaily = $maxDaily > 0 ? ($entry['count'] / $maxDaily) * 100 : 0;
                                    @endphp
                                    <div class="bg-info-500 h-2 rounded-full" style="width: {{ $pctDaily }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 w-10 text-right">{{ $entry['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No data for selected period.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
