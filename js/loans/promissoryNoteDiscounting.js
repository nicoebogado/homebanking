var hiddenDiv;
var clone;
var div;
var button;
var i=0;

(function($){

	jQuery(document).ready(function($) {

		var table='<div class="col-md-12" style="clear:both;margin-bottom:10px;"><table class="items table table-striped table-condensed table-hover" id="detail" >'
		table=table+'<thead><tr><td>Plazo</td><td>Librador</td><td>Importe</td></tr></thead><tbody>'
		table=table+'</tbody></table></div>';

		$('#wizard-PromissoryNoteDiscountingForm-p-1').wrapInner('<div style="clear:both;" class="contform" />');
		$("#wizard-PromissoryNoteDiscountingForm-p-1 .contform :input").each(function(index, el) {
			name=$(this).attr("name");
			matches = name.match(/\[(.*?)\]/);
			if (matches) {
				submatch = matches[1];
				$(this).attr("name",submatch+"[]");
			}
		});
		clone=$("#wizard-PromissoryNoteDiscountingForm-p-1").clone().html();
		div = $('<div class="col-md-12 form-group" style="text-align:right;padding-top:20px;"></div>');
		button = $('<button name="isertButton" type="button" id="isertButton" class="btn btn-primary">+</button>');
		hiddenDiv=$('<div style="display:none;clear:both;"></div>');
		div.append(button);
		$('#wizard-PromissoryNoteDiscountingForm-p-1').append(div);
		$('#wizard-PromissoryNoteDiscountingForm-p-1').append(hiddenDiv);
		$('#wizard-PromissoryNoteDiscountingForm-p-1').append(table);

		$("#isertButton").click(function(event) {
			clonar();
		});

	});

})(jQuery);


function clonar(){

	noteLimit=$(".contform #PromissoryNoteDiscountingForm_noteLimit").val();
	noteAmount=$(".contform #PromissoryNoteDiscountingForm_noteAmount").val();

	if(noteLimit.length==0 && noteAmount.length==0){
		return;
	}

	$('.contform').attr('class', 'contform'+i);
	hiddenDiv.append($('.contform'+i));
	$('#wizard-PromissoryNoteDiscountingForm-p-1').prepend(clone);

	var tr = $('<tr></tr>');
	td=$('<td>'+$('.contform'+i+' :input[name="noteLimit[]"]').val()+'</td>');
	tr.append(td);
	td=$('<td>'+$('.contform'+i+' :input[name="drawerName[]"]').val()+'</td>');
	tr.append(td);
	td=$('<td>'+$('.contform'+i+' :input[name="noteAmount[]"]').val()+'</td>');
	tr.append(td);
	$('#detail tbody').append(tr);
	i=i+1;

	intHeight=parseInt($(".wizard > .content").height())+20;
	$(".wizard > .content").animate({height:intHeight}, 'slow');

	tableCopy=$('#detail').clone();
	$('#wizard-PromissoryNoteDiscountingForm-p-2 #detail').remove();
	$('#wizard-PromissoryNoteDiscountingForm-p-2').append(tableCopy);
}

function updateDetails(){
	$('tr.beneficiaryNameDetailView>td').text($('#PromissoryNoteDiscountingForm_beneficiaryName').val());
	$('tr.beneficiaryDocumentDetailView>td').text($('#PromissoryNoteDiscountingForm_beneficiaryDocument').val());
	$('tr.branchOfficeDetailView>td').text($('#PromissoryNoteDiscountingForm_branchOffice option:selected').text());
	$('tr.timeLimitDetailView>td').text($('#PromissoryNoteDiscountingForm_timeLimit').val());
	$('tr.amountDetailView>td').text($('#PromissoryNoteDiscountingForm_amount').val());
	clonar();
}
