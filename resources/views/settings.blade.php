<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Application Settings') }} - Diabery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .app-container { max-width: 80%; margin: 20px auto; background-color: var(--bs-card-bg); border-radius: 15px; }
    </style>
</head>
<body>

@include('components.navbar')

<div class="app-container shadow-sm p-4 mb-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-gear-fill me-2 text-primary"></i>{{ __('Application Settings') }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-transparent fw-bold text-primary"><i class="bi bi-person-fill me-2"></i>{{ __('Account') }}</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">{{ __('New Password') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Leave blank to keep current') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">{{ __('Confirm New Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-transparent fw-bold text-primary"><i class="bi bi-capsule me-2"></i>{{ __('Diabetes Parameters') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">{{ __('Carbohydrate Ratio (CH)') }}</label>
                        <input type="number" step="0.1" name="carb_insulin_ratio" class="form-control" value="{{ auth()->user()->carb_insulin_ratio }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">{{ __('Insulin Sensitivity Factor (ISF)') }}</label>
                        <input type="number" step="0.1" name="insulin_sensitivity_factor" class="form-control" value="{{ auth()->user()->insulin_sensitivity_factor }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">{{ __('Target Glucose') }}</label>
                        <input type="number" name="target_glucose" class="form-control" value="{{ auth()->user()->target_glucose }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-transparent fw-bold text-primary"><i class="bi bi-sliders me-2"></i>{{ __('Visual Preferences') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">{{ __('Language') }}</label>
                        <select id="languageSelect" class="form-select">
                            <option value="es">{{ __('Spanish') }}</option>
                            <option value="en">{{ __('English') }}</option>
                        </select>
                    </div>
                    <!-- <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                            <label class="form-check-label fw-semibold ms-2" for="darkModeSwitch">{{ __('Enable Dark Mode') }}</label>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary px-5 fw-bold">{{ __('Save Changes') }}</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const languageSelect = document.getElementById('languageSelect');
        const htmlElement = document.documentElement;

        const savedLang = localStorage.getItem('lang');
        if (savedLang) {
            languageSelect.value = savedLang;
        } else {
            languageSelect.value = "{{ app()->getLocale() }}";
        }

        languageSelect.addEventListener('change', function() {
            const selectedLang = this.value;
            localStorage.setItem('lang', selectedLang);
            document.cookie = `lang=${selectedLang}; expires=${new Date(Date.now() + 365*24*60*60*1000).toUTCString()}; path=/; SameSite=Lax`;
            setTimeout(() => {
                window.location.href = window.location.pathname;
            }, 50);
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>