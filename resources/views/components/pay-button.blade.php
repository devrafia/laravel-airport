@props(['snap_token', 'transactionId', 'id'])

<div>
    @dump($snap_token)
    <button type="submit" id="{{ $id }}" data-snap-token="{{ $snap_token }}"
        data-transaction-id="{{ $transactionId }}"
        class="p-2 px-4 text-white bg-blue-400 rounded-lg pay-button hover:bg-blue-500 active:bg-blue-600">Pay
    </button>
</div>

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        document.querySelectorAll('.pay-button').forEach(function(button) {
            button.addEventListener('click', function() {
                // Ambil snap_token dan transactionId dari data atribut tombol
                var snapToken = button.getAttribute('data-snap-token');
                var transactionId = button.getAttribute('data-transaction-id');

                // SnapToken acquired from previous step
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        fetch('{{ route('checkout.mail', ['id' => '__transaction_id__']) }}'
                                .replace('__transaction_id__', transactionId), {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify(result)
                                })
                            .then(response => response.json())
                            .then(data => {
                                console.log("Email sent successfully:", data);
                                window.location.href =
                                    '{{ route('checkout.success', ['id' => '__transaction_id__']) }}'
                                    .replace('__transaction_id__', transactionId);
                            })
                            .catch(error => console.error("Error sending email:", error));
                    },
                    onPending: function(result) {
                        document.getElementById('result-json').innerHTML += JSON.stringify(
                            result, null, 2);
                    },
                    onError: function(result) {
                        document.getElementById('result-json').innerHTML += JSON.stringify(
                            result, null, 2);
                    }
                });
            });
        });
    </script>
@endsection
