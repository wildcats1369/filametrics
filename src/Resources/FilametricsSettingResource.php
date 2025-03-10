<?php

namespace wildcats1369\Filametrics\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use wildcats1369\Filametrics\Models\FilametricsSetting;
use wildcats1369\Filametrics\Resources\FilametricsSettingResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class FilametricsSettingResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FilametricsSetting::class;

    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Filametrics';
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
            // 'filametricsSetting:create',
            // 'filametricsSetting:update',
            // 'filametricsSetting:delete',
            // 'filametricsSetting:pagination',
            // 'filametricsSetting:detail',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('view_id')
                ->label('View ID')
                ->required()
                ->maxLength(255),
            Forms\Components\FileUpload::make('service_account_credentials_json')
                ->label('Google Service Account Credentials')
                ->disk('local') // Specify the storage disk
                ->directory('credentials') // Specify directory
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('view_id')
                ->label('View ID')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Last Updated')
                ->dateTime(),
        ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->label('Created Date')
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilametricsSettings::route('/'),
            'create' => Pages\CreateFilametricsSetting::route('/create'),
            'edit' => Pages\EditFilametricsSetting::route('/{record}/edit'),
            'view' => Pages\ViewFilametricsSetting::route('/{record}'),
        ];
    }

    // public static function getRouteName(): string
    // {
    //     return 'filametrics-site';
    // }

    // public static function getRouteNamePrefix(): string
    // {
    //     return 'filament.admin.resources.';
    // }
}
