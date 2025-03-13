<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;

class FilametricsAccountRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    public $record;

    public function form(Form $form): Form
    {
        $providers = config('filametrics.providers.select');
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'numeric' => 'Numeric',
                    'upload' => 'Upload',
                ])->required(),
            Forms\Components\Select::make('provider')
                ->options($providers)
                ->required(),
            Forms\Components\Hidden::make('filametrics_site_id')->default(fn ($record) => $record ? $record->filametrics_site_id : request()->route('record')),
        ]);
    }

    // public function table(Table $table): Table
    // {
    //     return $table->columns([
    //         Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
    //         Tables\Columns\TextColumn::make('label')->sortable(),
    //         Tables\Columns\TextColumn::make('type'),
    //         Tables\Columns\TextColumn::make('provider'),
    //     ]);
    // }
}