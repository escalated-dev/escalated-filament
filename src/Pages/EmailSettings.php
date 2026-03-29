<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\EscalatedSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EmailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 97;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-email-settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-envelope';
    }

    public function getView(): string
    {
        return 'escalated-filament::pages.support-email-settings';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.email_settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.email_settings.title');
    }

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public function mount(): void
    {
        $emailAddresses = EscalatedSettings::get('email_addresses', '[]');

        $this->form->fill([
            'default_reply_address' => EscalatedSettings::get('default_reply_address', ''),
            'email_addresses' => json_decode($emailAddresses, true) ?: [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.pages.email_settings.default_reply_address'))
                    ->schema([
                        Forms\Components\TextInput::make('default_reply_address')
                            ->label(__('escalated-filament::filament.pages.email_settings.default_reply_address_label'))
                            ->email(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.email_settings.email_addresses'))
                    ->schema([
                        Forms\Components\Repeater::make('email_addresses')
                            ->label(__('escalated-filament::filament.pages.email_settings.email_addresses_label'))
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label(__('escalated-filament::filament.pages.email_settings.email'))
                                    ->email()
                                    ->required(),

                                Forms\Components\TextInput::make('display_name')
                                    ->label(__('escalated-filament::filament.pages.email_settings.display_name')),

                                Forms\Components\Select::make('department_id')
                                    ->label(__('escalated-filament::filament.pages.email_settings.department'))
                                    ->options(fn () => Department::pluck('name', 'id'))
                                    ->nullable(),

                                Forms\Components\TextInput::make('dkim_status')
                                    ->label(__('escalated-filament::filament.pages.email_settings.dkim_status'))
                                    ->disabled()
                                    ->default('unknown'),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        EscalatedSettings::set('default_reply_address', $data['default_reply_address'] ?? '');
        EscalatedSettings::set('email_addresses', json_encode($data['email_addresses'] ?? []));

        Notification::make()
            ->title(__('escalated-filament::filament.pages.email_settings.save_success'))
            ->success()
            ->send();
    }
}
