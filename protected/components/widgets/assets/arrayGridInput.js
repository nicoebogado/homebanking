(function($) {

    $(document).ready(function() {
        $('.agi-modal')
        .on('submit', 'form', function(e) {
            var $this = $(this),
                inputCollection = $this.find(':input'),
                modelName = $this.data('agimodel'),
                row = $('<tr>'),
                containerId = $this.data('containerid'),
                modelId = $this.data('modelid'),
                formUrl = $this.data('formurl'),
                input,
                col;

            e.preventDefault();

            var rowIndex = 0;
            // si modelId posee valor es que el formulario es de edición
            if (modelId) {
                rowIndex = modelId;
            } else {
                // recuperar el último modelId para poder calcular
                // los nuevos códigos de filas insertadas
                var lastAddressCode = $('div#'+containerId+'>table>tbody>tr:last-child')
                    .find('input.modelId');
                rowIndex = lastAddressCode ? parseInt(lastAddressCode.val())+1 : 0;
            }

            var firstColumn = true,
                model = {};
            for(var i = 0; i < inputCollection.length; i++) {
                input = $(inputCollection[i]);
                if (input.hasClass('form-control')) {
                    model[input.data('attrname')] = input.val();
                    col = $('<td>');
                    // en la primera columna se debe agregar un input
                    // con class modelId y el valor del índice de la fila
                    if (firstColumn) {
                        col.append(_makeHiddenInput('modelId', rowIndex).addClass('modelId'));
                        firstColumn = false;
                    }
                    col.append(getValueToShow(input)).append(cloneHiddenInput(input, rowIndex));

                    // para ciudad y barrio se deben enviar sus descripciones
                    if (input.is('select')) {
                        var description = input.children('option:selected').text();
                        col.append(_makeHiddenInput(nameForLabel(input, rowIndex), description));
                    }

                    row.append(col);
                }
            }

            // si modelId posee valor es que el formulario es de edición
            if (modelId) {
                var rowToReplace = $('button#btn-editar-'+containerId+'-'+modelId).closest('tr');

                // agregar botón de editar
                var formDatas = {
                        'model': objToBase64(model),
                        'containerId': containerId,
                        'modelId': modelId,
                    },
                    btn = $('<button class="btn btn-primary editar-fila" id="btn-editar-'+
                        containerId+
                        '-'+
                        modelId+
                        '" type="button" data-toggle="modal" data-target="#modal-form" data-formurl="'+
                        formUrl+
                        '" data-formdatas=\''+
                        JSON.stringify(formDatas)+
                        '\'><i class="icon-pencil"></i></button>');

                row.append($('<td>').append(btn));

                rowToReplace.replaceWith(row);
            } else {
                // si es un nuevo registro, se da la posibilidad de eliminarlo
                row.append($('<td>').append(_makeDeleteButton()));
                $('div#'+containerId+'>table>tbody').append(row);
            }

            $this.closest('div.modal').modal('hide');
        })
        .on('hidden.bs.modal', function () {
            $('#modal-form-body').text('Recuperando datos...');
        });

        $('.agi-table')
        .on('click', 'button.editar-fila', function(){
            var $this = $(this),
                formurl = $this.data('formurl'),
                formdatas = $this.data('formdatas');

            $.ajax({
                url: formurl,
                data: formdatas, 
                cache: false,
                success: function(html){
                    $("#modal-form-body").html(html);
                }
            });
            return true;
        })
        .on('click', 'button.eliminar-fila', function(){
            $(this).closest('tr').remove();
        });
    });

    /**
     * Recibe un nombre tipo Modelo[attr]
     * y lo cambia por Model[i][attr]
     */
    function nameForElement(name, i) {
        return name.replace(/(\w*)(\[(\w*)\])/, '$1['+i+']$2');
    }

    /**
     * Recibe un input con data-labelid y name tipo Modelo[attr]
     * y lo cambia por Model[{i}][{data-labelid}]
     */
    function nameForLabel(input, i) {
        if (!input.data('labelid')) return false;

        return input.prop('name').replace(/(\w*)(\[(\w*)\])/, '$1['+i+']['+input.data('labelid')+']');
    }

    function getValueToShow(el) {
        return el.is('select') ? el.children('option:selected').text() : el.val();
    }

    function cloneHiddenInput(input, rowIndex) {
        return _makeHiddenInput(nameForElement(input.prop('name'), rowIndex), input.val());
    }

    function _makeHiddenInput(name, val) {
        return $('<input>')
            .prop('type', 'hidden')
            .prop('name', name)
            .val(val);
    }

    function objToBase64(obj) {
        var objJsonStr = JSON.stringify(obj);

        return window.btoa(objJsonStr);
    }

    function _makeDeleteButton() {
        return $('<button>')
            .addClass('btn btn-danger eliminar-fila')
            .prop('title', 'Eliminar')
            .append($('<i>').addClass('icon-close'));
    }
}) (jQuery);
