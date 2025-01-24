
const productsD = [];
// const productsD = productsData();
const invoice = invoiceData();
const products = [];
const table = [];
$(() => {
    setTimeout(async () => {
        await loadProducts(invoice.customer_id);
        invoice.line_invoice.map((line) => {
            console.log(line);
            line.line_invoice_id = line.id;
            line.isDelete = false;
            var product = productsD.find(p => p.id == line.product_id);
            console.log(productsD);
            product.value = line.value;
            line.discount = parseFloat(line.discount_amount) == 0 && parseFloat(line.discount_percentage) == 0 ? false : true;
            let combined = $.extend({}, line, product);
            products.push(combined)
        });
        reloadTable()
    }, 1000)
});

async function loadProducts(customer = null){
    if(customer != null){
        let customers = customersData();
        let cust = customers.find(c => c.id == customer);

        const url = base_url(['data/products']);
        const data = { customer }
        const res = await proceso_fetch(url, data, 0);
        productsD.splice(0, productsD.length, ...res.data);
        products.map(p => {
            let prod = productsD.find(pd => pd.id == p.id)
            p.value = prod.value
        });
        $('#products_id').select2({
            data: productsD.map(item => ({
                id: item.id,
                text: `${item.name} - ${item.code}`
            }))
        });
        if(cust.discount_percentage > 0){
            var aux_customer = `
                Descuento sugerido del ${cust.discount_percentage}%: ${cust.discount_detail}
            `
        }else
            var aux_customer = "";
        $('#address').val(cust.address)
        $('#id_descuento_customer').html(aux_customer)
    }else{
        $('#products_id').select2({
            data: [],
            language: {
                noResults: function() {
                    return "Seleccione un cliente"; // Mensaje personalizado
                }
            },
        });
    }
    if(table[0] == undefined)
        loadTable();
    else reloadTable()
} 

function changeDiscount(value){
    switch (value) {
        case 'monto':
            products.map(p => {
                p.discount_amount = 0;
                p.discount_percentage = 0;
                p.discount = false;
            })
            $('#discount_percentaje').val(0)
            $('#input_descuento_monto').prop('disabled', false)
            $('#input_descuento_porcentaje').prop('disabled', true)
            $('#input_descuento_porcentaje').val('')
            break;
        case 'porcentaje':
            products.map(p => {
                p.discount_amount = 0;
                p.discount_percentage = 0;
                p.discount = false;
            })
            $('#discount_amount').val(0)
            $('#input_descuento_monto').prop('disabled', true)
            $('#input_descuento_porcentaje').prop('disabled', false)
            $('#input_descuento_monto').val('')
            break;
        default:
            products.map(p => {
                p.discount = true;
            });
            $('#discount_amount').val(0)
            $('#discount_percentaje').val(0)
            $('#input_descuento_monto').prop('disabled', true)
            $('#input_descuento_porcentaje').prop('disabled', true)
            break;
    }
}

function changeDiscountValue(type, value){
    $(`#${type == 2 ? 'discount_percentaje' : 'discount_amount'}`).val(type == 2 ? value : value.replace(/,/, ''))
}

function addProduct(id){
    let producto = products.find(p => p.id == id);
    if(producto){
        producto.quantity = producto.isDelete ? producto.quantity : parseInt(producto.quantity)+1;
        producto.isDelete = false;
    }else{
        let producto = productsD.find(p => p.id == id)
        producto = { ...producto, quantity: 1, discount_amount:0, discount_percentage:0, line_invoice_id: null, isDelete: false, discount: products.find(p => p.discount) !== undefined };
        products.push(producto);
    }
    reloadTable();
}

function productDelete(id){
    let product = products.find(p => p.id == id);
    product.isDelete = true;
    reloadTable();
}

function handleChange(value, id, campo){
    let producto = products.find(p => p.id == id);
    value = campo == 'value' || campo == 'discount_amount' ? format_number(value) : value;
    if(campo == 'discount_amount'){
        validate = value >= (parseFloat(producto.value) * parseInt(producto.quantity)) ? false : true;
        if(!validate){
            alert('Valor Excesivo', 'El valor del descuento es superior al valor del producto.', 'warning', 3000);
            value = 0;
        }else{
            producto['discount_percentage'] = 0;
        }
    }
    if(campo == 'discount_percentage'){
        let value_parcial = (parseFloat(value) / 100) * (parseFloat(producto.value) * parseInt(producto.quantity));
        validate = value_parcial >= (parseFloat(producto.value) * parseInt(producto.quantity)) ? false : true;
        if(!validate){
            alert('Valor Excesivo', 'El porcentaje del descuento es superior al valor del producto.', 'warning', 3000);
            value = 0;
        }else{
            producto['discount_amount'] = 0;
        }
    }
    producto[campo] = value;
    reloadTable();
}

function loadTable(){
    table[0] = $('#table_datatable').DataTable({
        data:products.filter(p => !p.isDelete),
        columns: [
            {title: 'Producto', data: 'name', render: (n, _, p) => `${n}<br>${p.code}`},
            {title: 'Cantidad', data: 'quantity', render: (q, _, p) => {
                return `
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" value="${q}" onchange="handleChange(this.value, ${p.id}, 'quantity')">
                        </div>
                    </div>
                `;
            }},
            {title: 'Valor Unitario', data: 'value', render: (v, _, p) => {
                return `
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" onkeyup="updateFormattedValue(this)" value="${separador_miles(v)}" onchange="handleChange(this.value, ${p.id}, 'value')">
                        </div>
                    </div>
                `;
            }},
            {title: 'Porcentaje <br> Descuento', data: 'discount_percentage', render: (_, __, p) => { 
                return !p.discount ? '0 %':`
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input  type="text" class="form-control" value="${parseFloat(_)}" onchange="handleChange(this.value, ${p.id}, 'discount_percentage')">
                        </div>
                    </div>
                `;
            }},
            {title: 'Total <br>Descuento', data: '', render: (_, __, p) => {
                let value = parseFloat(p.value) * parseInt(p.quantity);
                let value_parcial = p.discount_percentage != 0 ? (parseFloat(p.discount_percentage) / 100) * value : p.discount_amount;
                return formatPrice(value_parcial);
            }},
            {title: 'Sub Total', data: '', render: (_, __, p) => {
                let value = parseFloat(p.value) * parseInt(p.quantity);
                let value_parcial = p.discount_percentage != 0 ? (parseFloat(p.discount_percentage) / 100) * value : p.discount_amount;
                return formatPrice(value - value_parcial);
            }},
            {title: 'Acciones', data: 'id', render: (id) => {
                return `<a class="btn btn-default btn btn-icon me-2 btn-label-danger rounded-pill" onclick="productDelete(${id})" href="javascript:void();" role="button" target=""><i class="ri-close-large-line"></i></a>`
            }}
        ],
        dom: 't<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>><"card-header flex-column flex-md-row border-bottom"<"head-label text-left"><"dt-action-buttons text-end pt-0 pt-md-0"B>>',
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        responsive: false,
        scrollX: true,
        ordering: false,
        initComplete: ()  => {
            let info = `
                <p id="id_descuento_customer"></p>
                <table>
                    <tbody>
                        <tr>
                            <td><b>Total Base: </b></td>
                            <td id="td_productos">$0.00</td>
                        </tr>
                        <tr>
                            <td><b>Descuentos: </b></td>
                            <td id="td_descuentos">$0.00</td>
                        </tr>
                        <tr>
                            <td><b>Total: </b></td>
                            <td id="td_cotizacion">$0.00</td>
                        </tr>
                    </tbody>
                </table>
            `
            $('div.head-label').html(info);
        },
        drawCallback: () => {
            $('#products_id').val('');
            $('#products_id').removeClass('is-invalid');
            
            loadSelect();
            let products = table[0].data().toArray();
            if(products.length == 0){
                $('#discount_amount').val(0);
                $('#discount_percentaje').val(0);
            }
            $('.btn-discount').prop('disabled', products.length == 0 ? true : false);
            var value_total = products.reduce((a, b) => {
                return a + (parseInt(b.quantity) * parseFloat(b.value));
            }, 0);
            let valid_descount = $('#discount_amount').val() == 0 && $('#discount_percentaje').val() == 0 ? true : false;
            if(valid_descount){
                var value_descount = products.reduce((a, b) => {
                    let value = b.discount_percentage == 0 ? b.discount_amount : (parseFloat(b.discount_percentage) / 100) * b.value
                    return a + (parseInt(b.quantity) * parseFloat(value));
                }, 0);
            }else{
                var value_descount = $('#discount_amount').val() == 0 ? ($('#discount_percentaje').val() / 100) * value_total : format_number($('#discount_amount').val());
            }
            setTimeout(() => {
                $('#td_productos').html(formatPrice(parseFloat(value_total)));
                $('#td_descuentos').html(formatPrice(parseFloat(value_descount)));
                $('#td_cotizacion').html(formatPrice(parseFloat(value_total) - parseFloat(value_descount)));
            }, 1)
        },
        buttons: [
            {
                text: `<i class="ri-add-line ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Editar ${invoice.type_document_id == 1 ? 'Cotización' : 'Remisión'}</span>`,
                className: `btn btn-primary waves-effect waves-light`,
                action: () => sendCotizacion()
            },
            {
                text: '<i class="ri-refund-2-fill"></i> <span class="d-none d-sm-inline-block">Añadir Descuento</span>',
                className: `btn btn-warning waves-effect waves-light mx-2 btn-discount`,
                action: async () => {
                    const info = {
                        checked_amount: $('#discount_amount').val() != 0 ? true : false,
                        amount: $('#discount_amount').val() != 0 ? $('#discount_amount').val() : '',
                        checked_percentaje: $('#discount_percentaje').val() != 0 ? true : false,
                        percentaje: $('#discount_percentaje').val() != 0 ? $('#discount_percentaje').val() : '',
                        checked_products: products.find(p => p.discount) !== undefined
                    }
                    if($('#discount_amount')){

                    }

                    let inputs = `
                        <div class="row w-100">
                            <div class="col-sm-12 mb-3">
                                <div class="form-check custom-option custom-option-basic">
                                    <label class="form-check-label custom-option-content" for="descuento_fijo">
                                    <input name="descuento" class="form-check-input" type="radio" onchange="changeDiscount(this.value)" value="monto" id="descuento_fijo" ${info.checked_amount ? 'checked' : ''}>
                                    <span class="custom-option-header">
                                        <span class="h6 mb-0">Descuento único fijo en toda la cotización</span>
                                        <small class="text-muted">$</small>
                                    </span>
                                    <span class="custom-option-body">
                                        <small>
                                            <div class="col-sm-12">
                                                <div class="input-group input-group-floating">
                                                    <span class="input-group-text">$</span>
                                                    <div class="form-floating">
                                                        <input ${info.checked_amount ? '' : 'disabled'} type="text" value="${info.checked_amount ? separador_miles(parseFloat(info.amount)) : ''}" onchange="changeDiscountValue(1, this.value)" onkeyup="updateFormattedValue(this)" id="input_descuento_monto" class="form-control" placeholder="">
                                                        <label>Monto del descuento</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </small>
                                    </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-check custom-option custom-option-basic">
                                    <label class="form-check-label custom-option-content" for="descuento_porcentaje">
                                    <input name="descuento" class="form-check-input" type="radio" onchange="changeDiscount(this.value)" value="porcentaje" id="descuento_porcentaje" ${info.checked_percentaje ? 'checked' : ''}>
                                    <span class="custom-option-header">
                                        <span class="h6 mb-0">Descuento por porcentaje fijo en toda la cotización</span>
                                        <small class="text-muted">%</small>
                                    </span>
                                    <span class="custom-option-body">
                                        <small>
                                            <div class="col-sm-12">
                                                <div class="input-group input-group-floating">
                                                    <span class="input-group-text">%</span>
                                                    <div class="form-floating">
                                                        <input ${info.checked_percentaje ? '' : 'disabled'} type="number" value="${info.percentaje}" onchange="changeDiscountValue(2, this.value)" class="form-control" id="input_descuento_porcentaje" placeholder="">
                                                        <label>Porcentaje del descuento</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </small>
                                    </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-check custom-option custom-option-basic">
                                    <label class="form-check-label custom-option-content" for="descuento_producto">
                                    <input name="descuento" class="form-check-input" type="radio" onchange="changeDiscount(this.value)" value="porductos" id="descuento_producto" ${info.checked_products ? 'checked' : ''}>
                                    <span class="custom-option-header">
                                        <span class="h6 mb-0">Aplicar descuentos linea por linea en productos especificos</span>
                                    </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    `

                    const {value: data} = await Swal.fire({
                        title: 'Descuento',
                        html: inputs,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        customClass: {
                            htmlContainer: 'd-flex',
                            confirmButton: 'btn btn-primary waves-effect'
                        },
                    })
                    reloadTable();
                }
            },
            {
                text: '<i class="ri-arrow-go-back-line"></i> <span class="d-none d-sm-inline-block">Regresar</span>',
                className: `btn btn-secondary waves-effect waves-light mx-2`,
                action: () => window.location.href = base_url(['dashboard/cotizaciones'])
            }
        ]
    })
}

function reloadTable(){
    table[0].clear();
    table[0].rows.add(products.filter(p => !p.isDelete));
    table[0].draw(true);
}

async function sendCotizacion(){
    if(products.length == 0){
        $('#products_id').addClass('is-invalid');
        return alert('Campos obligatorios', 'Por favor ingrese como minimo un producto.', 'warning', 5000)
    }
    const form = $("#form_cotizacion");
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
    data.products = products;
    data.discount_amount = format_number(data.discount_amount);
    data.id = invoice.id;
    let products_aux = table[0].data().toArray();
    data.value_invoice = products_aux.reduce((a, b) => {
        return a + (parseInt(b.quantity) * parseFloat(b.value));
    }, 0);
    let valid_descount = $('#discount_amount').val() == 0 && $('#discount_percentaje').val() == 0 ? true : false;
    if(valid_descount){
        data.value_descount = products_aux.reduce((a, b) => {
            let value = b.discount_percentage == 0 ? b.discount_amount : (parseFloat(b.discount_percentage) / 100) * b.value
            return a + (parseInt(b.quantity) * parseFloat(value));
        }, 0);
    }else{
        data.value_descount = $('#discount_amount').val() == 0 ? (data.discount_percentaje / 100) * data.value_total : data.discount_amount;
    }
    let url = base_url(['invoices/edit']);
    const res = await proceso_fetch(url, data);
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
    console.log(res);
}