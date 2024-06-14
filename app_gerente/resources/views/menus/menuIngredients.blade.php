@extends('layouts.app')

@section('title', 'Menús con Ingredientes')

@section('content')
    <h1 class="mb-4 text-center">Menús</h1>

    @if (isset($error))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @else
        @foreach($menus as $menu)
            <div class="card mb-4">
                <div class="card-header">
                    <h2>{{ $menu['name'] }}</h2>
                </div>
                <div class="card-body">
                    <h4>Ingredientes:</h4>
                    <ul>
                        @foreach($menu['ingredients'] as $ingredient)
                            <li>{{ $ingredient['ingredient_name'] }} ({{ $ingredient['quantity'] }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @endif
@endsection
