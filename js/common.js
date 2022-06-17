var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function ShowProcessing(message, container)
{
    var container = typeof container !== 'undefined' ? container : '';
    var message   = typeof message !== 'undefined' ? message : '';

    if(container != '')
    {
        $(container).html('<div class="text-center font16"><div class="spinner-layer spinner-white-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div><p>' + (message != '' ? message : 'Please wait...') +'</p></div>');
    }
    else
    {
        if(!$('#HolderDiv').length)
        {
            var holderDivHTML = '<DIV class="modal processingModal" id="HolderDiv"><div class="modal-content" style="min-height: 175px;"></div></DIV>';
            $('body').append(holderDivHTML);
        }

    	$('#HolderDiv').find('.modal-content').html('<div class="preloader-wrapper big active"><div class="spinner-layer spinner-white-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div><p>' + (message != '' ? message : 'Please wait...') +'</p>');

        $('#HolderDiv').modal({ dismissible: false, endingTop: '30%', });
        $('#HolderDiv').modal('open');
    }
}

function CloseProcessing()
{
    $('#HolderDiv').modal('close');
    $('#HolderDiv').remove();
}

function ThrowError(obj, formID)
{
    $('.error').remove();
    formID = $('#' + formID);

    $.each(obj, function(index, value)
    {
        var input = $(formID).find(':input[name='+ index +']');
        input.after('<div class="error red-text">'+ value +'</div>');
    });
}

function AjaxFormSubmit(t, formID, jsonData, url)
{
    var formID   = typeof formID !== 'undefined' ? formID : '';
    var url      = typeof url !== 'undefined' ? url : '';
    var jsonData = typeof jsonData !== 'undefined' ? jsonData : '';

    if(typeof $(t).closest('form')[0] !== 'undefined')
    {
        if(formID != '')
        {
            var $form = $('#' + formID)[0];
            var url = $('#' + formID).attr('action');
        }
        else
        {
            var $form = $(t).closest('form')[0];
            var url = $(t).closest('form').attr('action');
        }

        var formData = new FormData($form);
    }
    else
    {
        if(formID != '')
        {
            var formData = new FormData($('#' + formID)[0]);
            var url = $('#' + formID).attr('action');
        }
        else
        {
            var formData = new FormData();
        }
    }

    if(jsonData != '')
    {
        $.each(jsonData, function( index, value )
        {
            formData.append(index, value);
        });
    }

    AjaxResponse = $.ajax({
        url: url,
        type: "post",
        data: formData,
        cache: false,
        dataType: 'json',
        contentType: false,
        processData: false,
    });

    AjaxResponse.fail(function (jqXHR, textStatus, errorThrown)
    {
        alert("ERROR: "+ errorThrown);
    });

    return AjaxResponse;
}

function ShowModal(content)
{
    content = typeof content !== 'undefined' ? content : '';

    if(content == '')
    {
        var modalBox = '<div class="modal show-modal"><div class="modal-content"><div class="center" style="padding: 80px 0"><img src="/images/loader.gif"><br>Loading...</div></div></div>';
    }
    else
    {
        var modalBox = '<div class="modal show-modal"><div class="modal-content">'+content+'</div></div>';
    }

    if($('.show-modal').length == 0)
    {
        $('body').append(modalBox);

        $('.show-modal').modal({
            dismissible: false,
        });
        $('.show-modal').modal('open');
    }
    else
    {
        $('.show-modal .modal-content').html(content);
        $('.show-modal').modal('open');
        setTimeout( function(){ $('.material-select').material_select(); } , 300);
    }
}

function CloseModal()
{
    $('.show-modal').modal('close');
    setTimeout( function(){ $('.show-modal').remove(); } , 300);
}

function StopNonInt(e, AllowNegative, AllowDecimal)
{
	AllowNegative = typeof AllowNegative !== 'undefined' ? AllowNegative : false;
	AllowDecimal = typeof AllowDecimal !== 'undefined' ? AllowDecimal : false;
	var r=e.which?e.which:event.keyCode;
	if(AllowNegative && AllowDecimal)
	{
		return r>31&&(48>r||r>57)&&45!=r&&46!=r?!1:void 0
	}
	else if(AllowNegative)
	{
		return r>31&&(48>r||r>57)&&45!=r?!1:void 0
	}
	else if(AllowDecimal)
	{
		return r>31&&(48>r||r>57)&&46!=r?!1:void 0
	}
	else
	{
		return r>31&&(48>r||r>57)?!1:void 0
	}
}

function AlertBox(message, type, fn)
{
    fn = typeof fn !== 'undefined' ? fn : '';       //  ANY 'ACTION' IF 'CANCEL' IS SELECTED BY USER
    type = typeof type !== 'undefined' ? type : 'error';

    if(!$('#AlertBox').length)
    {
        var alertBoxHTML = '<div class="modal" id="AlertBox"><div class="modal-content" style="text-align: center"><div class="message-wrapper"></div></div></div>';
        $('body').append(alertBoxHTML);
    }

    message = type == 'success' ? '<div class="alertbox-success">'+ message +'</div>' : '<div class="alertbox-error">'+ message +'</div>';

    $('#AlertBox').find('.modal-content .message-wrapper').html(message);
    $('#AlertBox').find('.modal-content').append('<div class="alert-ok"><button class="btn modal-close">OK</button></div>');
    $('.modal').modal({
        dismissible: false,
    });
    $('#AlertBox').modal('open');

    $('#AlertBox').find('.modal-close').on('click', function (e)
    {
        $('#AlertBox').remove();
        if(fn != '')
        {
            fn();
        }
    });
}

function ConfirmBox(message, fn, cancelFn, closeBox)
{
    fn = typeof fn !== 'undefined' ? fn : '';
    cancelFn = typeof cancelFn !== 'undefined' ? cancelFn : '';
    closeBox = typeof closeBox !== 'undefined' ? closeBox : 'y';

    if(!$('#ConfirmDiv').length)
    {
        var confirmBoxHTML = '<div class="modal" id="ConfirmDiv"><div class="modal-content" style="text-align: center"><div class="message-wrapper"></div></div></div>';
        $('body').append(confirmBoxHTML);
    }

    $('#ConfirmDiv').find('.modal-content .message-wrapper').html(message);
    $('#ConfirmDiv').find('.modal-content').append('<div class="confirm-actions-buttons"><button type="button" class="btn blue" id="ConfirmProceed">YES</button>&nbsp;&nbsp;<button type="button" class="btn red" id="ConfirmProceedCancel">NO</button></div>');
    $('.modal').modal({
        dismissible: false,
    });
    $('#ConfirmDiv').modal('open');

    $('#ConfirmProceed').on('click', function(e)
    {
        if(closeBox == 'y')
        {
           CloseConfirmBox()
           setTimeout(function(){ $('#ConfirmDiv').remove(); }, 300);
        }
        if(fn != '')
        {
            fn();
        }
    });

    //  NOW LETS SETUP 'CANCEL' BUTTON
    $('#ConfirmProceedCancel').on('click', function()
    {
        CloseConfirmBox()
        setTimeout(function(){ $('#ConfirmDiv').remove(); }, 300);

        if(cancelFn != '')
        {
            cancelFn();
        }
    });
}

function CloseConfirmBox()
{
    $('#ConfirmDiv').modal('close');
    $('#ConfirmDiv').find('.modal-content').html('');
}

function Toast(message, time, color)
{
    time = typeof time !== 'undefined' ? time : 3000;
    M.Toast.dismissAll();
    
    var color = typeof color !== 'undefined' ? color : 'black';
    M.toast({html: message, classes: color, displayLength: time});
}

function InitializeTooltips()
{
    if($('.tooltipped').length > 0)
    {
        $('.tooltipped').tooltip(
        {
            delay: 50,
            html: true,
        });
    }
}