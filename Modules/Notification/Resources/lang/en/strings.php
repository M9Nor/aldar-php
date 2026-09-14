<?php

return [

    'create_new'   => 'إضافة جديد',
    'send_notification'   => 'إرسال إشعار',
    'notifications'   => 'الإشعارات',
    'agreed'   => 'أوافق',
    'declined' => 'أرفض',
    'ok' => 'Yes',
    'cancel' => 'No',
    'close' => 'Close',
    'notifications' => 'الإشعارات',
    'new_notifications' => 'جديدة',
    'alerts' => 'التنبيهات',
    'events' => 'الاحداث',
    'logs' => 'السجلات',
    'all_caught_up' => 'تم بنجاح',
    'no_new_notifications' => 'لا يوجد إشعارات جديدة',
    'please_wait' => 'يرجى الانتظار',
    'no_new_notifications' => 'لا يوجد إشعارات جديدة',
    'notification_permissions' => [
        'APPROVED' => [
            'title'       => 'Notifications can be received now!',
            'description' => 'You have successfully enabled receiving notifications',
        ],
        'REJECTED' => [
            'title'       => 'Notificaitons will not be received.',
            'description' => 'You have disabled notifications. You can no longer receive notifications.',
        ],
        'ASK' => [
            'title'       => 'Receiving notifications needs your permission.',
            'description' => 'To receive the best offers about Turkey Real Estate you need to allow notifications.',
        ],
    ],
    'fields' => [
        'group' => [
            'label'         => 'المجموعة',
            'placeholder'   => 'حدد المجموعة المخصوصة بالإشعار',
            'help'          => 'سيصل الإشعار إلى كافة المستخدمين ضمن المجموعة المحددة (عدم تحديد أي مجموعة سيؤدي إلى إرسال الإشعار لكافة المستخدمين)',
        ],
        'notification_link' => [
            'label'         => 'الرابط',
            'placeholder'   => 'ادخل الرابط',
            'help'          => 'الرابط إلى الوجهة المقصودة عند الضغط على الإشعار',
        ],
        'notification_title' => [
            'label'         => 'العنوان',
            'placeholder'   => 'ادخل العنوان',
            'help'          => 'عنوان الإشعار',
        ],
        'notification_body' => [
            'label'         => 'المحتوى',
            'placeholder'   => 'ادخل المحتوى',
            'help'          => 'محتوى الإشعار',
        ],
    ],

];
