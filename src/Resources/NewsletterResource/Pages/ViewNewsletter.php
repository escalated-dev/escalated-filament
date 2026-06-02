<?php

namespace Escalated\Filament\Resources\NewsletterResource\Pages;

use Escalated\Filament\Resources\NewsletterResource;
use Escalated\Filament\Support\NewsletterOperations;
use Escalated\Laravel\Models\Newsletter\Newsletter;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewNewsletter extends ViewRecord
{
    protected static string $resource = NewsletterResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Newsletter')
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state): string => match ($state) {
                                'scheduled', 'sending' => 'info',
                                'sent' => 'success',
                                'paused' => 'warning',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('targetList.name')
                            ->label('List'),
                        Infolists\Components\TextEntry::make('from_email'),
                        Infolists\Components\TextEntry::make('reply_to')
                            ->placeholder('None'),
                        Infolists\Components\TextEntry::make('scheduled_at')
                            ->dateTime()
                            ->placeholder('Not scheduled'),
                        Infolists\Components\TextEntry::make('sent_at')
                            ->dateTime()
                            ->placeholder('Not sent'),
                    ])
                    ->columns(2),
                Section::make('Performance')
                    ->schema([
                        Infolists\Components\TextEntry::make('summary_total')
                            ->label('Recipients'),
                        Infolists\Components\TextEntry::make('summary_sent')
                            ->label('Sent'),
                        Infolists\Components\TextEntry::make('summary_opened')
                            ->label('Opened'),
                        Infolists\Components\TextEntry::make('summary_clicked')
                            ->label('Clicked'),
                        Infolists\Components\TextEntry::make('summary_bounced')
                            ->label('Bounced'),
                        Infolists\Components\TextEntry::make('summary_complained')
                            ->label('Complaints'),
                    ])
                    ->columns(3),
                Section::make('Body')
                    ->schema([
                        Infolists\Components\TextEntry::make('body_markdown')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['draft', 'scheduled'], true)),
            Actions\Action::make('send')
                ->label('Send')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, ['draft', 'scheduled'], true))
                ->action(function (): void {
                    NewsletterResource::sendNewsletterNow($this->record);
                }),
            Actions\Action::make('schedule')
                ->label('Schedule')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->visible(fn (): bool => in_array($this->record->status, ['draft', 'scheduled'], true))
                ->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label('Send at')
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    /** @var Newsletter $record */
                    $record = $this->record;

                    $record->update([
                        'status' => 'scheduled',
                        'scheduled_at' => $data['scheduled_at'],
                    ]);

                    Notification::make()
                        ->title('Newsletter scheduled')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('testSend')
                ->label('Test send')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Recipient email')
                        ->email()
                        ->required()
                        ->default(fn () => auth()->user()?->email),
                ])
                ->action(function (array $data): void {
                    app(NewsletterOperations::class)
                        ->sendTest($this->record, $data['email'], auth()->user()?->name);

                    Notification::make()
                        ->title('Test newsletter sent')
                        ->success()
                        ->send();
                }),
        ];
    }
}
