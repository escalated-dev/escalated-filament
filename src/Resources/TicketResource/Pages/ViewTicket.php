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
                    Infolists\Components\Section::make('Ticket Information')
                        ->schema([
                            Infolists\Components\TextEntry::make('reference')
                                ->label('Reference')
                                ->badge()
                                ->color('primary')
                                ->copyable(),

                            Infolists\Components\TextEntry::make('subject')
                                ->label('Subject')
                                ->columnSpanFull(),

                            Infolists\Components\TextEntry::make('description')
                                ->label('Description')
                                ->html()
                                ->columnSpanFull(),

                            Infolists\Components\TextEntry::make('channel')
                                ->label('Channel')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(2),

                    Infolists\Components\Section::make('Conversation')
                        ->schema([
                            Infolists\Components\Livewire::make(
                                \Escalated\Filament\Livewire\TicketConversation::class,
                                fn (Ticket $record) => ['ticketId' => $record->id]
                            )->columnSpanFull(),
                        ]),
                ])->columnSpan(2),

                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Details')
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
                                ->label('Department')
                                ->default('None'),

                            Infolists\Components\TextEntry::make('assignee.name')
                                ->label('Assigned To')
                                ->default('Unassigned')
                                ->color(fn (Ticket $record) => $record->assigned_to ? null : 'warning'),

                            Infolists\Components\TextEntry::make('requester_name')
                                ->label('Requester'),

                            Infolists\Components\TextEntry::make('requester_email')
                                ->label('Requester Email'),
                        ]),

                    Infolists\Components\Section::make('SLA')
                        ->schema([
                            Infolists\Components\TextEntry::make('slaPolicy.name')
                                ->label('SLA Policy')
                                ->default('No policy'),

                            Infolists\Components\TextEntry::make('first_response_due_at')
                                ->label('First Response Due')
                                ->dateTime()
                                ->color(fn (Ticket $record) => $record->sla_first_response_breached ? 'danger' : null),

                            Infolists\Components\TextEntry::make('first_response_at')
                                ->label('First Response At')
                                ->dateTime()
                                ->default('Not yet responded'),

                            Infolists\Components\TextEntry::make('resolution_due_at')
                                ->label('Resolution Due')
                                ->dateTime()
                                ->color(fn (Ticket $record) => $record->sla_resolution_breached ? 'danger' : null),

                            Infolists\Components\IconEntry::make('sla_first_response_breached')
                                ->label('Response Breached')
                                ->boolean()
                                ->trueIcon('heroicon-o-x-circle')
                                ->falseIcon('heroicon-o-check-circle')
                                ->trueColor('danger')
                                ->falseColor('success'),

                            Infolists\Components\IconEntry::make('sla_resolution_breached')
                                ->label('Resolution Breached')
                                ->boolean()
                                ->trueIcon('heroicon-o-x-circle')
                                ->falseIcon('heroicon-o-check-circle')
                                ->trueColor('danger')
                                ->falseColor('success'),
                        ])
                        ->collapsible(),

                    Infolists\Components\Section::make('Tags')
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

                    Infolists\Components\Section::make('Satisfaction Rating')
                        ->schema([
                            Infolists\Components\Livewire::make(
                                \Escalated\Filament\Livewire\SatisfactionRating::class,
                                fn (Ticket $record) => ['ticketId' => $record->id]
                            )->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Infolists\Components\Section::make('Timestamps')
                        ->schema([
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime(),

                            Infolists\Components\TextEntry::make('updated_at')
                                ->label('Updated')
                                ->dateTime(),

                            Infolists\Components\TextEntry::make('resolved_at')
                                ->label('Resolved At')
                                ->dateTime()
                                ->default('Not resolved'),

                            Infolists\Components\TextEntry::make('closed_at')
                                ->label('Closed At')
                                ->dateTime()
                                ->default('Not closed'),
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
                ->label('Reply')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->form([
                    Forms\Components\RichEditor::make('body')
                        ->label('Reply')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->reply($this->record, auth()->user(), $data['body']);

                    Notification::make()
                        ->title('Reply sent')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('addNote')
                ->label('Add Note')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->form([
                    Forms\Components\RichEditor::make('body')
                        ->label('Internal Note')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->addNote($this->record, auth()->user(), $data['body']);

                    Notification::make()
                        ->title('Internal note added')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('assign')
                ->label('Assign')
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('agent_id')
                        ->label('Agent')
                        ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(\Escalated\Laravel\Services\AssignmentService::class)
                        ->assign($this->record, $data['agent_id'], auth()->user());

                    Notification::make()
                        ->title('Ticket assigned')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('changeStatus')
                ->label('Status')
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
                        ->title('Status updated')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('changePriority')
                ->label('Priority')
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
                        ->title('Priority updated')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('follow')
                ->label(fn () => $this->record->isFollowedBy(auth()->id()) ? 'Unfollow' : 'Follow')
                ->icon(fn () => $this->record->isFollowedBy(auth()->id()) ? 'heroicon-s-bell-slash' : 'heroicon-o-bell')
                ->color('gray')
                ->action(function (): void {
                    if ($this->record->isFollowedBy(auth()->id())) {
                        $this->record->unfollow(auth()->id());
                        Notification::make()->title('Unfollowed ticket')->success()->send();
                    } else {
                        $this->record->follow(auth()->id());
                        Notification::make()->title('Following ticket')->success()->send();
                    }
                }),

            Actions\Action::make('applyMacro')
                ->label('Apply Macro')
                ->icon('heroicon-o-bolt')
                ->color('purple')
                ->form([
                    Forms\Components\Select::make('macro_id')
                        ->label('Macro')
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
                        ->title("Macro '{$macro->name}' applied")
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->isOpen()),

            Actions\Action::make('resolve')
                ->label('Resolve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->resolve($this->record, auth()->user());

                    Notification::make()
                        ->title('Ticket resolved')
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->isOpen()),

            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->close($this->record, auth()->user());

                    Notification::make()
                        ->title('Ticket closed')
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status !== TicketStatus::Closed),

            Actions\Action::make('reopen')
                ->label('Reopen')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('info')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(\Escalated\Laravel\Services\TicketService::class)
                        ->reopen($this->record, auth()->user());

                    Notification::make()
                        ->title('Ticket reopened')
                        ->success()
                        ->send();
                })
                ->visible(fn () => in_array($this->record->status, [TicketStatus::Resolved, TicketStatus::Closed])),
        ];
    }
}
