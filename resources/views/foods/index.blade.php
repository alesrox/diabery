<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diabery - Food Dictionary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .app-container { max-width: 80%; margin: 0 auto; padding: 0 0 10px 0; background: white; border-radius: 15px; }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm">
    <div class="p-3">
        <h4 class="fw-bold mb-3">Food Dictionary</h4>

        <div class="card mb-4 border-0 bg-light">
            <div class="card-body">
                <form action="{{ route('foods.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="small fw-bold">Food Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Pasta" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Carbs (per 100g)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="carbs_100g" class="form-control" placeholder="0.0" required>
                            <span class="input-group-text">g</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Add to my list</button>
                </form>
            </div>
        </div>

        <ul class="list-group list-group-flush">
            @forelse($foods as $food)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <span class="fw-bold text-capitalize">{{ $food->name }}</span>
                        <div class="small text-muted">{{ $food->carbs_100g }}g CH / 100g</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info rounded-pill text-dark me-2">{{ $food->carbs_100g / 10 }} raciones</span>
                        
                        <form action="{{ route('foods.destroy', $food) }}" method="POST" onsubmit="return confirm('¿Borrar este alimento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </li>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted fs-1"></i>
                    <p class="text-muted mt-2">Your dictionary is empty.</p>
                </div>
            @endforelse
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>