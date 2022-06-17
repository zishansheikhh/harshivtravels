<?php

    $errors = array();

	$response['status'] = 'error';

    if($_POST['option'] == 'contact_us')
    {
        if($_POST['FullName'] == '')
        {
            $errors['FullName'] = 'Enter name!';  
        }

        if($_POST['Email'] == '')
        {
            $errors['Email'] = 'Enter email!';  
        }
        elseif(!preg_match( "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $_POST['Email']))
        {
            $errors['Email'] = 'Enter a valid email!';
        }
        if($_POST['Mobile'] == '')
        {
            $errors['Mobile'] = 'Enter mobile number!';  
        }

        if(count($errors))
        {
            $response['status'] = 'validation';
            $response['errors'] = $errors;
        }
        else
        {
            //  Let's send an email to the admin
            $MailSubject = 'Contact enquiry from '.stripslashes($_POST['FullName']);
            $MailBody = '<TR>
                            <TD>
                                <table cellpadding="5" cellspacing="1" style="width:100%;background:#dddddd;">
                                    <tr>
                                        <td style="background:#ffffff;">Name</td>
                                        <td style="background:#ffffff;">'.stripslashes($_POST['FullName']).'</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#ffffff;">Email</td>
                                        <td style="background:#ffffff;">'.stripslashes($_POST['Email']).'</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#ffffff;">Mobile</td>
                                        <td style="background:#ffffff;">'.stripslashes($_POST['Mobile']).'</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#ffffff;">Message</td>
                                        <td style="background:#ffffff;">'.stripslashes($_POST['Message']).'</td>
                                    </tr>
                                </table>
                            </TD>
                        </TR>';

            $MailBody = FormatEmail($MailBody);

            if(SendMailHTML('inquiry.harshivtravels@gmail.com', $MailSubject, $MailBody))
            {
                $response['status'] = 'success';
                $response['message'] = 'Thank you!<br>You can expect a call from us within next 48 hours.';
            }
            else
            {
                $response['message'] = 'An error ocurred while sending mail';
                LogError(1, 'Contact admin email failed', 66, json_encode($SiteSettings));
            }
        }   
    }

    echo json_encode($response);
?>