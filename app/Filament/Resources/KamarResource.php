<?php

namespace App\Filament\Resources;

use App\Models\Kamar;
use App\Models\TipeKamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KamarResource\Pages;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Kelola Kamar';
    protected static ?string $navigationGroup = 'Manajemen Data';
    protected static ?int $navigationSort = 2;

    /**
     * 🔥 OPTIMASI QUERY (HINDARI N+1)
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['tipeKamar', 'detailPemesanan.pemesanan']); // ✅ TAMBAH ini untuk load status
    }

    /**
     * FORM CREATE & EDIT
     */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('nomor_kamar')
                ->label('Nomor Kamar')
                ->required()
                ->unique(ignoreRecord: true)
                ->numeric()
                ->placeholder('Contoh: 101'),

            Forms\Components\Select::make('id_tipe_kamar')
                ->label('Tipe Kamar')
                ->relationship('tipeKamar', 'nama_tipe_kamar')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('nama_kamar')
                ->label('Nama Kamar')
                ->required()
                ->maxLength(100)
                ->placeholder('Contoh: Standard Room 101'),
        ]);
    }

    /**
     * TABLE VIEW
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('nomor_kamar')
                    ->label('Nomor Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_kamar')
                    ->label('Nama Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipeKamar.nama_tipe_kamar')
                    ->label('Nama Tipe Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipeKamar.harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                // ✅ TAMBAH KOLOM INI - Status Kamar
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        // Cek apakah kamar sedang dipesan (ada booking aktif hari ini)
                        $hasActiveBooking = $record->detailPemesanan()
                            ->whereHas('pemesanan', function ($query) {
                                $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                                    ->where('payment_status', 'paid')
                                    ->where('tgl_check_in', '<=', now())
                                    ->where('tgl_check_out', '>=', now());
                            })
                            ->exists();

                        return $hasActiveBooking ? 'Terisi' : 'Tersedia';
                    })
                    ->colors([
                        'success' => 'Tersedia',
                        'danger' => 'Terisi',
                    ])
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query; // Bisa dikustomisasi nanti
                    }),

                // ✅ TAMBAH KOLOM INI - Info Tamu (Opsional)
                Tables\Columns\TextColumn::make('current_guest')
                    ->label('Tamu Saat Ini')
                    ->getStateUsing(function ($record) {
                        $currentBooking = $record->detailPemesanan()
                            ->whereHas('pemesanan', function ($query) {
                                $query->where('status_pemesanan', 'checkin')
                                    ->where('payment_status', 'paid')
                                    ->where('tgl_check_in', '<=', now())
                                    ->where('tgl_check_out', '>=', now());
                            })
                            ->with('pemesanan')
                            ->first();

                        if ($currentBooking) {
                            $checkout = $currentBooking->pemesanan->tgl_check_out->format('d M Y');
                            return $currentBooking->pemesanan->nama_pemesan . " (s/d {$checkout})";
                        }

                        return '-';
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false), // ← Tampilkan by default

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_tipe_kamar')
                    ->label('Filter Tipe Kamar')
                    ->relationship('tipeKamar', 'nama_tipe_kamar'),

                // ✅ TAMBAH FILTER INI - Filter berdasarkan Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Kamar')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'terisi' => 'Terisi',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        if ($data['value'] === 'terisi') {
                            return $query->whereHas('detailPemesanan', function ($q) {
                                $q->whereHas('pemesanan', function ($query) {
                                    $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                                        ->where('payment_status', 'paid')
                                        ->where('tgl_check_in', '<=', now())
                                        ->where('tgl_check_out', '>=', now());
                                });
                            });
                        } elseif ($data['value'] === 'tersedia') {
                            return $query->whereDoesntHave('detailPemesanan', function ($q) {
                                $q->whereHas('pemesanan', function ($query) {
                                    $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                                        ->where('payment_status', 'paid')
                                        ->where('tgl_check_in', '<=', now())
                                        ->where('tgl_check_out', '>=', now());
                                });
                            });
                        }

                        return $query;
                    }),
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
     * RELATIONS (jika ada)
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * PAGES
     */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKamars::route('/'),
            'create' => Pages\CreateKamar::route('/create'),
            'edit'   => Pages\EditKamar::route('/{record}/edit'),
        ];
    }
}
