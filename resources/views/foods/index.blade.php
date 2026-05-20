<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diabery - {{ __('Food Dictionary') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .app-container { max-width: 80%; margin: 20px auto; padding: 0 0 10px 0; background: white; border-radius: 15px; }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm">
    <div class="p-3">
        <h4 class="fw-bold mb-3">{{ __('Food Dictionary') }}</h4>

        <div class="card mb-4 border-0 bg-light">
            <div class="card-body">
                <form action="{{ route('foods.store') }}" method="POST">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="small fw-bold">{{ __('Food Name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="{{ __('e.g. Pasta') }}" required>
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label class="small fw-bold">{{ __('Measurement Type') }}</label>
                            <select name="measure_type" id="main_measure" class="form-select" onchange="updateLabels()">
                                <option value="grams">{{ __('Grams (g)') }}</option>
                                <option value="units">{{ __('Units (uds)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold" id="label_carbs">{{ __('Carbs (per 100g)') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="quantity" id="input_carbs" class="form-control" placeholder="0.0" required>
                            <span class="input-group-text" id="addon_carbs">g</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">{{ __('Add to my list') }}</button>
                </form>
            </div>
        </div>

        <ul class="list-group list-group-flush">
            @forelse($foods as $food)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <span class="fw-bold text-capitalize">{{ $food->name }}</span>
                        <div class="small text-muted mt-1">
                            <span>
                                <i class="bi bi-box-seam me-1"></i>
                                {{ $food->quantity }}g CH / 
                                @if($food->measure_type == 'units')
                                    {{ __('unit') }}
                                @else
                                    100g
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info rounded-pill text-dark me-2">
                            {{ number_format($food->quantity / 10, 1) }} {{ __('ratios') }}
                        </span>
                        
                        <form action="{{ route('foods.destroy', $food) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this food?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </li>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted fs-1"></i>
                    <p class="text-muted mt-2">{{ __('Your dictionary is empty.') }}</p>
                </div>
            @endforelse
        </ul>
    </div>
</div>

<script>
function updateLabels() {
    const measure = document.getElementById('main_measure').value;
    const labelCarbs = document.getElementById('label_carbs');
    const addonCarbs = document.getElementById('addon_carbs');

    if (measure === 'grams') {
        labelCarbs.innerText = "{{ __('Carbs (per 100g)') }}";
        addonCarbs.innerText = "g";
    } else {
        labelCarbs.innerText = "{{ __('Carbs (per 1 single unit)') }}";
        addonCarbs.innerText = "g / {{ __('unit') }}";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>