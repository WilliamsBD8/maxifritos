const table = [];
const local_coord = {
    lat:0,
    lng:0
}

async function loadTable(){
    table[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url(['dashboard/clientes/data']),
            data: (d) => {
                d.name = $('#name_filter').val().trim();
                d.user_origin = $('#user_origin_filter').val();
            },
            dataSrc: 'data'
        },
        columns: [
            {title: 'Creado por', data:'origin_name', visible: user.role_id != 3},
            {title: 'Nombre', data:'name'},
            {title: 'email', data:'email'},
            {title: 'tipo de documento', data:'document_identification'},
            {title: 'n° de documento', data:'identification_number'},
            {title: 'n° de telefono', data:'phone'},
            {title: 'dirección fisica', data:'address'},
            {title: 'dirección mapa', data:'address_origin', render:(coor) => `<a onclick="view_map_detail('${coor}')" href="javascript:void(0)" class="btn btn-sm btn-text-primary rounded-pill btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" data-bs-original-title="Ver mapa"><i class="ri-map-pin-line"></i></a>`},
            {title: '% Descuento General', data:'discount_percentage', render:(d) => `${d} %`},
            {title: 'Descuento Detalle', data:'discount_detail'},
            {title: 'Estado', data:'status', render:(s => s == 'active' ? 'Activo' : 'Inactivo')},
            {title: 'Acciones', data:"id", render: (id) => `
                <div class="demo-inline-spacing d-flex">
                    <a class="btn rounded-pill btn-icon btn-outline-primary waves-effect" onclick="edit(${id})" href="javascript:void(0);" role="button" target=""><i class="ri-pencil-line p-0"></i></a>
                    <a class="btn rounded-pill btn-icon btn-outline-danger waves-effect" onclick="deleteCustomer(${id})" href="javascript:void(0);" role="button" target=""><i class="ri-close-large-line"></i></a>
                </div>
            `}
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
        },
        buttons: [
            {
                text: '<i class="ri-add-line ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Crear Cliente</span>',
                className: 'btn btn-primary waves-effect waves-light mx-2',
                action: async() => {
                    $('#canvasCustomerLabel').html('Crear Cliente');
                    $('#formCustomer').attr('onsubmit', "saveCustomer(event)");
                    $('#formCustomer')[0].reset();
                    $('#formCustomer .status').hide();

                    const offCanvasElement = document.querySelector('#canvasCustomer');
                    let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    offCanvasEl.show();
                    const lat_lng = await coordenadas();
                    local_coord.lat = lat_lng.lat
                    local_coord.lng = lat_lng.lng
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
    })
}

async function saveCustomer(e){
    e.preventDefault();
    const form = $("#formCustomer");
    const inputs = form.find('input, select, textarea');
    const data = {};
    let isValid = true;
    inputs.each(function () {
        const input = $(this);
        const value = input.val()
        if (!value && input.attr('required') != undefined) {
            isValid = false;
            input.addClass('is-invalid');
        } else {
            input.removeClass('is-invalid');
        }
        data[input.attr('id')] = value;
    });
    if(!isValid){
        return alert('Campos obligatorios', 'Por favor llenar los campos rerqueridos.', 'warning', 5000)
    }
    data['coordenadas'] = JSON.stringify(local_coord);
    console.log(local_coord);
    const url = base_url(['dashboard/clientes/created']);
    const res = await proceso_fetch(url, data);
    if(res.status == 'error'){
        console.log(res.errors);
        $.each(res.errors, function (key, message) {
            let input = $(`[name="${key}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                alert('Campos obligatorio', message, 'warning', 0, res.errors.length)
            }
        });
    }else{
        Swal.fire({
            title: res.title,
            icon: res.status,
            customClass: {
                confirmButton: "btn btn-primary",
            },
            didOpen: () => {
                table[0].ajax.reload();
                $('#canvasCustomer .btn-close').click();
            }
        })
    }
    console.log(res);
}

async function viewMap(){
    Swal.fire({
        title: 'Ubicación Actual',
        html:`
            <div style="height:300px;width:100%" id="map"></div>
        `,
        customClass: {
            confirmButton: "btn btn-primary",
        },
        didOpen: () => {
            // setTimeout(() => {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: local_coord,
                    zoom: 20,
                });
                marker = new google.maps.Marker({
                    position: local_coord,
                    map: map,
                    draggable: true, // Permitir arrastrar el marcador
                });

                // // Escuchar el evento de arrastre del marcador
                // marker.addListener('dragend', (event) => {
                //   const lat = event.latLng.lat();
                //   const lng = event.latLng.lng();

                //   // Mostrar las coordenadas en la página
                //   document.getElementById('coordinates').innerText =
                //     `Latitud: ${lat}, Longitud: ${lng}`;
                // });

                // Permitir que el usuario coloque el marcador al hacer clic en el mapa
                map.addListener('click', (event) => {
                  const clickedLocation = event.latLng;
                  marker.setPosition(clickedLocation);
                  local_coord.lat = clickedLocation.lat()
                  local_coord.lng = clickedLocation.lng()
                });
            // }, 1000)
        }
    });
}

async function edit(id_customer){

    const customers = table[0].rows().data().toArray();
    const customer = customers.find(c => c.id == id_customer);

    console.log(customer);

    $('#canvasCustomerLabel').html('Editar Cliente');
    $('#formCustomer').attr('onsubmit', "editCustomer(event)");
    $('#formCustomer .status').show();
    $('#formCustomer')[0].reset();
    const offCanvasElement = document.querySelector('#canvasCustomer');
    let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
    offCanvasEl.show();
    const lat_lng = await coordenadas();
    local_coord.lat = lat_lng.lat
    local_coord.lng = lat_lng.lng
    $('#name').val(customer.name);
    $('#email').val(customer.email);
    $('#type_document_identification').val(customer.type_document_identification_id);
    $('#identification_number').val(customer.identification_number);
    $('#phone').val(customer.phone);
    $('#discount_percentage').val(customer.discount_percentage);
    $('#discount_detail').val(customer.discount_detail);
    $('#address').val(customer.address);
    $('#status').val(customer.status);
    $('#id_customer').val(customer.id);
}

async function editCustomer(e){
    e.preventDefault();
    const form = $("#formCustomer");
    const inputs = form.find('input, select, textarea');
    const data = {};
    let isValid = true;
    inputs.each(function () {
        const input = $(this);
        const value = input.val()
        if (!value && input.attr('required') != undefined) {
            isValid = false;
            input.addClass('is-invalid');
        } else {
            input.removeClass('is-invalid');
        }
        data[input.attr('id')] = value;
    });
    if(!isValid){
        return alert('Campos obligatorios', 'Por favor llenar los campos rerqueridos.', 'warning', 5000)
    }
    data.coordenadas = JSON.stringify(local_coord);
    console.log(data);
    const url = base_url(['dashboard/clientes/edit']);
    const res = await proceso_fetch(url, data);
    if(res.status == 'error'){
        console.log(res.errors);
        $.each(res.errors, function (key, message) {
            let input = $(`[name="${key}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                alert('Campos obligatorio', message, 'warning', 0, res.errors.length)
            }
        });
    }else{
        Swal.fire({
            title: res.title,
            icon: res.status,
            customClass: {
                confirmButton: "btn btn-primary",
            },
            didOpen: () => {
                table[0].ajax.reload();
                $('#canvasCustomer .btn-close').click();
            }
        })
    }
}

async function deleteCustomer(id_customer){
    const customers = table[0].rows().data().toArray();
    const customer = customers.find(c => c.id == id_customer);
    Swal.fire({
        title: `¿Seguro de eliminar al cliente ${customer.name} - ${customer.identification_number}?`,
        text: `Recuerde que al eliminar no podra recuperar el registro.`,
        showCancelButton: true,
        confirmButtonText: "Eliminar cliente",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-outline-danger"
        },
      }).then(async (result) => {
        if (result.isConfirmed) {
            const url = base_url(['dashboard/clientes/delete']);
            const data = {id_customer}
            const {title, icon} = await proceso_fetch(url, data);
            Swal.fire({
                icon,
                title,
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                didOpen: () => {
                    reloadTable();
                    $('#canvasCustomer .btn-close').click();
                }
            });
        }
    });
}

function sendFilter(e){
    e.preventDefault();
    reloadTable();
}

function reloadTable(){
    table[0].ajax.reload();
}

window.loadTable();