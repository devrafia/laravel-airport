<?php

use function Livewire\Volt\{state, mount};
use App\Models\FlightClass;
use App\Models\FlightSeat;
use App\Models\Transaction;
use Midtrans\Config;
state([
    'classes' => [],
    'seats' => [],
    'flight_id',
    'price',
    'selectedClassId',
    'name',
    'email',
    'phone',
    'flightClass',
]);

mount(function ($id) {
    $this->classes = FlightClass::query()->where('flight_id', $id)->get();
    $this->seats = FlightSeat::query()->where('flight_id', $id)->get();
    $this->flight_id = $id;
});

$updatedSelectedClassId = function ($value) {
    $class = FlightClass::find($value);
    $this->price = $class ? $class->price : null;
    $this->flightClass = FlightClass::find($this->selectedClassId);
};

$submit = function () {
    $this->validate([
        'name' => 'required',
        'email' => 'required',
        'phone' => 'required',
    ]);

    $transaction = Transaction::create([
        'flight_id' => $this->flight_id,
        'flight_class_id' => $this->selectedClassId,
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'number_of_passengers' => 1,
        'payment_status' => 'pending',
        'subtotal' => $this->price,
        'grandtotal' => $this->price,
    ]);

    // Set your Merchant Server Key
    Config::$serverKey = config('midtrans.serverKey');
    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    Config::$isProduction = false;
    // Set sanitization on (default)
    Config::$isSanitized = true;
    // Set 3DS transaction for credit card to true
    Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => $transaction->code, // ID unik untuk transaksi
            'gross_amount' => 1000, // Total pembayaran
        ],
        'customer_details' => [
            'first_name' => $transaction->name,
            'email' => $transaction->email,
            'phone' => $transaction->phone,
        ],
        'item_details' => [
            [
                'id' => 'flight-' . $this->flight_id,
                'name' => 'Flight Ticket - ' . $this->flightClass->class_type, // Nama kelas penerbangan
                'quantity' => 1,
                'price' => $this->price, // Harga tiket
            ],
        ],
    ];
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    $transaction->snap_token = $snapToken;
    $transaction->save();

    return to_route('flight.pay', ['id' => $transaction->id]);
};

?>

<div>
    <div class="flex flex-col gap-2">
        <h1>Isi Form</h1>
        <form id="payment-form" wire:submit.prevent="submit">
            <div class="flex flex-col">
                <label for="tier">Select Tier</label>
                <select name="tier" id="tier" wire:model.live="selectedClassId">
                    <option value="">Select Class</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="seat">Select Seat</label>
                <select name="seat" id="seat">
                    @foreach ($seats as $seat)
                        <option value="{{ $seat->id }}">{{ $seat->row }}{{ $seat->column }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" wire:model.live="name">
                @error('name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex flex-col">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" wire:model="email">
                @error('email')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex flex-col">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" wire:model="phone">
                @error('phone')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex flex-col">
                <label for="price">price</label>
                <input type="number" wire:model.live="price" name="price" id="price" value="{{ $price ?? 0 }}"
                    readonly>
                @error('price')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="p-2 mt-2 text-white bg-blue-500 rounded-md shadow-lg">Checkout</button>
        </form>
        {{-- <button type="submit" id="pay-button" class="p-2 mt-2 text-white bg-blue-500 rounded-md shadow-lg">Bayar
            Sekarang</button> --}}
        <div class="bg-white rounded-md shadow-lg">
            <h1>Transaction details</h1>
            <div>
                <h3>Price</h3>
                {{-- <input type="number" wire:model.live="price" name="price" id="price" value="{{ $price ?? 0 }}"
                    readonly> --}}
                <span>{{ $price ?? '-' }}</span>
            </div>
        </div>
    </div>

</div>
{{-- @section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            let classId = document.getElementById('tier').value;
            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let phone = document.getElementById('phone').value;
            let price = document.getElementById('price').value;

            fetch('{{ route('checkout.create') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "flight_id": {{ $flight_id }},
                        "selectedClassId": classId,
                        "name": name,
                        "email": email,
                        "phone": phone,
                        "price": price,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    snap.pay(data.snap_token, {
                        // Optional
                        onSuccess: function(result) {

                        },
                        // Optional
                        onPending: function(result) {
                            /* You may add your own js here, this is just example */
                            document.getElementById('result-json').innerHTML += JSON.stringify(result,
                                null, 2);
                        },
                        // Optional
                        onError: function(result) {
                            /* You may add your own js here, this is just example */
                            document.getElementById('result-json').innerHTML += JSON.stringify(result,
                                null, 2);
                        }
                    });
                })
                .catch(error => console.error("Error sending email:", error));
            // SnapToken acquired from previous step

        };
    </script>
@endsection --}}
