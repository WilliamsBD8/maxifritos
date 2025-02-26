const table             = [];
const periods           = periodsData();
const type_documents    = typeDocumentData();
const customers         = customersData();
const products          = productsData();
function loadTable(){
    table[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url(['dashboard/productos/history/data']),
            data: function(d) {
                d.date_init     = $('#date_init').val();
                d.date_end      = $('#date_end').val();
                d.customer_id   = $('#customer_filter').val();
                d.product_id    = $('#product_filter').val();
                d.type_document = $('#type_document_filter').val();
            },
            dataSrc: 'data'
        },
        columns: [
            {title: 'Fecha Creación', data: 'created_at'},
            {title: 'Fecha Entrega', data: 'delivery_date'},
            {title: '# Resolución', data: 'resolution'},
            {title: 'Tipo Documento', data: 'type_document_name'},
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
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
                    let getData = {
                        length:         -1,
                        date_init:      $('#date_init').val(),
                        date_end:       $('#date_end').val(),
                        customer_id:    $('#customer_filter').val(),
                        product_id:     $('#product_filter').val(),
                        type_document:  $('#type_document_filter').val(),
                    }
                    const url_data = base_url(['dashboard/productos/history/data'], getData);
                    const exportData = await proceso_fetch_get(url_data).then(res => res.data);
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
        drawCallback: (setting) => {

            if(setting.json != undefined){
                let indicadores     = setting.json.indicadores;
                
                let type_document   = type_documents.find(td => td.id == $('#type_document_filter').val());
                let customer        = customers.find(c => c.id == $('#customer_filter').val());
                let product         = products.find(p => p.id == $('#product_filter').val());

                let rowData = [{
                    type_document_name: type_document ? type_document.name : "", 
                    type_document_code: type_document ? type_document.code : "", 
                    customer_name: customer ? customer.name : "", 
                    product_name: product ? product.name : "", 
                    product_code: product ? product.code : "", 
                }];

                console.log(rowData);
                
                if(!$.fn.DataTable.isDataTable("#table_data_filter")){
                    table[1] = $("#table_data_filter").DataTable({
                        data: rowData,
                        columns: [
                            {
                                title: "Tipo Documento",
                                data:null, render: (_,__,_data) => `${_data.type_document_name} - ${_data.type_document_code}`
                            },
                            {
                                title: "Producto",
                                data:null, render: (_,__,_data) => `${_data.product_name} - ${_data.product_code}`,
                                visible: false
                            },
                            {title: "Cliente", data:'customer_name', visible: false}
                        ],
                        paging: false, // Evita paginación innecesaria
                        searching: false, // Desactiva la búsqueda,
                        info: false,        // Sin información adicional
                        ordering: false,
                        scrollX: true,
                    })
                }else{
                    table[1].clear().rows.add(rowData).draw(false);
                }

                table[1].column(1).visible(!!product);  // Muestra la columna de "Producto" si product no es undefined
                table[1].column(2).visible(!!customer);
                
                $('#indicadores').html(
                    Object.entries(indicadores).map(([key, value]) => `
                        <div class="col-sm-12 col-md-6 col-lg-6 border-end">
                            <div class="d-flex justify-content-between align-items-start card-widget-1 pb-4 pb-sm-0">
                                <div class="card-body py-0">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <h2 class="mb-0">${key == 'total_value' ? formatPrice(parseFloat(value)) : value}</h2>
                                    </div>
                                    <h6 class="mb-0 fw-normal text-center"><b>${key == 'total_value' ? 'Valor Total' : 'Cantidad Productos'}</b></h6>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-md-none d-lg-none me-6 my-0">
                        </div>
                    `).join('')
                );
            }

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