<?php
HScript::registerExternal([
    'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/core.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/aes.min.js',
]);
HScript::register([
    'commons/cypher.min',
    'libs/jquery.inputmask/jquery.inputmask.bundle.min',
    'libs/jquery-validation/dist/jquery.validate',
    'libs/jquery.steps/build/jquery.steps.min',
]);
HScript::registerCode('wizard-init', '(function($){
    $(document).ready(function() {
        $(":input").inputmask();
        var form = $("form#' . $form->id . '");
        form.validate({
            errorPlacement: function errorPlacement(error, element) { element.after(error); },
        });
        $("div#wizard-' . $form->id . '", form).steps({
            headerTag: "h4",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex)
            {
                // validación para formulario de transferencia
                var accountName = $("#TransferForm_beneficiaryName", $(this));
                if ( accountName ){
                    var creditAccount = $("#TransferForm_thirdCreditAccount", $(this)).val();
                    var docNumber = $("#TransferForm_thirdDocNumber", $(this)).val();

                    if(
                        accountName.val() == "12 - Cuenta Inexistente!!!" ||
                        !accountName.val() && creditAccount && docNumber
                    ) {
                        return false;
                    }
                }

                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            },
            onFinished: function (event, currentIndex)
            {
                var thirdAccount = $("#TransferForm_thirdCreditAccount").val();
                if ( thirdAccount ) {
                    var creditAccount = CryptoJSAesEncrypt(suid, thirdAccount);
                    $("#TransferForm_thirdCreditAccount").val(creditAccount);

                    var docNumber = CryptoJSAesEncrypt(suid, $("#TransferForm_thirdDocNumber").val());
                    $("#TransferForm_thirdDocNumber").val(docNumber);
                }

                var isSipap = $("#sipap_transfers");
                if ( isSipap.length ) {
                    var creditAccount = CryptoJSAesEncrypt(suid, $("#TransferForm_creditAccount").val());
                    $("#TransferForm_creditAccount").val(creditAccount);

                    var docNumber = CryptoJSAesEncrypt(suid, $("#TransferForm_documentData").val());
                    $("#TransferForm_documentData").val(docNumber);
                }

                $("div.wizard>div.actions li").hide();
                $("div.wizard>div.actions").append("<div>Enviando datos...</div>");
                form.submit();
            },
            labels: {
                cancel: "' . Yii::t('commons', 'Cancelar') . '",
                current: "' . Yii::t('commons', 'Paso') . ':",
                pagination: "' . Yii::t('commons', 'Paginación') . '",
                finish: "' . Yii::t('commons', 'Verificar') . '",
                next: "' . Yii::t('commons', 'Siguiente') . '",
                previous: "' . Yii::t('commons', 'Anterior') . '",
                loading: "' . Yii::t('commons', 'Cargando') . '..."
            },
        });
    });
})(jQuery);');

/*$form->activeForm = array(
    'class' => 'TbActiveForm',
    'type' => 'horizontal',
);*/

$h = '<div class="panel panel-default">';

if ($isSipap) $h .= '<input type="hidden" id="sipap_transfers" value="true" />';

if (!empty($form->title))
    $h .= '<div class="panel-heading>' . $form->title . '</div>';

$h .= '<div class="panel-body row">';
$h .= $form->renderBegin() . '<div id="wizard-' . $form->id . '">';
$aux = 1;
foreach ($wizardOptions as $opt) {
    $h .= '<h4>' . $opt['title'] . '<br/><small>' . $opt['subtitle'] . '</small></h4>';
    $h .= "<fieldset>";
    if (isset($opt['elements'])) {
        foreach ($opt['elements'] as $el) {
            $h .= $form->elements[$el]->render();
        }
    } elseif (isset($opt['view'])) {
        $h .= $this->renderPartial($opt['view'], null, true);
    }
    if ($aux == 2) {
        if (isset($frequentAccountsNormal->listatransferencias->array) && count($frequentAccountsNormal->listatransferencias->array) > 0) {
            $h .= '<div class="frequentAccounts col-md-12 form-group">
                    <label class="control-label">' . Yii::t('sipapTransfer', 'Transferencias Frecuentes') . '</label>
                    <table class="items table table-striped table-condensed table-hover">
                    <thead>
                        <tr>
                            <td>' . Yii::t('sipapTransfer', 'Seleccionar') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Cuenta') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Nombre') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Documento') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Acción') . '</td>
                        </tr>
                    </thead>';
            foreach ($frequentAccountsNormal->listatransferencias->array as $account) {
                $h .= '<tr>
                            <td>
                                <input class="selectAccount" name="selected" type="radio" value="' . $this->encryptAccountDatas($account) . '">
                            </td>
                            <td>' . $account->cuentabeneficiario . '</td>
                            <td>
                                ' . (isset($account->nombrebeneficiario) ? $account->nombrebeneficiario : '') . '
                            </td>
                            <td>' . (isset($account->numerodocbeneficario) ? $account->numerodocbeneficario : '') . '</td>
                            <td>
                                <a href="#" data-url="' . Yii::app()->createUrl('transfer/deleteFrequent') . '" id="' . $account->numerotransferencia . '" class="deleteAccount" style="text-decoration:none;"><em class="icon-trash">&nbsp;</em></a>
                            </td>
                        </tr>';
            }
            $h .= '</table></div>';
        }
    }
    if ($aux == 3) {
        if (isset($frequentAccounts) && count($frequentAccounts) > 0) {
            $h .= '<div class="frequentAccounts col-md-12 form-group">
                    <label class="control-label">
                        ' . Yii::t('sipapTransfer', 'Transferencias Frecuentes') . '
                    </label>
                    <table class="items table table-striped table-condensed table-hover">
                    <thead>
                        <tr>
                            <td>' . Yii::t('sipapTransfer', 'Seleccionar') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Cuenta') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Tipo') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Documento') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Nombre') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Dirección') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Banco') . '</td>
                            <td>' . Yii::t('sipapTransfer', 'Acción') . '</td>
                        </tr>
                    </thead>';
            foreach ($frequentAccounts as $account) {
                $h .= '<tr>
                            <td>
                                <input class="selectAccount" name="selected" type="radio" value="' . $this->encryptAccountDatas($account) . '">
                            </td>
                            <td>' . $account['cuentabeneficiario'] . '</td>
                            <td>' .
                    (isset($account['tipodocbeneficiario']) ?
                        $account['tipodocbeneficiario'] :
                        '')
                    . '</td>
                            <td>' .
                    (isset($account['numerodocbeneficario']) ?
                        $account['numerodocbeneficario'] :
                        '')
                    . '</td>
                            <td>' .
                    (isset($account['nombrebeneficiario']) ?
                        $account['nombrebeneficiario'] :
                        '')
                    . '</td>
                            <td>' .
                    (isset($account['direccionbeneficiario']) ?
                        $account['direccionbeneficiario'] :
                        '')
                    . '</td>
                            <td>' . $account['swiftbeneficiario'] . '-' . getEntityName($entities, $account['swiftbeneficiario']) . '</td>
                            <td>
                                <a href="#" data-url="' . Yii::app()->createUrl('sipapTransfers/deleteFrequent') . '" id="' . $account['numeroplanilla'] . '" class="deleteAccount" style="text-decoration:none;"><em class="icon-trash">&nbsp;</em></a>
                            </td>
                        </tr>';
            }
            $h .= '</table></div>';
        }
    }
    $h .= '</fieldset>';
    $aux++;
}
$h .= '</div>' . $form->renderEnd();
$h .= '</div>';

$h .= '</div>';

echo $h;

function getEntityName($array, $key)
{
    return isset($array[$key]) ? $array[$key] : '';
}
