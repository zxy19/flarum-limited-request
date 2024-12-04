<?php
namespace Xypp\LimitedRequest\Middleware;

use Flarum\Settings\SettingsRepositoryInterface;

class ApiLimit extends RequestLimitMiddlewareBase
{
    public function __construct(SettingsRepositoryInterface $settings)
    {
        parent::__construct($settings, "api");
    }
}