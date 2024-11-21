<?php
$this->pageTitle = Yii::app()->name . ' - Gráfico de Ahorro Programado';
?>

<?php 
        if (isset($_GET['data'])) {
            $encryptedData = $_GET['data'];
            $decodedData = json_decode(base64_decode($encryptedData), true);

            $monto = $decodedData['monto'];
            $mtoInteres = $decodedData['mtoInteres'];
            $plazo = $decodedData['plazo'];
            $codMoneda = $decodedData['codMoneda'];
            $tipoAhorro = $decodedData['tipoAhorro'];
            $formData = $decodedData['formData'];
        }
?>


<?php Yii::log('monto: ' . $monto, 'info', 'application.controllers.ScheduledSavingsController'); ?>
<?php Yii::log('mtoInteres: ' . $mtoInteres, 'info', 'application.controllers.ScheduledSavingsController'); ?>
<?php Yii::log('plazo: ' . $plazo, 'info', 'application.controllers.ScheduledSavingsController'); ?>
<?php Yii::log('codMoneda: ' . $codMoneda, 'info', 'application.controllers.ScheduledSavingsController'); ?>
<?php Yii::log('tipoAhorro: ' . $tipoAhorro, 'info', 'application.controllers.ScheduledSavingsController'); ?>
<?php Yii::log('formData: ' . json_encode($formData), 'info', 'application.controllers.ScheduledSavingsController'); ?>

<h3>Gráfico de Ahorro Programado</h3>

<ul class="breadcrumb">
    <li><a class="breadcrumb-np" href="<?php echo Yii::app()->createUrl('/'); ?>">Inicio</a></li>
    <li><a class="breadcrumb-np" href="<?php echo Yii::app()->createUrl('scheduledSavings/create'); ?>">Crear Ahorro </a></li>
    <li class="breadcrumb-p">Visualizar Ahorro</li>
</ul>


<p>Este gráfico muestra la progresión de tus ahorros durante <?php echo $plazo; ?> meses.</p>

<!-- Agrega un canvas donde se renderizará el gráfico -->
 <div class="row">
    <div class="col-md-7 col-sm-12">
        <div class="card">
            <div class="card-body">
                <canvas id="ahorroChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Botón para generar ahorro programado -->
<button type="button" class="btn btn-primary mt-3" id="generateSavingsBtn">
    Generar Ahorro Programado
</button>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Obtener los datos pasados desde el controlador (monto inicial y mtoInteres)
    var montoForm = <?php echo $monto; ?>;
    var codMoneda = '<?php echo $codMoneda; ?>';
    var mtoInteres = <?php echo str_replace(',', '.', $mtoInteres);?>;
    var mesesCalc = <?php echo $plazo; ?>;
    var tipoAhorro = '<?php echo $tipoAhorro; ?>';

    console.log(montoForm);
    // Crear un array para almacenar los montos acumulados de cada mes
    var montos = [];
    console.log(tipoAhorro);
    if (tipoAhorro === 'M') {
        for (var i = 0; i < (mesesCalc-1); i++) {
            montos.push(montoForm * (i + 1));
        }
        montos.push(mtoInteres);
    } else {
        //para el monto total vamos a dividir montoForm / mesesCalc
        var montoTotal = (montoForm / mesesCalc)
        //hacer ceil de montoTotal
        //si monedA = GS math.ceil si no toFixed(2)
        if (codMoneda === 'GS') {
            montoTotal = Math.ceil(montoTotal);
        } else {
            montoTotal = montoTotal.toFixed(2);
        }
        console.log(montoTotal);
        for (var i = 0; i <  (mesesCalc-1); i++) {
            //hacer el roof y push
            montos.push(montoTotal * (i + 1));
        }
        montos.push(mtoInteres);
    }
        

    // Lista de todos los meses

    let baseLabels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Generar etiquetas de acuerdo al número de meses, comenzando desde el mes actual
    let labels = [];
    let currentMonth = new Date().getMonth(); // Obtener el mes actual (0-11)
    for (let i = 0; i < mesesCalc; i++) {
        labels.push(baseLabels[(currentMonth + i) % 12]); // Usa el módulo (%) para repetir los meses cuando sea necesario
    }

    var ctx = document.getElementById('ahorroChart').getContext('2d');
    var ahorroChart = new Chart(ctx, {
        type: 'bar',  // Tipo de gráfico (barras)
        data: {
            labels: labels.slice(0, mesesCalc),  // Los nombres de los meses
            datasets: [{
                label: 'Ahorro Acumulado',
                data: montos,  // Los valores acumulados de cada mes
                backgroundColor: '#c70c0c80',  // Color con opacidad
                borderColor: '#c70c0c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Meses'
                    },
                    ticks: {
                        font: {
                            size: 10  // Cambiar el tamaño de la fuente en el eje x
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: `Monto en ${codMoneda}`
                    },
                    ticks: {
                        callback: function (value, index, values) {
                            return codMoneda + ' ' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return codMoneda + ' ' + tooltipItem.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        }
    });
 


    // Manejar el clic en el botón "Generar Ahorro Programado"
    document.getElementById('generateSavingsBtn').addEventListener('click', function () {

        Swal.fire({
            title: 'Confirmación',
            text: "¿Estás seguro de que quieres generar el ahorro programado?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar',
            customClass: {
                    confirmButton: 'swal-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar el POST request
                $.ajax({
                    url: 'https://secure.hml.fic.com.py/api/public/ahProgramado/inserta',
                    type: 'POST',
                    data: <?php echo json_encode($formData); ?>,
                    //ayuda para tirar un sweetalert si response.codresultado !== 000, si es ==000 entonces hacer el success sino mostrar sweetalert y con el mensaje de error que seria response.desResultado
                    success: function (response) {
                        if ('000' === JSON.parse(response).codResultado) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ahorro Programado Generado',
                                text: 'El ahorro programado ha sido generado exitosamente.',
                                customClass: {
                                        confirmButton: 'swal-btn'
                                }
                            }).then(() => {
                                window.location.href = '<?php echo Yii::app()->createUrl('/site/index'); ?>';
                                <?php Yii::app()->user->accounts->refresh(); ?>;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al generar el ahorro programado',
                                text: JSON.parse(response).desResultado,
                                customClass: {
                                        confirmButton: 'swal-btn'
                                }
                            });
                        }
                    },
                });
            }
        });
    });
</script>

<style>
    .breadcrumb-np {
        color: #333;
        text-decoration: none;
    }

    .breadcrumb-np:hover {
        text-decoration: underline;
    }

    .breadcrumb-p {
        color: #c70c0c;
    }

    .swal-btn {
    background-color: #c70c0c;
    border-color: #c70c0c;
    transition: 0.3s;
    }

    .swal-btn:hover {
        background-color: #c70c0c;
        border-color: #c70c0c;
        scale: 1.1;
    }

</style>