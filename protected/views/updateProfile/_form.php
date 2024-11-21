<?php
$form->activeForm = array(
	'class' => 'TbActiveForm',
	'type' => 'horizontal',
);
$htmlCode = '<div class="panel panel-default">';
$htmlCode .= '<div class="panel-body">';
$htmlCode .= '<div class="panel-heading"></div>';

$htmlCode .= $form->renderBegin();
$i=0;
foreach($form->elements as $element) {
	if($i>4){
		break;
	}
	$htmlCode .= $element->render();
	$i++;
}
$htmlCode .= '<div class="panel-heading"></div><div id="address1" style="clear:both;padding-top:40px;">';
$i=0;
foreach($form->elements as $element) {
	if($i<5){
		$i++;
		continue;
	}
	if($i>11){
		break;
	}
	$htmlCode .= $element->render();
	$i++;
}
$htmlCode .= '</div>';
$htmlCode .= '<div class="panel-heading"></div><div id="address2" style="clear:both;">';
$i=0;
foreach($form->elements as $element) {
	if($i<5){
		$i++;
		continue;
	}
	if($i>11){
		break;
	}
	$htmlCode .= $element->render();
	$i++;
}
$htmlCode .= '</div>';
$htmlCode .= '<div class="panel-heading"></div><div id="telephone1" style="clear:both;padding-top:40px;">';
$i=0;
foreach($form->elements as $element) {
	if($i<12){
		$i++;
		continue;
	}
	$htmlCode .= $element->render();
	$i++;
}
$htmlCode .= '</div>';
$htmlCode .= '<div class="panel-heading"></div><div id="telephone2" style="clear:both;">';
$i=0;
foreach($form->elements as $element) {
	if($i<12){
		$i++;
		continue;
	}
	$htmlCode .= $element->render();
	$i++;
}
$htmlCode .= '</div>';

$htmlCode .= '</div>';
$htmlCode .= '</div>';
$htmlCode .= $form->renderButtons();
$htmlCode .= $form->renderEnd();

echo $htmlCode;
