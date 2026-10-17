
const CACHE_NAME = 'c20-turnos-v2';

const APP_SHELL = [
  '/turnos/vistas/auth/login.php',
  '/turnos/manifest.json',
  '/turnos/icons/icon-192.png',
  '/turnos/icons/icon-512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      for (const url of APP_SHELL) {
        try {
          await cache.add(url);
        } catch (error) {
          console.warn('No se pudo guardar en caché:', url, error);
        }
      }
    })
  );

  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    )
  );

  self.clients.claim();
});
