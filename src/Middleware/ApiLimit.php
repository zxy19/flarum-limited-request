<?php
namespace Xypp\LimitedRequest\Middleware;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;

class ApiLimit extends RequestLimitMiddlewareBase
{
    public function __construct(SettingsRepositoryInterface $settings, Translator $translator)
    {
        parent::__construct($settings, $translator, "api");
    }
}