<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriptoMaster - Control de Inversiones</title>
    
    <!-- Fuentes y CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4 p-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fa fa-chart-line me-2"></i> CRIPTOMASTER
        </a>
        <div class="ms-auto d-flex gap-2">
            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdmin">
                <i class="fa fa-cog me-1"></i> Administración
            </button>
            <button class="btn btn-outline-light btn-sm" id="btn-sync" onclick="actualizarPrecios()">
                <i class="fa fa-sync-alt me-1"></i> Actualizar Precios
            </button>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <!-- Area para mensajes -->
    <div id="alert_container"></div>
    <!-- Dashboard Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm stats-card">
                <small class="text-muted text-uppercase fw-semibold">Valor Total Portfolio</small>
                <h3 class="fw-bold mb-0" id="stat_total_value">$0.00</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm stats-card">
                <small class="text-muted text-uppercase fw-semibold">Inversión Total</small>
                <h3 class="fw-bold mb-0" id="stat_total_invested">$0.00</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm stats-card">
                <small class="text-muted text-uppercase fw-semibold">Utilidad Total</small>
                <h3 class="fw-bold mb-0" id="stat_total_utility">$0.00</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm stats-card">
                <small class="text-muted text-uppercase fw-semibold">Retorno (ROI)</small>
                <h3 class="fw-bold mb-0" id="stat_total_percent">0%</h3>
            </div>
        </div>
    </div>

    <!-- Main Table Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Historial de Compras</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSummary">
                    <i class="fa fa-table me-1"></i> Resumen
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInvestments">
                    <i class="fa fa-plus me-1"></i> Agregar Compra
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="p-3 bg-light border-bottom d-flex gap-3">
                <select id="search_moneda" class="form-select w-auto" onchange="loadInvestments()"></select>
                <select id="search_exchange" class="form-select w-auto" onchange="loadInvestments(); loadStats();"></select>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Valor USD</th>
                            <th class="text-center">Precio de compra</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Precio actual</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-center">Exchange</th>
                            <th class="text-center">Utilidades</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="items_investments"></tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td class="text-center">TOTAL</td>
                            <td class="text-center" id="total_invested_footer">$0.00</td>
                            <td colspan="7">
                                <div id="pagination_container" class="d-flex justify-content-end"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva Compra -->
<div class="modal fade" id="modalInvestments" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_nueva_compra" onsubmit="saveInvestment(event)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Nueva Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Monto de Compra (USD)</label>
                        <input type="number" step="0.01" class="form-control" name="compra" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Precio del Token (USD)</label>
                        <input type="number" step="0.00000001" class="form-control" name="precio" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="fecha" value="<?=date('Y-m-d')?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Moneda</label>
                        <select name="moneda" id="moneda" class="form-select" required></select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Exchange</label>
                        <select name="exchange" id="exchange" class="form-select" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Registro</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Editar Compra -->
<div class="modal fade" id="update_investment" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_update" onsubmit="updateInvestment(event)">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Editar Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Monto de Compra (USD)</label>
                        <input type="number" step="0.01" class="form-control" name="edit_compra" id="edit_compra" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Precio del Token (USD)</label>
                        <input type="number" step="0.00000001" class="form-control" name="edit_precio" id="edit_precio" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="edit_fecha" id="edit_fecha" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Moneda</label>
                        <select name="edit_moneda" id="edit_moneda" class="form-select" required></select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Exchange</label>
                        <select name="edit_exchange" id="edit_exchange" class="form-select" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Resumen -->
<div class="modal fade" id="modalSummary" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Resumen de Inversiones</h5>
                <div class="ms-auto me-3">
                    <select id="search_exchange_resumen" class="form-select form-select-sm" onchange="loadStats()"></select>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Moneda</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Precio USD</th>
                                <th class="text-end">AVG USD</th>
                                <th class="text-end">Valor USD</th>
                                <th class="text-end">Inversión</th>
                                <th class="text-end">Utilidad</th>
                            </tr>
                        </thead>
                        <tbody id="resumen_items"></tbody>
                        <tfoot class="bg-light fw-bold" id="resumen_footer">
                            <tr>
                                <td>Totales</td>
                                <td colspan="3"></td>
                                <td class="text-end" id="resumen_total_value">$0.00</td>
                                <td class="text-end" id="resumen_total_invested">$0.00</td>
                                <td class="text-end" id="resumen_total_utility">$0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Precio Moneda -->
<div class="modal fade" id="modalEditCoin" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="form_edit_coin" onsubmit="updateCoinPrice(event)">
            <input type="hidden" name="id" id="coin_edit_id">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Ajustar Precio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" id="coin_edit_label">Precio USD</label>
                    <input type="number" step="0.0000000001" class="form-control" name="price" id="coin_edit_price" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Guardar Precio</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Administración -->
<div class="modal fade" id="modalAdmin" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa fa-cog me-2"></i>Panel de Administración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-coins" data-bs-toggle="tab" href="#content-coins" role="tab"><i class="fa fa-coins me-1"></i> Monedas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-exchanges" data-bs-toggle="tab" href="#content-exchanges" role="tab"><i class="fa fa-university me-1"></i> Exchanges</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Tab Monedas -->
                    <div class="tab-pane fade show active" id="content-coins" role="tabpanel">
                        <form id="form_coin_admin" class="row g-2 mb-3" onsubmit="saveCoinAdmin(event)">
                            <input type="hidden" name="id" id="admin_coin_id">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="name" id="admin_coin_name" placeholder="Nombre (ej: Bitcoin)" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="symbol" id="admin_coin_symbol" placeholder="Símbolo (BTC)" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="decimales" id="admin_coin_decimales" placeholder="Dec." value="6">
                            </div>
                            <div class="col-md-4 d-flex gap-1">
                                <button type="submit" class="btn btn-primary w-100" id="btn_save_coin">Guardar</button>
                                <button type="button" class="btn btn-secondary" onclick="resetCoinForm()">Limpiar</button>
                            </div>
                        </form>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Moneda</th>
                                        <th>Símbolo</th>
                                        <th class="text-center">Dec.</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="admin_coins_list"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Tab Exchanges -->
                    <div class="tab-pane fade" id="content-exchanges" role="tabpanel">
                        <form id="form_exchange_admin" class="row g-2 mb-3" onsubmit="saveExchangeAdmin(event)">
                            <input type="hidden" name="id" id="admin_exchange_id">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" id="admin_exchange_name" placeholder="Nombre del Exchange (ej: Binance)" required>
                            </div>
                            <div class="col-md-4 d-flex gap-1">
                                <button type="submit" class="btn btn-primary w-100" id="btn_save_exchange">Guardar</button>
                                <button type="button" class="btn btn-secondary" onclick="resetExchangeForm()">Limpiar</button>
                            </div>
                        </form>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="admin_exchanges_list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>

</body>
</html>
