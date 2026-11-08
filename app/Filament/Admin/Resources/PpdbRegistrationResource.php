<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\PpdbStatus;
use App\Filament\Admin\Resources\PpdbRegistrationResource\Pages;
use App\Models\PpdbRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PpdbRegistrationResource extends Resource
{
    protected static ?string $model = PpdbRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Seleksi PPDB Online';

    protected static ?string $modelLabel = 'Pendaftar PPDB';

    protected static ?string $pluralModelLabel = 'Data Pendaftar PPDB';

    protected static ?string $navigationGroup = 'Layanan PPDB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Registrasi & Seleksi')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status Seleksi')
                            ->options(collect(PpdbStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Panitia Seleksi')
                            ->placeholder('Jadwal observasi, kekurangan berkas, dll.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Data Calon Santri')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('child_name')
                            ->label('Nama Lengkap')
                            ->required(),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required(),
                        Forms\Components\TextInput::make('previous_school')
                            ->label('Asal Lembaga (RA/TK)')
                            ->required(),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Data Orang Tua / Wali')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('parent_name')
                            ->label('Nama Orang Tua')
                            ->required(),
                        Forms\Components\TextInput::make('parent_whatsapp')
                            ->label('No. WhatsApp')
                            ->required(),
                        Forms\Components\TextInput::make('parent_job')
                            ->label('Pekerjaan Orang Tua'),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Domisili')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Berkas Dokumen')
                    ->columns(3)
                    ->schema([
                        Forms\Components\FileUpload::make('file_birth_certificate')
                            ->label('Akta Kelahiran')
                            ->disk('public')
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('file_family_card')
                            ->label('Kartu Keluarga')
                            ->disk('public')
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('file_photo')
                            ->label('Pasfoto Santri')
                            ->disk('public')
                            ->openable()
                            ->downloadable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('child_name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'L' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('previous_school')
                    ->label('Asal Sekolah')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('parent_name')
                    ->label('Orang Tua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_whatsapp')
                    ->label('WhatsApp')
                    ->url(fn ($record) => "https://wa.me/{$record->parent_whatsapp}", shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (PpdbStatus $state): string => $state->label())
                    ->color(fn (PpdbStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Seleksi')
                    ->options(collect(PpdbStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn (PpdbRegistration $record): string => route('ppdb.print_proof', $record->registration_number), shouldOpenInNewTab: true),
            ])
            ->bulkActions([
                // Intentionally empty for applicant integrity & no-intl environment compatibility
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPpdbRegistrations::route('/'),
            'view' => Pages\ViewPpdbRegistration::route('/{record}'),
        ];
    }
}
