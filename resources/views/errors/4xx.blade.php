@php
    $statusCode = (string) ((isset($exception) && method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : 400);
    $messages = [
        '400' => [
            'pageTitle' => 'Bad Request',
            'eyebrow' => 'Request Rejected',
            'heading' => 'The request could not be understood.',
            'message' => 'Glacier received this request, but the page could not be served as sent.',
            'supportingCopy' => 'Go back, refresh the page, and try the action again with a clean request.',
        ],
        '401' => [
            'pageTitle' => 'Unauthorized',
            'eyebrow' => 'Authentication Needed',
            'heading' => 'You need to sign in first.',
            'message' => 'This route requires an authenticated session before Glacier can open it.',
            'supportingCopy' => 'Head to the login page, sign in, and then return to the route you were trying to reach.',
        ],
        '405' => [
            'pageTitle' => 'Method Not Allowed',
            'eyebrow' => 'Route Mismatch',
            'heading' => 'That HTTP method is not allowed here.',
            'message' => 'The route exists, but it does not accept the kind of request that was sent.',
            'supportingCopy' => 'Return to the previous page and retry the action through the normal flow.',
        ],
    ];

    $config = $messages[$statusCode] ?? [
        'pageTitle' => "Error {$statusCode}",
        'eyebrow' => 'Client Error',
        'heading' => 'The request could not be completed.',
        'message' => 'Glacier could not serve this page because the request was rejected.',
        'supportingCopy' => 'Try a safer route back into the app and repeat the action from there.',
    ];
@endphp
@include('errors.partials.page', array_merge($config, ['statusCode' => $statusCode]))
