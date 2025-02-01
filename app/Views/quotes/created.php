<?= $this->extend('layouts/page'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/select2/select2.css']) ?>" />
<?= $this->include('layouts/css_datatables') ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">
        <div class="col-md-12 col-xxl-12">
            <div class="card">
                <div class="d-flex align-items-end row mb-1">
                    <div class="col-md-12">
                        <div class="card-body">
                            <form action="javascript:void(0);" id="form_cotizacion">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-3 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control flatpickr-input" value="<?= date('Y-m-d') ?>" placeholder="YYYY-MM-DD" id="date" readonly>
                                            <label for="date">Fecha</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-3 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select
                                            onchange="loadProducts(this.value)"
                                            class="select2 form-select form-select-lg"
                                            id="customer_id" name="customer" required
                                            data-allow-clear="false" data-placeholder="Seleccione un cliente">
                                                <option value="" disabled selected>Seleccione un cliente</option>
                                                <?php foreach($customers as $customer): ?>
                                                    <option value="<?= $customer->id ?>"><?= "$customer->name - $customer->identification_number" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="customer_id">* Cliente de cotización</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-3 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select
                                            class="select2 form-select form-select-lg"
                                            id="seller_id" name="seller" required
                                            data-allow-clear="false" data-placeholder="Seleccione un vendedor">
                                                <option value="" disabled selected>Seleccione un vendedor</option>
                                                <?php foreach($sellers as $seller): ?>
                                                    <option value="<?= $seller->id ?>"><?= $seller->name ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="seller_id">* Vendedor</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-3 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select
                                            class="form-select form-select-lg"
                                            id="products_id" name="product" onchange="addProduct(this.value)" disabled>
                                                <option value="" disabled selected>Seleccione un producto</option>
                                                <?php foreach($products as $product): ?>
                                                    <option value="<?= $product->id ?>"><?= "$product->code - $product->name" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="products_id">Añadir Producto</label>
                                        </div>
                                    </div>
                                    <div class="form-floating form-floating-outline col-sm-12 col-lg-6 col-md-12 mb-2">
                                        <textarea class="form-control h-px-100" id="address" placeholder=""></textarea>
                                        <label for="address">Facturar a:</label>
                                    </div>
                                    <div class="form-floating form-floating-outline col-sm-12 col-lg-6 col-md-12 mb-2">
                                        <textarea class="form-control h-px-100" id="notes" placeholder=""></textarea>
                                        <label for="notes">Nota: </label>
                                    </div>
                                    <input type="hidden" id="discount_amount" name="discount_amount">
                                    <input type="hidden" id="discount_percentaje" name="discount_percentaje">
                                    <!-- <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-check custom-option custom-option-basic checked">
                                            <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                                <input class="form-check-input" type="radio" name="type_descount" value="discount_invoice" onchange="changeDiscount(this.value)" id="customRadioTemp2" checked>
                                                <span class="custom-option-header p-0">
                                                    <span class="h6 mb-0">Descuento General</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                                <input class="form-check-input" type="radio" name="type_descount" value="discount_products" onchange="changeDiscount(this.value)" id="customRadioTemp1">
                                                <span class="custom-option-header p-0">
                                                    <span class="h6 mb-0">Descuento por producto</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div class="input-group input-group-floating">
                                            <span class="input-group-text">$</span>
                                            <div class="form-floating">
                                                <input type="text" onchange="changeDiscount(1, this.value)" onkeyup="updateFormattedValue(this)" class="form-control" id="discount_amount" name="discount_amount" value="0">
                                                <label>Valor del descuento</label>
                                            </div>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div class="input-group input-group-floating">
                                            <span class="input-group-text">%</span>
                                            <div class="form-floating">
                                                <input type="number" onchange="changeDiscount(2, this.value)" class="form-control" id="discount_percentaje" name="discount_percentaje" value="0">
                                                <label>Porcentaje del descuento</label>
                                            </div>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xxl-12">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-md-12">
                        <div class="card-body py-0 pt-4">
                            <div class="col s12 card-datatable">
                                <table class="datatables-basic table table-bordered text-center" id="table_datatable">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>


<script src="<?= base_url(['assets/vendor/libs/select2/select2.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/tagify/tagify.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/bootstrap-select/bootstrap-select.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/typeahead-js/typeahead.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/bloodhound/bloodhound.js']) ?>"></script>

<?= $this->include('layouts/js_datatables') ?>
<script>
    const productsData = () => <?= json_encode($products) ?>;
    const sellersData = () => <?= json_encode($sellers) ?>;
    const customersData = () => <?= json_encode($customers) ?>;
</script>
<script src="<?= base_url(['assets/js/forms-selects.js']) ?>"></script>
<script src="<?= base_url(['master/js/quotes/created.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
