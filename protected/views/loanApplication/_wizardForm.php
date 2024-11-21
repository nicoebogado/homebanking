<?php
HScript::register('libs/jquery.inputmask/jquery.inputmask.bundle.min');
HScript::register('libs/jquery-validation/dist/jquery.validate');
HScript::register('libs/jquery.steps/build/jquery.steps.new');
HScript::registerCode('wizard-init', '(function($){
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
						if (typeof updateDetails == "function") {
							updateDetails();
						}
				    form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            form.submit();
        },
				labels: {
		        cancel: "' . Yii::t('commons', 'Cancelar') . '",
		        current: "' . Yii::t('commons', 'Paso') . ':",
		        pagination: "' . Yii::t('commons', 'Paginación') . '",
		        finish: "' . Yii::t('commons', 'Finalizar') . '",
		        next: "' . Yii::t('commons', 'Siguiente') . '",
		        previous: "' . Yii::t('commons', 'Anterior') . '",
		        loading: "' . Yii::t('commons', 'Cargando') . '..."
		    },
	});
})(jQuery);');

/*$form->activeForm = array(
	'class' => 'TbActiveForm',
	'type' => 'horizontal',
);*/

$h = '<div class="panel panel-default">';

if (!empty($form->title))
	$h .= '<div class="panel-heading>' . $form->title . '</div>';

$h .= '<div class="panel-body">';
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
