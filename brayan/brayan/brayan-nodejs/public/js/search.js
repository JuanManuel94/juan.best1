const searchComponent = (valueId, renderId, loading = { value: false }) => {
  let id;
  const container = document.getElementById(`container-search-${renderId}`);
  const box = document.getElementById(`box-search-${renderId}`);
  const input = document.getElementById(valueId);
  const value = input.value;

  const renderLoading = () => {
    clearBox();
    const item = document.createElement("li");
    item.textContent = "Buscando...";
    box.appendChild(item);
    container.classList.add("active");
  };

  const renderNotFound = (callback) => {
    clearBox();
    if (typeof callback == "function") callback();
    const item = document.createElement("li");
    item.textContent = `No se encontro "${value}"`;
    box.appendChild(item);
  };

  const renderItem = (text) => {
    const itemEl = document.createElement("li");
    itemEl.innerText = text;
    box.append(itemEl);
    return itemEl;
  };

  const clearBox = () => {
    box.innerText = "";
  };

  const closeContainer = () => {
    container.classList.remove("active");
  };

  const closeEvent = () => {
    loading.value = false;
    if (id) clearTimeout(id);
  };

  return new Promise((resolve) => {
    if (!value) return;
    id = setTimeout(() => {
      if (loading.value) return;
      loading.value = true;
      renderLoading();
      resolve({
        id,
        input,
        value,
        container,
        box,
        renderNotFound,
        renderLoading,
        renderItem,
        clearBox,
        closeContainer,
        closeEvent,
      });
    }, 1000);
  });
};
