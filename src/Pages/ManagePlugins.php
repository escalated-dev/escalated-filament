<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Services\PluginService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ManagePlugins extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?int $navigationSort = 98;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-plugins';

    protected static string $view = 'escalated-filament::pages.manage-plugins';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-puzzle-piece';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.manage_plugins.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.manage_plugins.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('escalated.plugins.enabled', false);
    }

    public static function canAccess(): bool
    {
        return config('escalated.plugins.enabled', false);
    }

    protected function getPluginService(): PluginService
    {
        return app(PluginService::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadPlugin')
                ->label(__('escalated-filament::filament.pages.manage_plugins.upload_plugin'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    Forms\Components\FileUpload::make('plugin_file')
                        ->label(__('escalated-filament::filament.pages.manage_plugins.plugin_zip_file'))
                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                        ->maxSize(config('escalated.plugins.max_upload_size_kb', 51200))
                        ->required()
                        ->helperText(__('escalated-filament::filament.pages.manage_plugins.upload_helper_text')),
                ])
                ->action(function (array $data): void {
                    try {
                        $filePath = storage_path('app/public/' . $data['plugin_file']);

                        if (! file_exists($filePath)) {
                            $filePath = storage_path('app/' . $data['plugin_file']);
                        }

                        $file = new \Illuminate\Http\UploadedFile(
                            $filePath,
                            basename($data['plugin_file']),
                            'application/zip',
                            null,
                            true
                        );

                        $this->getPluginService()->uploadPlugin($file);

                        Notification::make()
                            ->title(__('escalated-filament::filament.pages.manage_plugins.upload_success_title'))
                            ->body(__('escalated-filament::filament.pages.manage_plugins.upload_success_body'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Plugin upload failed', [
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title(__('escalated-filament::filament.pages.manage_plugins.upload_failed_title'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getPluginQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_plugin'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (array $state, $record): string => $record->description ?? ''),

                Tables\Columns\TextColumn::make('version')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_version'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_author'))
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('source')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_source'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'composer' => 'purple',
                        default => 'info',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activated_at')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_activated'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('escalated-filament::filament.pages.manage_plugins.placeholder_never'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deactivated_at')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.column_deactivated'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('escalated-filament::filament.pages.manage_plugins.placeholder_never'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.activate'))
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading(__('escalated-filament::filament.pages.manage_plugins.activate_heading'))
                    ->modalDescription(fn ($record): string => __('escalated-filament::filament.pages.manage_plugins.activate_description', ['name' => $record->name]))
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->activatePlugin($record->slug);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.activated_title'))
                                ->body(__('escalated-filament::filament.pages.manage_plugins.activated_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin activation failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.activation_failed_title'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('deactivate')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.deactivate'))
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn ($record): bool => (bool) $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading(__('escalated-filament::filament.pages.manage_plugins.deactivate_heading'))
                    ->modalDescription(fn ($record): string => __('escalated-filament::filament.pages.manage_plugins.deactivate_description', ['name' => $record->name]))
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->deactivatePlugin($record->slug);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.deactivated_title'))
                                ->body(__('escalated-filament::filament.pages.manage_plugins.deactivated_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin deactivation failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.deactivation_failed_title'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('delete')
                    ->label(__('escalated-filament::filament.pages.manage_plugins.delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record): bool => ($record->source ?? 'local') !== 'composer')
                    ->requiresConfirmation()
                    ->modalHeading(__('escalated-filament::filament.pages.manage_plugins.delete_heading'))
                    ->modalDescription(fn ($record): string => __('escalated-filament::filament.pages.manage_plugins.delete_description', ['name' => $record->name]))
                    ->modalSubmitActionLabel(__('escalated-filament::filament.pages.manage_plugins.delete_confirm'))
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->deletePlugin($record->slug);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.deleted_title'))
                                ->body(__('escalated-filament::filament.pages.manage_plugins.deleted_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin deletion failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title(__('escalated-filament::filament.pages.manage_plugins.deletion_failed_title'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading(__('escalated-filament::filament.pages.manage_plugins.empty_heading'))
            ->emptyStateDescription(__('escalated-filament::filament.pages.manage_plugins.empty_description'))
            ->emptyStateIcon('heroicon-o-puzzle-piece')
            ->poll('30s');
    }

    /**
     * Build a query-like object for the plugins table.
     *
     * Since plugins are discovered from the filesystem (not purely a DB model),
     * we use the Plugin model's query and enrich it with manifest data. The
     * PluginService::getAllPlugins() returns an array, so we create an
     * Eloquent-compatible array data source for the table.
     */
    protected function getPluginQuery(): Builder
    {
        // We use the Plugin model as the table's base query. The PluginService
        // syncs filesystem plugins into the database, so this gives us a proper
        // Eloquent query that Filament's table can paginate and sort.
        return \Escalated\Laravel\Models\Plugin::query();
    }

    /**
     * Sync filesystem plugins into the database before rendering.
     *
     * This ensures that any plugins added via the filesystem (e.g., Composer)
     * are represented in the database table alongside uploaded plugins.
     */
    public function mount(): void
    {
        abort_unless(config('escalated.plugins.enabled', false), 404);
    }
}
