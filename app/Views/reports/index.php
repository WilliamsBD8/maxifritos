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
                            <h4 class="card-title m-0 text-center title-report">Reporte General</h4>
                            <div class="d-flex justify-content-center flex-wrap">
                                <?php foreach ($documents as $key => $td): ?>
                                <span
                                    class="badge rounded-pill bg-<?= $td->color->class ?> mx-3 my-2 px-4 py-2"><?= "$td->name - $td->code" ?></span>
                                <?php endforeach ?>
                                <button class="btn btn-primary waves-effect waves-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasFilter" aria-controls="canvasFilter">
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Impression & Order Chart -->
        <div class="col-lg-2 col-sm-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Total hoy</h5>
                    </div>
                </div>
                <div class="card-body pb-0 text-center">
                    <?php foreach($documents as $key => $td): ?>
                    <div class="d-flex align-items-center">
                        <div class="w-100">
                            <div class="card-info">
                                <div>
                                    <h5 class="mb-0">$ <?= number_format($td->data_day->total, '2', '.', ',') ?></h5>
                                    <h4 class="mb-1"><?= $td->data_day->total_inv ?></h4>
                                </div>
                                <p class="mb-0 mt-1">
                                <div class="d-flex gap-2 align-items-center justify-content-center mb-2">
                                    <div class="avatar avatar-xs flex-shrink-0">
                                        <div class="avatar-initial rounded bg-label-<?= $td->color->class ?>">
                                            <i class="<?= $td->icon ?> ri-16px"></i>
                                        </div>
                                    </div>
                                    <p class="mb-0"><?= $td->name ?></p>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php if($key == 0): ?>
                    <hr class="my-2" />
                    <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <!--/ Total Impression & Order Chart -->


        <div class="col-12 col-xxl-5 col-md-5">
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
                <div class="card-body pt-xl-5">
                    <div id="total_semana"></div>
                    <!-- <div class="d-flex justify-content-between mt-6">
                        <div>
                            <h6 class="mb-0">Most Visited Day</h6>
                            <p class="mb-0 small">Total 62.4k Visits on Thursday</p>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ri-arrow-right-s-line ri-24px scaleX-n1-rtl"></i>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- Shipment statistics-->
        <div class="col-12 col-lg-5 col-xxl-5">
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
                <div class="card-body">
                    <div id="shipmentStatisticsChart"></div>
                </div>
            </div>
        </div>
        <!--/ Shipment statistics -->

        <!-- Monthly Budget Chart-->
        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Total este año</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="monthlyBudgetChart"></div>
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

        <!-- Top Referral Source  -->
        <div class="col-12 col-xxl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Top Clientes</h5>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs nav-tabs-widget pb-6 gap-6 mx-1 d-flex flex-nowrap" role="tablist">
                        <?php foreach ($documents as $key => $document): ?>
                        <li class="nav-item">
                            <a href="javascript:void(0);"
                                class="nav-link btn <?= $key == 0 ? 'active' : '' ?> d-flex flex-column align-items-center justify-content-center"
                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-orders-id-<?= $document->id ?>"
                                aria-controls="navs-orders-id-<?= $document->id ?>" aria-selected="true">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-<?= $document->color->class ?> rounded-3">
                                            <div class="<?= $document->icon ?> ri-20px"></div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-1"><?= $document->code ?></p>
                                </div>
                            </a>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <div class="tab-content p-0">
                    <?php foreach ($documents as $key => $document): ?>
                    <div class="tab-pane fade <?= $key == 0 ? 'show active' : '' ?> "
                        id="navs-orders-id-<?= $document->id ?>" role="tabpanel">
                        <div class="card-body pb-1 px-0">
                            <div id="customer-<?= $document->id ?>"></div>
                        </div>
                    </div>
                    <?php endforeach ?>
                    <!-- <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                          <table class="table border-top">
                            <thead>
                              <tr>
                                <th class="bg-transparent border-bottom">Product Name</th>
                                <th class="bg-transparent border-bottom">STATUS</th>
                                <th class="text-end bg-transparent border-bottom">Profit</th>
                                <th class="text-end bg-transparent border-bottom">REVENUE</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td>facebook Adsense</td>
                                <td>
                                  <div class="badge bg-label-info rounded-pill">In Draft</div>
                                </td>
                                <td class="text-success fw-medium text-end">+5%</td>
                                <td class="text-end fw-medium">$5</td>
                              </tr>
                              <tr>
                                <td>Affiliation Program</td>
                                <td>
                                  <div class="badge bg-label-primary rounded-pill">Active</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-24%</td>
                                <td class="text-end fw-medium">$5,576</td>
                              </tr>
                              <tr>
                                <td>Email Marketing Campaign</td>
                                <td>
                                  <div class="badge bg-label-warning rounded-pill">warning</div>
                                </td>
                                <td class="text-success fw-medium text-end">+5%</td>
                                <td class="text-end fw-medium">$2,857</td>
                              </tr>
                              <tr>
                                <td>facebook Workspace</td>
                                <td>
                                  <div class="badge bg-label-success rounded-pill">Completed</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-12%</td>
                                <td class="text-end fw-medium">$850</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                          <table class="table border-top">
                            <thead>
                              <tr>
                                <th class="bg-transparent border-bottom">Product Name</th>
                                <th class="bg-transparent border-bottom">STATUS</th>
                                <th class="text-end bg-transparent border-bottom">Profit</th>
                                <th class="text-end bg-transparent border-bottom">REVENUE</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td>Affiliation Program</td>
                                <td>
                                  <div class="badge bg-label-primary rounded-pill">Active</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-24%</td>
                                <td class="text-end fw-medium">$5,576</td>
                              </tr>
                              <tr>
                                <td>instagram Adsense</td>
                                <td>
                                  <div class="badge bg-label-info rounded-pill">In Draft</div>
                                </td>
                                <td class="text-success fw-medium text-end">+5%</td>
                                <td class="text-end fw-medium">$5</td>
                              </tr>
                              <tr>
                                <td>instagram Workspace</td>
                                <td>
                                  <div class="badge bg-label-success rounded-pill">Completed</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-12%</td>
                                <td class="text-end fw-medium">$850</td>
                              </tr>
                              <tr>
                                <td>Email Marketing Campaign</td>
                                <td>
                                  <div class="badge bg-label-danger rounded-pill">warning</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-5%</td>
                                <td class="text-end fw-medium">$857</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                          <table class="table border-top">
                            <thead>
                              <tr>
                                <th class="bg-transparent border-bottom">Product Name</th>
                                <th class="bg-transparent border-bottom">STATUS</th>
                                <th class="text-end bg-transparent border-bottom">Profit</th>
                                <th class="text-end bg-transparent border-bottom">REVENUE</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td>reddit Workspace</td>
                                <td>
                                  <div class="badge bg-label-warning rounded-pill">process</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-12%</td>
                                <td class="text-end fw-medium">$850</td>
                              </tr>
                              <tr>
                                <td>Affiliation Program</td>
                                <td>
                                  <div class="badge bg-label-primary rounded-pill">Active</div>
                                </td>
                                <td class="text-danger fw-medium text-end">-24%</td>
                                <td class="text-end fw-medium">$5,576</td>
                              </tr>
                              <tr>
                                <td>reddit Adsense</td>
                                <td>
                                  <div class="badge bg-label-info rounded-pill">In Draft</div>
                                </td>
                                <td class="text-success fw-medium text-end">+5%</td>
                                <td class="text-end fw-medium">$5</td>
                              </tr>
                              <tr>
                                <td>Email Marketing Campaign</td>
                                <td>
                                  <div class="badge bg-label-success rounded-pill">Completed</div>
                                </td>
                                <td class="text-success fw-medium text-end">+50%</td>
                                <td class="text-end fw-medium">$857</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div> -->
                </div>
            </div>
        </div>
        <!--/ Top Referral Source  -->

    </div>

    <div class="row gy-3">

        <div class="col-lg-3 col-md-6">
            <div class="offcanvas offcanvas-end" tabindex="-1" id="canvasFilter"
                aria-labelledby="canvasFilterLabel">
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
                                    <select class="form-select" id="seller_filter" name="seller_filter">
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
                            data-bs-dismiss="offcanvas">Cerrar</button>
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
    const sellersData   = () => <?= json_encode($sellers) ?>;
</script>

<script src="<?= base_url(['master/js/reports/index.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>