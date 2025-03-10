<?php

namespace wildcats1369\Filametrics\Resources;

use wildcats1369\Filametrics\Pages;
use wildcats1369\Filametrics\RelationManagers;
use App\Models\GoogleAnalyticsAccount;
use Spatie\Analytics\Facades\Analytics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Filametrics extends Resource
{
    protected static ?string $model = GoogleAnalyticsAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('view_id')->required(),
                Forms\Components\FileUpload::make('service_account_credentials_json')
                    ->required()
                    ->disk('local')
                    ->directory('analytics'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('view_id')->label('View ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('service_account_credentials_json')->label('Credentials File')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->sortable(),
            ])
            ->filters([
                // Define filters if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoogleAnalyticsAccounts::route('/'),
            'create' => Pages\CreateGoogleAnalyticsAccount::route('/create'),
            'edit' => Pages\EditGoogleAnalyticsAccount::route('/{record}/edit'),
            'view' => Pages\ViewGoogleAnalyticsAccount::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\VisitorsWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersTwentyEightDayWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsDurationWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByCountryWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByDeviceWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget::class,
            \BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget::class,
        ];
    }
}
