<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ isset($title) && $title ? "{$title} - " : '' }}Invoices Interview Task</title>
        <style>
            * {
                margin: 0;
                padding: 5px;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                line-height: 1.6;
                background-color: #f4f4f4;
                color: #333;
            }

            table {
                border: 1px solid #ccc;
                border-collapse: collapse;
            }

            table td, table th {
                border: 1px solid #ccc;
                padding: 6px;
            }

            .flex  {
                display: flex;
                gap: 10px;
            }

            .flexColumn {
                flex-direction: column;
            }

            .flexRow {
                flex-direction: row;
            }

            a.btn,
            button {
                width: auto;
                display: inline-block;
                margin: 5px;
                padding: 10px 20px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                font-family: inherit;
            }

            a.btn:hover,
            button:hover {
                background-color: #0056b3;
            }

            a.btn:active,
            button:active {
                outline: none;
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.2/dist/cdn.min.js" defer></script>
    </head>
    <body>
        <header>
            <h2>{{ $title ?? '' }}</h2>
        </header>

        <div>
            @if (session()->has('flash_success'))
                <div role="alert" style="color:green;border:1px solid green;padding:5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('flash_success') }}</span>
                </div>
            @endif
            @if (session()->has('flash_error'))
                <div role="alert" style="color:red;border:1px solid red;padding:5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('flash_error') }}</span>
                </div>
            @endif
        </div>
        <div {{ $attributes }} >
            {{ $slot }}
        </div>
    </body>
</html>
