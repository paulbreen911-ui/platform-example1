</main>

<footer class="footer">
  <div class="footer-logo">PRODUCTION<span>.</span>CENTRAL</div>
  <div class="footer-links">
    <a class="footer-link" href="/forum/forum.php">Forum</a>
    <a class="footer-link" href="/education/#education">Education</a>
    <a class="footer-link" href="/reference/#reference">Reference</a>
    <a class="footer-link" href="/tools/tools.php">Tools</a>
    <a class="footer-link" href="/life/#life">Life</a>
    <a class="footer-link" href="/legal/#privacy">Privacy</a>
    <a class="footer-link" href="/legal/#terms">Terms</a>
  </div>
  <div class="footer-copy">© 2026 Production Central</div>
</footer>

<!-- Design token live-preview listener (harmless on production, only fires when postMessage received) -->
<script>
(function () {
  var _dtStyle = null;
  window.addEventListener('message', function (e) {
    if (!e.data || e.data.type !== 'DT_TOKENS') return;
    if (e.origin !== window.location.origin) return; // same-origin only
    if (!_dtStyle) {
      _dtStyle = document.createElement('style');
      _dtStyle.id = 'dt-live-tokens';
      document.head.appendChild(_dtStyle);
    }
    _dtStyle.textContent = e.data.css;
  });
})();
</script>

</body>
</html>
