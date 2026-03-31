@include('errors.partials.page', [
    'statusCode' => '429',
    'pageTitle' => 'Too Many Requests',
    'eyebrow' => 'Rate Limited',
    'heading' => 'Too many requests hit this route.',
    'message' => 'Glacier is slowing things down for a moment to protect the app from repeated requests.',
    'supportingCopy' => 'Pause briefly, then try again. If this keeps happening during normal use, the rate limit may need adjustment.',
])
