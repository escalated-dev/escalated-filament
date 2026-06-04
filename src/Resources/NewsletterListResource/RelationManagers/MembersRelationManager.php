<?php

namespace Escalated\Filament\Resources\NewsletterListResource\RelationManagers;

use Escalated\Laravel\Models\Contact;
use Escalated\Laravel\Models\Newsletter\NewsletterList;
use Escalated\Laravel\Models\Newsletter\NewsletterListMember;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public static function getIcon(Model $ownerRecord, string $pageClass): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Members';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('contact_id')
                    ->label('Contact')
                    ->relationship('contact', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('added_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Actions\Action::make('addMember')
                    ->label('Add member')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (): bool => $this->isStaticList())
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->label('Contact')
                            ->options(fn () => Contact::query()
                                ->orderBy('email')
                                ->pluck('email', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        NewsletterListMember::firstOrCreate(
                            [
                                'list_id' => $this->getOwnerRecord()->getKey(),
                                'contact_id' => $data['contact_id'],
                            ],
                            [
                                'added_by' => auth()->id(),
                                'added_at' => now(),
                            ],
                        );

                        Notification::make()
                            ->title('Member added')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('importCsv')
                    ->label('Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->visible(fn (): bool => $this->isStaticList())
                    ->schema([
                        Forms\Components\Textarea::make('csv')
                            ->label('CSV rows')
                            ->helperText('Use one email per row, or CSV rows where the first column is email and the second column is name.')
                            ->rows(8)
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $imported = $this->importCsvRows((string) $data['csv']);

                        Notification::make()
                            ->title("Imported {$imported} members")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Actions\DeleteAction::make()
                    ->label('Remove')
                    ->visible(fn (): bool => $this->isStaticList()),
            ])
            ->defaultSort('added_at', 'desc');
    }

    private function isStaticList(): bool
    {
        return $this->getOwnerRecord()->kind === 'static';
    }

    private function importCsvRows(string $csv): int
    {
        /** @var NewsletterList $list */
        $list = $this->getOwnerRecord();
        $imported = 0;

        foreach (preg_split('/\r\n|\r|\n/', $csv) ?: [] as $line) {
            $row = str_getcsv($line);
            $email = filter_var(strtolower(trim($row[0] ?? '')), FILTER_VALIDATE_EMAIL);

            if (! $email) {
                continue;
            }

            $contact = Contact::findOrCreateByEmail($email, trim($row[1] ?? '') ?: null);
            $member = NewsletterListMember::firstOrCreate(
                [
                    'list_id' => $list->getKey(),
                    'contact_id' => $contact->getKey(),
                ],
                [
                    'added_by' => auth()->id(),
                    'added_at' => now(),
                ],
            );

            if ($member->wasRecentlyCreated) {
                $imported++;
            }
        }

        return $imported;
    }
}
