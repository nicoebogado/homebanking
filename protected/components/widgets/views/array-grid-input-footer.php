<div class="row ">
    <div class="col-md-12 text-right">

        <?= $extraContent ?>

        <!-- Button trigger modal -->
        <?= XHtml::ajaxButton('Nuevo registro', [
            $url,
            'containerId' => $containerId,
        ], [
            'update'    => '#modal-form-body',
        ],[
            'class' => 'btn btn-success',
            'style' => 'margin-top:10px',
            'data-toggle' => 'modal',
            'data-target' => '#modal-form',
            'return' => true,
        ]) ?>

    </div>
</div>