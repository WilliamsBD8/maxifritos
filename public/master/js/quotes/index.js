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
                d.date_end = $('#date_end').val();
                d.resolution = $('#resolution_filter').val().trim()
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
            {
                title: "Ubicación",
                data:"address_origin",
                render:(coor) => `<a onclick="view_map_detail('${coor}')" href="javascript:void(0)" class="btn btn-sm btn-text-primary rounded-pill btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" data-bs-original-title="Ver mapa"><i class="ri-map-pin-line"></i></a>`,
                visible: user.role_id != 3
            },
            {title: 'Acciones', data:'id', render: (_, __, c) => {

                let actions = `
                    <div class="d-inline-block">
                        <a href="${base_url(['invoices/download', c.id])}" target="_blank" class="btn btn-sm btn-text-secondary rounded-pill btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" data-bs-original-title="Descargar ${c.td_name}"><i class="ri-file-pdf-2-line"></i></a>
                        ${c.status_id == 1  ? `
                            <a href="javascript:void(0);" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-2-line"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end m-0" style="">
                                <li><a href="${base_url(['dashboard/cotizaciones/editar', c.id])}" class="dropdown-item">Editar</a></li>
                                ${user.role_id != 3 ? `<li><a href="${base_url(['dashboard/cotizaciones/invoice', c.id])}" class="dropdown-item">Remisionar</a></li>` : ""}
                                <li><a href="javascript:void(0);" onclick="decline(${c.id}, ${c.resolution})" class="dropdown-item text-danger">Rechazar</a></li>
                            </ul>                
                        ` : `
                            ${user.role_id != 3 ? `
                                <a href="javascript:void(0);" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-2-line"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end m-0" style="">
                                    <li><a href="${base_url(['dashboard/cotizaciones/editar', c.id])}" class="dropdown-item">Editar</a></li>
                                    <li><a href="javascript:void(0);" onclick="decline(${c.id}, ${c.resolution})" class="dropdown-item text-danger">Rechazar</a></li>
                                </ul>
                            ` : ''}
                            
                        `}
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
                let val_period = periods.find(p => p.value == $('#period').val());

                let dates = val_period.value == "" ? `Desde <b>${$("#date_init").val()}</b> hasta <b>${$("#date_end").val()}</b>` : val_period.name;
                $('.text-date').html(dates)

                indicadores.map(i => {
                    console.log(i);
                    $(`#div_${i.document} h2`).html(formatPrice(parseFloat(i.payable_amount)));

                    $(`#div_${i.document} .inv`).html(`Documentos: <b>${parseInt(i.count)}</b>`);
                    $(`#div_${i.document} .pro`).html(`Productos: <b> ${parseInt(i.products)}</b>`);
                })
            }
        },
        buttons: [
            {
                text: '<i class="ri-add-line ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Crear Nueva Cotizacion</span>',
                className: 'btn btn-primary waves-effect waves-light mx-2 mt-2',
                action: () => {
                    window.location.href = base_url(['dashboard/cotizaciones/new']);
                }
            },
            {
                extend: 'excel',
                text: '<i class="ri-file-excel-line me-1"></i><span class="d-none d-sm-inline-block">Excel</span>',
                className: `btn btn-primary waves-effect waves-light mx-2 mt-2 ${user.role_id == 3 ? 'd-none' : ''}`,
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
                text: '<i class="ri-file-excel-line me-1"></i> <span class="d-none d-sm-inline-block">Pedidos</span>',
                className: `btn btn-primary waves-effect waves-light mx-2 mt-2  ${user.role_id == 3 ? 'd-none' : ''}`,
                action: async () => {
                    const {value:data} = await Swal.fire({
                        title: 'Ingrese el rango de busqueda',
                        html: `
                            <div class="input-field col s12">
                                <div class="form-floating form-floating-outline mb-6 mt-5">
                                    <input class="form-control" type="date" id="sweet_date_init" value="${$("#date_init").val()}">
                                    <label for="sweet_date_init">Fecha de inicio</label>
                                </div>
                            </div>
                            <div class="input-field col s12">
                                <div class="form-floating form-floating-outline mb-6 mt-5">
                                    <input class="form-control" type="date" id="sweet_date_end" value="${$("#date_end").val()}">
                                    <label for="sweet_date_end">Fecha de finalización</label>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Buscar',
                        preConfirm: () => {
                            var date_init = $('#sweet_date_init').val();
                            var date_end = $('#sweet_date_end').val();
                            if(date_init.length == 0 && date_end.length == 0)
                                return Swal.showValidationMessage(`
                                    'Necesita minimo una fecha para buscar'
                                `)
                            else if(date_init.length == 0)
                                return Swal.showValidationMessage(`
                                    'Necesita la fecha de incio para buscar'
                                `)
                            else if(date_end.length == 0)
                                return Swal.showValidationMessage(`
                                    'Necesita la fecha de finalizacion para buscar'
                                `)
                            return {date_init, date_end}
                        }
                    });
                    if(data){
                        let url = base_url(['invoices/load/order']);
                        const {file, type, status} = await proceso_fetch(url, data);
                        if(status)
                            downloadBase64(file, "hoja_cargue.xlsx", type);
                        else
                            Swal.fire({
                                icon: "warning",
                                title:"No se encontraron documentos en el rango de fechas",
                                customClass: {
                                    confirmButton: 'btn btn-primary waves-effect'
                                },
                            })
                    }

                }
            },     
            {
                text: '<i class="ri-filter-3-fill ri-16px me-2"></i> <span class="d-none d-sm-inline-block">Filtrar</span>',
                className: 'btn btn-primary waves-effect waves-light mx-2 mt-2',
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

function changePeriod(value){
    let period = periods.find(p => p.value == value)
    $('#date_init').val(period.dates.date_init).prop('readonly', period.value == '' ? false : true);
    $('#date_end').val(period.dates.date_end).prop('readonly', period.value == '' ? false : true);
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
                showConfirmButton: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            reloadTable()
        }
    });
}