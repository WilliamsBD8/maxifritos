<div class="invoice-preview">
    <!-- Invoice -->
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
        <div class="card invoice-preview-card p-sm-12 p-6">
            <div class="card-body invoice-preview-header rounded-4 p-6">
                <div class="header-invoice">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-6">
                                        <span class="name-app"><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : 'Name' ?></span>
                                    </div>
                                </td>
                                <td class="text-rigth">
                                    <h3 class="mb-6"><?= $invoice->name_document ?> #<?= $invoice->resolution ?></h3>
                                    <div>
                                        <span><?= $invoice->created_at ?></span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="card-body py-6 px-0">
                <div class="d-flex justify-content-between flex-wrap">
                    <div class="w-100">
                        <table class="w-100 text-left">
                            <tbody>
                                <tr>
                                    <td class="pe-4">Nombre:</td>
                                    <td><?= $invoice->customer->name ?></td>
                                    <td class="pe-4">Documento:</td>
                                    <td><?= "{$invoice->customer->type_document}. {$invoice->customer->identification_number}" ?>
                                    </td>
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
            <br>
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Descripción</th>
                        <th>Valor Unitario</th>
                        <th>Descuento</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $discount_line = 0 ?>
                    <?php foreach($invoice->line_invoices as $line_invoice): ?>
                    <tr>
                        <td><?= "{$line_invoice->quantity}" ?></td>
                        <td class="text-heading">
                            <?= "{$line_invoice->product_code} {$line_invoice->product_name}" ?></td>
                        <td><?= number_format($line_invoice->value, '2', '.', ',') ?></td>
                        <?php
                            $discount = 0;
                            if($line_invoice->discount_percentage != 0) $discount = ($line_invoice->discount_percentage / 100) * ($line_invoice->value * $line_invoice->quantity);
                            if($line_invoice->discount_amount != 0) $discount = $line_invoice->discount_amount;
                            $discount_line += $discount;
                        ?>
                        <td><?= number_format($discount, '2', '.', ',')?><?= $line_invoice->discount_percentage != 0 ? "<br>($line_invoice->discount_percentage %)" : ""  ?>
                        </td>
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
            <br>
            <div class="table-responsive">
                <table class="table m-0 table-borderless">
                    <tbody>
                        <tr>
                            <td class="align-top px-0 py-6" style="width: 50%">
                                <p class="mb-1">
                                    <span class="me-2 fw-medium text-heading">Vendedor:</span>
                                    <span>
                                        <?= $invoice->seller->name ?>
                                    </span>
                                </p>
                                <!-- <span>Thanks for your business</span> -->
                            </td>
                            <td class="pe-0 py-6 w-px-100"  style="width: 25%">
                                <p class="mb-1">Total Base:</p>
                                <p class="mb-1">Descuento:</p>
                                <p class="mb-0 pt-2">Total:</p>
                            </td>
                            <td class="text-end px-0 py-6 w-px-100" style="width: 25%">
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
                        <span><?= $invoice->note ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Invoice -->
</div>