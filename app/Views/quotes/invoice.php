<?= $this->extend('layouts/page'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/css/pages/app-invoice.css']) ?>">

<!-- <= $this->include('layouts/css_datatables') ?> -->
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row invoice-preview">

        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
            <div class="card invoice-preview-card p-sm-12 p-6">
                <div class="card-body invoice-preview-header rounded-4 p-6">
                    <div
                        class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column text-heading align-items-xl-center align-items-md-start align-items-sm-center flex-wrap gap-6">
                        <div>
                            <!-- <div class="d-flex svg-illustration align-items-center gap-2 mb-6">
                                <span class="mb-0 app-brand-text fw-semibold demo"><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : 'Name' ?></span>
                            </div> -->
                            <!-- <p class="mb-1">Office 149, 450 South Brand Brooklyn</p>
                            <p class="mb-1">San Diego County, CA 91905, USA</p>
                            <p class="mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p> -->
                        </div>
                        <div>
                            <h5 class="mb-6"><?= "$invoice->name_document # $invoice->resolution" ?></h5>
                            <div>
                                <span><?= format_date($invoice->created_at) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body py-6 px-0">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="w-100">
                          <table class="w-100 text-left">
                            <tbody>
                              <tr>
                                <td class="pe-4">Nombre:</td>
                                <td><?= $invoice->customer->name ?></td>
                                <td class="pe-4">Documento:</td>
                                <td><?= "{$invoice->customer->type_document}. {$invoice->customer->identification_number}" ?></td>
                              </tr>
                              <tr>
                                <td class="pe-4">Dirección:</td>
                                <td><?= $invoice->address ?></td>
                                <td class="pe-4">Telefono:</td>
                                <td><?= $invoice->customer->phone ?></td>
                              </tr>
                              <tr>
                                <td class="pe-4">Correo:</td>
                                <td><?= $invoice->customer->email ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
                <div class="border rounded-4 border-bottom-0">
                    <table class="table m-0 text-center table-sm">
                        <thead>
                            <tr>
                                <th>Cant.</th>
                                <th>Descripción</th>
                                <th>Precio Unitario</th>
                                <th>Descuento</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $discount_line = 0 ?>
                            <?php foreach($invoice->line_invoices as $line_invoice): ?>
                            <tr>
                                <td class=""><?= "{$line_invoice->quantity}" ?></td>
                                <td class="text-heading"><?= "{$line_invoice->product_code} {$line_invoice->product_name}" ?></td>
                                <td><?= number_format($line_invoice->value, '2', '.', ',') ?></td>
                                <?php
                                    $discount = 0;
                                    if($line_invoice->discount_percentage != 0) $discount = ($line_invoice->discount_percentage / 100) * ($line_invoice->value * $line_invoice->quantity);
                                    if($line_invoice->discount_amount != 0) $discount = $line_invoice->discount_amount;
                                    $discount_line += $discount;
                                ?>
                                <td><?= number_format($discount, '2', '.', ',')?><?= $line_invoice->discount_percentage != 0 ? "<br>($line_invoice->discount_percentage %)" : ""  ?></td>
                                <?php 
                                    $value = 0;
                                    if($line_invoice->discount_percentage != 0){
                                        $value = ($line_invoice->discount_percentage / 100) * $line_invoice->value;
                                    }else if($line_invoice->discount_amount != 0){
                                        $value = $line_invoice->discount_amount;
                                    }
                                ?>
                                <td><?= number_format(($line_invoice->value * $line_invoice->quantity) - ($value * $line_invoice->quantity), '2', '.', ',') ?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 table-borderless">
                        <tbody>
                            <tr>
                                <td class="align-top px-0 py-6">
                                    <p class="mb-1">
                                        <span class="me-2 fw-medium text-heading">Vendedor:</span>
                                        <span>
                                            <?= $invoice->seller->name ?>
                                        </span>
                                    </p>
                                    <!-- <span>Thanks for your business</span> -->
                                </td>
                                <td class="pe-0 py-6 w-px-100">
                                    <p class="mb-1">Subtotal:</p>
                                    <p class="mb-1">Descuento:</p>
                                    <p class="mb-0 pt-2">Total:</p>
                                </td>
                                <td class="text-end px-0 py-6 w-px-100">
                                    <p class="fw-medium mb-1"><?= number_format($invoice->invoice_amount, 2, '.', ',') ?></p>
                                    <?php 
                                        $descuento = 0;
                                        if($invoice->discount_percentage > 0){
                                            $descuento = ($invoice->discount_percentage / 100) * $invoice->invoice_amount;
                                        }else if($invoice->discount_amount > 0){
                                            $descuento = $invoice->discount_amount;
                                        }else{
                                            $descuento = $discount_line;
                                        }
                                    ?>
                                    <p class="fw-medium mb-1"><?= number_format($descuento, 2, '.', ',') ?></p>
                                    <p class="fw-medium mb-0 pt-2"><?= number_format($invoice->payable_amount, 2, '.', ',') ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="mt-0 mb-6">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-medium text-heading">Notas:</span>
                            <ul>
                                <?= !empty($invoice->note) ? "<li>$invoice->note</li>" : "" ?>
                                <?php
                                    if($invoice->discount_amount > 0)
                                        echo "<li>La {$invoice->name_document} cuenta con un descuento de $ ".number_format($invoice->discount_amount, 2, '.', ',').".</li>";
                                    else if($invoice->discount_percentage > 0)
                                        echo "<li>La {$invoice->name_document} cuenta con un descuento del $invoice->discount_percentage %.</li>";
                                    else if($discount_line > 0){
                                        echo "<li>La {$invoice->name_document} cuenta con un descuento por linea en productos especificos.</li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Invoice -->

        
        <!-- Invoice Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary d-grid w-100 me-4 waves-effect waves-light mb-4" onclick="sendInvoice()">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                        class="ri-send-plane-line ri-16px scaleX-n1-rtl me-2"></i>Remisionar</span>
                    </button>
                    <div class="d-flex mb-4">
                        <a class="btn btn-outline-secondary d-grid w-100 me-4 waves-effect" target="_blank"
                            href="<?= base_url(['invoices/download', $invoice->id]) ?>">
                            Descargar
                        </a>
                        <a href="<?= base_url(['dashboard/cotizaciones/editar', $invoice->id]) ?>" class="btn btn-outline-secondary d-grid w-100 waves-effect">
                            Editar </a>
                    </div>
                    <a class="btn btn-outline-info d-grid w-100 me-4 waves-effect"
                        href="<?= base_url(['dashboard/cotizaciones']) ?>">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ri-arrow-left-s-line"></i>Regresar
                    </a>
                </div>
            </div>
        </div>
        <!-- /Invoice Actions -->
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<script>
    const invoiceData = () => {
        let data = <?= json_encode($invoice) ?>;
        return data;
    }
</script>
<script src="<?= base_url(['master/js/quotes/invoice.js?v='.getCommit()]) ?>"></script>
<!-- <= $this->include('layouts/js_datatables') ?> -->
<?= $this->endSection(); ?>