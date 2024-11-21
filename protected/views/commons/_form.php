<?php
$withPanel = !isset($withoutPanel);
$config = [
	'class' => 'TbActiveForm',
	'type' => 'horizontal',
];
if (isset($id)) $config['id'] = $id;

$form->activeForm = $config;

if (isset($action)) {
    $form->action = $action;
}

if ($withPanel) {
	$htmlCode = '<div class="panel panel-default">';
	if($form->title!==null)
		$htmlCode .= '<div class="panel-heading">'.$form->title."</div>\n";
	$htmlCode .= '<div class="panel-body">';
} else {
	$htmlCode = '';
}

$htmlCode .= $form->renderBegin();

foreach($form->elements as $element) {

    if ( $element->getVisible() )
	   $htmlCode .= $element->render();

}

$htmlCode .= $form->renderButtons();
$htmlCode .= $form->renderEnd();

if ($withPanel) {
	$htmlCode .= '</div></div>';
}

echo $htmlCode;
