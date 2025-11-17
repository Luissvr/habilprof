<nav class="bg-white shadow-md sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between items-center h-16">

            {{-- Logo + Título --}}
            <div class="flex items-center space-x-2">
                <img src="{{ asset('elogin/logo.png') }}" alt="Logo UCSC" class="h-12">
                <h1 class="text-xl font-bold text-gray-800">Panel HabilProf</h1>
            </div>

            {{-- Cerrar sesión --}}
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-md transition-all text-sm">
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>
