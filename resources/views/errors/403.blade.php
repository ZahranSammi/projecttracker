@include('errors.partials.page', [
    'statusCode' => '403',
    'pageTitle' => 'Forbidden',
    'eyebrow' => 'Permission Blocked',
    'heading' => 'You do not have access to this page.',
    'message' => 'Glacier recognized the route, but your account is not allowed to open this area.',
    'supportingCopy' => 'Return to a page you can access, or sign in with an account that belongs to the right workspace or role.',
])
