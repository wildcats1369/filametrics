<?php
namespace wildcats1369\Filametrics\RelationManagers;

use Filament\Resources\RelationManagers\HasManyRelationManager;

class AccountsRelationManager extends HasManyRelationManager
{
    protected static string $relationship = 'accounts';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'numeric' => 'Numeric',
                        'upload' => 'Upload',
                    ]),
                TextInput::make('label'),
                TextInput::make('provider'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type'),
                TextColumn::make('provider'),
            ]);
    }
}