<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Modules\Invoices\Domain\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;
use Modules\Invoices\Domain\Factories\InvoiceProductLineFactory;
use Modules\Invoices\Infrastructure\Persistence\Facades\InvoiceFacade;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InvoiceFacadeInterface::class, InvoiceFacade::class);
        $this->registerFactories();
        $this->registerViews();
    }

    protected function registerViews(): void
    {
        $moduleViewPaths = [
            modules_path('Invoices/Presentation/Resources/Views')
        ];

        foreach ($moduleViewPaths as $path) {
            View::addLocation($path);
        }
    }

    protected function registerFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return match ($modelName) {
                Invoice::class => InvoiceFactory::class,
                InvoiceProductLine::class => InvoiceProductLineFactory::class,
                default => null,
            };
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(modules_path('Invoices/Infrastructure/Persistence/Migrations'));
    }
}
