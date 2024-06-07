<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Create Order</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="quantity" class="form-label">Number of Dishes</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Submit Order</button>
    </form>
</div>
</body>
</html>
