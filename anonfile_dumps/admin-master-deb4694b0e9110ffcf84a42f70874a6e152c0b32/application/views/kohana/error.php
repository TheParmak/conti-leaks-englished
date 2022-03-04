<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php
    if ( Kohana::DEVELOPMENT !== Kohana::$environment ) :
?>
<link rel="stylesheet" href="/template/css/bootstrap.min.css">
<link rel="stylesheet" href="/template/css/bootstrap-theme.min.css">
<div class="container-fluid" style="margin-top: 20px;">
    <div class="alert alert-danger small">
        <?php if ( 0 != $code ) : ?>
        <?php
            $message = (string) $message;
            if ( '' == $message )
            {
                $messages = [
                    // Client Error 4xx
                    400 => 'Bad Request',
                    401 => 'Unauthorized',
                    402 => 'Payment Required',
                    403 => 'Forbidden',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    406 => 'Not Acceptable',
                    407 => 'Proxy Authentication Required',
                    408 => 'Request Timeout',
                    409 => 'Conflict',
                    410 => 'Gone',
                    411 => 'Length Required',
                    412 => 'Precondition Failed',
                    413 => 'Request Entity Too Large',
                    414 => 'Request-URI Too Long',
                    415 => 'Unsupported Media Type',
                    416 => 'Requested Range Not Satisfiable',
                    417 => 'Expectation Failed',
        
                    // Server Error 5xx
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                    502 => 'Bad Gateway',
                    503 => 'Service Unavailable',
                    504 => 'Gateway Timeout',
                    505 => 'HTTP Version Not Supported',
                    509 => 'Bandwidth Limit Exceeded'
                ];
                $message = isset($messages[$code]) ? $messages[$code] : 'Unknown error';
            }
        ?>
        <?= htmlspecialchars( $message, ENT_QUOTES, Kohana::$charset, TRUE); ?>
        <?php else: ?>
        Internal Server Error
        <?php endif; ?>
    </div>
</div>
<?php
    else: ?><?php
        include(SYSPATH . 'views/kohana/error' . EXT); ?><?php
    endif; ?><?php
?>