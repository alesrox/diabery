<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
    <div class="container-fluid px-4"> 
        
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-droplet-fill me-2"></i> Diabery
        </a>

        <div class="d-flex align-items-center">
            <button class="btn btn-light rounded-pill me-3 fw-bold shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#actionModal">
                <i class="bi bi-plus-lg me-1"></i> Añadir
            </button>

            <div class="dropdown">
                <a class="text-white fs-4" href="#" role="button" data-bs-dropdown="toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="{{ route('foods.index') }}"><i class="bi bi-book me-2"></i>Diccionario</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Salir</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>

<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nuevo Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="d-grid gap-3">
                    <a href="{{ route('entries.create') }}" class="btn btn-primary btn-lg py-3 shadow-sm">
                        <i class="bi bi-calendar-plus me-2"></i> Nueva Entrada de Comida
                    </a>
                    <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary btn-lg py-3 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> Añadir Alimento al Diccionario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>