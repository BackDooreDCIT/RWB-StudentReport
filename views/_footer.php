  </div><!-- /#app -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form").forEach(function (form) {
      form.addEventListener("submit", function () {
        form.querySelectorAll("button, input[type=submit]").forEach(function (btn) {
          btn.disabled = true;
        });
      });
    });
  });
  </script>
</body>
</html>
