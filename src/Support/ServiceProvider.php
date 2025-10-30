<?php

namespace Ludens\Support;

/**
 * Service Provider interface?
 * 
 * @package Ludens\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
interface ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void;
}