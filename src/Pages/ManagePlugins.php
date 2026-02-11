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

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 98;

    protected static ?string $title = 'Plugins';

    protected static ?string $slug = 'support-plugins';

    protected static string $view = 'escalated-filament::pages.manage-plugins';

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
                ->label('Upload Plugin')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    Forms\Components\FileUpload::make('plugin_file')
                        ->label('Plugin ZIP File')
                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                        ->maxSize(config('escalated.plugins.max_upload_size_kb', 51200))
                        ->required()
                        ->helperText('Upload a plugin ZIP archive containing a valid plugin.json manifest.'),
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
                            ->title('Plugin uploaded successfully')
                            ->body('The plugin has been extracted and is ready to activate.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Plugin upload failed', [
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Plugin upload failed')
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
                    ->label('Plugin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (array $state, $record): string => $record->description ?? ''),

                Tables\Columns\TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Author')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'composer' => 'purple',
                        default => 'info',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activated_at')
                    ->label('Activated')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deactivated_at')
                    ->label('Deactivated')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Activate Plugin')
                    ->modalDescription(fn ($record): string => "Are you sure you want to activate \"{$record->name}\"? This will load the plugin and register its hooks.")
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->activatePlugin($record->slug);

                            Notification::make()
                                ->title('Plugin activated')
                                ->body("\"{$record->name}\" has been activated successfully.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin activation failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Activation failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn ($record): bool => (bool) $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate Plugin')
                    ->modalDescription(fn ($record): string => "Are you sure you want to deactivate \"{$record->name}\"? Its hooks and extensions will be unregistered.")
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->deactivatePlugin($record->slug);

                            Notification::make()
                                ->title('Plugin deactivated')
                                ->body("\"{$record->name}\" has been deactivated.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin deactivation failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Deactivation failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record): bool => ($record->source ?? 'local') !== 'composer')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Plugin')
                    ->modalDescription(fn ($record): string => "Are you sure you want to permanently delete \"{$record->name}\"? This will remove all plugin files and cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete plugin')
                    ->action(function ($record): void {
                        try {
                            $this->getPluginService()->deletePlugin($record->slug);

                            Notification::make()
                                ->title('Plugin deleted')
                                ->body("\"{$record->name}\" has been removed.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Plugin deletion failed', [
                                'slug' => $record->slug,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Deletion failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No plugins installed')
            ->emptyStateDescription('Upload a plugin ZIP file or install one via Composer to get started.')
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
