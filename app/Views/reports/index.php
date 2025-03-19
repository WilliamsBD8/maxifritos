<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Informes<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>

<link rel="stylesheet" href="<?= base_url(["assets/vendor/css/pages/cards-statistics.css"]) ?>" />
<link rel="stylesheet" href="<?= base_url(["assets/vendor/css/pages/cards-analytics.css"]) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">

        <div class="col-md-12 col-xxl-12">
            <div class="card">
                <div class="d-flex align-items-end row mb-2">
                    <div class="col-md-12">
                        <div class="card-body p-2">
                            <h4 class="card-title m-0 text-center title-report">
                                Reporte General
                                <button class="btn rounded-pill btn-label-primary waves-effect" type="button"
                                    data-bs-toggle="offcanvas" data-bs-target="#canvasFilter"
                                    aria-controls="canvasFilter">
                                    <i class="ri-filter-3-fill ri-16px"></i>
                                </button>
                            </h4>
                            <h4 class="text-muted text-center m-0 seller-title"></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-widget-separator-wrapper">
                    <div class="card-body">
                        <div class="row gy-4 gy-sm-1">

                            <h4 class="card-title mb-4 text-center">
                                Total Hoy
                            </h4>
                            <?php foreach ($documents as $key => $td): ?>

                            <div class="col-sm-12 col-md-6 col-lg-6 border-end" id="div_<?= $td->id ?>">
                                <div
                                    class="d-flex justify-content-between align-items-start card-widget-1 pb-4 pb-sm-0">
                                    <div class="card-body py-0">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <h2 class="mb-0">$ 0,00</h2>
                                        </div>
                                        <h6 class="mb-0 fw-normal text-center"><b><span
                                                    class="badge rounded-pill bg-<?= $td->color->class ?> mx-3 my-2 px-4 py-2"><?= "$td->name" ?></span></b>
                                        </h6>
                                        <div class="row mb-1">
                                            <div class="col-sm-12 col-lg-6">
                                                <p class="mb-0 fw-medium text-center inv">Documentos: <b>0</b></p>
                                            </div>
                                            <div class="col-sm-12 col-lg-6">
                                                <p class="mb-0 fw-medium text-center cli">Clientes: <b>0</b></p>
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

        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Top Vendedores</h5>
                    </div>
                </div>
                <div class="card-body pt-xl-5">
                    <div id="seller-1"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Top Clientes</h5>
                    </div>
                </div>
                <div class="card-body pt-xl-5">
                    <div id="customer-1"></div>
                </div>
            </div>
        </div>

        <!-- Top Referral Source  -->
        <!-- <div class="col-12 col-xxl-6">
            <div class="card h-100">
                <div class="card-header mb-0 pb-0 d-flex justify-content-center">
                    <div>
                        <h5 class="card-title mb-0">Top Vendedores</h5>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="card col-12">
                        <div class="mb-6">
                            <div class="card-header p-0">
                                <div class="nav-align-top">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <?php foreach ($documents as $key => $td): ?>
                                            <li class="nav-item">
                                                <button type="button" class="nav-link <?= $key == 0 ? 'active' : '' ?>" role="tab"
                                                    data-bs-toggle="tab" data-bs-target="#navs-seller-<?= $td->id ?>"
                                                    aria-controls="navs-seller-<?= $td->id ?>" aria-selected="true">
                                                    <span class="d-none d-sm-block text-<?= $td->color->class ?>"><i
                                                            class="tf-icons <?= $td->icon ?> me-2"></i>
                                                        <?= $td->name ?>
                                                    </span>
                                                </button>
                                            </li>
                                        <?php  endforeach ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <?php foreach ($documents as $key => $td): ?>
                                        <div class="tab-pane fade <?= $key == 0 ? 'show active' : '' ?>" id="navs-seller-<?= $td->id ?>" role="tabpanel">
                                            <div id="seller-<?= $td->id ?>"></div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!--/ Top Referral Source  -->

        <!-- Top Referral Source  -->
        <!-- <div class="col-12 col-xxl-6">
            <div class="card h-100">
                <div class="card-header mb-0 pb-0 d-flex justify-content-center">
                    <div>
                        <h5 class="card-title mb-0">Top Clientes</h5>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="col-12">
                        <div class="mb-6">
                            <div class="card-header p-0">
                                <div class="nav-align-top">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <?php foreach ($documents as $key => $td): ?>
                                            <li class="nav-item">
                                                <button type="button" class="nav-link <?= $key == 0 ? 'active' : '' ?>" role="tab"
                                                    data-bs-toggle="tab" data-bs-target="#navs-customer-<?= $td->id ?>"
                                                    aria-controls="navs-customer-<?= $td->id ?>" aria-selected="true">
                                                    <span class="d-none d-sm-block text-<?= $td->color->class ?>"><i
                                                            class="tf-icons <?= $td->icon ?> me-2"></i>
                                                        <?= $td->name ?>
                                                    </span>
                                                </button>
                                            </li>
                                        <?php  endforeach ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <?php foreach ($documents as $key => $td): ?>
                                        <div class="tab-pane fade <?= $key == 0 ? 'show active' : '' ?>" id="navs-customer-<?= $td->id ?>" role="tabpanel">
                                            <div id="customer-<?= $td->id ?>"></div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!--/ Top Referral Source  -->




        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Total esta semana</h5>
                    </div>
                    <p class="mb-0 card-subtitle">
                        <?php foreach ($documents as $key => $td): ?>
                        <span
                            class="badge rounded-pill bg-<?= $td->color->class ?> mx-3 my-2 px-4 py-2 week-type-document-<?= $td->id ?>">
                            $ 0.00
                        </span>
                        <?php endforeach ?>
                    </p>
                </div>
                <div class="card-body pt-xl-5" id="content-semana">
                    <div id="total_semana"></div>
                </div>
            </div>
        </div>

        <!-- Shipment statistics-->
        <div class="col-12 col-lg-6 col-xxl-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2 mb-1">Total este mes</h5>
                        <p class="mb-0 card-subtitle">
                            <?php foreach ($documents as $key => $td): ?>
                            <span
                                class="badge rounded-pill bg-<?= $td->color->class ?> mx-3 my-2 px-4 py-2 month-type-document-<?= $td->id ?>">
                                $ 0.00
                            </span>
                            <?php endforeach ?>
                        </p>
                    </div>
                </div>
                <div class="card-body" id="content-mes">
                    <div id="shipmentStatisticsChart"></div>
                </div>
            </div>
        </div>
        <!--/ Shipment statistics -->

        <!-- Monthly Budget Chart-->
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Total este año</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="content-year">
                        <div id="monthlyBudgetChart"></div>
                    </div>
                    <div class="mt-4">
                        <p class="mb-0 card-subtitle">
                            <?php foreach ($documents as $key => $td): ?>
                            <span
                                class="badge rounded-pill bg-<?= $td->color->class ?> mx-3 my-2 px-4 py-2 year-type-document-<?= $td->id ?>">
                                $ 0.00
                            </span>
                            <?php endforeach ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Monthly Budget Chart-->

    </div>

    <div class="row gy-3">

        <div class="col-lg-3 col-md-6">
            <div class="offcanvas offcanvas-end" tabindex="-1" id="canvasFilter" aria-labelledby="canvasFilterLabel">
                <div class="offcanvas-header">
                    <h5 id="canvasFilterLabel" class="offcanvas-title"></h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0">
                    <form action="" id="formFilter" onsubmit="sendFilter(event)">
                        <div class="row">
                            <div class="col-sm-12 mb-2">
                                <div class="form-floating">
                                    <select class="form-select" id="seller_filter" name="seller_filter"
                                        data-placeholder="Seleccione un vendedor" data-allow-clear="true">
                                        <option></option>
                                        <?php foreach($sellers as $seller): ?>
                                        <option value="<?= $seller->id ?>"><?= "{$seller->name}" ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <label for="seller_filter">Vendedor</label>
                                    <span class="form-floating-focused"></span>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <button type="submit"
                            class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Filtrar</button>
                        <button type="button" class="btn btn-danger mb-2 d-grid w-100 waves-effect waves-light"
                            onclick="resetFilter()">Reiniciar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/apex-charts/apexcharts.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
const DocumentsData = () => <?= json_encode($documents) ?>;
const sellersData = () => <?= json_encode($sellers) ?>;
</script>

<script src="<?= base_url(['master/js/reports/index.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>