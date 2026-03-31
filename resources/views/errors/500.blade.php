@include('errors.partials.page', [
    'statusCode' => '500',
    'pageTitle' => 'Server Error',
    'eyebrow' => 'System Fault',
    'heading' => 'The server hit an unexpected issue.',
    'message' => 'Something inside Glacier failed while trying to render this page.',
    'supportingCopy' => 'Try again in a moment. If the same page keeps failing, the server logs will be the next place to inspect.',
])
