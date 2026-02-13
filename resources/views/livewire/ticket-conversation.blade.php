<div class="space-y-6">
    {{-- Pinned Notes --}}
    @if($pinnedNotes->isNotEmpty())
        <div class="rounded-lg border border-warning-300 bg-warning-50 dark:border-warning-600 dark:bg-warning-900/20 p-4">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-s-bookmark class="h-4 w-4 text-warning-500" />
                <h4 class="text-sm font-semibold text-warning-700 dark:text-warning-400">{{ __('escalated-filament::filament.livewire.conversation.pinned_notes') }}</h4>
            </div>
            <div class="space-y-2">
                @foreach($pinnedNotes as $note)
                    <div class="text-sm text-warning-800 dark:text-warning-300">
                        <span class="font-medium">{{ $note->author?->name ?? __('escalated-filament::filament.livewire.conversation.system') }}</span>
                        <span class="text-warning-500 dark:text-warning-500">{{ $note->created_at->diffForHumans() }}</span>
                        <div class="mt-1 prose prose-sm dark:prose-invert max-w-none">{!! $note->body !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Conversation Thread --}}
    <div class="space-y-4">
        @forelse($replies as $reply)
            <div @class([
                'rounded-lg border p-4',
                'border-yellow-300 bg-yellow-50 dark:border-yellow-700 dark:bg-yellow-900/20' => $reply->is_internal_note,
                'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => !$reply->is_internal_note,
            ])>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 text-sm font-semibold">
                            {{ strtoupper(substr($reply->author?->name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $reply->author?->name ?? __('escalated-filament::filament.livewire.conversation.system') }}
                            </span>
                            @if($reply->is_internal_note)
                                <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-yellow-100 dark:bg-yellow-800 px-2 py-0.5 text-xs font-medium text-yellow-700 dark:text-yellow-300">
                                    <x-heroicon-s-lock-closed class="h-3 w-3" />
                                    {{ __('escalated-filament::filament.livewire.conversation.internal_note_badge') }}
                                </span>
                            @endif
                            @if($reply->is_pinned)
                                <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-primary-100 dark:bg-primary-800 px-2 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-300">
                                    <x-heroicon-s-bookmark class="h-3 w-3" />
                                    {{ __('escalated-filament::filament.livewire.conversation.pinned_badge') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($reply->is_internal_note)
                            <button
                                wire:click="togglePin({{ $reply->id }})"
                                class="text-gray-400 hover:text-primary-500 transition-colors"
                                title="{{ $reply->is_pinned ? __('escalated-filament::filament.actions.pin_reply.unpin') : __('escalated-filament::filament.actions.pin_reply.pin') }}"
                            >
                                @if($reply->is_pinned)
                                    <x-heroicon-s-bookmark class="h-4 w-4 text-primary-500" />
                                @else
                                    <x-heroicon-o-bookmark class="h-4 w-4" />
                                @endif
                            </button>
                        @endif
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $reply->created_at->format('M j, Y g:i A') }}
                        </span>
                    </div>
                </div>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! $reply->body !!}
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-chat-bubble-left-right class="h-12 w-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                <p class="text-sm">{{ __('escalated-filament::filament.livewire.conversation.no_replies') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Reply Composer --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
            {{ $this->isInternalNote ? __('escalated-filament::filament.livewire.conversation.add_internal_note') : __('escalated-filament::filament.livewire.conversation.reply') }}
        </h4>

        <form wire:submit="sendReply" class="space-y-4">
            {{ $this->form }}

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    @if($this->isInternalNote)
                        <x-heroicon-s-lock-closed class="h-3.5 w-3.5 text-yellow-500" />
                        <span>{{ __('escalated-filament::filament.livewire.conversation.note_visible_to_agents') }}</span>
                    @else
                        <x-heroicon-o-globe-alt class="h-3.5 w-3.5 text-gray-400" />
                        <span>{{ __('escalated-filament::filament.livewire.conversation.reply_visible_to_customer') }}</span>
                    @endif
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500 transition-colors"
                >
                    <x-heroicon-o-paper-airplane class="h-4 w-4" />
                    {{ $this->isInternalNote ? __('escalated-filament::filament.livewire.conversation.add_note_button') : __('escalated-filament::filament.livewire.conversation.send_reply_button') }}
                </button>
            </div>
        </form>
    </div>
</div>
