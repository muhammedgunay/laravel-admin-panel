<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Department;
use App\Models\Setting;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);

        // Sistemin çekirdek rollerinin (1 ve 2) hiçbir şekilde (kodla bile) silinmesini engelliyoruz
        \Spatie\Permission\Models\Role::deleting(function ($role) {
            if (in_array($role->id, [1, 2])) {
                throw new \Exception("Bu sistem rolü silinemez!");
            }
        });
    }
}
