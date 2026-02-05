const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');

searchBtn.addEventListener('click', () => {
  const term = searchInput.value.trim();
  if(term) loadProducts(null, term);
});
