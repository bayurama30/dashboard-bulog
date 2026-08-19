<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Chat Assistant — Poolside Provider
    |--------------------------------------------------------------------------
    |
    | Konfigurasi AI Assistant (menu "Tanya AI").
    |
    | Provider : Poolside-hosted OpenAI-compatible inference API
    | Base URL : https://inference.poolside.ai/v1
    | Endpoint : POST {api_url}/chat/completions  (di-append " /chat/completions" oleh ChatController)
    | Model    : poolside/laguna-s-2.1
    | Auth     : Authorization: Bearer {POOLSIDE_API_KEY}
    |
    | Dokumentasi: https://docs.poolside.ai/api/overview
    |
    */

    'provider' => env('AI_PROVIDER', 'poolside'),

    'api_key' => env('POOLSIDE_API_KEY'),

    'api_url' => env('POOLSIDE_API_URL', 'https://inference.poolside.ai/v1'),

    'model' => env('POOLSIDE_MODEL', 'poolside/laguna-s-2.1'),

    'max_tokens' => (int) env('AI_MAX_TOKENS', 1536),

    // Cast ke tipe skalar: Laravel env() selalu mengembalikan string,
    // sedangkan API Poolside (OpenAI-compatible) menolak string untuk
    // temperature (harus f32) dan max_tokens (harus integer).
    'temperature' => (float) env('AI_TEMPERATURE', 0.5),

];
