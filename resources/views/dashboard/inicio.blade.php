@extends('layouts.app')

@section('content')

    {{-- NAVBAR --}}
    @include('dashboard.partes.nav')

    {{-- MENÚ DE NAVEGACIÓN --}}
    @include('dashboard.partes.menu')

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- PANEL: INGRESO --}}
            @include('dashboard.paneles.ingreso')

            {{-- PANEL: EDITAR --}}
            @include('dashboard.paneles.editar')

            {{-- PANEL: LISTADO --}}
            @include('dashboard.paneles.listado')

            {{-- SUBPANELES (Proyecto y Práctica) --}}
            @include('dashboard.paneles.sub_proyecto')
            @include('dashboard.paneles.sub_practica')

        </div>
    </main>

    {{-- SCRIPTS --}}
    @include('dashboard.script.principal')

@endsection
