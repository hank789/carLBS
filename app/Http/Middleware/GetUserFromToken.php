<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Traits\CreateJsonResponseData;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;

class GetUserFromToken
{

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @param \Illuminate\Contracts\Events\Dispatcher  $events
     * @param \Tymon\JWTAuth\JWTAuth  $auth
     */
    public function __construct(ResponseFactory $response, Dispatcher $events, JWTAuth $auth)
    {
        $this->response = $response;
        $this->events = $events;
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_INVALID,'需登录后才能操作');
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_EXPIRED,'您的登录已过期');
        } catch (JWTException $e) {
             $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_INVALID,'您的登录已过期');
        }

        if (! $user) {
            $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            return CreateJsonResponseData::createJsonData(false,[],ApiException::USER_NOT_FOUND,'用户不存在');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

    /**
     * Fire event and return the response.
     *
     * @param  string   $event
     * @param  string   $error
     * @param  int  $status
     * @param  array    $payload
     * @return mixed
     */
    protected function respond($event, $error, $status, $payload = [])
    {
        $response = $this->events->fire($event, $payload, true);

        return $response ?: $this->response->json(['error' => $error], $status);
    }
}
