<h1>Lista de Ventas</h1>
<div class="clearfix"></div>
<hr>
<table id="lookup1" class="table table-bordered table-hover">
    <thead align="center">
        <th></th>
        <th></th>
        <th></th>
        <th>Folio</th>
        <th>Día</th>
        <th>Fecha</th>
        <th>Nombre del paciente</th>
        <th>Total</th>
        <th>Comentarios</th>
        <th>Pagado</th>
        <th>Facturado</th>
        <th>No de Factura</th>
        <th>Banco</th>
        <th>Estatus</th>
    </thead>
    <tbody>

    </tbody>
</table>

</div>
</div>

</div>
<!--/.content-->

<!--/.wrapper--><br />
<script>
    $(document).ready(function() {
        var dataTable = $('#lookup1').DataTable({
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "sSorting": false,

                "bSortable": false,
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },

                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },

            "ordering": false,

            "processing": true,
            "serverSide": true,
            "ajax": {

                url: "./?action=sales/get-all", // json datasource
                type: "post", // method  , by default get
                error: function() { // error handling
                    $(".lookup1-error").html("");
                    //$("#lookup1").append('<tbody class="employee-grid-error"><tr><th colspan="3">No se ha encontrado ningún dato</th></tr></tbody>');
                    $("#lookup_processing").css("display", "none");
                }
            }
        });
    });

    function confirmDelete() {
        var flag = confirm("¿Seguro que deseas eliminar la venta?");
        if (flag == true) {
            return true;
        } else {
            return false;
        }
    }
</script>