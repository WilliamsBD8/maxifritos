<?= $this->extend('layouts/page'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/select2/select2.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />
<?= $this->include('layouts/css_datatables') ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">
        <div class="col-md-12 col-xxl-12">
            <div class="card">
                <div class="d-flex align-items-end row mb-2">
                    <div class="col-md-12">
                        <div class="card-body">
                            <h4 class="card-title mb-4 text-center">Editar Cotización</h4>
                            <form action="javascript:void(0);" id="form_cotizacion">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control flatpickr-input" value="<?= date('Y-m-d', strtotime($invoice->created_at)) ?>" placeholder="YYYY-MM-DD" id="date" readonly>
                                            <label for="date">Fecha</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" required placeholder="YYYY-MM-DD" id="delivery_date" value="<?= $invoice->delivery_date ?>">
                                            <label for="delivery_date">* Fecha de entrega</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select
                                                onchange="loadProducts(this.value)"
                                                class="select2 form-select form-select-lg" id="customer_id" name="customer" required>
                                                <option value="" disabled selected>Seleccione un cliente</option>
                                                <?php foreach($customers as $customer): ?>
                                                    <?php if($customer->type_customer_id == 1): ?>
                                                        <option value="<?= $customer->id ?>"  <?= $customer->id == $invoice->customer_id ? 'selected' : '' ?>><?= $customer->name ?></option>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="customer_id">* Cliente de cotización</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select
                                                class="form-select form-select-lg"
                                                id="branch_office" name="branch_office">
                                            </select>
                                            <label for="branch_office">Seleccione Sucursal</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select form-select-lg" id="seller_id" name="seller" required>
                                                <option value="" disabled selected>Seleccione un vendedor</option>
                                                <?php foreach($sellers as $seller): ?>
                                                    <option value="<?= $seller->id ?>" <?= $seller->id == $invoice->seller_id ? 'selected' : '' ?>><?= $seller->name ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="seller_id">* Vendedor</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 col-md-6 mb-2">
                                        <div class="form-floating  form-floating-outline">
                                            <select data-allow-clear="true" class="form-select form-select-lg" id="products_id" name="product" onchange="addProduct(this.value)" data-placeholder="Seleccione un producto">
                                                <option value="" disabled selected>Seleccione un producto</option>
                                                <?php foreach($products as $product): ?>
                                                    <?php if($product->status == "active"): ?>
                                                        <option value="<?= $product->id ?>"><?= "$product->code - $product->name" ?></option>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="products_id">Añadir Producto</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                    <div class="form-floating form-floating-outline col-sm-12 col-lg-6 col-md-12 mb-2">
                                        <textarea class="form-control h-px-100" id="address" placeholder=""><?= $invoice->address ?></textarea>
                                        <label for="address">Facturar a:</label>
                                    </div>
                                    <div class="form-floating form-floating-outline col-sm-12 col-lg-6 col-md-12 mb-2">
                                        <textarea class="form-control h-px-100" id="notes" placeholder=""><?= $invoice->note ?></textarea>
                                        <label for="notes">Nota: </label>
                                    </div>
                                    <input type="hidden" id="discount_amount" name="discount_amount" value="<?= $invoice->discount_amount ?>">
                                    <input type="hidden" id="discount_percentaje" name="discount_percentaje" value="<?= $invoice->discount_percentage ?>">
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
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<?= $this->include('layouts/js_datatables') ?>
<script>
    const productsData = () => <?= json_encode($products) ?>;
    const invoiceData = () => {
        let invoice = <?= json_encode($invoice) ?>;
        return invoice;
    };
    const sellersData = () => <?= json_encode($sellers) ?>;
    const customersData = () => <?= json_encode($customers) ?>;
</script>
<script src="<?= base_url(['assets/js/forms-selects.js']) ?>"></script>
<script src="<?= base_url(['master/js/quotes/edit.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
