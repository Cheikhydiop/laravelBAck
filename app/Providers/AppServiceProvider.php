<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\ClientRepository;
use App\Services\ClientServiceInterface;
use App\Services\ClientService;
use App\Repositories\ArticleRepository;
use App\Services\UploadService;



class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the ClientRepositoryInterface to the ClientRepository implementation
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);

        // Bind the ClientServiceInterface to the ClientService implementation
        $this->app->bind(ClientServiceInterface::class, ClientService::class);

        $this->app->singleton('articleRepository', function ($app) {
            return new ArticleRepository();
        });
        
        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });

        $this->app->singleton(QrCodeService::class, function ($app) {
            return new QrCodeService();
        });
        
    }

    public function boot()
    {
        //
    }
}

