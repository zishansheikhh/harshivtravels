function SendNewsletterSubscription(t)
{
    $(t).addClass('spinner');
    $(t).closest('form').find('button').prop('disabled', true);
    AjaxResponse = AjaxFormSubmit(t);
    $.when(AjaxResponse).done(function(response)
    {
    	$(t).removeClass('spinner');
        $(t).closest('form').find('button').prop('disabled', false);
        if(response.status == 'success')
        {
            $(t).closest('form')[0].reset();
            $('#sendEnquiryModal').modal('close');
            AlertBox(response.message, 'success');
        }
        else if(response.status == 'validation')
        {
            ThrowError(response.errors, 'sendNewsletterSubscription');
        }
        else
        {
            Toast(response.message, 5000, 'red');
        }
    });

    return false;
}
