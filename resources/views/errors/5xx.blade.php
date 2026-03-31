@php
    $statusCode = (string) ((isset($exception) && method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : 500);
@endphp
@include('errors.partials.page', [
    'statusCode' => $statusCode,
    'pageTitle' => "Server Error {$statusCode}",
    'eyebrow' => 'Server Error',
    'heading' => 'The server could not finish this request.',
    'message' => 'Glacier ran into an internal problem while trying to render the page.',
    'supportingCopy' => 'Refresh after a moment or return to a stable route. If it persists, inspect the application logs for the failing request.',
])
