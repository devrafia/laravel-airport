<?php

use function Livewire\Volt\{state, mount, with, usesPagination};
use App\Models\Transaction;

state(['index' => 0]);

mount(function () {});

usesPagination();

with(fn() => ['transactions' => Transaction::query()->latest()->paginate(5)]);

?>

<div>


    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Code
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Phone
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Price
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    {{ $index++ }}
                    <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $transaction->code }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $transaction->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $transaction->email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $transaction->phone }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $transaction->grandtotal }}
                        </td>
                        <td class="px-6 py-4">
                            <x-pay-button :snap_token="$transaction->snap_token" :transactionId="$transaction->id" id="pay-button-{{ $index }}" />
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $transactions->links() }}
    </div>

</div>
