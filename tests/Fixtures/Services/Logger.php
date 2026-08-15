<?php

declare(strict_types=1);

namespace Tests\Fixtures\Services;

final class Logger
{
    public function __construct(private Mailer $mailer, private string $filename) {}
}
