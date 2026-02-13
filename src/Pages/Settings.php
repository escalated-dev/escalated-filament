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

    protected static ?int $navigationSort = 99;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-settings';

    protected static string $view = 'escalated-filament::pages.settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.settings.title');
    }

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
                Forms\Components\Section::make(__('escalated-filament::filament.pages.settings.general'))
                    ->description(__('escalated-filament::filament.pages.settings.general_description'))
                    ->schema([
                        Forms\Components\TextInput::make('ticket_reference_prefix')
                            ->label(__('escalated-filament::filament.pages.settings.ticket_reference_prefix'))
                            ->helperText(__('escalated-filament::filament.pages.settings.ticket_reference_prefix_helper'))
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Toggle::make('guest_tickets_enabled')
                            ->label(__('escalated-filament::filament.pages.settings.allow_guest_tickets'))
                            ->helperText(__('escalated-filament::filament.pages.settings.allow_guest_tickets_helper')),

                        Forms\Components\Toggle::make('allow_customer_close')
                            ->label(__('escalated-filament::filament.pages.settings.allow_customer_close'))
                            ->helperText(__('escalated-filament::filament.pages.settings.allow_customer_close_helper')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.settings.auto_close'))
                    ->description(__('escalated-filament::filament.pages.settings.auto_close_description'))
                    ->schema([
                        Forms\Components\TextInput::make('auto_close_resolved_after_days')
                            ->label(__('escalated-filament::filament.pages.settings.auto_close_days'))
                            ->helperText(__('escalated-filament::filament.pages.settings.auto_close_days_helper'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->suffix('days'),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.settings.attachments'))
                    ->description(__('escalated-filament::filament.pages.settings.attachments_description'))
                    ->schema([
                        Forms\Components\TextInput::make('max_attachments_per_reply')
                            ->label(__('escalated-filament::filament.pages.settings.max_attachments'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->suffix('files'),

                        Forms\Components\TextInput::make('max_attachment_size_kb')
                            ->label(__('escalated-filament::filament.pages.settings.max_attachment_size'))
                            ->numeric()
                            ->minValue(1024)
                            ->maxValue(102400)
                            ->suffix('KB')
                            ->helperText(__('escalated-filament::filament.pages.settings.max_attachment_size_helper')),
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
            ->title(__('escalated-filament::filament.pages.settings.save_success'))
            ->success()
            ->send();
    }
}
