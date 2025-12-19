const API_URL = 'api/index.php';
const CSRF_TOKEN = document.querySelector('meta[name="csrf"]').content;

async function api(action, payload = {}) {
  const form = new FormData();
  form.append('action', action);

  Object.keys(payload).forEach(key => {
    form.append(key, payload[key]);
  });

  const res = await fetch(API_URL, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    },
    body: form
  });

  return res.json();
}




