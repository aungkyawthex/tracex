<?php
$flash = getFlash();
if ($flash):
?>
<script>
  Swal.fire({
    toast: true,
    position: "top-end",
    icon: <?= json_encode($flash['type']) ?>,
    title: <?= json_encode($flash['message']) ?>,
    showConfirmButton: false,
    timer: 1500,
    width: "500px",
    timerProgressBar: true
  });
</script>
<?php endif; ?>
