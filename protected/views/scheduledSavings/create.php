<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('scheduledSavings', 'Crear Plan de Ahorro Programado');
?>

<h3><?php echo Yii::t('scheduledSavings', 'Crear Plan de Ahorro Programado'); ?></h3>

<ul class="breadcrumb">
    <li><a class="breadcrumb-np" href="<?php echo Yii::app()->createUrl('/'); ?>">Inicio</a></li>
    <li><a class="breadcrumb-p" href="<?php echo Yii::app()->createUrl('scheduledSavings/create'); ?>">Crear Ahorro</a></li>
</ul>


<form class="col-8 mx-3" id="generateForm"  method="post">
<?php echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken); ?>

    <div class="form-group d-flex align-items-center">
        <label for="renewalSwitch" class="me-2">Renovación automática:</label>
        <label class="switch">
            <input type="checkbox" id="renewalSwitch" name="renewal" value="1" checked>
            <span class="slider round"></span>
        </label>
        <span class="ms-2" id="renewalLabel">Sí</span>
    </div>

    <div class="form-group d-flex">
        <label for="mode">Modo de Ahorro:</label>
        <div class="radio">
            <label>
                <input type="radio" name="mode" value="T" required> Ahorro total
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="mode" value="M" required> Ahorro mensual
            </label>
        </div>
    </div>

    <div class="form-group d-flex">
        <label for="currency">Moneda:</label>
        <div class="radio">
            <label>
                <input type="radio" name="currency" value="USD" required disabled> Dólares (USD)
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="currency" value="GS" required> Guaraníes (GS)
            </label>
        </div>
    </div>

    <div class="form-group">
    <label for="amount">Monto:</label>
        <div class="input-group" style="display:flex!important;">
            <div class="input-group-append d-flex">
                <span class="input-group-text box-currency" id='currencyBox'>-</span>
            </div>
            <input type="text" name="amount" id="amount" class="form-control" required placeholder="Ingrese el monto sin comas ni puntos">
        </div>
    </div>

    <div class="form-group">
        <label for="duration">Plazo (en meses) :</label>
        <input type="number" name="duration" id="duration" class="form-control" required placeholder="Ingrese la duración en meses">
    </div>

    <?php
    // Obtener periodos desde la API
    $periodos = file_get_contents('http://10.90.20.197/api/public/ahProgramado/periodos');
    $data = json_decode($periodos, true);

    $listaPeriodos = [];
    if (isset($data['listaPeriodos'])) {
        foreach ($data['listaPeriodos'] as $item) {
            $listaPeriodos[] = $item['periodos'];
        }
    }

    $listaFechas = [];
    if (isset($data['listaPeriodos'])) {
        foreach ($data['listaPeriodos'] as $item) {
            $listaFechas[] = $item['fecha'];
        }
    }
    ?>

<!--colocar en opciones el PEriodo y en value=fecha correspondiente-->
    <div class="form-group">
        <label for="periodoDebito">Periodo de débito:</label>
        <select name="periodoDebito" id="periodoDebito" class="form-control" required>
            <option value="" disabled selected>Seleccione el periodo de débito</option>
            <?php foreach ($listaPeriodos as $key => $periodo): ?>
                <option value="<?php echo htmlspecialchars($listaFechas[$key]); ?>">
                    <?php echo htmlspecialchars($periodo); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php
    // Obtener cuentas desde la API
    $accountOptions = Yii::app()->user->accounts->getGridArray();

    // Filtrar cuentas que tengan 'accountTypeDesc' como 'Ahorros a la Vista'
    $accountOptions = array_filter($accountOptions['datas'], function ($account) {
        // Verifica si 'accountTypeDesc' existe y es igual a 'Ahorros a la Vista' y si 'currency' es distinto de 'USD'
        return isset($account['accountTypeDesc']) && $account['accountTypeDesc'] === 'Ahorros a la Vista' && $account['currency'] !== 'USD';
    });
    ?>
    <div class="form-group">
        <label for="account">Cuenta débito:</label>
        <select name="account" id="account" class="form-control" required>
            <option value="" disabled selected>Seleccione la cuenta débito</option>
            <?php foreach ($accountOptions as $account): ?>
                <option value="<?php echo htmlspecialchars($account['accountNumber']); ?>">
                    <?php echo htmlspecialchars($account['accountNumber'] . ' - ' . $account['currency'] . ' ' . number_format($account['credit'], 0, ',', '.')); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>


    <button type="button" onclick="actionGenerate()" class="btn d-flex gap-4 btn-primary">
        <span id="btn-calcular" >Calcular</span>
        <svg width="20" height="20" id="icon-calc" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 15H22C22.2652 15 22.5196 14.8946 22.7071 14.7071C22.8946 14.5196 23 14.2652 23 14V8C23 7.73478 22.8946 7.48043 22.7071 7.29289C22.5196 7.10536 22.2652 7 22 7H10C9.73478 7 9.48043 7.10536 9.29289 7.29289C9.10536 7.48043 9 7.73478 9 8V14C9 14.2652 9.10536 14.5196 9.29289 14.7071C9.48043 14.8946 9.73478 15 10 15ZM11 9H21V13H11V9ZM25 3H7C6.46957 3 5.96086 3.21071 5.58579 3.58579C5.21071 3.96086 5 4.46957 5 5V27C5 27.5304 5.21071 28.0391 5.58579 28.4142C5.96086 28.7893 6.46957 29 7 29H25C25.5304 29 26.0391 28.7893 26.4142 28.4142C26.7893 28.0391 27 27.5304 27 27V5C27 4.46957 26.7893 3.96086 26.4142 3.58579C26.0391 3.21071 25.5304 3 25 3ZM25 27H7V5H25V27ZM12.5 18.5C12.5 18.7967 12.412 19.0867 12.2472 19.3334C12.0824 19.58 11.8481 19.7723 11.574 19.8858C11.2999 19.9993 10.9983 20.0291 10.7074 19.9712C10.4164 19.9133 10.1491 19.7704 9.93934 19.5607C9.72956 19.3509 9.5867 19.0836 9.52882 18.7926C9.47094 18.5017 9.50065 18.2001 9.61418 17.926C9.72771 17.6519 9.91997 17.4176 10.1666 17.2528C10.4133 17.088 10.7033 17 11 17C11.3978 17 11.7794 17.158 12.0607 17.4393C12.342 17.7206 12.5 18.1022 12.5 18.5ZM17.5 18.5C17.5 18.7967 17.412 19.0867 17.2472 19.3334C17.0824 19.58 16.8481 19.7723 16.574 19.8858C16.2999 19.9993 15.9983 20.0291 15.7074 19.9712C15.4164 19.9133 15.1491 19.7704 14.9393 19.5607C14.7296 19.3509 14.5867 19.0836 14.5288 18.7926C14.4709 18.5017 14.5006 18.2001 14.6142 17.926C14.7277 17.6519 14.92 17.4176 15.1666 17.2528C15.4133 17.088 15.7033 17 16 17C16.3978 17 16.7794 17.158 17.0607 17.4393C17.342 17.7206 17.5 18.1022 17.5 18.5ZM22.5 18.5C22.5 18.7967 22.412 19.0867 22.2472 19.3334C22.0824 19.58 21.8481 19.7723 21.574 19.8858C21.2999 19.9993 20.9983 20.0291 20.7074 19.9712C20.4164 19.9133 20.1491 19.7704 19.9393 19.5607C19.7296 19.3509 19.5867 19.0836 19.5288 18.7926C19.4709 18.5017 19.5007 18.2001 19.6142 17.926C19.7277 17.6519 19.92 17.4176 20.1666 17.2528C20.4133 17.088 20.7033 17 21 17C21.3978 17 21.7794 17.158 22.0607 17.4393C22.342 17.7206 22.5 18.1022 22.5 18.5ZM12.5 23.5C12.5 23.7967 12.412 24.0867 12.2472 24.3334C12.0824 24.58 11.8481 24.7723 11.574 24.8858C11.2999 24.9993 10.9983 25.0291 10.7074 24.9712C10.4164 24.9133 10.1491 24.7704 9.93934 24.5607C9.72956 24.3509 9.5867 24.0836 9.52882 23.7926C9.47094 23.5017 9.50065 23.2001 9.61418 22.926C9.72771 22.6519 9.91997 22.4176 10.1666 22.2528C10.4133 22.088 10.7033 22 11 22C11.3978 22 11.7794 22.158 12.0607 22.4393C12.342 22.7206 12.5 23.1022 12.5 23.5ZM17.5 23.5C17.5 23.7967 17.412 24.0867 17.2472 24.3334C17.0824 24.58 16.8481 24.7723 16.574 24.8858C16.2999 24.9993 15.9983 25.0291 15.7074 24.9712C15.4164 24.9133 15.1491 24.7704 14.9393 24.5607C14.7296 24.3509 14.5867 24.0836 14.5288 23.7926C14.4709 23.5017 14.5006 23.2001 14.6142 22.926C14.7277 22.6519 14.92 22.4176 15.1666 22.2528C15.4133 22.088 15.7033 22 16 22C16.3978 22 16.7794 22.158 17.0607 22.4393C17.342 22.7206 17.5 23.1022 17.5 23.5ZM22.5 23.5C22.5 23.7967 22.412 24.0867 22.2472 24.3334C22.0824 24.58 21.8481 24.7723 21.574 24.8858C21.2999 24.9993 20.9983 25.0291 20.7074 24.9712C20.4164 24.9133 20.1491 24.7704 19.9393 24.5607C19.7296 24.3509 19.5867 24.0836 19.5288 23.7926C19.4709 23.5017 19.5007 23.2001 19.6142 22.926C19.7277 22.6519 19.92 22.4176 20.1666 22.2528C20.4133 22.088 20.7033 22 21 22C21.3978 22 21.7794 22.158 22.0607 22.4393C22.342 22.7206 22.5 23.1022 22.5 23.5Z" fill="white"/>
        </svg>
        <span id="load-i" class="loader d-none"></span>
    </button>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Escuchar el evento de submit del formulario
    document.querySelector('form').addEventListener('submit', function(event) {
        // Obtener el valor del campo 'amount'
        var amount = document.getElementById('amount').value.replace(/\./g, ''); // Eliminar los puntos
        amount = parseInt(amount); // Convertir a entero

        var plazo = document.getElementById('duration').value;
        
        var mode = document.querySelector('input[name="mode"]:checked').value;

        // Verificar si la moneda seleccionada es Guaraníes y el monto es menor a 50.000
        var currency = document.querySelector('input[name="currency"]:checked').value;



        // Obtener la cuenta seleccionada
        var accountSelect = document.getElementById('account');
        var selectedAccount = accountSelect.options[accountSelect.selectedIndex].text;
        var accountCurrency = selectedAccount.split(' - ')[2]; // Extraer la moneda de la opción
        accountCurrency = accountCurrency.split(' ')[0]; // Extraer solo las siglas de la moneda

        // Verificar si la moneda de la cuenta y la moneda seleccionada coinciden
        if ((accountCurrency === 'GS' && currency === 'USD') || (accountCurrency === 'USD' && currency === 'GS')) {
            event.preventDefault(); // Prevenir el envío del formulario
            Swal.fire({
                icon: 'error',
                title: 'Moneda incompatible',
                text: 'La moneda seleccionada no coincide con la moneda de la cuenta.',
                customClass: {
                    confirmButton: 'swal-btn'
                }
            });
            return;
        }

    });

    document.getElementById('renewalSwitch').addEventListener('change', function() {
        var renewalLabel = document.getElementById('renewalLabel');
        renewalLabel.textContent = this.checked ? 'Si' : 'No';
    });

    //seleccionar los dos radios de currency

    var currencyRadios = document.querySelectorAll('input[name="currency"]');
    var currencyBox = document.getElementById('currencyBox');

    currencyRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            currencyBox.textContent = this.value === 'USD' ? 'USD' : 'GS';
        });
    });

    async function actionGenerate() {
    // Obtener referencia al botón y al loader
    const button = document.querySelector('button');
    const loader = document.getElementById('load-i');
    const calcIcon = document.getElementById('icon-calc');
    
    // Mostrar el loader y deshabilitar el botón
    loader.classList.remove('d-none');
    calcIcon.classList.add('d-none');
    button.disabled = true;

    try {
        // Obtener los valores del formulario
        const plazo = document.querySelector('#duration').value;
        const fecInicio = new Date().toLocaleDateString('es-ES'); // Fecha actual en formato día/mes/año
        const tipoAhorro = document.querySelector('input[name="mode"]:checked').value;
        const nroCtaDebito = document.querySelector('#account').value;
        const fecPrimVenc = document.querySelector('#periodoDebito').value;
        const codMoneda = document.querySelector('input[name="currency"]:checked').value;
        let monto = document.querySelector('#amount').value;
        monto = monto.replace(/\./g, ''); // Eliminar puntos de miles
        const renovacion = document.querySelector('#renewalSwitch').checked ? 'S' : 'N';

        // Crear el objeto con los datos para enviar a la API
        const postData = {
            "monto": monto,
            "nro_cta_debito": nroCtaDebito,
            "estado": "P",
            "cod_moneda": codMoneda,
            "fec_inicio": "",
            "fec_pri_vencimiento": fecPrimVenc,
            "plazo": plazo,
            "tipo_ahorro": tipoAhorro,
            "ind_renovacion": renovacion,
            "tip_capitalizacion": 3,
            "origen": "WEB"
        };

        // Enviar los datos a la API usando fetch (formato application/x-www-form-urlencoded)
        const response = await sendDataToApiFormEncoded(postData);

        console.log('Respuesta de la API:', response);

        // Procesar la respuesta
        const responseData = await response.json();
        let mtoInteres = responseData.mtoInteres || 0;

        console.log('Respuesta de la API:', responseData.codResultado);

        if (responseData.codResultado === '000') {
            //mandar response al controlador
            // Encriptar los datos antes de redirigir
            document.getElementById('btn-calcular').innerHTML = 'Generando Ahorro Programado';
            const encryptedData = btoa(JSON.stringify({
                monto: postData.monto,
                mtoInteres: responseData.mtoInteres,
                plazo: postData.plazo,
                codMoneda: postData.cod_moneda,
                tipoAhorro: postData.tipo_ahorro,
                formData: postData
            }));

            window.location.href = `success?data=${encryptedData}`;

        } else {
            loader.classList.add('d-none');
            calcIcon.classList.remove('d-none');
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Error al calcular',
                text: responseData.desResultado,
                customClass: {
                    confirmButton: 'swal-btn'
                }
            });
        }
        
    } finally {
        // Ocultar el loader y habilitar el botón
        
    }
}

// Función para enviar los datos a la API
async function sendDataToApiFormEncoded(data) {
    const url = 'https://secure.hml.fic.com.py/api/public/ahProgramado/genera';
    
    // Convertir datos a formato form-urlencoded
    const formData = new URLSearchParams();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    // Hacer la solicitud POST con fetch
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    });

    return response;
}



</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');

        amountInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Elimina caracteres no numéricos

            // Formatear el valor con puntos como separadores de miles
            if (value) {
                this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            } else {
                this.value = '';
            }
        });
    });

    //evitar que en plazo se ingresen letras
    document.getElementById('duration').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>




<style>
/* Estilo base del radio */
input[type="radio"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 15px;
    height: 15px;
    border: 2px solid #c70c0c;
    border-radius: 50%;
    outline: none;
    cursor: pointer;
    position: relative;
    transition: background-color 0.2s, border-color 0.2s;
}

/* Estilo cuando está seleccionado */
input[type="radio"]:checked {
    border: 5px solid rgba(199, 12, 12, 0.3); /* Borde cuando está seleccionado, más grueso y con transparencia */
    background-color: rgba(199, 12, 12, 0.5); /* Fondo con más transparencia */
}

/* Círculo dentro cuando está seleccionado */
input[type="radio"]:checked::after {
    content: '';
    width: 6px;
    height: 6px;
    background-color: #c70c0c; /* Círculo interno sólido */
    display: block;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 50%;
}

.gap-4 {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Ajuste de espaciado entre los radios y el texto */
.radio label {
    margin-left: 10px;
    vertical-align: middle;
    font-size: 16px;
    color: #333;
}

.d-none {
    display: none !important;
}

/* Para alinear los radios */
.radio {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

/* breadcrumb-p y -np */

.breadcrumb-p {
    color: #c70c0c;
}

.breadcrumb-np {
    color: #333;
}

.switch input:checked + span {
    background-color: #c70c0c;
    border-color: #c70c0c;

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

.box-currency {
    display: flex;
    align-items: center;
    padding: 5.5px 10px;
    background: white;
    border: 1px solid #fff;
    box-shadow: 0 0 0 #000 !important;
    border-right-width: 0px;
    border-radius: 4px 0px 0px 4px;
}

.loader {
    width: 15px;
    height: 15px;
    border: 3px solid #FFF;
    border-bottom-color: #FF3D00;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
    }

    @keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    } 



</style>