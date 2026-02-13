<div>
    @if($hasRating)
        <div class="space-y-3">
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $rating)
                        <x-heroicon-s-star class="h-5 w-5 text-yellow-400" />
                    @else
                        <x-heroicon-o-star class="h-5 w-5 text-gray-300 dark:text-gray-600" />
                    @endif
                @endfor
                <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ $rating }}/5
                </span>
            </div>

            @if($comment)
                <div class="text-sm text-gray-600 dark:text-gray-400 italic">
                    "{{ $comment }}"
                </div>
            @endif
        </div>
    @else
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <x-heroicon-o-star class="h-4 w-4" />
                <span>{{ __('escalated-filament::filament.livewire.rating.no_rating') }}</span>
            </div>
        </div>
    @endif
</div>
