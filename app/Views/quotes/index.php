<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Documentos<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<?= $this->include('layouts/css_datatables') ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">

        <div class="col-md-12 col-xxl-12">
            <div class="row">
                <?php foreach ($type_documents as $key => $td): ?>
                    <div class="col-lg-6 col-sm-12" id="div_<?= $td->id ?>">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2"><?= "$td->name - $td->code" ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <h2 class="mb-0">$ 0.00</h2>
                                </div>
                                <p class="mt-1">Creadas hoy: 0</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
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
                                        <option value="<?= $period->value ?>" <?= $period->value == "day" ? "selected": "" ?>><?= $period->name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="period">Periodo</label>
                                <span class="form-floating-focused"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="date_init" value="<?= date('Y-m-d') ?>" readonly>
                                    <label for="date_init">Fecha Inicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="input-group input-group-floating">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="date_end" value="<?= date('Y-m-d') ?>" readonly>
                                    <label for="date_end">Fecha Fin</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Filtrar</button>
                    <button type="reset" class="btn btn-danger mb-2 d-grid w-100 waves-effect waves-light">Reiniciar</button>
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
</script>
<script src="<?= base_url(['master/js/quotes/index.js']) ?>"></script>
<?= $this->endSection(); ?>
