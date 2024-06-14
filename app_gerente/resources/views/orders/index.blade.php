@extends('layouts.app')

@section('title', 'Gestión de Ordenes')

@section('content')
    <h1 class="mb-4 text-center">Gestión de Ordenes</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Incluir el formulario -->
    @include('orders.partials.form')

    <!-- Listado de órdenes -->
    <div class="card">
        <div class="card-header">
            <h2>Listado de Ordenes</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Status</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ $order->status->name }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                @if($order->status->name == 'Despachada')
                                    <i class="fas fa-check text-success"></i>
                                @elseif($order->status->name == 'En proceso')
                                    <i class="fas fa-clock text-warning"></i>
                                @else
                                    <i class="fas fa-minus"></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
