<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\FlightClass;
use App\Models\FlightSeat;
use App\Models\PromoCode;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Pest\Laravel\get;

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
                        TextInput::make('code')
                            ->required(),
                        Select::make('flight_id')
                            ->relationship('flight', 'flight_number')
                            ->afterStateUpdated(function (callable $set) {
                                $set('flight_class_id', null);
                            })
                            ->live()
                            ->required(),
                        Select::make('flight_class_id')
                            ->relationship('class', 'class_type')
                            ->live()
                            ->required()
                            ->options(function ($get) {
                                $flightId = $get('flight_id'); // Get the selected flight_id
                                if (!$flightId) {
                                    return []; // Return empty options if no flight is selected
                                }

                                // Fetch classes related to the selected flight
                                $class =  FlightClass::where('flight_id', $flightId)->get();
                                return $class->pluck('class_type', 'id')
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $class = FlightClass::find($state);
                                    $set('subtotal', $class->price);
                                } else {
                                    $set('subtotal', null);
                                }
                            }),
                    ]),
                Section::make('Informasi Penumpang')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email'),
                        TextInput::make('phone'),
                        TextInput::make('number_of_passengers')
                            ->label('Jumlah Penumpang')
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('transaction_passengers', array_fill(0, $state, [
                                        'name' => null,
                                        'seat' => null,
                                        'date_of_birth' => null,
                                        'nationality' => null,
                                    ]));
                                } else {
                                    $set('number_of_passengers', 1);
                                    $set('transaction_passengers', array_fill(0, 1, [
                                        'name' => null,
                                        'seat' => null,
                                        'date_of_birth' => null,
                                        'nationality' => null,
                                    ]));
                                }
                            })
                            ->default(1)
                            ->minValue(1),
                        Section::make('Daftar Penumpang')
                            ->schema([
                                Repeater::make('transaction_passengers')
                                    ->relationship('passengers')
                                    ->deletable(false)
                                    ->collapsible()
                                    ->live()
                                    ->itemLabel(fn($state) => $state['name'] ?? null)
                                    ->maxItems(fn($get) => $get('number_of_passengers'))
                                    // ->afterStateUpdated(function ($state, callable $set) {
                                    //     $set('number_of_passengers', count($state)); // Mengatur total elemen repeater
                                    // })
                                    ->schema([
                                        Select::make('seat')
                                            ->options(function () {
                                                // Mengambil daftar kursi dari database atau array
                                                return FlightSeat::all()->pluck('column', 'id')->toArray();
                                            })
                                            ->searchable() // Menambahkan fitur pencarian
                                            ->placeholder('Select a seat') // Placeholder untuk select
                                            ->required(), // Menandakan field ini wajib diisi
                                        TextInput::make('name')
                                            ->live()
                                            ->afterStateUpdated(fn($state, $set) => $set('canAdd', !empty($state)))
                                            ->required(),
                                        DatePicker::make('date_of_birth')
                                            ->required(),
                                        TextInput::make('nationality')
                                            ->required(),
                                    ])
                                    ->addable(false)
                            ]),


                    ]),
                Section::make('Promo Code')
                    ->schema([
                        TextInput::make('promo_code_id')
                            ->live()
                            ->debounce(500)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $promo = PromoCode::query()->where('code', $state)->first();
                                    if ($promo) {
                                        $set('promo_code_message', 'Kode ada');
                                    } else {
                                        $set('promo_code_message', 'Kode tidak valid');
                                    }
                                } else {
                                    $set('promo_code_message', '');
                                }
                            })
                            ->helperText(function ($get) {
                                return $get('promo_code_message'); // Ambil pesan dari state
                            }),
                    ])->columns(2),
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
