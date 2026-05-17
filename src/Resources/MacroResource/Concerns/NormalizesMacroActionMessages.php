<?php

namespace Escalated\Filament\Resources\MacroResource\Concerns;

trait NormalizesMacroActionMessages
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeMacroActionsForForm(array $data): array
    {
        foreach ($data['actions'] ?? [] as $i => $action) {
            if (! is_array($action)) {
                continue;
            }

            if (in_array($action['type'] ?? null, ['reply', 'note'], true)) {
                $data['actions'][$i]['message'] = $action['value'] ?? '';
                unset($data['actions'][$i]['value']);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeMacroActionsForStorage(array $data): array
    {
        foreach ($data['actions'] ?? [] as $i => $action) {
            if (! is_array($action)) {
                continue;
            }

            if (in_array($action['type'] ?? null, ['reply', 'note'], true)) {
                $data['actions'][$i]['value'] = $action['message'] ?? '';
                unset($data['actions'][$i]['message']);
            }
        }

        return $data;
    }
}
