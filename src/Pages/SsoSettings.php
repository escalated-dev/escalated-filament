<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Models\EscalatedSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SsoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 96;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-sso-settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-finger-print';
    }

    public function getView(): string
    {
        return 'escalated-filament::pages.support-sso-settings';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.sso_settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.sso_settings.title');
    }

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public function mount(): void
    {
        $this->form->fill([
            'sso_provider' => EscalatedSettings::get('sso_provider', 'none'),
            'sso_entity_id' => EscalatedSettings::get('sso_entity_id', ''),
            'sso_url' => EscalatedSettings::get('sso_url', ''),
            'sso_certificate' => EscalatedSettings::get('sso_certificate', ''),
            'sso_jwt_secret' => EscalatedSettings::get('sso_jwt_secret', ''),
            'sso_jwt_algorithm' => EscalatedSettings::get('sso_jwt_algorithm', 'HS256'),
            'sso_attr_email' => EscalatedSettings::get('sso_attr_email', 'email'),
            'sso_attr_name' => EscalatedSettings::get('sso_attr_name', 'name'),
            'sso_attr_role' => EscalatedSettings::get('sso_attr_role', 'role'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.pages.sso_settings.provider'))
                    ->schema([
                        Forms\Components\Select::make('sso_provider')
                            ->label(__('escalated-filament::filament.pages.sso_settings.provider_label'))
                            ->options([
                                'none' => __('escalated-filament::filament.pages.sso_settings.provider_none'),
                                'saml' => __('escalated-filament::filament.pages.sso_settings.provider_saml'),
                                'jwt' => __('escalated-filament::filament.pages.sso_settings.provider_jwt'),
                            ])
                            ->default('none')
                            ->live()
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.sso_settings.saml_configuration'))
                    ->visible(fn (Get $get): bool => $get('sso_provider') === 'saml')
                    ->schema([
                        Forms\Components\TextInput::make('sso_entity_id')
                            ->label(__('escalated-filament::filament.pages.sso_settings.entity_id'))
                            ->required(),

                        Forms\Components\TextInput::make('sso_url')
                            ->label(__('escalated-filament::filament.pages.sso_settings.sso_url'))
                            ->url()
                            ->required(),

                        Forms\Components\Textarea::make('sso_certificate')
                            ->label(__('escalated-filament::filament.pages.sso_settings.certificate'))
                            ->rows(6)
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.sso_settings.jwt_configuration'))
                    ->visible(fn (Get $get): bool => $get('sso_provider') === 'jwt')
                    ->schema([
                        Forms\Components\TextInput::make('sso_jwt_secret')
                            ->label(__('escalated-filament::filament.pages.sso_settings.jwt_secret'))
                            ->password()
                            ->required(),

                        Forms\Components\Select::make('sso_jwt_algorithm')
                            ->label(__('escalated-filament::filament.pages.sso_settings.jwt_algorithm'))
                            ->options([
                                'HS256' => 'HS256',
                                'HS384' => 'HS384',
                                'HS512' => 'HS512',
                                'RS256' => 'RS256',
                                'RS384' => 'RS384',
                                'RS512' => 'RS512',
                            ])
                            ->default('HS256')
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.sso_settings.attribute_mapping'))
                    ->visible(fn (Get $get): bool => $get('sso_provider') !== 'none')
                    ->schema([
                        Forms\Components\TextInput::make('sso_attr_email')
                            ->label(__('escalated-filament::filament.pages.sso_settings.attr_email'))
                            ->default('email'),

                        Forms\Components\TextInput::make('sso_attr_name')
                            ->label(__('escalated-filament::filament.pages.sso_settings.attr_name'))
                            ->default('name'),

                        Forms\Components\TextInput::make('sso_attr_role')
                            ->label(__('escalated-filament::filament.pages.sso_settings.attr_role'))
                            ->default('role'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        EscalatedSettings::set('sso_provider', $data['sso_provider']);
        EscalatedSettings::set('sso_entity_id', $data['sso_entity_id'] ?? '');
        EscalatedSettings::set('sso_url', $data['sso_url'] ?? '');
        EscalatedSettings::set('sso_certificate', $data['sso_certificate'] ?? '');
        EscalatedSettings::set('sso_jwt_secret', $data['sso_jwt_secret'] ?? '');
        EscalatedSettings::set('sso_jwt_algorithm', $data['sso_jwt_algorithm'] ?? 'HS256');
        EscalatedSettings::set('sso_attr_email', $data['sso_attr_email'] ?? 'email');
        EscalatedSettings::set('sso_attr_name', $data['sso_attr_name'] ?? 'name');
        EscalatedSettings::set('sso_attr_role', $data['sso_attr_role'] ?? 'role');

        Notification::make()
            ->title(__('escalated-filament::filament.pages.sso_settings.save_success'))
            ->success()
            ->send();
    }
}
