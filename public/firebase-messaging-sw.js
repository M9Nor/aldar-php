// Web push is no longer used on this site. Browsers that registered an earlier
// worker fetch this version on their next update check; it cancels the push
// subscription and removes itself.
self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        self.registration.pushManager.getSubscription()
            .then(function (subscription) {
                return subscription ? subscription.unsubscribe() : false;
            })
            .catch(function () {
                return false;
            })
            .then(function () {
                return self.registration.unregister();
            })
    );
});
