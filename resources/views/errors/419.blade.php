@include('errors.partials.page', [
    'statusCode' => '419',
    'pageTitle' => 'Page Expired',
    'eyebrow' => 'Session Expired',
    'heading' => 'This page has expired.',
    'message' => 'Your session token is no longer valid, so Glacier could not complete the action safely.',
    'supportingCopy' => 'Refresh the page and submit the form again. If needed, sign in again before retrying.',
])
