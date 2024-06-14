@extends('layouts.app')

@section('title', 'Ingredientes en Bodega')

@section('content')
    <h1 class="mb-4 text-center">Ingredientes en Bodega</h1>

    @if (isset($error))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h2>Listado de Ingredientes</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th>Cant. Disponibles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredients as $ingredient)
                            <tr>
                                <td>{{ ucfirst($ingredient['name']) }}</td>
                                <td>{{ $ingredient['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
