<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\SlaPolicyResource\Pages;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Models\SlaPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaPolicyResource extends Resource
{
    protected static ?string $model = SlaPolicy::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 12;

    protected static ?string $modelLabel = 'SLA Policy';

    protected static ?string $pluralModelLabel = 'SLA Policies';

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Policy Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->rows(2),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Policy')
                            ->helperText('The default policy is applied to new tickets automatically.'),

                        Forms\Components\Toggle::make('business_hours_only')
                            ->label('Business Hours Only')
                            ->helperText('Count only business hours toward SLA deadlines.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('First Response Time (hours)')
                    ->description('Maximum hours for the first agent response, by priority level.')
                    ->schema(
                        collect(TicketPriority::cases())->map(fn (TicketPriority $p) => Forms\Components\TextInput::make("first_response_hours.{$p->value}")
                            ->label($p->label())
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5)
                            ->suffix('hours')
                        )->all()
                    )
                    ->columns(5),

                Forms\Components\Section::make('Resolution Time (hours)')
                    ->description('Maximum hours for full ticket resolution, by priority level.')
                    ->schema(
                        collect(TicketPriority::cases())->map(fn (TicketPriority $p) => Forms\Components\TextInput::make("resolution_hours.{$p->value}")
                            ->label($p->label())
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5)
                            ->suffix('hours')
                        )->all()
                    )
                    ->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('business_hours_only')
                    ->label('Business Hours')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default'),
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
            'index' => Pages\ListSlaPolicies::route('/'),
            'create' => Pages\CreateSlaPolicy::route('/create'),
            'edit' => Pages\EditSlaPolicy::route('/{record}/edit'),
        ];
    }
}
