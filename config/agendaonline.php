<?php

return [
    // Endereço que recebe as mensagens de contacto e os avisos de novas inscrições na beta.
    'notify_to' => env('AGENDAONLINE_NOTIFY_TO', env('MAIL_FROM_ADDRESS')),
];
