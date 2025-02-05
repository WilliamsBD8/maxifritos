<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Clientes<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<?= $this->include('layouts/css_datatables') ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">

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

    <div class="row gy-3">
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
                                    <div class="input-group input-group-floating">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name_filter" name="name_filter">
                                            <label for="name_filter">Cliente</label>
                                        </div>
                                    </div>
                                </div>
                                <?php if(!empty($sellers)): ?>
                                    <div class="col-sm-12 mb-2">
                                        <div class="form-floating">
                                            <select class="form-select" id="user_origin_filter" name="user_origin_filter">
                                                <option value="">Todos</option>
                                                <option value="<?= session('user')->id ?>"><?= session('user')->name ?></option>
                                                <?php foreach($sellers as $seller): ?>
                                                    <option value="<?= $seller->id ?>"><?= "{$seller->name}" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="user_origin_filter">Vendedor</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="user_origin_filter" id="user_origin_filter" value="<?= session('user')->id ?>">
                                <?php endif ?>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Filtrar</button>
                            <button type="reset" class="btn btn-danger mb-2 d-grid w-100 waves-effect waves-light">Reiniciar</button>
                        </form>
                    </div>
            </div>
        </div>
    
        <div class="col-lg-3 col-md-6">
        
            <div
                class="offcanvas offcanvas-end"
                tabindex="-1"
                id="canvasCustomer"
                aria-labelledby="canvasCustomerLabel">
                    <div class="offcanvas-header">
                        <h5 id="canvasCustomerLabel" class="offcanvas-title"></h5>
                        <button
                            type="button"
                            class="btn-close text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body mx-0 flex-grow-0">
                        <form action="" id="formCustomer">
                            <div class="row">
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="" required>
                                        <label for="name">Nombre</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="" required>
                                        <label for="email">Correo</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    
                                    <div class="form-floating">
                                        <select class="form-select" id="type_document_identification" name="type_document_identification" required>
                                            <?php foreach($type_documents_identifications as $type_document_identification): ?>
                                                <option value="<?= $type_document_identification->id ?>"><?= "{$type_document_identification->name} - {$type_document_identification->code}" ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="type_document_identification">Tipo de documento</label>
                                        <span class="form-floating-focused"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control" name="identification_number" id="identification_number" placeholder="" required>
                                        <label for="identification_number">N° Identificación</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="" required>
                                        <label for="phone">N° Telefono</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control" name="discount_percentage" id="discount_percentage" placeholder="" max="100" min="0" value="0">
                                        <label for="discount_percentage">Descuento general</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-px-100" name="discount_detail" id="discount_detail" placeholder=""></textarea>
                                        <label for="address">Descripción del detalle</label>
                                        <!-- <input type="number" class="form-control" name="discount_detail" id="discount_detail" placeholder="" max="100" min="0" value="0">
                                        <label for="discount_detail">Descuento Detalle</label> -->
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="address">Dirección</label>
                                    <div class="input-group form-floating form-floating-outline">
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Dirección" required>
                                        <button class="btn btn-outline-primary waves-effect" type="button" id="view_map" onclick="viewMap()"><i class="ri-map-pin-line"></i></button>
                                        <!-- <input type="number" class="form-control" name="address" id="address" placeholder=""> -->
                                    </div>
                                </div>

                                <?php if(!empty($sellers)): ?>
                                    

                                    <div class="col-sm-12 mb-2">
                                        
                                        <div class="form-floating">
                                            <select class="form-select" id="user_origin" name="user_origin">
                                                <option value=""></option>
                                                <?php foreach($sellers as $seller): ?>
                                                    <option value="<?= $seller->id ?>"><?= "{$seller->name}" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label for="user_origin">Vendedor</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="user_origin" id="user_origin" value="<?= session('user')->id ?>">
                                <?php endif ?>

                                <div class="col-sm-12 mb-2 status">
                                    
                                    <div class="form-floating">
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active">Activo</option>
                                            <option value="inactive">Inactivo</option>
                                        </select>
                                        <label for="status">Estado</label>
                                        <span class="form-floating-focused"></span>
                                    </div>
                                </div>

                                <input type="hidden" name="id_customer" id="id_customer">
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Guardar</button>
                            <button type="button" class="btn btn-danger mb-2 d-grid w-100 waves-effect waves-light" data-bs-dismiss="offcanvas">Cerrar</button>
                        </form>
                    </div>
            </div>
        </div>
    </div>

</div>



<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<?= $this->include('layouts/js_datatables') ?>
<script src="<?= base_url(['master/js/customers/index.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
