const table = [];
const local_coord = {
    lat:0,
    lng:0
}

async function loadTable(){
    table[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url(['dashboard/clientes/data']),
            dataSrc: 'data'
        },
        columns: [
            {title: 'Nombre', data:'name'},
            {title: 'email', data:'email'},
            {title: 'tipo de documento', data:'document_identification'},
            {title: 'n° de documento', data:'identification_number'},
            {title: 'n° de telefono', data:'phone'},
            {title: 'dirección fisica', data:'address'},
            {title: 'dirección mapa', data:'address_origin', render:(coor) => `<a onclick="view_map_detail('${coor}')" href="javascript:void(0)" class="btn btn-sm btn-text-primary rounded-pill btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" data-bs-original-title="Ver mapa"><i class="ri-map-pin-line"></i></a>`},
            {title: '% Descuento General', data:'discount_percentage', render:(d) => `${d} %`},
            {title: 'Descuento Detalle', data:'discount_detail'}
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

                    const offCanvasElement = document.querySelector('#canvasCustomer');
                    let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    offCanvasEl.show();
                    const lat_lng = await coordenadas();
                    local_coord.lat = lat_lng.lat
                    local_coord.lng = lat_lng.lng
                }
            },       
            // {
            //     text: '<i class="ri-filter-3-fill ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Filtrar</span>',
            //     className: 'btn btn-primary waves-effect waves-light',
            //     action: () => {
            //         const offCanvasElement = document.querySelector('#canvasFilter');
            //         let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
            //         offCanvasEl.show();
            //     }
            // }
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

function reloadTable(){
    table[0].ajax.reload();
}

window.loadTable();