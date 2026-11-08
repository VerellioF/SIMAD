<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PpdbRegistrationResource\Pages;

use App\Filament\Admin\Resources\PpdbRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPpdbRegistrations extends ListRecords
{
    protected static string $resource = PpdbRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_emis')
                ->label('Ekspor EMIS Kemenag (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('admin.ppdb.export_emis')),
        ];
    }
}
