<?php

return [

    'api_key' => env('OPENROUTER_API_KEY'),

    'api_url' => env('OPENROUTER_API_URL', 'https://opencode.ai/zen/v1'),

    'model' => env('OPENROUTER_MODEL', 'deepseek-v4-flash-free'),

    'max_tokens' => env('AI_MAX_TOKENS', 1536),

    'temperature' => env('AI_TEMPERATURE', 0.5),

];
