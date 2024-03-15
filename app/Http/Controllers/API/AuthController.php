<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    #[OA\Post(
        path: '/api/auth/login',
        description: 'Авторизация пользователя',
        tags: ['auth'],
        parameters: [
            new OA\Parameter(name: 'email', in: 'query', required: true),
            new OA\Parameter(name: 'password', in: 'query', required: true),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'access_token', type: 'string'),
                    new OA\Property(property: 'token_type', type: 'string', default: 'bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer')
                ])

            ),
            new OA\Response(
                response: 401,
                description: 'Не авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string',default: 'Unauthenticated.')
                ])
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'errors', type: 'object'),
                ])
            ),
        ]
    )]
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validated();

        if (!$token = auth()->attempt($credentials)) {
            throw new AuthenticationException();
        }

        return $this->respondWithToken($token);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        description: 'Выход',
        security: [['bearerAuth' => []]],
        tags: ['auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный выход',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string',default: 'Logged out success'),
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Не авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string',default: 'Unauthenticated.')
                ])
            ),
        ]
    )]
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Logged out success']);
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        description: 'Обновить токен пользователя',
        security: [['bearerAuth' => []]],
        tags: ['auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Токен обновлен',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'access_token', type: 'string'),
                    new OA\Property(property: 'token_type', type: 'string', default: 'bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer')
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Не авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string',default: 'Unauthenticated.')
                ])

            ),
        ]
    )]
    public function refresh(): \Illuminate\Http\JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    #[OA\Post(
        path: '/api/auth/getMe',
        description: 'Получить данные об авторизованном пользователе',
        security: [['bearerAuth' => []]],
        tags: ['auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные о пользователе успешно получены',
                content: [
                    new OA\Property(property: 'id', type: 'int'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                ],
            ),
            new OA\Response(
                response: 401,
                description: 'Не авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string',default: 'Unauthenticated.')
                ])

            ),
        ]
    )]
    public function getMe(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

}
