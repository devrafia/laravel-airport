<?php

use function Livewire\Volt\{state, mount};
use App\Models\Transaction;
state(['snap_token', 'transaction']);
mount(function () {
    $this->transaction = Transaction::find(request()->id);

    $this->snap_token = $this->transaction->snap_token;
});

?>

<div>
    <button class="p-3 text-white bg-blue-500 rounded-lg shadow-md" id="pay-button">Pay!</button>
</div>

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            // SnapToken acquired from previous step
            snap.pay("{{ $snap_token }}", {
                // Optional
                onSuccess: function(result) {
                    // fetch('{{ route('checkout.mail', ['id' => $transaction->id]) }}', {
                    //         method: "POST",
                    //         headers: {
                    //             "Content-Type": "application/json",
                    //             "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    //         },
                    //         body: JSON.stringify(result)
                    //     })
                    //     .then(response => response.json())
                    //     .then(data => {
                    //         console.log("Email sent successfully:", data);
                    //         window.location.href =
                    //             '{{ route('checkout.success', ['id' => $transaction->id]) }}';
                    //     })
                    //     .catch(error => console.error("Error sending email:", error));
                    window.location.href =
                        '{{ route('flight.history') }}';
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                }
            });
        };
    </script>
@endsection
