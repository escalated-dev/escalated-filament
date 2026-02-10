<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Models\EscalatedSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Support Settings';

    protected static ?string $slug = 'support-settings';

    protected static string $view = 'escalated-filament::pages.settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public function mount(): void
    {
        $this->form->fill([
            'guest_tickets_enabled' => EscalatedSettings::getBool('guest_tickets_enabled', true),
            'auto_close_resolved_after_days' => EscalatedSettings::getInt('auto_close_resolved_after_days', 7),
            'max_attachments_per_reply' => EscalatedSettings::getInt('max_attachments_per_reply', 5),
            'max_attachment_size_kb' => EscalatedSettings::getInt('max_attachment_size_kb', 10240),
            'ticket_reference_prefix' => EscalatedSettings::get('ticket_reference_prefix', 'ESC'),
            'allow_customer_close' => EscalatedSettings::getBool('allow_customer_close', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->description('Configure general ticket system behavior.')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_reference_prefix')
                            ->label('Ticket Reference Prefix')
                            ->helperText('Prefix for ticket reference numbers (e.g., ESC-00001).')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Toggle::make('guest_tickets_enabled')
                            ->label('Allow Guest Tickets')
                            ->helperText('Allow unauthenticated users to submit tickets.'),

                        Forms\Components\Toggle::make('allow_customer_close')
                            ->label('Allow Customer Close')
                            ->helperText('Allow customers to close their own tickets.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Auto-Close')
                    ->description('Automatically close resolved tickets after a specified period.')
                    ->schema([
                        Forms\Components\TextInput::make('auto_close_resolved_after_days')
                            ->label('Auto-Close Resolved After (days)')
                            ->helperText('Number of days after resolution before a ticket is automatically closed.')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->suffix('days'),
                    ]),

                Forms\Components\Section::make('Attachments')
                    ->description('Configure attachment limits for tickets and replies.')
                    ->schema([
                        Forms\Components\TextInput::make('max_attachments_per_reply')
                            ->label('Max Attachments per Reply')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->suffix('files'),

                        Forms\Components\TextInput::make('max_attachment_size_kb')
                            ->label('Max Attachment Size')
                            ->numeric()
                            ->minValue(1024)
                            ->maxValue(102400)
                            ->suffix('KB')
                            ->helperText('Maximum file size in kilobytes (1024 KB = 1 MB).'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        EscalatedSettings::set('guest_tickets_enabled', $data['guest_tickets_enabled'] ? '1' : '0');
        EscalatedSettings::set('auto_close_resolved_after_days', (string) $data['auto_close_resolved_after_days']);
        EscalatedSettings::set('max_attachments_per_reply', (string) $data['max_attachments_per_reply']);
        EscalatedSettings::set('max_attachment_size_kb', (string) $data['max_attachment_size_kb']);
        EscalatedSettings::set('ticket_reference_prefix', $data['ticket_reference_prefix']);
        EscalatedSettings::set('allow_customer_close', $data['allow_customer_close'] ? '1' : '0');

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
