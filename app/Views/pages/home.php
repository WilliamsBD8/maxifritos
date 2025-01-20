<?= $this->extend('layouts/page'); ?>

<?= $this->section('styles'); ?>
  <link rel="stylesheet" href="<?= base_url(["assets/vendor/css/pages/cards-statistics.css"]) ?>" />
  <link rel="stylesheet" href="<?= base_url(["assets/vendor/css/pages/cards-analytics.css"]) ?>" />
  <style>
    #map {
      height: 100%;
      width: 100%;
    }
  </style>
<?= $this->endSection(); ?>

<?php $user_permit = session('user')->role_id == 3 ? false : true ?>

<?= $this->section('content'); ?>
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-6">
    <!-- Gamification Card -->
    <div class="col-md-12 col-xxl-<?= $user_permit ? '8' : '12' ?>">
      <div class="card h-100">
        <div class="d-flex align-items-center row">
          <div class="col-md-8 order-2 order-md-1">
            <div class="card-body">
              <h4 class="card-title mb-4">Bienvenido <span class="fw-bold"><?= session('user')->name ?></span> 🎉</h4>
              <!-- <div class=" h-100 p-5">
                <div id="map"></div>
              </div> -->
              <!-- <p class="mb-0">You have done 68% 😎 more sales today.</p>
              <p>Check your new badge in your profile.</p>
              <a href="javascript:;" class="btn btn-primary">View Profile</a> -->
            </div>
          </div>
          <div class="col-md-4 text-center text-md-end order-1 order-md-2">
            <div class="card-body pb-0 px-0 pt-2">
              <img
                src="../../assets/img/illustrations/illustration-john-light.png"
                height="186"
                class="scaleX-n1-rtl"
                alt="View Profile"
                data-app-light-img="illustrations/illustration-john-light.png"
                data-app-dark-img="illustrations/illustration-john-dark.png" />
            </div>
          </div>
        </div>
        
      </div>
    </div>
    <!--/ Gamification Card -->

    <?php if($user_permit): ?>

      <!-- Project Statistics -->
      <div class="col-sm-12 col-md-6 col-xxl-4">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Historial de ingresos al sistema</h5>
            <div class="dropdown">
              <a
                class="btn btn-text-secondary rounded-pill text-muted border-0 p-1"
                type="button"
                id="projectStatus"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <i class="ri-group-line"></i>
              </a>
            </div>
          </div>
          <div class="d-flex justify-content-between p-4 border-bottom">
            <p class="mb-0 fs-xsmall">Usuario</p>
            <p class="mb-0 fs-xsmall">Fecha</p>
          </div>
          <div class="card-body">
            <ul class="p-0 m-0">
              <?php foreach ($historial as $key => $history): ?>
                <li class="d-flex align-items-center mb-6">
                  <div class="avatar avatar-md flex-shrink-0 me-4">
                    <div class="avatar-initial bg-light-gray rounded-3">
                      <div>
                        <img src="<?= empty($history->user_photo) ? base_url(['assets/img/avatars/1.png']) : base_url(['assets/upload/images', $history->user_photo]) ?>" alt="User" class="h-25" />
                      </div>
                    </div>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1"><?= $history->user_name ?></h6>
                      <small><?= $history->user_rol ?></small>
                    </div>
                    <span class=""><?= str_replace (' ', '<br>', $history->created_at) ?></span>
                  </div>
                </li>
              <?php endforeach ?>
            </ul>
          </div>
        </div>
      </div>
      <!--/ Project Statistics -->

    <?php endif ?>

    

    <!-- Sales Country Chart -->
    <div class="col-12 col-xxl-4 col-md-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex justify-content-between">
            <h5 class="mb-2">Documentos</h5>
            <div class="dropdown">
              <a href="<?= base_url(['dashboard/cotizaciones']) ?>" class="btn btn-text-secondary rounded-pill text-muted border-0 p-1">
                <i class="ri-file-list-3-line"></i>
              </a>
            </div>
          </div>
          <?php foreach ($type_documents as $key => $td): ?>
            <p class="card-subtitle mb-2">Total <?= $td->name ?> $ <?= number_format($td->total, 2, '.', ',') ?></p>
          <?php endforeach ?>
        </div>
        <div class="card-body pb-1">
          <div id="total_documentos"></div>
        </div>
      </div>
    </div>
    <!--/ Sales Country Chart -->

    <!-- Line Area Chart -->
    <div class="col-12 col-xxl-8 col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div>
            <h5 class="card-title mb-0">Documentos creados</h5>
            <small class="text-muted">Documentos creados en el ultimo año</small>
          </div>
        </div>
        <div class="card-body">
          <div id="lineAreaChart"></div>
        </div>
      </div>
    </div>
    <!-- /Line Area Chart -->

    
  </div>
</div>
<?php $this->endSection() ?>

<?= $this->section('javaScripts'); ?>


  <script src="<?= base_url(['assets/vendor/libs/apex-charts/apexcharts.js']) ?>"></script>
  <script>
    const typeDocumentData = () => {
      let type_documents = <?= json_encode($type_documents) ?>;
      return type_documents;
    }
  </script>
  
  <script src="<?= base_url(['master/js/home/index.js']) ?>"></script>
  <script src="<?= base_url(["assets/js/dashboards-analytics.js"]) ?>"></script>
<?= $this->endSection() ?>  