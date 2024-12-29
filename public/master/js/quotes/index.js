const table = [];
$(() => {
    load_datatable();
})

async function load_datatable(){
    table[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url(['dashboard/cotizaciones/data']),
            data: function(d) {
                d.date_init =  $('#date_init').val();
                d.date_end = $('#date_end').val()
            },
            dataSrc: 'data'
        },
        columns: [
            {title: '# Resolución', data:'resolution'},
            {title: 'Tipo Documento', data:'td_name'},
            {title: 'Cliente', data:'customer'},
            {title: 'Vendedor', data:'seller'},
            {title: 'Estado', data:'status_id', render:(status_id) => {
                let s = status_data.find(st => st.id == status_id);
                return `<span class="badge rounded-pill ${s.class}">${s.name}</span>`;
            }},
            {title: 'Valor', data: 'payable_amount', render: (v) => formatPrice(parseFloat(v))},
            {title: 'Fecha', data: 'created_at'},
            {title: 'Creado por', data: 'u_name'},
            {title: 'Acciones', data:'id', render: (_, __, c) => {

                let actions = `
                    <div class="d-inline-block">
                        <a href="${base_url(['invoices/download', c.id])}" target="_blank" class="btn btn-sm btn-text-secondary rounded-pill btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" data-bs-original-title="Descargar ${c.td_name}"><i class="ri-file-pdf-2-line"></i></a>
                        ${c.status_id == 1 ? `
                            <a href="javascript:void(0);" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-2-line"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end m-0" style="">
                                <li><a href="${base_url(['dashboard/cotizaciones/editar', c.id])}" class="dropdown-item">Editar</a></li>
                                <li><a href="${base_url(['dashboard/cotizaciones/invoice', c.id])}" class="dropdown-item">Remisionar</a></li>
                                <li><a href="javascript:void(0);" onclick="decline(${c.id}, ${c.resolution})" class="dropdown-item text-danger">Rechazar</a></li>
                            </ul>                
                        ` : ''}
                    </div>
                `
                return actions;
            }}
        ],
        dom: '<"card-header flex-column flex-md-row border-bottom"<"head-label text-center"><"dt-action-buttons text-end pt-0 pt-md-0"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        responsive: false,
        scrollX: true,
        scrollY: false,
        ordering: false,
        processing: true,
        serverSide: true,
        drawCallback: async (setting) => {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            if(setting.json != undefined){
                let indicadores = setting.json.indicadores;
                indicadores.map(i => {
                    $(`#div_${i.document} h2`).html(formatPrice(parseFloat(i.payable_amount)));
                    let val_period = periods.find(p => p.value == $('#period').val())
                    $(`#div_${i.document} p`).html(`Creadas ${ val_period.value == "" ? `desde ${$("#date_init").val()} hasta ${$("#date_end").val()}`:val_period.name}: <b>${i.count}</b>`)
                })
            }
        },
        buttons: [
            {
                text: '<i class="ri-add-line ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Crear Nueva Cotizacion</span>',
                className: 'btn btn-primary waves-effect waves-light mx-2',
                action: () => {
                    window.location.href = base_url(['dashboard/cotizaciones/new']);
                }
            },
            {
                extend: 'excel',
                text: '<i class="ri-file-excel-line me-1"></i>Excel',
                className: 'btn btn-primary waves-effect waves-light mx-2',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                                if (item.classList !== undefined && item.classList.contains('user-name')) {
                                    result = result + item.lastChild.firstChild.textContent;
                                } else if (item.innerText === undefined) {
                                    result = result + item.textContent;
                                } else {
                                    result = result + item.innerText;
                                }
                            });
                            return result;
                        }
                    }
                },
                action: async function (e, dt, button, config) {
                    const exportData = await proceso_fetch_get(`${table[0].ajax.url()}?length=-1&date_init=${table[0].ajax.params().date_init}&&date_end=${table[0].ajax.params().date_end}`).then(res => res.data);
                    dt.clear();
                    dt.rows.add(exportData);
                    dt.draw();
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                }
            },         
            {
                text: '<i class="ri-filter-3-fill ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Filtrar</span>',
                className: 'btn btn-primary waves-effect waves-light',
                action: () => {
                    const offCanvasElement = document.querySelector('#canvasFilter');
                    let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    offCanvasEl.show();
                }
            }
        ]
    });
}


function formatDate (date){
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

function changePeriod(period){
    switch(period){
        case 'day':
        default:
            var today = new Date(); // Obtener la fecha actual
            var year = today.getFullYear(); // Obtener el año
            var month = String(today.getMonth() + 1).padStart(2, '0'); // Mes (0-11) y añadir 0 si es menor a 10
            var day = String(today.getDate()).padStart(2, '0'); // Día y añadir 0 si es menor a 10
            var date_init = `${year}-${month}-${day}`;
            var date_end = `${year}-${month}-${day}`;
            break;
        case 'yesterday':
            var today = new Date(); // Obtener la fecha actual
            today.setDate(today.getDate() - 1); // Restar un día
            var year = today.getFullYear(); // Obtener el año
            var month = String(today.getMonth() + 1).padStart(2, '0'); // Mes (0-11) y añadir 0 si es menor a 10
            var day = String(today.getDate()).padStart(2, '0'); // Día y añadir 0 si es menor a 10

            var date_init = `${year}-${month}-${day}`;
            var date_end = `${year}-${month}-${day}`;
            break;
        case 'weekend':
            var today = new Date();
            var firstDay = new Date(today);
            var day = firstDay.getDay() || 7;
            firstDay.setDate(today.getDate() - day + 1);
            var lastDay = new Date(firstDay);
            lastDay.setDate(firstDay.getDate() + 6);
            var date_init = formatDate(firstDay);
            var date_end = formatDate(lastDay);
            break;
        case 'last_weekend':
            var today = new Date();
            var firstDayLastWeek = new Date(today);
            var day = firstDayLastWeek.getDay() || 7;
            firstDayLastWeek.setDate(today.getDate() - day - 6);
            var lastDayLastWeek = new Date(firstDayLastWeek);
            lastDayLastWeek.setDate(firstDayLastWeek.getDate() + 6);
            var date_init = formatDate(firstDayLastWeek);
            var date_end = formatDate(lastDayLastWeek);
            break;
        case 'month':
            var today = new Date();
            var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            var date_init = formatDate(firstDayOfMonth);
            var date_end = formatDate(lastDayOfMonth);
            break;
        case 'last_month':
            var today = new Date();
            var firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            var lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            var date_init = formatDate(firstDayOfLastMonth);
            var date_end = formatDate(lastDayOfLastMonth);
            break;
    }

    $('#date_init').val(date_init).prop('readonly', period == '' ? false : true);
    $('#date_end').val(date_end).prop('readonly', period == '' ? false : true);
}

function sendFilter(e){
    e.preventDefault();
    reloadTable();
}

function reloadTable(){
    table[0].ajax.reload();
}

function decline(id, resolution){
    Swal.fire({
        title: `Rechazar la cotización #${resolution}`,
        text: `Recuerde que al rechazar la cotización se perdera toda información.`,
        showCancelButton: true,
        confirmButtonText: "Rechazar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-outline-danger"
        },
      }).then(async (result) => {
        if (result.isConfirmed) {
            const data = {id}
            const url = base_url(['invoices/decline']);
            const res = await proceso_fetch(url, data);
            console.log(res);
            Swal.fire({
                icon: 'success',
                title: res.title,
                showConfirmButton: false,
                allowOutsideClick: false,
                customClass: {}
            });
            reloadTable()
        }
    });
}