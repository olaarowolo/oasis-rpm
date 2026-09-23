<script>
    // Logout function
    function logout() {
      if (confirm('Are you sure you want to log out?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(url('/logout')); ?>';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
      }
    }
  </script><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/supervisor-script.blade.php ENDPATH**/ ?>