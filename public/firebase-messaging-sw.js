importScripts('https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.19.1/firebase-messaging.js');

var config = {
    apiKey: "AIzaSyCDQVr9_vMIGo2Cw5RFrtz8k_KuyXK35Bc",
    authDomain: "binaa-prod.firebaseapp.com",
    projectId: "binaa-prod",
    storageBucket: "binaa-prod.appspot.com",
    messagingSenderId: "455249672224",
    appId: "1:455249672224:web:ec1b5960506f8d779da445"
};
firebase.initializeApp(config);

var messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
  // console.log('bbbbbb');
  console.log('[firebase-messaging-sw.js] Received background message: ', payload);
  // Customize notification here
  const notificationTitle   = payload.data.title;
  const notificationOptions = {
    body: payload.data.body,
    icon: payload.data.icon +'?v=1',
    image: payload.data.image,
    data: payload.data.link,
  };
  const promiseChain = clients.matchAll({
    type: 'window',
    includeUncontrolled: true
  })
  .then(function (windowClients) {
    for (let i = 0; i < windowClients.length; i++) {
      windowClients[i].postMessage(payload.data);
    }
    return self.registration.showNotification(notificationTitle, notificationOptions);
  });
  return promiseChain;
});


self.addEventListener("notificationclick", function (event) {
  console.log(event);
  var urlToRedirect = event.notification.data;
  event.notification.close();
  event.waitUntil(self.clients.openWindow(urlToRedirect));
});
