define([
    'jquery',
    'core/log',
    'core/ajax',
    'core/templates',
    'core/str',
    'core/modal_factory',
    'core/modal_events'
], function($, Log, Ajax, Templates, Str, ModalFactory, ModalEvents) {
    'use strict';

    var form = '#form-',
        entityType = '',
        bodyTr = '.error-summary-tr',
        buttonActionEdit = '.error-summary-table .actions-edit',
        alertForm = '#form-alert',
        success = 'alert-success',
        failed = 'alert-danger';

    /**
     * Initializes the block controls.
     */
    function init(options) {
        entityType = options;
        form = form + entityType;
        addEvents();
    }

    /**
     * Add events
     */
    function addEvents() {
        $(document).on('click', buttonActionEdit, openFormModal);
    }

    /**
     * Get form content
     *
     * @param {jQuery} $trParent
     * @returns {string}
     */
    function getFormContent($trParent) {
        var entityData = getEntityData($trParent),
            formStyles = '<style>' +
                '.form-group { margin-bottom: 15px; }' +
                'label { display: inline-block; margin-bottom: 5px; }' +
                'textarea, input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }' +
                '#form-alert { display: none; }' +
                '.alert { position: relative; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem;' +
                '.alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }' +
                '.alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }' +
                '</style>';

        return formStyles +
            '<div id="form-alert" class="alert" role="alert"></div>' +
            '<form id="form-' + entityType + '">' +
            '   <input type="hidden" id="type" name="type" value="' + entityType + '" readonly>' +
            '<div class="form-group">' +
            '   <label for="id">Id:</label>' +
            '   <input type="text" id="id" name="id" value="' + entityData.id + '" readonly>' +
            '</div>' +
            '<div class="form-group">' +
            '   <label for="json">JSON:</label>' +
            '   <textarea id="json" name="json" rows="40" cols="50">' + formatJson(entityData.json) + '</textarea>' +
            '</div>' +
            '<div class="form-group">' +
            '   <label for="response">Response:</label>' +
            '   <textarea id="response" name="response" rows="4" cols="50" readonly>' + entityData.response + '</textarea>' +
            '</div>' +
            '<div class="form-group">' +
            '   <label for="success">Success:</label>' +
            '   <input type="text" id="success" name="success" value="' + entityData.success + '" readonly>' +
            '</div>' +
            '<div class="form-group">' +
            '   <label for="ds_error">DS Error:</label>' +
            '   <input type="text" id="ds_error" name="ds_error" value="' + entityData.ds_error + '" readonly>' +
            '</div>' +
            '<div class="form-group">' +
            '   <label for="is_successful">Is Successful:</label>' +
            '   <input type="text" id="is_successful" name="is_successful" value="' + entityData.is_successful + '" readonly>' +
            '</div>' +
            '</form>';
    }

    /**
     * Update form
     *
     * @param $trParent
     * @param data
     */
    function updateForm($trParent, data)
    {
        try {
            var $form = $(form),
                dataFormat = JSON.parse(data.data),
                response = JSON.parse(dataFormat.response);
            $trParent.find('.response').text(JSON.stringify(response));
            $trParent.find('.ds_error').text(dataFormat.ds_error);
            $trParent.find('.is_sucessful').text(dataFormat.is_sucessful);
            $form.find('#response').val(formatJson(dataFormat.response)).trigger('change');
            $form.find('#ds_error').val(dataFormat.ds_error).trigger('change');
            $form.find('#is_sucessful').val(dataFormat.is_sucessful).trigger('change');
        } catch (error) {
            return false;
        }

    }

    /**
     * Get data row
     *
     * @param $trParent
     * @returns {{response, success, json, is_sucessful, id, ds_error}}
     */
    function getEntityData($trParent) {
        return {
            id: $trParent.find('.id').text(),
            json: $trParent.find('.json').text(),
            response: $trParent.find('.response').text(),
            success: $trParent.find('.success').text(),
            ds_error: $trParent.find('.ds_error').text(),
            is_successful: $trParent.find('.is_sucessful').text()
        };
    }

    /**
     * Validate form
     *
     * @returns {boolean}
     */
    function validateForm() {
        var $form = $(form),
            json = $form.find('#json').val(),
            id = $form.find('#id').val();
        if (!isValidInteger(id)) {
            showDangerAlert('El campo Id debe ser un número entero.');
            return false;
        }
        if (!isValidJSON(json)) {
            showDangerAlert('El campo JSON debe ser un JSON válido.');
            return false;
        }

        return true;
    }

    /**
     * Validate integer
     *
     * @param value
     * @returns {boolean}
     */
    function isValidInteger(value) {
        return /^\d+$/.test(value);
    }

    /**
     * Validate json format
     *
     * @param value
     * @returns {boolean}
     */
    function isValidJSON(value) {
        try {
            var parsedValue = JSON.parse(value);
            return (typeof parsedValue === 'object' && parsedValue !== null);
        } catch (error) {
            return false;
        }
    }

    /**
     * Format json
     *
     * @param {string} jsonString
     */
    function formatJson(jsonString) {
        try {
            var jsonObject = JSON.parse(jsonString);
            return JSON.stringify(jsonObject, null, 2);
        } catch (error) {
            return jsonString;
        }
    }

    /**
     * Show danger alert
     *
     * @param $message
     */
    function showDangerAlert($message)
    {
        if ($(alertForm).hasClass(success)) {
            $(alertForm).removeClass(success);
        }
        if (!$(alertForm).hasClass(failed)) {
            $(alertForm).addClass(failed);
        }
        $(alertForm).text($message);
        $(alertForm).show();
    }

    /**
     * Show success alert
     *
     *
     * @param $message
     */
    function showSuccessAlert($message)
    {
        if ($(alertForm).hasClass(failed)) {
            $(alertForm).removeClass(failed);
        }
        if (!$(alertForm).hasClass(success)) {
            $(alertForm).addClass(success);
        }
        $(alertForm).text($message);
        $(alertForm).show();
    }

    /**
     * Hide alert
     *
     * @param $time
     */
    function hideAlert($time)
    {
        setTimeout(function () {
            $(alertForm).hide();
        }, $time);
    }

    /**
     * Show Spinner
     */
    function showSpinner() {
        document.getElementById('overlay').style.display = 'flex';
    }

    /**
     * Hide spinner
     */
    function hideSpinner() {
        document.getElementById('overlay').style.display = 'none';
    }

    /**
     * Open modal wth form
     */
    function openFormModal(e) {
        var $trParent = $(e.target).parents(bodyTr),
            entityId = $trParent.find('.id').text(),
            modalOptions = {
            type: ModalFactory.types.SAVE_CANCEL,
            title: 'Edit uPlanner log ' + entityId,
            body: getFormContent($trParent),
            large: true
        };
        ModalFactory.create(modalOptions).then(function (modal) {
            $(modal.footer).on('click', '.btn.btn-primary', function () {
                var formData = $(form).serializeArray();
                showSpinner();
                if (validateForm()) {
                    Ajax.call([{
                        methodname: 'local_uplannerconnect_edit_log',
                        args: {
                            data: JSON.stringify(formData)
                        }
                    }])[0].fail(function(reason) {
                        hideSpinner();
                        showDangerAlert(reason.message);
                        modal.show();
                        //hideAlert(4000);
                    }).done(function(response) {
                        hideSpinner();
                        if (response.done) {
                            showSuccessAlert(response.message);
                            modal.show();
                            setTimeout(function () {
                                modal.destroy();
                                location.reload();
                            }, 4000);
                        } else {
                            updateForm($trParent, response.data);
                            showDangerAlert(response.message);
                            modal.show();
                        }
                    });
                } else {
                    setTimeout(function () {
                        hideSpinner();
                        modal.show();
                    }, 1000);
                }
            });
            modal.show();
        });
    }

    return {
        init: init
    };
});
