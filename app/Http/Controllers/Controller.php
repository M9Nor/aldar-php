<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use View;

use LaravelLocalization;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $data = [];

    public $actions = [];

    public $currentLocale;
    public $supportedLocales;
    public $currentLang;
    public $langDirection;
    public function __construct()
    {
        $this->currentLocale    = LaravelLocalization::getCurrentLocale();
        $this->supportedLocales = collect(LaravelLocalization::getLocalesOrder());
        $this->currentLang      = LaravelLocalization::getCurrentLocaleName();
        $this->langDirection    = LaravelLocalization::getCurrentLocaleDirection();
        $this->actions = [
            'edit' => [
                'label'  => __('cms::global.actions.edit'),
                'color'  => 'primary',
                'icon'   => 'fa fa-lg fa-fw fa-edit',
                'url'    => '',
                'action' => 'edit',
                'id'     => '',
            ],
            'delete' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.delete'),
                'color'  => 'danger',
                'icon'   => 'fa fa-lg fa-fw fa-trash',
                'url'    => '',
                'action' => 'delete',
                'id'     => '',
            ],
            'restore' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.restore'),
                'color'  => 'warning',
                'icon'   => 'fa fa-lg fa-fw fa-undo',
                'url'    => '',
                'action' => 'restore',
                'id'     => '',
            ],
            'force_delete' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.force_delete'),
                'color'  => 'danger',
                'icon'   => 'fa fa-lg fa-fw fa-trash',
                'url'    => '',
                'action' => 'forceDelete',
                'id'     => '',
            ],
            'disable' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.disable'),
                'color'  => 'danger',
                'icon'   => 'fa fa-lg fa-fw fa-ban',
                'url'    => '',
                'action' => 'disable',
                'id'     => '',
            ],
            'enable' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.enable'),
                'color'  => 'success',
                'icon'   => 'fa fa-lg fa-fw fa-check',
                'url'    => '',
                'action' => 'enable',
                'id'     => '',
            ],
            'special' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.special'),
                'color'  => 'success',
                'icon'   => 'fa fa-lg fa-fw fa-toggle-on',
                'url'    => '',
                'action' => 'special',
                'id'     => '',
            ],
            'not_special' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.not_special'),
                'color'  => 'danger',
                'icon'   => 'fa fa-lg fa-fw fa-toggle-off',
                'url'    => '',
                'action' => 'not_special',
                'id'     => '',
            ],
            'sold' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.sold'),
                'color'  => 'danger',
                'icon'   => 'fa fa-long-arrow-alt-up',
                'url'    => '',
                'action' => 'sold',
                'id'     => '',
            ],
            'not_sold' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.not_sold'),
                'color'  => 'success',
                'icon'   => 'fa fa-long-arrow-alt-down',
                'url'    => '',
                'action' => 'not_sold',
                'id'     => '',
            ],
            'copy' => [
                'type'   => 'form',
                'label'  => __('cms::global.actions.copy'),
                'color'  => 'success',
                'icon'   => 'fa fa-copy',
                'url'    => '',
                'action' => 'copy',
                'id'     => '',
            ],
        ];
    }
}
