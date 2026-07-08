<?php
return [
    'host' => getenv('BOOKSTORE_SMTP_HOST') ?: 'smtp.gmail.com',
    'port' => (int)(getenv('BOOKSTORE_SMTP_PORT') ?: 587),
    'username' => getenv('BOOKSTORE_SMTP_USER') ?: 'tiendabookstore@gmail.com',
    'password' => getenv('BOOKSTORE_SMTP_PASS') ?: 'rtql uhwa klli rltd',
    'from_email' => getenv('BOOKSTORE_SMTP_FROM') ?: 'tiendabookstore@gmail.com',
    'from_name' => getenv('BOOKSTORE_SMTP_FROM_NAME') ?: 'Bookstore',
];