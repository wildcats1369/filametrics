<?php

namespace wildcats1369\Filametrics\Resources;

use Filament\Resources\Resource;
use Illuminate\Support\Facades\Route;
use Filament\Forms;
use Filament\Tables;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Panel;

class FilametricsSiteResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FilametricsSite::class;

    protected static ?string $navigationLabel = 'Sites';
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Filametrics';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('domain_name')->required(),
            Forms\Components\TextInput::make('view_id')->label('View ID'),
            Forms\Components\Toggle::make('is_visible_to_all')->label('Visible to All')->default(false),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('domain_name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('view_id'),
            Tables\Columns\BooleanColumn::make('is_visible_to_all')->label('Visible'),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilametricsSites::route('/'),
            'create' => Pages\CreateFilametricsSite::route('/create'),
            'edit' => Pages\EditFilametricsSite::route('/{record}/edit'),
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
            // 'filametricsSite:create',
            // 'filametricsSite:update',
            // 'filametricsSite:delete',
            // 'filametricsSite:pagination',
            // 'filametricsSite:detail',
        ];
    }




}
