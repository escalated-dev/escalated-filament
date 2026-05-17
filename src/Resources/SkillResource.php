<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\SkillResource\Pages;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Skill;
use Escalated\Laravel\Models\Tag;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?int $navigationSort = 24;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('agents');
    }

    /**
     * @param  array<int, array{user_id?: int|string|null, proficiency?: int|string|null}>  $agents
     */
    public static function syncAgentsForSkill(Skill $skill, array $agents): void
    {
        $payload = collect($agents)
            ->filter(fn ($row) => is_array($row) && filled($row['user_id'] ?? null))
            ->mapWithKeys(fn (array $agent) => [
                (int) $agent['user_id'] => ['proficiency' => (int) ($agent['proficiency'] ?? 3)],
            ])
            ->all();

        $skill->agents()->sync($payload);
    }

    /**
     * @return array<int|string, string>
     */
    public static function agentUserOptions(): array
    {
        $userModel = Escalated::userModel();
        $userInstance = new $userModel;
        $userTable = $userInstance->getTable();
        $userKey = $userInstance->getKeyName();
        $displayColumn = Escalated::userDisplayColumn();

        $query = $userModel::query()->orderBy($displayColumn);

        $columns = SchemaFacade::getColumnListing($userTable);
        if (in_array('is_agent', $columns, true) || in_array('is_admin', $columns, true)) {
            $query->where(function (Builder $builder) use ($columns): void {
                if (in_array('is_agent', $columns, true)) {
                    $builder->orWhere('is_agent', true);
                }
                if (in_array('is_admin', $columns, true)) {
                    $builder->orWhere('is_admin', true);
                }
            });
        }

        return $query->pluck($displayColumn, $userKey)->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('routing_tag_ids')
                            ->label('Routing tags')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Tag::query()->orderBy('name')->pluck('name', 'id')->all()),

                        Forms\Components\Select::make('routing_department_ids')
                            ->label('Routing departments')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all()),

                        Forms\Components\Repeater::make('agents')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Agent')
                                    ->options(fn (): array => static::agentUserOptions())
                                    ->searchable()
                                    ->required()
                                    ->distinct(),
                                Forms\Components\Select::make('proficiency')
                                    ->label('Proficiency')
                                    ->options([
                                        1 => '1',
                                        2 => '2',
                                        3 => '3',
                                        4 => '4',
                                        5 => '5',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->default(3),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Add agent'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agents_count')
                    ->label('Agents')
                    ->sortable(),

                Tables\Columns\TextColumn::make('routing_tags_count')
                    ->label('Routing tags')
                    ->state(fn (Skill $record): int => count($record->routing_tag_ids ?? [])),

                Tables\Columns\TextColumn::make('routing_departments_count')
                    ->label('Routing departments')
                    ->state(fn (Skill $record): int => count($record->routing_department_ids ?? [])),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}
