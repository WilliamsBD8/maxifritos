<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Informe Vendedores<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<?= $this->include('layouts/css_datatables') ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<?php
    $date_period = array_values(array_filter($periods, function($period) {
        return $period->selected;
    }))[0];
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">
        <div class="col-md-12 col-xxl-12">
            <div class="row gy-6">

                <div class="col-12">
                    <div class="card">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body">
                                <div class="row gy-4 gy-sm-1">

                                <h4 class="card-title text-center">Informe de Vendedores - Productos
                                    <br>
                                    <span class="m-0 text-muted text-center text-date"><?= $date_period->name ?></span>
                                </h4>
                                <div class="col s12 card-datatable card-datatable-filter">
                                    <table class="datatables-basic table table-bordered text-center h-100" id="table_data_filter"></table>
                                </div>
                                
                                <div id="indicadores" class="row gy-4 gy-sm-1"></div>

                                <!--     <php foreach ($type_documents as $key => $td): ?>

                                        <div class="col-sm-12 col-md-6 col-lg-6 border-end" id="div_<= $td->id ?>">
                                            <div class="d-flex justify-content-between align-items-start card-widget-1 pb-4 pb-sm-0">
                                                <div class="card-body py-0">
                                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                                        <h2 class="mb-0">$ 0,00</h2>
                                                    </div>
                                                    <h6 class="mb-0 fw-normal text-center"><b><= "$td->name - $td->code" ?></b></h6>
                                                    <div class="row mb-1">
                                                        <div class="col-sm-12 col-lg-6">
                                                            <p class="mb-0 fw-medium text-center inv">Documentos: <b>0</b></p>
                                                        </div>
                                                        <div class="col-sm-12 col-lg-6">                                        
                                                            <p class="mb-0 fw-medium text-center pro">Productos: <b>0</b></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="d-none d-sm-block d-md-none d-lg-none me-6 my-0">
                                        </div>
                                    <php endforeach ?> -->
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xxl-12">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-md-12">
                        <div class="card-body py-0">
                            <div class="col s12 card-datatable ">
                                <table class="datatables-basic table table-bordered text-center h-100" id="table_datatable"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-3 col-md-6">
        
        <div
            class="offcanvas offcanvas-end"
            tabindex="-1"
            id="canvasFilter"
            aria-labelledby="canvasFilterLabel">
            <div class="offcanvas-header">
            <h5 id="canvasFilterLabel" class="offcanvas-title">Filtros</h5>
            <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form action="" id="formFilter" onsubmit="sendFilter(event)">
                    <div class="row">
                        <div class="col-sm-12 mb-2">
                            <div class="form-floating">
                                <select class="form-select" id="type_document_filter" name="type_document_filter">
                                    <?php foreach($type_documents as $type_document): ?>
                                        <option value="<?= $type_document->id ?>" <?= $type_document->id == 2 ? "selected" : "" ?>><?= "$type_document->name [$type_document->code]" ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="type_document_filter">Tipo de Documento</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="form-floating">
                                <select class="form-select" id="period" name="period" onchange="changePeriod(this.value)">
                                    <?php foreach($periods as $period): ?>
                                        <option value="<?= $period->value ?>" <?= $period->selected ? "selected" : "" ?>><?= $period->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="period">Periodo</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="date_init" value="<?= $date_period->dates->date_init ?>" readonly>
                                    <label for="date_init">Fecha Inicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="date_end" value="<?= $date_period->dates->date_end ?>" readonly>
                                    <label for="date_end">Fecha Fin</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" data-allow-clear="true" id="seller_filter" name="seller_filter" data-placeholder="Seleccione un cliente">
                                    <option value=""></option>
                                    <?php foreach($sellers as $seller): ?>
                                        <option value="<?= $seller->id ?>"><?= $seller->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="seller_filter">Vendedor</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" data-allow-clear="true" id="product_filter" name="product_filter" data-placeholder="Seleccione un producto">
                                    <option value=""></option>
                                    <?php foreach($products as $product): ?>
                                        <option value="<?= $product->id ?>"><?= $product->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="product_filter">Producto</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>

                        
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Filtrar</button>
                    <button type="button" onclick="resetFilter()" class="btn btn-danger mb-2 d-grid w-100 waves-effect waves-light">Reiniciar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<?= $this->include('layouts/js_datatables') ?>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script>
    const periodsData       = () => <?= json_encode($periods) ?>;
    const typeDocumentData  = () => <?= json_encode($type_documents) ?>;
    const sellersData       = () => <?= json_encode($sellers) ?>;
    const productsData      = () => <?= json_encode($products) ?>;
</script>
<script src="<?= base_url(['master/js/reports/sellers.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
