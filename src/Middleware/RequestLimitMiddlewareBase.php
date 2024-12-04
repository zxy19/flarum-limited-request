<?php

namespace Xypp\LimitedRequest\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestLimitMiddlewareBase implements MiddlewareInterface
{
    protected SettingsRepositoryInterface $settings;
    protected string $name;
    public function __construct(SettingsRepositoryInterface $settings, string $name)
    {
        $this->settings = $settings;
        $this->name = $name;
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $limitList = $this->settings->get('xypp.request_limit') ?? "[]";
        $limitList = json_decode($limitList, true);
        $path = $request->getUri()->getPath();
        $params = $request->getQueryParams();
        $method = strtoupper($request->getMethod());
        $actor = RequestUtil::getActor($request);


        if (is_array($limitList) && count($limitList) > 0) {
            try {
                $this->shouldLimit($actor, $limitList, $method, $path, $params);
            } catch (PermissionDeniedException $e) {
                if ($name === "api") {
                    throw $e;
                } else {
                    return new RedirectResponse("/");
                }
            }
        }

        $response = $handler->handle($request);

        return $response;
    }

    protected function shouldLimit(User $actor, array $limitList, string $method, string $path, array $params)
    {
        foreach ($limitList as $limit) {
            $match = false;

            if ($limit['method'] != $method) {
                continue;
            }
            if ($limit['group'] !== $this->name) {
                continue;
            }

            // check path
            if ($limit['mode'] === "regex") {
                $match = preg_match($limit['path'], $path);
            } else if ($limit['mode'] === "prefix") {
                $match = str_starts_with($path, $limit['path']);
            } else {
                $match = $path === $limit['path'];
            }
            if (!$match) {
                continue;
            }

            // check param
            if (isset($limit['params'])) {
                foreach ($limit['params'] as $key) {
                    if (!Arr::has($params, $key)) {
                        $match = false;
                    }
                }
            }
            if (!$match) {
                continue;
            }

            $actor->assertRegistered();
            if (isset($limit['group_id']) && is_numeric($limit['group_id']) && ((int) ($limit['group_id']) !== 0)) {
                if (!$actor->groups()->whereIn('id', $limit['group_id'])->exists()) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }
}