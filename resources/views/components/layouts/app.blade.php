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
    <div class="container w-[80%] mx-auto">
        <nav class="min-h-[10vh] mt-2 flex items-center justify-between p-4 bg-white rounded-lg shadow-md">
            <div class="flex items-center gap-7">
                <a href="{{ route('flight') }}" class="text-lg font-bold tracking-wider">
                    Airport
                </a>
            </div>
            <div class="flex items-center gap-7">
                <a href="{{ route('flight') }}" class="font-semibold">
                    Flights
                </a>
                <a href="#" class="font-semibold">
                    Schedule
                </a>
                <a href="#" class="font-semibold">
                    Testimonials
                </a>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <div class="relative">
                        <button id="profile-menu" class="flex items-center focus:outline-none">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}"
                                class="w-10 h-10 rounded-full">
                            <span class="ml-2 font-semibold">{{ Auth::user()->name }}</span>
                        </button>
                        <div id="profile-dropdown"
                            class="absolute right-0 z-10 hidden w-48 mt-2 bg-white rounded-md shadow-lg">
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="{{ route('flight.history') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Booking</a>
                            <form action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-blue-500 hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="font-semibold text-blue-500 hover:underline">Register</a>
                @endauth
            </div>
        </nav>
        <div class="mt-8">
            {{ $slot }}
        </div>
    </div>
    @yield('scripts')

    <script>
        document.getElementById('profile-menu').onclick = function() {
            var dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        };
    </script>
</body>

</html>
