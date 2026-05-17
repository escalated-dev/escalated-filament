<?php

namespace Escalated\Filament\Resources\SkillResource\Pages;

use Escalated\Filament\Resources\SkillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditSkill extends EditRecord
{
    protected static string $resource = SkillResource::class;

    /**
     * @var list<array{user_id?: int|string|null, proficiency?: int|string|null}>|null
     */
    protected ?array $pendingAgentRows = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->getRecord()->loadMissing('agents');

        $data['agents'] = $this->getRecord()->agents->map(fn ($agent): array => [
            'user_id' => $agent->getKey(),
            'proficiency' => (int) ($agent->pivot->proficiency ?? 3),
        ])->all();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingAgentRows = $data['agents'] ?? [];
        unset($data['agents']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $record = parent::handleRecordUpdate($record, $data);
            SkillResource::syncAgentsForSkill($record, $this->pendingAgentRows ?? []);

            return $record;
        });
    }
}
