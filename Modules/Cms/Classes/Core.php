<?php namespace Modules\Cms\Classes;
use Closure;
use Modules\Notification\Entities\Config;
use Modules\Notification\Entities\FirebaseNotificationReceiver;
class Core
{
    public $headerMenu;
    public $asideMenu;
    protected $quickMenu;


    protected $notificationsLinks;

    public function __construct()
    {
        $this->asideMenu = collect([]);

        $this->notificationsLinks = [];
        $this->headerMenu = collect();

    }

    public function startSendingNotifications()
    {
        return FirebaseNotificationReceiver::startSending();
    }
    public function headerMenu($menu = [])
    {
        $default = [
            'header'      => null,
            'label'       => 'No Name Item',
            'link'        => 'javascript:;',
            'icon_class'  => null,
            'items'       => null,
            'is_active'   => false,
            'ordering'    => 0
        ];

        $menu = array_merge($default, $menu);

        if(request()->fullUrl() ==  $menu['link'])
        {
            $menu['is_active'] = true;
        }

        if(is_array($menu['items']))
        {
            foreach ($menu['items'] as $key => $item)
            {
                $menu['items'][ $key ] = array_merge($default, $item);
                if(request()->fullUrl() ==  $menu['items'][ $key ]['link'])
                {
                    $menu['items'][ $key ]['is_active'] = true;
                    $menu['is_active'] = true;
                }
            }
        }

        $this->headerMenu->push($menu);
    }

    public function asideMenu($menu = [])
    {
        $default = [
            'header'      => null,
            'label'       => 'No Name Item',
            'link'        => 'javascript:;',
            'icon_class'  => null,
            'badge_count' => 0,
            'badge_type'  => 'danger',
            'items'       => null,
            'is_active'   => false,
            'ordering'    => 0
        ];

        $menu = array_merge($default, $menu);

        if(request()->fullUrl() ==  $menu['link'])
        {
            $menu['is_active'] = true;
        }

        if(is_array($menu['items']))
        {
            foreach ($menu['items'] as $key => $item)
            {
                $menu['items'][ $key ] = array_merge($default, $item);
                if(request()->fullUrl() ==  $menu['items'][ $key ]['link'])
                {
                    $menu['items'][ $key ]['is_active'] = true;
                    $menu['is_active'] = true;
                }
            }
        }

        $this->asideMenu->push($menu);
    }




    public function registerNotificationLink(string $Type, Closure $closure)
    {
        $this->notificationsLinks[ $Type ] = $closure;
    }

    public function getNotificationLink(string $Type, $params)
    {
        if (!isset($this->notificationsLinks[ $Type ])) {
            return null;
        }
        try {
            $Result = call_user_func_array($this->notificationsLinks[ $Type ], [
                $params
            ]);
            return $Result;
        }
        catch (\Exception $th) {
            return null;
        }
        return null;
    }



    // public function asideMenu($Options)
    // {
    //     $I = $this->asideMenu->count() + 1;
    //     $O = array_merge([
    //         'id'        => 'MENU_' . $I,
    //         'parent_id' => null,
    //         'type'      => 'ITEM', //ITEM | HEADER
    //         'link'      => 'javascript:;',
    //         'icon_type' => null, // null: i | svg
    //         'icon'      => null,
    //         'ordering'  => $I,
    //         'title'     => null,
    //     ], $Options);

    //     $O['is_active'] = request()->url() == $O['link'] ? true : false;
    //     $O['sub_items'] = [];
    //     $this->asideMenu->push( $O );
    // }

    public function asideMenuGet()
    {
        $RES = [];
        foreach ($this->asideMenu->where('parent_id', null)->sortBy('ordering') as $I => $item) {
            $_SUBS_    = $this->asideMenu->where('parent_id', $item['id']);
            $RES[ $I ] = $item;
            $RES[ $I ]['sub_items'] = $_SUBS_->toArray();
            $RES[ $I ]['is_active'] = (request()->url() == $item['link'] ? true : false) || ! is_null( $_SUBS_->where('is_active', true)->first() );
            foreach ($_SUBS_->sortBy('ordering') as $II => $sub_item) {
                $_SUBS_2    = $this->asideMenu->where('parent_id', $sub_item['id']);
                $RES[ $I ]['sub_items'][ $II ] = $sub_item;
                $RES[ $I ]['sub_items'][ $II ]['sub_items'] = $_SUBS_2->toArray();
                $RES[ $I ]['sub_items'][ $II ]['is_active'] = (request()->url() == $sub_item['link'] ? true : false) || ! is_null( $_SUBS_2->where('is_active', true)->first() );
            }
        }
        return $RES;
    }
}
