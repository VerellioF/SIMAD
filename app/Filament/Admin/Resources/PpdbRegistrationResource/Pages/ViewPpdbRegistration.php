<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PpdbRegistrationResource\Pages;

use App\Filament\Admin\Resources\PpdbRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPpdbRegistration extends ViewRecord
{
    protected static string $resource = PpdbRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('cetak_pdf')
                ->label('Cetak Lembar Bukti (PDF)')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn (): string => route('ppdb.print_proof', $this->record->registration_number), shouldOpenInNewTab: true),
        ];
    }
}
