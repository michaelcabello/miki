<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-M9mK0n+W9xzj7X+Zj8Rr83KKnMc6wK2Xt1S5n9mSyCKz3gkA2X2aE3tn9v8qRrxsN5RG5Z5F2E7Yl2HbS8x5lw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />



@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
