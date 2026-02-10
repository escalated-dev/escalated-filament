<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\TicketResource\Pages;
use Escalated\Filament\Resources\TicketResource\RelationManagers;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Tag;
use Escalated\Laravel\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Ticket::open()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Ticket::open()->count();

        return $count > 10 ? 'danger' : ($count > 0 ? 'warning' : 'success');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Details')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('priority')
                            ->options(collect(TicketPriority::cases())->mapWithKeys(
                                fn (TicketPriority $p) => [$p->value => $p->label()]
                            ))
                            ->default(TicketPriority::Medium->value)
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned Agent')
                            ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (Ticket $record) => $record->subject),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (TicketStatus $state): string => match ($state) {
                        TicketStatus::Open => 'info',
                        TicketStatus::InProgress => 'primary',
                        TicketStatus::WaitingOnCustomer => 'warning',
                        TicketStatus::WaitingOnAgent => 'warning',
                        TicketStatus::Escalated => 'danger',
                        TicketStatus::Resolved => 'success',
                        TicketStatus::Closed => 'gray',
                        TicketStatus::Reopened => 'info',
                    })
                    ->formatStateUsing(fn (TicketStatus $state) => $state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (TicketPriority $state): string => match ($state) {
                        TicketPriority::Low => 'gray',
                        TicketPriority::Medium => 'info',
                        TicketPriority::High => 'warning',
                        TicketPriority::Urgent => 'warning',
                        TicketPriority::Critical => 'danger',
                    })
                    ->formatStateUsing(fn (TicketPriority $state) => $state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->default('Unassigned')
                    ->color(fn (Ticket $record) => $record->assigned_to ? null : 'warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('requester_name')
                    ->label('Requester')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('sla_status')
                    ->label('SLA')
                    ->state(fn (Ticket $record): string => match (true) {
                        $record->sla_first_response_breached || $record->sla_resolution_breached => 'breached',
                        $record->resolution_due_at && $record->resolution_due_at->isPast() => 'breached',
                        $record->resolution_due_at && $record->resolution_due_at->diffInMinutes(now()) < 60 => 'warning',
                        default => 'ok',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'breached' => 'heroicon-o-exclamation-triangle',
                        'warning' => 'heroicon-o-clock',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'breached' => 'danger',
                        'warning' => 'warning',
                        default => 'success',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(TicketStatus::cases())->mapWithKeys(
                        fn (TicketStatus $s) => [$s->value => $s->label()]
                    ))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(collect(TicketPriority::cases())->mapWithKeys(
                        fn (TicketPriority $p) => [$p->value => $p->label()]
                    ))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned Agent')
                    ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('sla_breached')
                    ->label('SLA Breached')
                    ->queries(
                        true: fn ($query) => $query->breachedSla(),
                        false: fn ($query) => $query->where('sla_first_response_breached', false)
                            ->where('sla_resolution_breached', false),
                    ),

                Tables\Filters\TernaryFilter::make('unassigned')
                    ->label('Unassigned')
                    ->queries(
                        true: fn ($query) => $query->unassigned(),
                        false: fn ($query) => $query->whereNotNull('assigned_to'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('agent_id')
                            ->label('Agent')
                            ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        app(\Escalated\Laravel\Services\AssignmentService::class)
                            ->assign($record, $data['agent_id'], auth()->user());
                    })
                    ->visible(fn (Ticket $record) => $record->isOpen()),
                Tables\Actions\Action::make('changeStatus')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(collect(TicketStatus::cases())->mapWithKeys(
                                fn (TicketStatus $s) => [$s->value => $s->label()]
                            ))
                            ->required(),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        app(\Escalated\Laravel\Services\TicketService::class)
                            ->changeStatus($record, TicketStatus::from($data['status']), auth()->user());
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assignBulk')
                        ->label('Assign Agent')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('agent_id')
                                ->label('Agent')
                                ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $service = app(\Escalated\Laravel\Services\AssignmentService::class);
                            foreach ($records as $ticket) {
                                $service->assign($ticket, $data['agent_id'], auth()->user());
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('changeStatusBulk')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(collect(TicketStatus::cases())->mapWithKeys(
                                    fn (TicketStatus $s) => [$s->value => $s->label()]
                                ))
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $service = app(\Escalated\Laravel\Services\TicketService::class);
                            foreach ($records as $ticket) {
                                $service->changeStatus($ticket, TicketStatus::from($data['status']), auth()->user());
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('changePriorityBulk')
                        ->label('Change Priority')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('priority')
                                ->options(collect(TicketPriority::cases())->mapWithKeys(
                                    fn (TicketPriority $p) => [$p->value => $p->label()]
                                ))
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $service = app(\Escalated\Laravel\Services\TicketService::class);
                            foreach ($records as $ticket) {
                                $service->changePriority($ticket, TicketPriority::from($data['priority']), auth()->user());
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('addTagsBulk')
                        ->label('Add Tags')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('tags')
                                ->options(Tag::pluck('name', 'id'))
                                ->multiple()
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $service = app(\Escalated\Laravel\Services\TicketService::class);
                            foreach ($records as $ticket) {
                                $service->addTags($ticket, $data['tags'], auth()->user());
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('closeBulk')
                        ->label('Close')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $service = app(\Escalated\Laravel\Services\TicketService::class);
                            foreach ($records as $ticket) {
                                $service->close($ticket, auth()->user());
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\FollowersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
