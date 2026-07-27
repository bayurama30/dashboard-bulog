<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Sheets API Configuration
    |--------------------------------------------------------------------------
    |
    | This file centralizes all Google Sheets API settings to avoid
    | hardcoding the spreadsheet ID and sheet names in multiple places.
    |
    */

    'spreadsheet_id' => env('GOOGLE_SPREADSHEET_ID', '16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E'),

    'spreadsheet_url' => 'https://docs.google.com/spreadsheets/d/' . env('GOOGLE_SPREADSHEET_ID', '16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E') . '/edit',

    'sheets' => [
        'gkp'        => 'data dashboard GKP',
        'jagung'     => 'data dashboard Jagung',
        'beras_pso'  => 'data dashboard beras PSO',
        'pengolahan' => 'dashboard pengolahan',
    ],

];
