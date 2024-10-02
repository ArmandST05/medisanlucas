

        <link rel="stylesheet" href="datatables/dataTables.bootstrap.css"/>
       <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>


                            <h1>Cuentas por cobrar</h1>
                            <div class="clearfix"></div>
                            <hr>
                                    <table id="lookup1" class="table table-bordered table-hover">  
                                       <thead bgcolor="#eeeeee" align="center">
                                       
                                      
                                      <th>Folio</th>
                                      <th>Día</th>
                                      <th>Fecha</th>
                                      <th>Nombre del paciente</th>
                                      <th>Total</th  >
                                      <th>Comentarios</th>
                                      <th>Hospi-Cir</th>
                                      <th>Forma de pago</th>
                                      <th>Facturado</th>
                                      <th>No de Factura</th>
                                      <th>Banco</th>
                                     <th >Estatus</th>
                                      </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            
                                </div>
                            </div>
                            
                        </div>
                        <!--/.content-->
                  
         
        
        <!--/.wrapper--><br />

        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        
        <script src="datatables/jquery.dataTables.js"></script>
        <script src="datatables/dataTables.bootstrap.js"></script>
        <script>
        $(document).ready(function() {
                var dataTable = $('#lookup1').DataTable( {
                    
                 "language":    {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                    "ordering": false,
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                    
                        url :"./?action=getSellC", // json datasource
                        type: "post",  // method  , by default get
                        error: function(){  // error handling
                            $(".lookup-error").html("");
                            $("#lookup1").append('<tbody class="employee-grid-error"><tr><th colspan="3">No se ha encontrado ningún dato</th></tr></tbody>');
                            $("#lookup_processing").css("display","none");
                            
                        }
                    }
                } );
            } );

        </script>
      
  