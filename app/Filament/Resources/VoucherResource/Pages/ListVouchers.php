<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Voucher Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-ticket')
                ->badge(fn() => \App\Models\Voucher::count()),

            'active' => Tab::make('Aktif')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\Voucher::where('is_active', true)->count())
                ->badgeColor('success'),

            'valid' => Tab::make('Valid Hari Ini')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('is_active', true)
                        ->where('tanggal_mulai', '<=', now())
                        ->where('tanggal_berakhir', '>=', now())
                )
                ->badge(fn() => \App\Models\Voucher::where('is_active', true)
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_berakhir', '>=', now())
                    ->count())
                ->badgeColor('primary'),

            'welcome' => Tab::make('Welcome Vouchers')
                ->icon('heroicon-o-gift')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('tipe_voucher', 'welcome'))
                ->badge(fn() => \App\Models\Voucher::where('tipe_voucher', 'welcome')->count()),

            'expired' => Tab::make('Expired')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('tanggal_berakhir', '<', now()))
                ->badge(fn() => \App\Models\Voucher::where('tanggal_berakhir', '<', now())->count())
                ->badgeColor('danger'),
        ];
    }
}
