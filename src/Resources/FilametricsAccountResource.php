<?php

namespace wildcats1369\Filametrics\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use wildcats1369\Filametrics\Resources\FilametricsAccountResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class FilametricsAccountResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FilametricsAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Accounts';
    protected static ?string $navigationGroup = 'Filametrics';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'numeric' => 'Numeric',
                    'upload' => 'Upload',
                ])->required(),
            Forms\Components\TextInput::make('provider')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('label')->sortable(),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('provider'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilametricsAccounts::route('/'),
            'create' => Pages\CreateFilametricsAccount::route('/create'),
            'edit' => Pages\EditFilametricsAccount::route('/{record}/edit'),
            'view' => Pages\ViewFilametricsAccount::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            // 'filametricsAccount:create',
            // 'filametricsAccount:update',
            // 'filametricsAccount:delete',
            // 'filametricsAccount:pagination',
            // 'filametricsAccount:detail',
        ];
    }
}
