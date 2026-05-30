<?php

namespace Escalated\Filament\Resources\TicketResource\RelationManagers;

use Escalated\Filament\Support\TicketSubjectTypeResolver;
use Escalated\Laravel\Contracts\TicketSubject;
use Escalated\Laravel\Models\TicketSubjectLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! TicketSubjectTypeResolver::isConfigured()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getIcon(Model $ownerRecord, string $pageClass): ?string
    {
        return 'heroicon-o-link';
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('escalated-filament::filament.resources.ticket_subjects.title');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('subject'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.column_title'))
                    ->state(function (TicketSubjectLink $record): string {
                        $subject = $record->subject;

                        if ($subject instanceof TicketSubject) {
                            return $subject->ticketSubjectTitle();
                        }

                        return $subject ? (string) $subject->getKey() : '—';
                    })
                    ->url(function (TicketSubjectLink $record): ?string {
                        $subject = $record->subject;

                        return $subject instanceof TicketSubject ? $subject->ticketSubjectUrl() : null;
                    })
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('subtitle')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.column_subtitle'))
                    ->state(function (TicketSubjectLink $record): ?string {
                        $subject = $record->subject;

                        return $subject instanceof TicketSubject ? $subject->ticketSubjectSubtitle() : null;
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('role')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.column_role'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.column_type'))
                    ->formatStateUsing(fn (string $state): string => TicketSubjectTypeResolver::labelForType($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('attachSubject')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.action_attach'))
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label(__('escalated-filament::filament.resources.ticket_subjects.field_type'))
                            ->options(fn (): array => TicketSubjectTypeResolver::typeOptions())
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('subject_id')
                            ->label(__('escalated-filament::filament.resources.ticket_subjects.field_subject'))
                            ->options(fn (Get $get): array => TicketSubjectTypeResolver::subjectOptionsForType($get('type')))
                            ->searchable()
                            ->required()
                            ->visible(fn (Get $get): bool => filled($get('type'))),

                        Forms\Components\TextInput::make('role')
                            ->label(__('escalated-filament::filament.resources.ticket_subjects.field_role'))
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $class = TicketSubjectTypeResolver::resolveModelClass($data['type']);
                        $subject = $class::query()->find($data['subject_id']);

                        if ($subject === null) {
                            throw ValidationException::withMessages([
                                'subject_id' => __('escalated-filament::filament.resources.ticket_subjects.validation_subject_not_found'),
                            ]);
                        }

                        $this->getOwnerRecord()->attachSubject($subject, $data['role'] ?? null);

                        Notification::make()
                            ->title(__('escalated-filament::filament.resources.ticket_subjects.notification_attached'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('detach')
                    ->label(__('escalated-filament::filament.resources.ticket_subjects.action_detach'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TicketSubjectLink $record): void {
                        $subject = $record->subject;

                        if ($subject !== null) {
                            $this->getOwnerRecord()->detachSubject($subject);
                        } else {
                            $record->delete();
                        }

                        Notification::make()
                            ->title(__('escalated-filament::filament.resources.ticket_subjects.notification_detached'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
