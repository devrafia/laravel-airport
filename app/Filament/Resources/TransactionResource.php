<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\FlightSeat;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Umum')
                    ->schema([

                        TextInput::make('code'),
                        Select::make('flight_id')
                            ->relationship('flight', 'flight_number'),
                        Select::make('flight_class_id')
                            ->relationship('class', 'class_type')
                    ]),
                Section::make('Informasi Penumpang')
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('email'),
                        TextInput::make('phone'),
                        Section::make('Daftar Penumpang')
                            ->schema([
                                Repeater::make('transaction_passengers')
                                    ->relationship('passengers')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('number_of_passengers', count($state)); // Mengatur total elemen repeater
                                    })
                                    ->schema([
                                        Select::make('seat')
                                            ->options(function () {
                                                // Mengambil daftar kursi dari database atau array
                                                return FlightSeat::all()->pluck('column', 'id')->toArray();
                                            })
                                            ->searchable() // Menambahkan fitur pencarian
                                            ->placeholder('Select a seat') // Placeholder untuk select
                                            ->required(), // Menandakan field ini wajib diisi
                                        TextInput::make('name'),
                                        DatePicker::make('date_of_birth'),
                                        TextInput::make('nationality'),
                                    ])
                            ]),
                        TextInput::make('number_of_passengers')
                            ->default(1)
                            ->minValue(1)
                            ->disabled()

                    ]),
                Section::make('Promo Code')
                    ->schema([
                        TextInput::make('promo_code_id')
                    ]),
                Section::make('Informasi Harga')
                    ->schema([
                        TextInput::make('subtotal')
                            ->numeric()
                            ->required(),
                        TextInput::make('grandtotal')
                            ->numeric()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('flight.flight_number'),
                TextColumn::make('flight_class.class_type'),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('number_of_passangers'),
                TextColumn::make('promo_code.code'),
                TextColumn::make('payment_status'),
                TextColumn::make('subtotal'),
                TextColumn::make('grandtotal'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
