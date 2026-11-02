<?php

namespace App\Filament\Resources\TipeKamarResource\Pages;

use App\Filament\Resources\TipeKamarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipeKamar extends EditRecord
{
    protected static string $resource = TipeKamarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
