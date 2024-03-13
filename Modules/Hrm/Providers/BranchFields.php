<?php

namespace Modules\hrm\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Hrm\Entities\Branch;
use Modules\Hrm\Entities\ExperienceCertificate;
use Modules\Hrm\Entities\IpRestrict;
use Modules\Hrm\Entities\JoiningLetter;
use Modules\Hrm\Entities\NOC;


class BranchFields extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */


    public function boot()
    {
        view()->composer(['assets::create','assets::edit','assets::defective.create','assets::distributions.create'], function ($view)
        {
            if(\Auth::check())
            {
                $branches = Branch::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name','id');
                if(!empty($branches))
                {
                    $view->getFactory()->startPush('add_branch_in_asset_create', view('assets::branch',compact('branches')));
                }
            }
        });
    }
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
