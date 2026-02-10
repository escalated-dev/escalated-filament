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
            ->label('Apply Macro')
            ->icon('heroicon-o-bolt')
            ->color('purple')
            ->form([
                Forms\Components\Select::make('macro_id')
                    ->label('Macro')
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
                    ->title("Macro '{$macro->name}' applied successfully")
                    ->success()
                    ->send();
            })
            ->visible(fn (Ticket $record) => $record->isOpen());
    }
}
