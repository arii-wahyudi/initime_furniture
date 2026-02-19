(function(){
  function sendEvent(event, productId, categoryId){
    try{
      var fd = new FormData();
      if (productId) fd.append('product_id', productId);
      if (categoryId) fd.append('category_id', categoryId);
      fd.append('event', event);
      // Prefer sendBeacon for navigation-safe delivery
      if (navigator.sendBeacon){
        // sendBeacon requires Blob or URLSearchParams; convert FormData to URLSearchParams
        var params = new URLSearchParams();
        fd.forEach(function(v,k){ params.append(k,v); });
        navigator.sendBeacon('admin/track_event.php', params);
        return;
      }
      fetch('admin/track_event.php', { method: 'POST', body: fd }).catch(function(){});
    }catch(e){}
  }

  // delegate clicks
  document.addEventListener('click', function(e){
    var el = e.target;
    // find anchor with category-link
    var a = el.closest && el.closest('.category-link');
    if (a){
      var cat = a.getAttribute('data-cat-id');
      if (cat){ sendEvent('category', 0, parseInt(cat,10)); }
      return; // allow navigation
    }

    // product detail click
    var pd = el.closest && el.closest('.btn-view-detail');
    if (pd){
      e.preventDefault();
      var pid = pd.getAttribute('data-product-id');
      var href = pd.getAttribute('href');
      sendEvent('click', pid, 0);
      // navigate after small delay to allow beacon
      setTimeout(function(){ window.location = href; }, 120);
      return;
    }

    // whatsapp link
    var wa = el.closest && el.closest('.wa-link');
    if (wa){
      var pid = wa.getAttribute('data-product-id');
      sendEvent('wa_click', pid, 0);
      return; // allow navigation
    }
  }, false);

  // also expose helper for manual events
  window._trackEvent = sendEvent;
})();
