</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<footer>
    <div class="col-md-12 text-center mb-0">Developed by Spectrum Soft Labs</div>
</footer>
</body>

<script>
    // Toggle sidebar visibility on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    });

</script>

</html>

<?php $conn->close(); ?>