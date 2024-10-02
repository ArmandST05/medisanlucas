<?php
if (isset($_GET["id"])) :
    $product = ProductData::getById($_GET["id"]);
    $operations = OperationDetailData::getAllByProductId($product->id);
?>
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group pull-right">
            </div>
            <h1><?php echo $product->name;; ?> <small>Historial</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php
            $totalInputs = OperationDetailData::getByOperationTypeProduct($product->id, 1);
            ?>
            <div class="jumbotron">
                <center>
                    <h2>Entradas</h2>
                    <h1><?php echo $totalInputs; ?></h1>
                </center>
            </div>
            <br>
        </div>

        <div class="col-md-4">
            <?php
            $totalStock = OperationDetailData::getStockByProduct($product->id);
            ?>
            <div class="jumbotron">
                <center>
                    <h2>Disponibles</h2>
                    <h1><?php echo $totalStock; ?></h1>
                </center>
            </div>
            <div class="clearfix"></div>
            <br>

        </div>

        <div class="col-md-4">
            <?php
            $totalOutputs = -1 * OperationDetailData::getByOperationTypeProduct($product->id, 2);
            ?>
            <div class="jumbotron">
                <center>
                    <h2>Salidas</h2>
                    <h1><?php echo $totalOutputs; ?></h1>
                </center>
            </div>

            <div class="clearfix"></div>
            <br>
            <?php
            ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (count($operations) > 0) : ?>
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <th>Fecha registro</th>
                        <th>Cantidad</th>
                        <th>Tipo</th>
                        <th>Lote</th>
                        <th>Fecha caducidad</th>
                    </thead>
                    <?php foreach ($operations as $operation) : ?>
                        <tr>
                            <td><?php echo $operation->date; ?></td>
                            <td><?php echo $operation->quantity; ?></td>
                            <td><?php echo strToUpper($operation->getOperationType()->name); ?></td>
                            <td><?php echo $operation->lot; ?></td>
                            <td><?php echo $operation->expiration_date_format; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>
<script>
    $(document).ready(function() {
        var dataTable = $('#dataTable').DataTable({
            pageLength: 50,
            ordering: false,
            language: {
                url: 'plugins/datatables/languages/es-mx.json'
            }
        });
    });
</script>
