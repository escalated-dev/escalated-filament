<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Plugin Stats Summary --}}
        @php
            $plugins = \Escalated\Laravel\Models\Plugin::all();
            $totalCount = $plugins->count();
            $activeCount = $plugins->where('is_active', true)->count();
            $inactiveCount = $totalCount - $activeCount;
        @endphp

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Plugins</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $activeCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Active</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $inactiveCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Inactive</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Plugins Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
