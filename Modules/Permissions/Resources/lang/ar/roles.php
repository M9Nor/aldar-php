<?php

return [
    'title' => 'الأدوار',
    'create_new' => 'إضافة دور',
    'edit' => 'تعديل الدور',

    'sections' => [
        'main' => 'المعلومات الرئيسية',
        'programmatic' => 'المعلومات البرمجية',
        'management' => 'المعلومات الإدارية',
    ],

    'datatable' => [
        'id' => '#',
        'name' => 'الاسم',
        'title' => 'العنوان',
        'description' => 'الشرح',
        'abilities_count' => 'الصلاحيات',
        'actions' => 'الأدوات',
    ],

    'dual_listbox' => [
        'available_items' => 'الصلاحيات المتاحة',
        'selected_items' => 'الصلاحيات المحددة',
    ],

    'fields' => [
        'name' => [
            'label'         => 'الاسم',
            'placeholder'   => 'ادخل الاسم',
            'help'          => 'الرمز البرمجي المعبر عن الدور، مثال: SUPERADMIN.',
        ],
        'title' => [
            'label'         => 'العنوان',
            'placeholder'   => 'ادخل العنوان',
            'help'          => '',
        ],
        'description' => [
            'label'         => 'الشرح',
            'placeholder'   => 'ادخل الشرح',
            'help'          => '',
        ],
        'icon' => [
            'label'         => 'الأيقونة',
            'placeholder'   => 'ادخل الأيقونة',
            'help'          => 'مثال: fa fa-users.',
        ],
        'color' => [
            'label'         => 'اللون',
            'placeholder'   => 'اختر اللون',
            'help'          => 'انتقِ لوناً.',
        ],
        'permissions' => [
            'label'         => 'الصلاحيات',
            'placeholder'   => '',
            'help'          => 'اختر الصلاحيات المناسبة للدور.',
        ],
        'manageable_roles' => [
            'label'         => 'الأدوار المشرف عليها',
            'placeholder'   => '',
            'help'          => 'اختر الأدوار التي يمكن لهذا الدور إدارتها.',
        ],
    ],

    'filter' => [
        'title' => 'الفلترة',
        'description' => 'قم بفترة الأدوار عن طريق ملء الحقول التالية:',
    ],

    'modal' => [
        'title' => 'تفاصيل المستخدم',
        'description' => 'قم بفترة الأدوار عن طريق ملء الحقول التالية:',
    ],
];
