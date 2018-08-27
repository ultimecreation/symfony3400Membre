<?php

namespace AppBundle;

final class EventsConstants
{
    const ON_REGISTRATION_SUCCESS_SEND_CONFIRMATION_TOKEN = 'on_registration_success_send_confirmation_token';
    const ON_EXPIRED_TOKEN_SEND_NEW_CONFIRMATION_TOKEN = 'on_expired_token_send_new_confirmation_token';
    const ON_USER_REQUEST_PASSWORD_RESET = 'on_user_request_password_reset';
}