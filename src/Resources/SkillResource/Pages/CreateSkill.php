<?php

namespace Escalated\Filament\Resources\SkillResource\Pages;

use Escalated\Filament\Resources\SkillResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSkill extends CreateRecord
{
    protected static string $resource = SkillResource::class;

    /**
     * @var list<array{user_id?: int|string|null, proficiency?: int|string|null}>|null
     */
    protected ?array $pendingAgentRows = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingAgentRows = $data['agents'] ?? [];
        unset($data['agents']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $record = parent::handleRecordCreation($data);
            SkillResource::syncAgentsForSkill($record, $this->pendingAgentRows ?? []);

            return $record;
        });
    }
}
