<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Voucher';

    protected static ?string $modelLabel = 'Voucher';

    protected static ?string $pluralModelLabel = 'Voucher';

    protected static ?string $navigationGroup = 'Manajemen Promo';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Voucher')
                    ->description('Detail informasi voucher')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('kode_voucher')
                                    ->label('Kode Voucher')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('WELCOME50K')
                                    ->helperText('Kode unik untuk voucher ini')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('nama_voucher')
                                    ->label('Nama Voucher')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Voucher Selamat Datang 50K')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Diskon Rp 50.000 untuk member baru dengan minimal transaksi Rp 500.000')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pengaturan Diskon')
                    ->description('Atur nilai diskon dan persyaratan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('jumlah_diskon')
                                    ->label('Jumlah Diskon (Rp)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('50000')
                                    ->helperText('Nominal diskon dalam rupiah')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('minimal_transaksi')
                                    ->label('Minimal Transaksi (Rp)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->placeholder('500000')
                                    ->helperText('Minimal pembelian untuk menggunakan voucher')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('tipe_voucher')
                                    ->label('Tipe Voucher')
                                    ->options([
                                        'welcome' => 'Welcome (Otomatis untuk user baru)',
                                        'promo' => 'Promo (Dibuat manual admin)',
                                        'special' => 'Special (Event khusus)',
                                    ])
                                    ->required()
                                    ->default('promo')
                                    ->helperText('Welcome voucher otomatis diberikan ke user baru')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Periode & Batasan')
                    ->description('Atur masa berlaku dan batasan penggunaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('tanggal_berakhir')
                                    ->label('Tanggal Berakhir')
                                    ->required()
                                    ->default(now()->addMonth())
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->after('tanggal_mulai')
                                    ->columnSpan(1),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('maksimal_penggunaan')
                                    ->label('Maksimal Penggunaan')
                                    ->numeric()
                                    ->nullable()
                                    ->placeholder('Kosongkan untuk unlimited')
                                    ->helperText('Batas total penggunaan voucher (kosongkan untuk unlimited)')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Voucher hanya bisa digunakan jika aktif')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Statistik')
                    ->description('Data penggunaan voucher (read-only)')
                    ->schema([
                        Forms\Components\TextInput::make('total_digunakan')
                            ->label('Total Digunakan')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Jumlah voucher yang sudah digunakan'),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_voucher')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode voucher disalin!')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('nama_voucher')
                    ->label('Nama Voucher')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('jumlah_diskon')
                    ->label('Diskon')
                    ->money('IDR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('minimal_transaksi')
                    ->label('Min. Transaksi')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('tipe_voucher')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'welcome',
                        'primary' => 'promo',
                        'warning' => 'special',
                    ])
                    ->icons([
                        'heroicon-o-gift' => 'welcome',
                        'heroicon-o-tag' => 'promo',
                        'heroicon-o-star' => 'special',
                    ]),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn($record) => $record->tanggal_berakhir < now() ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penggunaan')
                    ->label('Penggunaan')
                    ->formatStateUsing(function ($record) {
                        $max = $record->maksimal_penggunaan ?? '∞';
                        return "{$record->total_digunakan} / {$max}";
                    })
                    ->color(function ($record) {
                        if (!$record->maksimal_penggunaan) return 'gray';

                        $percentage = ($record->total_digunakan / $record->maksimal_penggunaan) * 100;

                        if ($percentage >= 90) return 'danger';
                        if ($percentage >= 70) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipe_voucher')
                    ->label('Tipe')
                    ->options([
                        'welcome' => 'Welcome',
                        'promo' => 'Promo',
                        'special' => 'Special',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua voucher')
                    ->trueLabel('Hanya yang aktif')
                    ->falseLabel('Hanya yang nonaktif'),

                Tables\Filters\Filter::make('expired')
                    ->label('Sudah Expired')
                    ->query(fn(Builder $query): Builder => $query->where('tanggal_berakhir', '<', now())),

                Tables\Filters\Filter::make('valid')
                    ->label('Masih Valid')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('is_active', true)
                        ->where('tanggal_mulai', '<=', now())
                        ->where('tanggal_berakhir', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),

                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                        ->action(fn($record) => $record->update(['is_active' => !$record->is_active]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
