<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Documentos<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<?= $this->include('layouts/css_datatables') ?>
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

                                <h3 class="m-0 text-muted text-center text-date"><?= $date_period->name ?></h3>

                                    <?php foreach ($type_documents as $key => $td): ?>

                                        <div class="col-sm-12 col-md-6 col-lg-6 border-end" id="div_<?= $td->id ?>">
                                            <div class="d-flex justify-content-between align-items-start card-widget-1 pb-4 pb-sm-0">
                                                <div class="card-body py-0">
                                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                                        <h2 class="mb-0">$ 0,00</h2>
                                                    </div>
                                                    <h6 class="mb-0 fw-normal text-center"><b><?= "$td->name - $td->code" ?></b></h6>
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
                                    <?php endforeach ?>
                                    
                                    
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
                                    <input type="date" class="form-control" id="date_init" value="<?= $date_period->dates->date_init ?>" readonly>
                                    <label for="date_init">Fecha Inicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="date_end" value="<?= $date_period->dates->date_end ?>" readonly>
                                    <label for="date_end">Fecha Fin</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="resolution_filter" placeholder="">
                                    <label for="resolution_filter">N° Resolución</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" data-allow-clear="true" id="customer_filter" name="customer_filter" data-placeholder="Seleccione un cliente">
                                    <option value=""></option>
                                    <?php foreach($customers as $customer): ?>
                                        <option value="<?= $customer->id ?>"><?= $customer->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="customer_filter">Cliente</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" data-allow-clear="true" id="type_document_filter" name="type_document_filter" data-placeholder="Seleccione un cliente">
                                    <option value=""></option>
                                    <?php foreach($type_documents as $key => $td): ?>
                                        <option value="<?= $td->id ?>"><?= $td->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="type_document_filter">Tipo de documento</label>
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
<script>
    const status_data = <?= json_encode($status)?>;
    status_data.map(s => {
        switch (s.id) {
            case '1':
                s.class = ' bg-label-warning';
                break;
            case '2':
                s.class = ' bg-label-success';
                break;
        
            default:
                s.class = ' bg-label-danger';
                break;
        }
    });
    const periods = <?= json_encode($periods) ?>;
    const type_documents_data = () => (<?= json_encode($type_documents) ?>);
</script>
<script src="<?= base_url(['master/js/quotes/index.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
