<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <title>{{ $title ?? 'Page Title' }}</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-slate-100">
    <div class="container  w-[80%] mx-auto">
        <nav class="min-h-[10vh] mt-2 ">
            <ul class="flex flex-row items-center justify-around p-4 bg-white rounded-lg gap-7">
                <li class="ml-5 mr-auto ">
                    <a href="{{ route('flight') }}" class="text-lg font-bold tracking-wider">
                        Airport
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('flight') }}" class="font-semibold">
                        Flights
                    </a>
                </li>
                <li class="">
                    <a href="#" class="font-semibold">
                        Schedule
                    </a>
                </li>
                <li class="">
                    <a href="#" class="font-semibold">
                        Testimonials
                    </a>
                </li>
                <li class="ml-auto">
                    <a href="{{ route('flight.history') }}"
                        class="p-3 px-8 font-semibold text-center text-white rounded-full shadow-lg bg-slate-600 hover:bg-slate-900 active:bg-slate-800">
                        My Booking
                    </a>
                </li>
            </ul>
        </nav>
        {{ $slot }}
    </div>
    @yield('scripts')
</body>

</html>
