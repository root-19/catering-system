<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'your_database',
        'user' => getenv('DB_USER') ?: 'your_username',
        'pass' => getenv('DB_PASS') ?: 'your_password',
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: 'a161964d5a466dfb98602525cf37b36d719158f034d52d0cbb2212a91a81f3c9',
        'expiration' => getenv('JWT_EXPIRATION') ?: 3600,
    ],
    'app' => [
        'url' => getenv('APP_URL') ?: 'http://localhost:8000',
        'env' => getenv('APP_ENV') ?: 'development',
    ],
    'deepseek' => [
        'api_key' => getenv('DEEPSEEK_API_KEY') ?: '',
        'api_url' => 'https://api.deepseek.com/v1/chat/completions',
        'model' => 'deepseek-chat',
        // sk-a05081075c064ce8a6f669e09535ca6c
    ]
]; 