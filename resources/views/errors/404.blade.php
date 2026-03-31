@include('errors.partials.page', [
    'statusCode' => '404',
    'pageTitle' => 'Page Not Found',
    'eyebrow' => 'Route Missing',
    'heading' => 'That page drifted out of view.',
    'message' => 'The link or route you opened could not be found in this workspace.',
    'supportingCopy' => 'Check the URL, go back to the previous screen, or jump to the dashboard and keep moving from there.',
])
