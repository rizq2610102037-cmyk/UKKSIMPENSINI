<?php

namespace App\Filament\Resources;

use App\Models\TipeKamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TipeKamarResource\Pages;

class TipeKamarResource extends Resource
{
    protected static ?string $model = TipeKamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Tipe Kamar';
    protected static ?string $navigationGroup = 'Manajemen Data';
    protected static ?int $navigationSort = 3;

    /**
     * hitung kamar sekalian semua, dengan di optimasi pengambilan datanya
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('kamar');
    }

    /**
     * form create edit
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_tipe_kamar')
                ->label('Nama Tipe Kamar')
                ->required()
                ->maxLength(200)
                ->placeholder('Contoh: Standard Room 101'),

            Forms\Components\TextInput::make('harga')
                ->label('Harga per Malam')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->step(1000)
                ->maxValue(99999999)
                ->placeholder('350000'),

            Forms\Components\FileUpload::make('gambar')
                ->label('Gambar Tipe Kamar')
                ->image()
                ->directory('tipe-kamar')
                ->disk('public')
                ->imagePreviewHeight('400')
                ->maxSize(2048)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    /**
     * table view
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id_tipe_kamar')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(100)
                    ->width(100)
                    ->square(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga per Malam')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_tipe_kamar')
                    ->label('Nama Tipe Kamar'),

                Tables\Columns\TextColumn::make('kamar_count')
                    ->label('Jumlah Kamar')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * pages
     */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTipeKamars::route('/'),
            'create' => Pages\CreateTipeKamar::route('/create'),
            'edit'   => Pages\EditTipeKamar::route('/{record}/edit'),
        ];
    }
}
