<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Models\EscalatedSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class NewsletterSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 28;

    protected static ?string $slug = 'newsletter-settings';

    public ?array $data = [];

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return 'Newsletter settings';
    }

    public function getTitle(): string
    {
        return 'Newsletter settings';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('escalated.enable_newsletters', false);
    }

    public function getView(): string
    {
        return 'escalated-filament::pages.settings';
    }

    public function mount(): void
    {
        $this->form->fill([
            'default_from' => $this->setting('default_from'),
            'default_reply_to' => $this->setting('default_reply_to'),
            'default_theme' => $this->setting('default_theme', 'default'),
            'rate_limit_per_minute' => (int) $this->setting('rate_limit_per_minute', 60),
            'batch_size' => (int) $this->setting('batch_size', 50),
            'tracking_enabled' => filter_var($this->setting('tracking_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'brand_accent' => $this->setting('brand_accent', '#2563eb'),
            'brand_logo_url' => $this->setting('brand_logo_url'),
            'brand_physical_address' => $this->setting('brand_physical_address'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Defaults')
                    ->schema([
                        Forms\Components\TextInput::make('default_from')
                            ->label('Default from email')
                            ->email(),
                        Forms\Components\TextInput::make('default_reply_to')
                            ->label('Default reply-to email')
                            ->email(),
                        Forms\Components\TextInput::make('default_theme')
                            ->label('Default theme')
                            ->required()
                            ->maxLength(64),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Delivery')
                    ->schema([
                        Forms\Components\TextInput::make('rate_limit_per_minute')
                            ->label('Rate limit per minute')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10000)
                            ->required(),
                        Forms\Components\TextInput::make('batch_size')
                            ->label('Batch size')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->required(),
                        Forms\Components\Toggle::make('tracking_enabled')
                            ->label('Tracking enabled'),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\ColorPicker::make('brand_accent')
                            ->label('Accent color'),
                        Forms\Components\TextInput::make('brand_logo_url')
                            ->label('Logo URL')
                            ->url(),
                        Forms\Components\Textarea::make('brand_physical_address')
                            ->label('Physical address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            EscalatedSettings::set(
                "newsletter.{$key}",
                is_bool($value) ? (int) $value : ($value ?? ''),
            );
        }

        Notification::make()
            ->title('Newsletter settings saved')
            ->success()
            ->send();
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        return EscalatedSettings::get("newsletter.{$key}", config("escalated.newsletters.{$key}", $default));
    }
}
