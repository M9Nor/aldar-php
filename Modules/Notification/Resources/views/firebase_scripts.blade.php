<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-messaging.js"></script>
<!-- If you enabled Analytics in your project, add the Firebase SDK for Analytics -->
<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-analytics.js"></script>

<!-- Add Firebase products that you want to use -->
{{-- <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-firestore.js"></script> --}}
<script>
    $(document).ready(function(){
        $('body').trigger('click');
    });

    // Initialize Firebase
    var config = {
        apiKey            : "{!! config('notification.firebase.apiKey') !!}",
        authDomain        : "{!! config('notification.firebase.authDomain') !!}",
        projectId         : "{!! config('notification.firebase.projectId') !!}",
        storageBucket     : "{!! config('notification.firebase.storageBucket') !!}",
        messagingSenderId : "{!! config('notification.firebase.messagingSenderId') !!}",
        appId             : "{!! config('notification.firebase.appId') !!}",
        // measurementId     : "{!! config('notification.firebase.measurementId') !!}"
    };
    // Initialize Firebase
    firebase.initializeApp(config);
    // firebase.analytics();
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey("BPmpOIhJVioLUykj3WZOK-IJeuY9RAu3fpxXxG6epXGr6sL-iJ4ABNcZ-s93_viNJNqKm5Tjm2oHep6x4YQTfGA");
    if ('serviceWorker' in navigator) {
        // console.log('registered')
        navigator.serviceWorker
        .register('/service-worker.js')
        .then(function(registration) {
            messaging.useServiceWorker( registration );
        })
        .catch(function(err) {
            // console.log(11);
        });
    }

    var request_notification_permissions = function(){

    }

    if (Notification.permission === 'default') {
        swal.fire({
            title: '{{ __('notification::strings.notification_permissions.ASK.title') }}',
            text: '{{ __('notification::strings.notification_permissions.ASK.description') }}',
            type: 'warning',
            confirmButtonText: "<i class='la la-check'></i> {{ __('notification::strings.close') }}"
        });

        Notification
        .requestPermission(function(permission) {
            if (permission === 'granted') {
                var notify = new Notification('{{ __('notification::strings.notification_permissions.APPROVED.title') }}', {
                    body: '{{ __('notification::strings.notification_permissions.APPROVED.description') }}',
                    icon: '{!! asset('images/icon.png') !!}',
                });
                StartNotificationApp();
            }
            if (permission === 'denied') {
                swal.fire({
                    title: '{{ __('notification::strings.notification_permissions.REJECTED.title') }}',
                    text: '{{ __('notification::strings.notification_permissions.REJECTED.description') }}',
                    type: 'error',
                    confirmButtonText: '{!! __('notification::strings.ok') !!}'
                });
            }
        })
        .then((permission) => {

        });
    }
    else if (Notification.permission === 'granted') {

        StartNotificationApp();
    }

    function StartNotificationApp() {
        messaging.getToken()
        .then((currentToken) => {
            if (currentToken) {
                saveToken(currentToken);
            }
            else {
                // Show permission request.
                // console.log('No Instance ID token available. Request permission to generate one.');
            }
        })
        .catch((err) => {
            // console.log('An error occurred while retrieving token. ', err);
        });
        messaging.onTokenRefresh(() => {
            messaging.getToken()
            .then((refreshedToken) => {
                saveToken(refreshedToken);
            })
            .catch((err) => {
                // console.log('Unable to retrieve refreshed token ', err);
            });
        });
    }

    if( navigator.serviceWorker ) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            console.log('serviceWorker');
            // console.log(event.data['firebaseMessaging'] || event.data);
            var Received = event.data['firebaseMessaging'].payload || event.data;
            $(document).trigger('MESSAGE_RECEIVED', [ Received ]);
            $(document).trigger('MESSAGE_RECEIVED['+ Received.data.system_type +']', [ Received ]);
            var NotificationSound = Received.data['gcm.notification.sound'] || Received.data['sound']
            if( ! NotificationSound ){
                return;
            }
            var audio = new Audio(NotificationSound);
            try {
                audio.play();
            }
            catch (error) {}
        });
    }
    else {
        messaging.onMessage(function(event){
            console.log('onMessage');
            // console.log(event.data['firebaseMessaging'] || event.data);
            var Received = event.data['firebaseMessaging'].payload || event.data;
            $(document).trigger('MESSAGE_RECEIVED', [ Received ]);
            $(document).trigger('MESSAGE_RECEIVED['+ Received.data.system_type +']', [ Received ]);
            var NotificationSound = Received.data['gcm.notification.sound'] || Received.data['sound']
            if( ! NotificationSound ){
                return;
            }
            var audio = new Audio(NotificationSound);
            try {
                audio.play();
            }
            catch (error) {}
        });
    }

    $(document).on('MESSAGE_RECEIVED', function(event, Received){
        console.log(Received)
        var notify = new Notification(Received.notification.title, {
            body: Received.notification.body,
            icon: Received.notification.icon,
            click_action: Received.notification.click_action
        });
    });

    var saveToken = function(_FIREBASE_TOKEN_){
        $.ajax({
            url      : '{!! route('NotificationsController@postWebToken') !!}',
            method   : 'POST',
            dataType : 'json',
            data     : {
                data_token : _FIREBASE_TOKEN_,
                _token     : '{!! csrf_token() !!}',
            },
            statusCode: {
                404: function(xhr) {
                    var data = xhr.responseJSON;
                    // console.log(data);
                },
                403: function(xhr) {
                    var data = xhr.responseJSON;
                    // console.log(data);
                },
                401: function(xhr) {
                    var data = xhr.responseJSON;
                    // console.log(data);
                },
                500: function(xhr) {
                    var data = xhr.responseJSON;
                    // console.log(data);
                },
                200: function(data) {
                    // console.log(data);
                }
            }
        });
    };

    if($('#notifications_application').length > 0)
    {
        const NotificationsApplication = new Vue({
            el: '#notifications_application',
            data: {
                is_loading: false,
                notifications: {
                    data      : [],
                    new_count : 0,
                },
            },
            watch   : {},
            computed: {},
            methods : {
                getNotifications: function(page = 1) {
                    var vm = this;
                    vm.startNotificationLoader();
                    $.ajax({
                        url      : '{!! route('NotificationsController@getList') !!}',
                        method   : 'POST',
                        dataType : 'json',
                        data     : {
                            page   : page,
                            _token : '{!! csrf_token() !!}',
                        },
                        statusCode: {
                            404: function(xhr) {
                                var data = xhr.responseJSON;
                                // console.log(data);
                            },
                            403: function(xhr) {
                                var data = xhr.responseJSON;
                                // console.log(data);
                            },
                            401: function(xhr) {
                                var data = xhr.responseJSON;
                                // console.log(data);
                            },
                            500: function(xhr) {
                                var data = xhr.responseJSON;
                                setTimeout(() => {
                                    vm.getNotifications();
                                }, 2000);
                            },
                            200: function(data) {
                                vm.stopNotificationLoader();
                                vm.$data.notifications.data      = data.notifications || [];
                                vm.$data.notifications.new_count = data.new_count     || 0;
                                vm.refreshScrollbar();
                            }
                        }
                    });
                },
                startNotificationLoader: function(){
                    this.is_loading = true;
                    this.$nextTick(() => {
                        KTApp.block("#notifications_container", {
                            overlayColor:"#000000",
                            type        :"v2",
                            state       :"primary",
                            message     :"{{ __('notification::strings.please_wait') }}"
                        });
                    });
                },
                stopNotificationLoader : function(){
                    this.$nextTick(() => {
                        KTApp.unblock("#notifications_container");
                    });
                    this.is_loading = false;
                },
                refreshScrollbar: function(){
                    this.$nextTick(() => {
                        $('.kt-notification.kt-scroll').css({
                            height: '300px'
                        });
                        const ps = new PerfectScrollbar('.kt-notification.kt-scroll', {
                            wheelSpeed: 2,
                            wheelPropagation: true,
                            minScrollbarLength: 20,
                            maxScrollbarLength: 300
                        });
                        ps.update();
                    });
                }
            },
        });
    }
</script>
