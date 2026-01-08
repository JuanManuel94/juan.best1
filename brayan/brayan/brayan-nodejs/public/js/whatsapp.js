function getWhatsappApi() {
  return $("#whatsapp_api_value").val();
}

function getWhatsappKey() {
  return $("#whatsapp_key_value").val();
}

function sendMessageApiWhatsapp({ phone, message, file }) {
  const apiUrl = getWhatsappApi();
  const token = getWhatsappKey();
  let payload = { number: phone, body: message };
  if (file) {
    payload = new FormData();
    payload.set("number", phone);
    payload.set("medias", file);
  }
  return new Promise((resolve, reject) => {
    axios
      .post(`${apiUrl}/api/messages/send`, payload, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((data) => resolve(data))
      .catch((err) => reject(err));
  });
}

function sendMessageWhatsapp({ phone, message, file }) {
  if (!file) return sendMessageApiWhatsapp({ phone, message });
  const request = [sendMessageApiWhatsapp({ phone, message, file })];
  if (message) request.push(sendMessageApiWhatsapp({ phone, message }));
  return Promise.all(request);
}
