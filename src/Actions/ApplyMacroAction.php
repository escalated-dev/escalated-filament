<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Models\Macro;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Services\MacroService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ApplyMacroAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'applyMacro';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('escalated-filament::filament.actions.apply_macro.label'))
            ->icon('heroicon-o-bolt')
            ->color('purple')
            ->form([
                Forms\Components\Select::make('macro_id')
                    ->label(__('escalated-filament::filament.actions.apply_macro.macro_field'))
                    ->options(
                        Macro::forAgent(auth()->id())->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
            ])
            ->action(function (Ticket $record, array $data): void {
                $macro = Macro::findOrFail($data['macro_id']);

                app(MacroService::class)->apply($macro, $record, auth()->user());

                Notification::make()
                    ->title(__('escalated-filament::filament.actions.apply_macro.success', ['name' => $macro->name]))
                    ->success()
                    ->send();
            })
            ->visible(fn (Ticket $record) => $record->isOpen());
    }
}
