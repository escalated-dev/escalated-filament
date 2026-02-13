<?php

namespace Escalated\Filament\Resources\TicketResource\Pages;

use Escalated\Filament\Resources\TicketResource;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Macro;
use Escalated\Laravel\Models\Ticket;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_ticket_info'))
                        ->schema([
                            Infolists\Components\TextEntry::make('reference')
                                ->label(__('escalated-filament::filament.resources.ticket.field_reference'))
                                ->badge()
                                ->color('primary')
                                ->copyable(),

                            Infolists\Components\TextEntry::make('subject')
                                ->label(__('escalated-filament::filament.resources.ticket.field_subject'))
                                ->columnSpanFull(),

                            Infolists\Components\TextEntry::make('description')
                                ->label(__('escalated-filament::filament.resources.ticket.field_description'))
                                ->html()
                                ->columnSpanFull(),

                            Infolists\Components\TextEntry::make('channel')
                                ->label(__('escalated-filament::filament.resources.ticket.field_channel'))
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(2),

                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_conversation'))
                        ->schema([
                            Infolists\Components\Livewire::make(
                                \Escalated\Filament\Livewire\TicketConversation::class,
                                fn (Ticket $record) => ['ticketId' => $record->id]
                            )->columnSpanFull(),
                        ]),
                ])->columnSpan(2),

                Infolists\Components\Group::make([
                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_details'))
                        ->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->color(fn (TicketStatus $state): string => match ($state) {
                                    TicketStatus::Open => 'info',
                                    TicketStatus::InProgress => 'primary',
                                    TicketStatus::WaitingOnCustomer, TicketStatus::WaitingOnAgent => 'warning',
                                    TicketStatus::Escalated => 'danger',
                                    TicketStatus::Resolved => 'success',
                                    TicketStatus::Closed => 'gray',
                                    TicketStatus::Reopened => 'info',
                                })
                                ->formatStateUsing(fn (TicketStatus $state) => $state->label()),

                            Infolists\Components\TextEntry::make('priority')
                                ->badge()
                                ->color(fn (TicketPriority $state): string => match ($state) {
                                    TicketPriority::Low => 'gray',
                                    TicketPriority::Medium => 'info',
                                    TicketPriority::High => 'warning',
                                    TicketPriority::Urgent => 'warning',
                                    TicketPriority::Critical => 'danger',
                                })
                                ->formatStateUsing(fn (TicketPriority $state) => $state->label()),

                            Infolists\Components\TextEntry::make('department.name')
                                ->label(__('escalated-filament::filament.resources.ticket.field_department'))
                                ->default(__('escalated-filament::filament.resources.ticket.default_none')),

                            Infolists\Components\TextEntry::make('assignee.name')
                                ->label(__('escalated-filament::filament.resources.ticket.column_assigned_to'))
                                ->default(__('escalated-filament::filament.resources.ticket.default_unassigned'))
                                ->color(fn (Ticket $record) => $record->assigned_to ? null : 'warning'),

                            Infolists\Components\TextEntry::make('requester_name')
                                ->label(__('escalated-filament::filament.resources.ticket.field_requester')),

                            Infolists\Components\TextEntry::make('requester_email')
                                ->label(__('escalated-filament::filament.resources.ticket.field_requester_email')),
                        ]),

                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_sla'))
                        ->schema([
                            Infolists\Components\TextEntry::make('slaPolicy.name')
                                ->label(__('escalated-filament::filament.resources.ticket.field_sla_policy'))
                                ->default(__('escalated-filament::filament.resources.ticket.default_no_policy')),

                            Infolists\Components\TextEntry::make('first_response_due_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_first_response_due'))
                                ->dateTime()
                                ->color(fn (Ticket $record) => $record->sla_first_response_breached ? 'danger' : null),

                            Infolists\Components\TextEntry::make('first_response_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_first_response_at'))
                                ->dateTime()
                                ->placeholder(__('escalated-filament::filament.resources.ticket.placeholder_not_responded')),

                            Infolists\Components\TextEntry::make('resolution_due_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_resolution_due'))
                                ->dateTime()
                                ->color(fn (Ticket $record) => $record->sla_resolution_breached ? 'danger' : null),

                            Infolists\Components\IconEntry::make('sla_first_response_breached')
                                ->label(__('escalated-filament::filament.resources.ticket.field_response_breached'))
                                ->boolean()
                                ->trueIcon('heroicon-o-x-circle')
                                ->falseIcon('heroicon-o-check-circle')
                                ->trueColor('danger')
                                ->falseColor('success'),

                            Infolists\Components\IconEntry::make('sla_resolution_breached')
                                ->label(__('escalated-filament::filament.resources.ticket.field_resolution_breached'))
                                ->boolean()
                                ->trueIcon('heroicon-o-x-circle')
                                ->falseIcon('heroicon-o-check-circle')
                                ->trueColor('danger')
                                ->falseColor('success'),
                        ])
                        ->collapsible(),

                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_tags'))
                        ->schema([
                            Infolists\Components\RepeatableEntry::make('tags')
                                ->schema([
                                    Infolists\Components\TextEntry::make('name')
                                        ->badge()
                                        ->color(fn ($record) => \Filament\Support\Colors\Color::hex($record->color ?? '#6B7280')),
                                ])
                                ->grid(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_satisfaction'))
                        ->schema([
                            Infolists\Components\Livewire::make(
                                \Escalated\Filament\Livewire\SatisfactionRating::class,
                                fn (Ticket $record) => ['ticketId' => $record->id]
                            )->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Infolists\Components\Section::make(__('escalated-filament::filament.resources.ticket.section_timestamps'))
                        ->schema([
                            Infolists\Components\TextEntry::make('created_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_created'))
                                ->dateTime(),

                            Infolists\Components\TextEntry::make('updated_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_updated'))
                                ->dateTime(),

                            Infolists\Components\TextEntry::make('resolved_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_resolved_at'))
                                ->dateTime()
                                ->placeholder(__('escalated-filament::filament.resources.ticket.placeholder_not_resolved')),

                            Infolists\Components\TextEntry::make('closed_at')
                                ->label(__('escalated-filament::filament.resources.ticket.field_closed_at'))
                                ->dateTime()
                                ->placeholder(__('escalated-filament::filament.resources.ticket.placeholder_not_closed')),
                        ])
                        ->collapsible(),
                ])->columnSpan(1),
            ])
            ->columns(3);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label(__('escalated-filament::filament.resources.ticket.action_reply'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->form([
                    Forms\Components\RichEditor::make('body')
                        ->label(__('escalated-filament::filament.resources.ticket.action_reply'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->reply($this->record, auth()->user(), $data['body']);

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_reply_sent'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('addNote')
                ->label(__('escalated-filament::filament.resources.ticket.action_add_note'))
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->form([
                    Forms\Components\RichEditor::make('body')
                        ->label(__('escalated-filament::filament.resources.ticket.field_internal_note'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->addNote($this->record, auth()->user(), $data['body']);

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_note_added'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('assign')
                ->label(__('escalated-filament::filament.actions.assign_ticket.label'))
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('agent_id')
                        ->label(__('escalated-filament::filament.actions.assign_ticket.agent_field'))
                        ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\AssignmentService::class)
                        ->assign($this->record, $data['agent_id'], auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_assigned'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('changeStatus')
                ->label(__('escalated-filament::filament.resources.ticket.action_status'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(collect(TicketStatus::cases())->mapWithKeys(
                            fn (TicketStatus $s) => [$s->value => $s->label()]
                        ))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->changeStatus($this->record, TicketStatus::from($data['status']), auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_status_updated'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('changePriority')
                ->label(__('escalated-filament::filament.resources.ticket.action_priority'))
                ->icon('heroicon-o-flag')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('priority')
                        ->options(collect(TicketPriority::cases())->mapWithKeys(
                            fn (TicketPriority $p) => [$p->value => $p->label()]
                        ))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->changePriority($this->record, TicketPriority::from($data['priority']), auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_priority_updated'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('follow')
                ->label(fn () => $this->record->isFollowedBy(auth()->id()) ? __('escalated-filament::filament.actions.follow_ticket.unfollow') : __('escalated-filament::filament.actions.follow_ticket.follow'))
                ->icon(fn () => $this->record->isFollowedBy(auth()->id()) ? 'heroicon-s-bell-slash' : 'heroicon-o-bell')
                ->color('gray')
                ->action(function (): void {
                    if ($this->record->isFollowedBy(auth()->id())) {
                        $this->record->unfollow(auth()->id());
                        Notification::make()->title(__('escalated-filament::filament.resources.ticket.notification_unfollowed'))->success()->send();
                    } else {
                        $this->record->follow(auth()->id());
                        Notification::make()->title(__('escalated-filament::filament.resources.ticket.notification_following'))->success()->send();
                    }
                }),

            Actions\Action::make('applyMacro')
                ->label(__('escalated-filament::filament.actions.apply_macro.label'))
                ->icon('heroicon-o-bolt')
                ->color('purple')
                ->form([
                    Forms\Components\Select::make('macro_id')
                        ->label(__('escalated-filament::filament.actions.apply_macro.macro_field'))
                        ->options(
                            Macro::forAgent(auth()->id())->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $macro = Macro::findOrFail($data['macro_id']);
                    app(\Escalated\Laravel\Services\MacroService::class)
                        ->apply($macro, $this->record, auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_macro_applied', ['name' => $macro->name]))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->isOpen()),

            Actions\Action::make('resolve')
                ->label(__('escalated-filament::filament.resources.ticket.action_resolve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->resolve($this->record, auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_resolved'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->isOpen()),

            Actions\Action::make('close')
                ->label(__('escalated-filament::filament.resources.ticket.action_close'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->close($this->record, auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_closed'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status !== TicketStatus::Closed),

            Actions\Action::make('reopen')
                ->label(__('escalated-filament::filament.resources.ticket.action_reopen'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('info')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->reopen($this->record, auth()->user());

                    Notification::make()
                        ->title(__('escalated-filament::filament.resources.ticket.notification_reopened'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => in_array($this->record->status, [TicketStatus::Resolved, TicketStatus::Closed])),
        ];
    }
}
