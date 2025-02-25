const table = [];
const periods = periodsData();
function loadTable(){
    table[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url(['dashboard/productos/history/data']),
            data: function(d) {
                d.date_init     = $('#date_init').val();
                d.date_end      = $('#date_end').val();
                d.customer_id   = $('#customer_filter').val();
                d.product_id    = $('#product_filter').val();
            },
            dataSrc: 'data'
        },
        columns: [
            {title: 'Fecha Creación', data: 'created_at'},
            {title: 'Fecha Entrega', data: 'delivery_date'},
            {title: '# Resolución', data: 'resolution'},
            {title: 'Producto', data: 'product_name'},
            {title: 'Cliente', data: 'customer_name'},
            {title: 'Cantidad', data: 'quantity'},
            {title: 'Valor', data: 'value', render:(v) => formatPrice(parseFloat(v))},
            {title: 'Descuento', data: 'discount_percentage', render: (d) => `${d} %`},
        ],
        dom: '<"card-header flex-column flex-md-row border-bottom"<"head-label text-center"><"dt-action-buttons text-end pt-0 pt-md-0"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        responsive: false,
        scrollX: true,
        scrollY: false,
        ordering: false,
        processing: true,
        serverSide: true,
        buttons: [
            {
                extend: 'excel',
                text: '<i class="ri-file-excel-line me-1"></i><span class="d-none d-sm-inline-block">Excel</span>',
                className: `btn rounded-pill btn-label-success waves-effect mx-2 mt-2 ${user.role_id == 3 ? 'd-none' : ''}`,
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
                text: '<i class="ri-filter-3-fill ri-16px me-2"></i> <span class="d-none d-sm-inline-block">Filtrar</span>',
                className: 'btn btn-label-secondary waves-effect waves-light mx-2 mt-2',
                action: () => {
                    const offCanvasElement = document.querySelector('#canvasFilter');
                    let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    offCanvasEl.show();
                    $(`#customer_filter`).select2({
                        dropdownParent: $('#canvasFilter')
                    });
                    $(`#product_filter`).select2({
                        dropdownParent: $('#canvasFilter')
                    });
                    $('#date_init').flatpickr({
                        locale:             "es",
                        monthSelectorType:  'static',
                        clickOpens: $("#period").val() == "",
                    });
                    $('#date_end').flatpickr({
                        locale:             "es",
                        monthSelectorType:  'static',
                        clickOpens: $("#period").val() == "",
                    });
                }
            }
        ],
        drawCallback: () => {
            $('#canvasFilter .btn-close').click();
        }
    })
}

function changePeriod(value){
    const period = periods.find(p => p.value == value);
    $('#date_init').flatpickr({
        locale:             "es",
        monthSelectorType:  'static',
        clickOpens: period.value == "",
        defaultDate: period.dates.date_init
    });
    $('#date_end').flatpickr({
        locale:             "es",
        monthSelectorType:  'static',
        clickOpens: period.value == "",
        defaultDate: period.dates.date_end
    });
}

async function reloadTable(){
    await table[0].ajax.reload();
}

function sendFilter(e){
    e.preventDefault();
    reloadTable();
}

function resetFilter(){
    $('#formFilter')[0].reset();
    $('#customer_filter').val(null).trigger('change'); // Limpia Select2
    $('#product_filter').val(null).trigger('change'); // Limpia Select2
    reloadTable()
}

window.addEventListener("load", () => {
    loadTable();
})