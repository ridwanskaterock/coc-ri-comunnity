<?= $template['partials']['header']; ?><!-- see view/admin/partial/header_partial.php -->
<div class="wrapper row-offcanvas row-offcanvas-left">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <?= $template['partials']['sidebar']; ?><!-- see view/admin/partial/sidebar_partial.php -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
       <?= $template['body']; ?><!-- dinamic -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->
<?= $template['partials']['footer']; ?><!-- see view/admin/partial/footer_partial.php -->

