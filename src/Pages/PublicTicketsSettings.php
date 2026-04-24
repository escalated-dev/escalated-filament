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

/**
 * Admin page for the public-ticket guest policy.
 *
 * Controls the identity assigned to tickets submitted via the public widget
 * or inbound email. Read at request time by the widget controller, so admins
 * can switch modes at runtime without a redeploy.
 */
class PublicTicketsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 95;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-public-tickets-settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-plus';
    }

    public function getView(): string
    {
        return 'escalated-filament::pages.support-public-tickets-settings';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.public_tickets_settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.public_tickets_settings.title');
    }

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public function mount(): void
    {
        $userIdRaw = EscalatedSettings::get('guest_policy_user_id', '');

        $this->form->fill([
            'guest_policy_mode' => EscalatedSettings::get('guest_policy_mode', 'unassigned'),
            'guest_policy_user_id' => $userIdRaw === '' ? null : (int) $userIdRaw,
            'guest_policy_signup_url_template' => EscalatedSettings::get(
                'guest_policy_signup_url_template',
                ''
            ),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.pages.public_tickets_settings.mode_section'))
                    ->description(__('escalated-filament::filament.pages.public_tickets_settings.mode_description'))
                    ->schema([
                        Forms\Components\Select::make('guest_policy_mode')
                            ->label(__('escalated-filament::filament.pages.public_tickets_settings.mode_label'))
                            ->options([
                                'unassigned' => __('escalated-filament::filament.pages.public_tickets_settings.mode_unassigned'),
                                'guest_user' => __('escalated-filament::filament.pages.public_tickets_settings.mode_guest_user'),
                                'prompt_signup' => __('escalated-filament::filament.pages.public_tickets_settings.mode_prompt_signup'),
                            ])
                            ->default('unassigned')
                            ->live()
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.public_tickets_settings.guest_user_section'))
                    ->visible(fn (Get $get): bool => $get('guest_policy_mode') === 'guest_user')
                    ->schema([
                        Forms\Components\TextInput::make('guest_policy_user_id')
                            ->label(__('escalated-filament::filament.pages.public_tickets_settings.user_id_label'))
                            ->helperText(__('escalated-filament::filament.pages.public_tickets_settings.user_id_helper'))
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.pages.public_tickets_settings.signup_section'))
                    ->visible(fn (Get $get): bool => $get('guest_policy_mode') === 'prompt_signup')
                    ->schema([
                        Forms\Components\TextInput::make('guest_policy_signup_url_template')
                            ->label(__('escalated-filament::filament.pages.public_tickets_settings.signup_url_label'))
                            ->helperText(__('escalated-filament::filament.pages.public_tickets_settings.signup_url_helper'))
                            ->maxLength(500)
                            ->placeholder('https://app.example.com/register?email={{email}}'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $mode = in_array($data['guest_policy_mode'] ?? 'unassigned', ['unassigned', 'guest_user', 'prompt_signup'], true)
            ? $data['guest_policy_mode']
            : 'unassigned';

        EscalatedSettings::set('guest_policy_mode', $mode);

        EscalatedSettings::set(
            'guest_policy_user_id',
            $mode === 'guest_user' ? (string) ($data['guest_policy_user_id'] ?? '') : ''
        );

        EscalatedSettings::set(
            'guest_policy_signup_url_template',
            $mode === 'prompt_signup' ? (string) ($data['guest_policy_signup_url_template'] ?? '') : ''
        );

        Notification::make()
            ->title(__('escalated-filament::filament.pages.public_tickets_settings.save_success'))
            ->success()
            ->send();
    }
}
