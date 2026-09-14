<?php

return [
    'title' => 'المستخدمون',
    'create_new_user' => 'إضافة مستخدم جديد',
    'update_user' => 'تعديل مستخدم ',
    'manage_password' => 'إدارة كلمة المرور',

    'statuses' => [
        'PENDING'   => [
            'label' => 'ينتظر التفعيل',
            'color' => 'warning'
        ],
        'SUSPENDED' => [
            'label' => 'مفصول',
            'color' => 'danger'
        ],
        'ACTIVE'    => [
            'label' => 'فعال',
            'color' => 'success'
        ],
    ],

    'sexOptions' => [
        'UNSPECIFIED'   => [
            'label' => 'لم يتم التحديد',
        ],
        'MALE' => [
            'label' => 'ذكر',
        ],
        'FEMALE'    => [
            'label' => 'أنثى',
        ],
    ],

    'datatable' => [
        'id' => '#',
        'user' => 'المستخدم',
        'email' => 'البريد الإلكتروني',
        'status' => 'الحالة',
        'role' => 'الدور',
        'actions' => 'الأدوات',
    ],
'home'=>'الرئيسية',
'myprofile'=>'حسابي',
    'fields' => [
        'image' => [
            'label'         => 'الصورة الشخصية',
            'help'          => 'يفضّل أن تكون أبعاد الصورة: :prefered_dimensions، ونوع الصورة: :mimes.',
            'add_text'      => 'أضف صورة',
            'remove_text'   => 'إلغاء الصورة',
        ],
        'username' => [
            'label'         => 'اسم المستخدم',
            'placeholder'   => 'ادخل اسم المستخدم',
            'help'          => 'يجب إدخال حروف لاتينية فقط، يجب ألا يحتوي الاسم على فواصل وألا يكون موجوداً من قبل.',
        ],
        'first_name' => [
            'label'         => 'الاسم',
            'placeholder'   => 'ادخل الاسم',
            'help'          => '',
        ],
        'statuses' => [
            'label'         => 'الحالة',
            'placeholder'   => 'اختر الحالة',
            'help'          => '',
        ],
        'last_name' => [
            'label'         => 'الكنية',
            'placeholder'   => 'ادخل الكنية',
            'help'          => '',
        ],
        'name' => [
            'label'         => 'الاسم',
            'placeholder'   => 'ادخل الاسم',
            'help'          => '',
        ],
        'sex' => [
            'label'         => 'الجنس',
            'placeholder'   => 'اختر الجنس',
            'help'          => '',
        ],
        'address' => [
            'label'         => 'العنوان',
            'placeholder'   => 'اختر العنوان',
            'help'          => '',
        ],
        'email' => [
            'label'         => 'البريد الإلكتروني',
            'placeholder'   => 'ادخل البريد الإلكتروني',
            'help'          => '',
        ],
        'phone' => [
            'label'         => 'رقم الجوال',
            'placeholder'   => 'أدخل رقم الجوال',
            'help'          => '',
        ],
        'password' => [
            'label'         => 'كلمة المرور',
            'placeholder'   => 'ادخل كلمة المرور',
            'help'          => 'اترك الحقل فارغاً للحفاظ على كلمة المرور الحالية.',
        ],
        'password_confirmation' => [
            'label'         => 'تأكيد كلمة المرور',
            'placeholder'   => 'ادخل كلمة المرور مجدداً',
            'help'          => 'للتأكد من أنك قد قمتَ بإدخال كلمة المرور بشكل صحيح.',
        ],
        'status' => [
            'label'         => 'الحالة',
            'placeholder'   => '',
            'help'          => '',
        ],
        'roles' => [
            'label'         => 'الأدوار',
            'placeholder'   => '',
            'help'          => '',
        ],
        'role' => [
            'label'         => 'الدور',
            'placeholder'   => '',
            'help'          => '',
        ],
        'attachments' => [
            'label'         => 'المرفقات',
            'placeholder'   => 'قم بسحب المرفقات وإفلاتها هنا',
            'help'          => '',
        ],
    ],

    'sections' => [
        'previous' => 'السابق',
        'submit' => 'حفظ',
        'next_step' => 'التالي',
        'profile' => [
            'title'         => 'الملف الشخصي',
            'description'   => 'معلومات المستخدم الشخصية',
        ],
        'account' => [
            'title'         => 'الحساب',
            'description'   => 'معلومات حساب المستخدم',
        ],
        'address' => [
            'title'         => 'العنوان',
            'description'   => 'تفاصيل عنوان المستخدم',
        ],
        'review' => [
            'title'         => 'الحفظ',
            'description'   => 'استعراض المعلومات وحفظها',
        ],
    ],

    'filter' => [
        'title' => 'الفلترة',
        'description' => 'قم بفترة المستخدمين عن طريق ملء الحقول التالية:',
    ],

    'modal' => [
        'title' => 'تفاصيل المستخدم',
        'description' => 'قم بفترة المستخدمين عن طريق ملء الحقول التالية:',
    ],
];
