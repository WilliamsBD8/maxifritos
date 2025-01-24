const local_coord = {
    lat:0,
    lng:0
}

async function sendInvoice(){
    let url = base_url(['invoices/created']);
    let data = invoiceData();
    const lat_lng = await coordenadas();
    local_coord.lat = lat_lng.lat
    local_coord.lng = lat_lng.lng
    data.coordenadas = JSON.stringify(local_coord);
    data.type_document = 2;
    data.resolution_reference = data.id;
    data.products = data.line_invoices;
    data.notes = data.note;
    delete data.line_invoices;
    delete data.note;
    Swal.fire({
        title: `Remisionar cotización #${data.resolution}`,
        text: `Recuerde que al remisionar no podra modificar la cotización.`,
        showCancelButton: true,
        confirmButtonText: "Crear remisión",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-outline-danger"
        },
      }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await proceso_fetch(url, data);
            console.log(res);
            Swal.fire({
                icon: 'success',
                title: res.title,
                showConfirmButton: false,
                allowOutsideClick: false,
                customClass: {},
                willOpen: function () {
                    Swal.showLoading();
                }
            });
            window.location.href = base_url(['dashboard/cotizaciones'])
        }
    });
}