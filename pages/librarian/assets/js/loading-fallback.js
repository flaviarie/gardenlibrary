// Fallback: Force-hide loading overlay after 2 seconds
setTimeout(function() {
  var loading = document.getElementById('page-loading');
  if (loading) loading.style.display = 'none';
}, 2000);
