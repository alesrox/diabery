<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Edit Entry') }} - Diabery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: var(--bs-body-bg); }
        .app-container { max-width: 80%; margin: 0 auto; padding: 0 0 10px 0; background-color: var(--bs-card-bg); border-radius: 15px; }
        .food-card { border-left: 4px solid #198754; background-color: var(--bs-card-bg); border-top: 1px solid var(--bs-border-color); border-right: 1px solid var(--bs-border-color); border-bottom: 1px solid var(--bs-border-color); }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm mt-md-4">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0 text-body">{{ __('Edit Entry') }}</h2>
            <a href="{{ route('dashboard') }}" class="btn-close"></a>
        </div>

        <form action="{{ route('entries.update', $entry) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('PRE GLUCOSE') }}</label>
                    <input type="number" name="glucose_pre" class="form-control form-control-lg" value="{{ $entry->glucose_pre }}">
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold text-primary small">{{ __('POST GLUCOSE') }}</label>
                    <input type="number" name="glucose_post" class="form-control form-control-lg border-primary" value="{{ $entry->glucose_post }}" placeholder="{{ __('Pending') }}">
                </div>
            </div>

            <div class="card mb-4 border-0 bg-body-tertiary">
                <div class="card-body">
                    <label class="form-label fw-bold small text-body-secondary">{{ __('ADD MORE FOODS') }}</label>
                    <div class="row g-2">
                        <div class="col-8">
                            <select id="foodSelector" class="form-select">
                                <option value="">{{ __('Select...') }}</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}" data-carbs="{{ $food->carbs_100g }}">{{ $food->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" id="foodWeight" class="form-control" placeholder="g">
                                <button type="button" onclick="addFoodToList()" class="btn btn-dark"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="selectedFoodsList" class="list-group mb-4">
                @foreach($entry->foods as $f)
                <div class="list-group-item d-flex justify-content-between align-items-center food-card mb-2 shadow-sm rounded">
                    <div>
                        <strong class="text-capitalize text-body">{{ $f->name }}</strong><br>
                        <small class="text-body-secondary">{{ $f->pivot->weight_grams }}g | {{ $f->pivot->calculated_carbs }}g CH</small>
                        <input type="hidden" name="foods[{{ $f->id }}][weight_grams]" value="{{ $f->pivot->weight_grams }}">
                        <input type="hidden" name="foods[{{ $f->id }}][calculated_carbs]" value="{{ $f->pivot->calculated_carbs }}">
                    </div>
                    <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.remove(); updateTotals();">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>

            <div class="bg-primary text-white p-3 rounded-4 d-flex justify-content-between align-items-center mb-4">
                <span class="fw-bold">{{ __('Total Carbs:') }}</span>
                <span class="fs-3 fw-bold"><span id="totalCarbsLabel">{{ $entry->total_carbs_sum }}</span>g</span>
                <input type="hidden" name="total_carbs_sum" id="total_carbs_sum" value="{{ $entry->total_carbs_sum }}">
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow">{{ __('Save Changes') }}</button>
        </form>
    </div>
</div>

<script>
    function addFoodToList() {
        const selector = document.getElementById('foodSelector');
        const weightInput = document.getElementById('foodWeight');
        const list = document.getElementById('selectedFoodsList');

        if (!selector.value || !weightInput.value) return;

        const foodId = selector.value;
        const foodName = selector.options[selector.selectedIndex].text;
        const carbs100g = parseFloat(selector.options[selector.selectedIndex].dataset.carbs);
        const weight = parseFloat(weightInput.value);
        const calculatedCarbs = (weight * carbs100g) / 100;

        const item = document.createElement('div');
        // Añadida la clase 'text-body' para asegurar consistencia cromática al inyectar HTML vía JS
        item.className = "list-group-item d-flex justify-content-between align-items-center food-card mb-2 shadow-sm rounded text-body";
        item.innerHTML = `
            <div>
                <strong>${foodName}</strong><br>
                <small class="text-body-secondary">${weight}g | ${calculatedCarbs.toFixed(1)}g CH</small>
                <input type="hidden" name="foods[${foodId}][weight_grams]" value="${weight}">
                <input type="hidden" name="foods[${foodId}][calculated_carbs]" value="${calculatedCarbs.toFixed(1)}">
            </div>
            <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.remove(); updateTotals();"><i class="bi bi-trash"></i></button>
        `;
        list.appendChild(item);
        selector.value = ""; weightInput.value = "";
        updateTotals();
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('input[name*="calculated_carbs"]').forEach(input => {
            total += parseFloat(input.value);
        });
        document.getElementById('totalCarbsLabel').innerText = total.toFixed(1);
        document.getElementById('total_carbs_sum').value = total.toFixed(1);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>