<h3>
    Confirmación de número de celular
    <small>Para poder utilizar el sistema necesitamos confirmar su número de celular</small>
</h3>

<div class="panel panel-default">
    <div class="panel-heading">Verifique su número de celular para poder recibir códigos de autorización por SMS</div>
    <div class="panel-body">
        <form method="POST">
            <input type="hidden" value="<?= Yii::app()->request->csrfToken ?>" name="YII_CSRF_TOKEN">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-3 control-label required" for="inlineFormInput">
                        Número de celular
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9">
                        <span class="form-text">No puede editar su número desde este formulario. Para actualizar su número debe comunicarse al 021 438 0000.</span>
                        <input class="form-control" id="inlineFormInput" type="text" readonly placeholder="<?= $cellphone ?>">
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Confirmar</button>
                </div>
            </div>
        </form>
    </div>
</div>