@extends('layouts.app')

@section('content')

    {{-- NAV superior (logo + cerrar sesión) --}}
    @include('dashboard.partes.nav')

    {{-- MENÚ simple con links reales entre dashboards --}}
    <div class="bg-red-600 shadow-lg sticky top-16 z-30">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-3">

                {{-- Ir al dashboard original (Ingreso) --}}
                <a href="{{ route('dashboard.inicio') }}"
                   class="tab-boton flex items-center justify-center space-x-3 py-8 text-white">
                    <ion-icon name="add-circle-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Ingreso Habilitación</span>
                </a>

                {{-- También al dashboard original (Actualizar/Eliminar) --}}
                <a href="{{ route('dashboard.inicio') }}"
                   class="tab-boton flex items-center justify-center space-x-3 py-8 text-white">
                    <ion-icon name="create-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Actualizar / Eliminar</span>
                </a>

                {{-- Dashboard actual: Listados Varios (activo) --}}
                <a href="{{ route('dashboard.inicio_listados') }}"
                   class="tab-boton activo flex items-center justify-center space-x-3 py-8 text-white">
                    <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Listados Varios</span>
                </a>

            </div>
        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL: solo el listado --}}
    <div class="max-w-7xl mx-auto p-6">
        @include('dashboard.paneles.listado')
    </div>

@endsection
