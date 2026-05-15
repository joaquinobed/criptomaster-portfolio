$(document).ready(function() {
    loadInvestments();
    loadOptions();
    loadStats();

    // Actualizar precios al iniciar
    actualizarPrecios();

    // Atar eventos de modal admin
    const modalAdmin = document.getElementById('modalAdmin');
    if (modalAdmin) {
        modalAdmin.addEventListener('show.bs.modal', function() {
            loadAdminCoins();
            loadAdminExchanges();
        });
        modalAdmin.addEventListener('hidden.bs.modal', function() {
            loadOptions(); // Recargar opciones por si hubo cambios
            loadInvestments();
            loadStats();
        });
    }
});

function loadAdminCoins() {
    $('#admin_coins_list').html('<tr><td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    fetch('api/coins.php?action=list')
        .then(r => {
            if (!r.ok) return r.json().then(e => { throw e; });
            return r.json();
        })
        .then(data => {
            if (!Array.isArray(data)) throw new Error("Respuesta inválida del servidor");
            let html = '';
            data.forEach(c => {
                const coinJson = JSON.stringify(c).replace(/'/g, "&#39;");
                html += `
                    <tr>
                        <td><img src="img/${c.img || (c.symbol + '.png')}" class="crypto-icon" onerror="this.src='https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/${(c.symbol || '').toLowerCase()}.png'; this.onerror=null;"></td>
                        <td>${c.name}</td>
                        <td><span class="badge bg-secondary">${c.symbol}</span></td>
                        <td class="text-center">${c.decimales}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-link text-warning" onclick='editCoinAdmin(${coinJson})'><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-link text-danger" onclick="deleteCoinAdmin(${c.id})"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            $('#admin_coins_list').html(html || '<tr><td colspan="5" class="text-center">No hay monedas registradas</td></tr>');
        })
        .catch(err => {
            console.error(err);
            $('#admin_coins_list').html(`<tr><td colspan="5" class="text-center text-danger">Error: ${err.error || err.message || 'Desconocido'}</td></tr>`);
        });
}

function saveCoinAdmin(e) {
    e.preventDefault();
    const id = $('#admin_coin_id').val();
    const action = id ? 'update' : 'save';
    const formData = new FormData(e.target);
    
    fetch(`api/coins.php?action=${action}`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                resetCoinForm();
                loadAdminCoins();
            } else {
                showAlert(res.error, 'danger');
            }
        });
}

function editCoinAdmin(c) {
    $('#admin_coin_id').val(c.id);
    $('#admin_coin_name').val(c.name);
    $('#admin_coin_symbol').val(c.symbol);
    $('#admin_coin_decimales').val(c.decimales);
    $('#btn_save_coin').text('Actualizar');
}

function deleteCoinAdmin(id) {
    if (confirm('¿Seguro que desea eliminar esta moneda?')) {
        fetch(`api/coins.php?action=delete&id=${id}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert(res.message);
                    loadAdminCoins();
                } else {
                    showAlert(res.error, 'danger');
                }
            });
    }
}

function resetCoinForm() {
    $('#admin_coin_id').val('');
    $('#form_coin_admin')[0].reset();
    $('#btn_save_coin').text('Guardar');
}

function loadAdminExchanges() {
    $('#admin_exchanges_list').html('<tr><td colspan="3" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    fetch('api/exchanges.php?action=list')
        .then(r => {
            if (!r.ok) return r.json().then(e => { throw e; });
            return r.json();
        })
        .then(data => {
            if (!Array.isArray(data)) throw new Error("Respuesta inválida del servidor");
            let html = '';
            data.forEach(e => {
                const exchangeJson = JSON.stringify(e).replace(/'/g, "&#39;");
                html += `
                    <tr>
                        <td>${e.id}</td>
                        <td>${e.name}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-link text-warning" onclick='editExchangeAdmin(${exchangeJson})'><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-link text-danger" onclick="deleteExchangeAdmin(${e.id})"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            $('#admin_exchanges_list').html(html || '<tr><td colspan="3" class="text-center">No hay exchanges registrados</td></tr>');
        })
        .catch(err => {
            console.error(err);
            $('#admin_exchanges_list').html(`<tr><td colspan="3" class="text-center text-danger">Error: ${err.error || err.message || 'Desconocido'}</td></tr>`);
        });
}

function saveExchangeAdmin(e) {
    e.preventDefault();
    const id = $('#admin_exchange_id').val();
    const action = id ? 'update' : 'save';
    const formData = new FormData(e.target);

    fetch(`api/exchanges.php?action=${action}`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                resetExchangeForm();
                loadAdminExchanges();
            } else {
                showAlert(res.error, 'danger');
            }
        });
}

function editExchangeAdmin(e) {
    $('#admin_exchange_id').val(e.id);
    $('#admin_exchange_name').val(e.name);
    $('#btn_save_exchange').text('Actualizar');
}

function deleteExchangeAdmin(id) {
    if (confirm('¿Seguro que desea eliminar este exchange?')) {
        fetch(`api/exchanges.php?action=delete&id=${id}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert(res.message);
                    loadAdminExchanges();
                } else {
                    showAlert(res.error, 'danger');
                }
            });
    }
}

function resetExchangeForm() {
    $('#admin_exchange_id').val('');
    $('#form_exchange_admin')[0].reset();
    $('#btn_save_exchange').text('Guardar');
}

function loadOptions() {
    // Cargar monedas
    fetch('api/util.php?type=coins')
        .then(r => r.json())
        .then(data => {
            let options = '<option value="0">Moneda</option>';
            data.forEach(c => {
                options += `<option value="${c.id}">${c.name} (${c.symbol})</option>`;
            });
            $('#search_moneda, #moneda, #edit_moneda').html(options);
        });

    // Cargar exchanges
    fetch('api/util.php?type=exchanges')
        .then(r => r.json())
        .then(data => {
            let options = '<option value="0">Exchange</option>';
            data.forEach(e => {
                options += `<option value="${e.id}">${e.name}</option>`;
            });
            $('#search_exchange, #exchange, #edit_exchange, #search_exchange_resumen').html(options);
        });
}

function loadInvestments(page = 1) {
    const moneda = $('#search_moneda').val();
    const exchange = $('#search_exchange').val();
    
    $('#items_investments').html('<tr><td colspan="9" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');

    // Update main dashboard stats when filtering
    loadStats();

    fetch(`api/investments.php?action=list&moneda=${moneda}&exchange=${exchange}&page=${page}`)
        .then(r => r.json())
        .then(res => {
            let html = '';
            const data = res.data;
            
            data.forEach(item => {
                const valueUsd = item.total * item.current_price;
                const utility = valueUsd - item.buy;
                const statusClass = utility >= 0 ? 'text-success' : 'text-danger';
                const rowClass = utility >= 0 ? 'table-success-light' : 'table-danger-light';

                html += `
                    <tr class="${rowClass}">
                        <td class="text-center">${formatDate(item.created_at)}</td>
                        <td class="text-center fw-bold text-primary">$${formatNumber(item.buy, 2)}</td>
                        <td class="text-center">$${formatNumber(item.price, 5)}</td>
                        <td class="text-center">
                            <span class="fw-bold">${formatNumber(item.total, item.decimales)}</span><br>
                            <small class="text-muted">Val: $${formatNumber(valueUsd, 2)}</small>
                        </td>
                        <td class="text-center text-secondary">$${formatNumber(item.current_price, 4)}</td>
                        <td class="text-center">
                            <img src="img/${item.img}" class="crypto-icon" onerror="this.src='https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/${item.symbol.toLowerCase()}.png'; this.onerror=null;">
                            <span class="fw-semibold">${item.symbol}</span>
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${item.exchange_name}</span></td>
                        <td class="text-center fw-bold ${statusClass}">$${formatNumber(utility, 2)}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-warning" onclick="editInvestment(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteInvestment(${item.id})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            $('#items_investments').html(html || '<tr><td colspan="9" class="text-center">No hay registros</td></tr>');
            $('#total_invested_footer').text(`$${formatNumber(res.totals.total_buy || 0, 2)}`);
            
            renderPagination(res.pagination);
        });
}

function renderPagination(p) {
    let html = '<nav><ul class="pagination pagination-sm mb-0">';
    for (let i = 1; i <= p.total_pages; i++) {
        html += `<li class="page-item ${i === p.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadInvestments(${i}); return false;">${i}</a>
                 </li>`;
    }
    html += '</ul></nav>';
    $('#pagination_container').html(html);
}

function loadStats() {
    // Si estamos en el modal, usamos su propio filtro de exchange. 
    // Si no, usamos el filtro principal de la página.
    const modalExchange = $('#search_exchange_resumen').val();
    const searchExchange = $('#search_exchange').val();
    
    // El exchange a usar depende de si el modal está abierto o si estamos actualizando el dashboard principal
    const isModalOpen = $('#modalSummary').hasClass('show');
    const exchange = isModalOpen ? (modalExchange || 0) : (searchExchange || 0);

    fetch(`api/dashboard.php?exchange=${exchange}`)
        .then(r => r.json())
        .then(data => {
            // Actualizar Dashboard Cards siempre con el filtro principal si no es el modal el que disparó esto
            // O mejor: si exchange es el del buscador principal, actualizar cards. 
            // Si es el del modal, solo actualizar modal.
            
            if (!isModalOpen) {
                $('#stat_total_value').text(`$${formatNumber(data.totals.value, 2)}`);
                $('#stat_total_invested').text(`$${formatNumber(data.totals.invested, 2)}`);
                $('#stat_total_utility').text(`$${formatNumber(data.totals.utility, 2)}`);
                $('#stat_total_percent').text(`${formatNumber(data.totals.percent, 2)}%`);
                
                const utilityClass = data.totals.utility >= 0 ? 'text-success' : 'text-danger';
                $('#stat_total_utility').removeClass('text-success text-danger').addClass(utilityClass);
            }

            // Si el modal está abierto o si queremos que el resumen siempre esté disponible
            // Resumen table items
            let html = '';
            data.coins.forEach(c => {
                const utilityClass = c.utility >= 0 ? 'text-success' : 'text-danger';
                const lockIcon = c.is_manual ? 'fa-lock text-danger' : 'fa-sync-alt text-success';
                const lockTitle = c.is_manual ? 'Precio Manual (Bloqueado)' : 'Precio Sincronizado';

                html += `
                    <tr>
                        <td>
                            <img src="img/${c.img}" class="crypto-icon" onerror="this.src='https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/${c.symbol.toLowerCase()}.png'; this.onerror=null;">
                            ${c.name}
                            <button class="btn btn-sm btn-link p-0 text-warning ms-1" onclick="openEditCoin(${c.id}, '${c.name}', ${c.current_price})" title="Editar precio manualmente">
                                <i class="fa fa-edit" style="font-size: 10px;"></i>
                            </button>
                            <button class="btn btn-sm btn-link p-0 ms-1" onclick="toggleSync(${c.id}, ${c.is_manual})" title="${lockTitle}">
                                <i class="fa ${lockIcon}" style="font-size: 10px;"></i>
                            </button>
                        </td>
                        <td class="text-end">${formatNumber(c.balance, c.decimales)}</td>
                        <td class="text-end">$${formatNumber(c.current_price, 2)}</td>
                        <td class="text-end">$${formatNumber(c.avg_price, 2)}</td>
                        <td class="text-end">$${formatNumber(c.value_usd, 2)}</td>
                        <td class="text-end">$${formatNumber(c.investment, 2)}</td>
                        <td class="text-end ${utilityClass}">$${formatNumber(c.utility, 2)} <small>(${formatNumber(c.percent, 2)}%)</small></td>
                    </tr>
                `;
            });
            $('#resumen_items').html(html);

            // Resumen table footer
            const totalUtilityClass = data.totals.utility >= 0 ? 'text-success' : 'text-danger';
            $('#resumen_total_value').text(`$${formatNumber(data.totals.value, 2)}`);
            $('#resumen_total_invested').text(`$${formatNumber(data.totals.invested, 2)}`);
            $('#resumen_total_utility').html(`<span class="${totalUtilityClass}">$${formatNumber(data.totals.utility, 2)} (${formatNumber(data.totals.percent, 2)}%)</span>`);
        });
}

function saveInvestment(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('api/investments.php?action=save', {
        method: 'POST',
        body: formData
    }).then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Compra guardada con éxito', 'success');
            $('#modalInvestments').modal('hide');
            e.target.reset();
            loadInvestments();
            loadStats();
        } else {
            showAlert(data.error || 'Error al guardar', 'danger');
        }
    }).catch(err => {
        console.error(err);
        showAlert('Error en el servidor al intentar guardar', 'danger');
    });
}

function updateInvestment(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('api/investments.php?action=update', {
        method: 'POST',
        body: formData
    }).then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Registro actualizado correctamente', 'info');
            $('#update_investment').modal('hide');
            loadInvestments();
            loadStats();
        } else {
            showAlert(data.error || 'Error al actualizar', 'danger');
        }
    }).catch(err => {
        console.error(err);
        showAlert('Error en el servidor al intentar actualizar', 'danger');
    });
}

function editInvestment(item) {
    $('#edit_id').val(item.id);
    $('#edit_compra').val(item.buy);
    $('#edit_precio').val(item.price);
    // Formatear fecha para input date (YYYY-MM-DD)
    const fecha = item.created_at.split(' ')[0];
    $('#edit_fecha').val(fecha);
    $('#edit_moneda').val(item.coin_id);
    $('#edit_exchange').val(item.exchange_id);
    $('#update_investment').modal('show');
}

function deleteInvestment(id) {
    if (confirm('¿Desea eliminar este registro permanentemente?')) {
        fetch(`api/investments.php?action=delete&id=${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Registro eliminado', 'danger');
                    loadInvestments();
                    loadStats();
                }
            });
    }
}

function actualizarPrecios() {
    const btn = $('#btn-sync');
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sincronizando...');
    
    fetch('api/prices.php')
        .then(r => r.json())
        .then(() => {
            loadInvestments();
            loadStats();
            btn.prop('disabled', false).html(originalText);
        });
}

function showAlert(message, type = 'success') {
    const id = 'alert-' + Date.now();
    const html = `
        <div id="${id}" class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-${type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : 'info-circle')} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#alert_container').append(html);
    setTimeout(() => {
        $(`#${id}`).alert('close');
    }, 4000);
}

function openEditCoin(id, name, price) {
    $('#coin_edit_id').val(id);
    $('#coin_edit_label').text(`Precio de ${name} (USD)`);
    $('#coin_edit_price').val(price);
    $('#modalEditCoin').modal('show');
}

function updateCoinPrice(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('api/coins.php?action=update_price', {
        method: 'POST',
        body: formData
    }).then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message);
            $('#modalEditCoin').modal('hide');
            loadInvestments();
            loadStats();
        }
    });
}

function toggleSync(id, isManual) {
    const action = isManual ? 'enable_sync' : null;
    if (!isManual) {
        showAlert('Para bloquear la sincronización, edite el precio manualmente', 'info');
        return;
    }
    
    if (confirm('¿Reactivar sincronización automática para esta moneda? El precio manual será sobrescrito en la próxima sincronización.')) {
        const formData = new FormData();
        formData.append('id', id);
        fetch('api/coins.php?action=enable_sync', {
            method: 'POST',
            body: formData
        }).then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message);
                loadStats();
            }
        });
    }
}

// Helpers
function formatNumber(num, dec) {
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString();
}
