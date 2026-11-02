<?php

namespace App\Filament\Widgets;

use App\Models\Pemesanan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pemesanan::query()
                    ->with(['user', 'detailPemesanan.kamar.tipeKamar'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id_booking')
                    ->label('ID Booking')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('nama_pemesan')
                    ->label('Nama Pemesan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('detailPemesanan')
                    ->label('Kamar')
                    ->formatStateUsing(function ($record) {
                        $rooms = $record->detailPemesanan->map(function ($detail) {
                            return $detail->kamar->nomor_kamar;
                        })->join(', ');

                        return $rooms ?: '-';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('tgl_check_in')
                    ->label('Check In')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_check_out')
                    ->label('Check Out')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_pemesanan')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirm',
                        'primary' => 'checkin',
                        'danger' => 'cancel',
                        'gray' => 'checkout',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'confirm',
                        'heroicon-o-arrow-right-on-rectangle' => 'checkin',
                        'heroicon-o-x-circle' => 'cancel',
                        'heroicon-o-arrow-left-on-rectangle' => 'checkout',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Pemesanan Terbaru')
            ->description('10 pemesanan terbaru di sistem');
    }
}
