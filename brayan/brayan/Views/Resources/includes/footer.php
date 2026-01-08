</div>

<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i
    class="fa fa-angle-up"></i></a>
</div>
<!-- ================== FIN FOOTER ================== -->
<!-- ================== INICIO RUTA  ============== -->
<script>
  const base_url = "<?= base_url(); ?>";
  const permission_create = "<?= empty($_SESSION['permits_module']['r']) ? 0 : $_SESSION['permits_module']['r'] ?>";
  const permission_edit = "<?= empty($_SESSION['permits_module']['a']) ? 0 : $_SESSION['permits_module']['a'] ?>";
  const permission_remove = "<?= empty($_SESSION['permits_module']['e']) ? 0 : $_SESSION['permits_module']['e'] ?>";
  const permission_view = "<?= empty($_SESSION['permits_module']['v']) ? 0 : $_SESSION['permits_module']['v'] ?>";
  const currency_symbol = "<?= empty($_SESSION['businessData']['symbol']) ? "" : $_SESSION['businessData']['symbol'] ?>";
  const user_profile = "<?= empty($_SESSION['userData']['profileid']) ? 0 : $_SESSION['userData']['profileid'] ?>";
  const key_google = "<?= empty($_SESSION['businessData']['google_apikey']) ? "" : $_SESSION['businessData']['google_apikey'] ?>";
</script>
<!-- ================== INICIO RUTA  ============== -->
<!-- ================== INICIO ARCHIVOS JS ======== -->
<script src="<?= base_style() ?>/js/app.min.js"></script>
<script src="<?= base_style() ?>/js/moment.min.js"></script>
<script src="<?= base_style() ?>/js/functions.js?v=<?= time(); ?>"></script>
<script src="<?= base_style() ?>/js/utils.min.js"></script>
<script src="<?= base_style() ?>/js/initial.min.js"></script>
<script src="<?= base_style() ?>/js/theme/default.min.js"></script>
<script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
<script src="<?= base_style() ?>/js/jquery.bootstrap-touchspin.min.js"></script>
<script src="<?= base_style() ?>/js/datatables.min.js"></script>
<script src="<?= base_style() ?>/bookstores/jszip/jszip.min.js"></script>
<script src="<?= base_style() ?>/bookstores/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_style() ?>/bookstores/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_style() ?>/bookstores/tinymce/tinymce.min.js"></script>
<script src="<?= base_style() ?>/bookstores/select2/js/select2.min.js"></script>
<script src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
<script src="<?= base_style() ?>/bookstores/smartwizard/js/jquery.smartWizard.js"></script>
<script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
<script src="<?= base_style() ?>/bookstores/jquery.maskedinput/jquery.maskedinput.js"></script>
<script src="<?= base_style() ?>/bookstores/chartjs/js/chart.min.js"></script>
<script src="<?= base_style() ?>/bookstores/lightbox/js/lightbox.min.js"></script>
<script src="<?= base_style() ?>/bookstores/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?= base_style() ?>/bookstores/axios/axios.min.js"></script>
<script src="<?= base_style() ?>/js/datatable-helper.js"></script>
<script src="<?= base_style() ?>/js/files.js"></script>
<script src="<?= base_style() ?>/js/whatsapp.js"></script>
<script src="<?= base_style() ?>/js/search.js"></script>
<script src="<?= base_style() ?>/js/datatable-helper.js"></script>
<script src="<?= base_style() ?>/js/inatividad.js"></script>
<script src="<?= base_style() ?>/js/network.js"></script>
<!-- ================== FIN ARCHIVOS JS =========== -->
<!-- ================== FIN API GOOGLE MAPS JS ================== -->
<?php if (isset($data['page_functions_js'])) { ?>
  <!-- ================== INICIO FUNCION JS ============ -->
  <script src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>?v=<?= time(); ?>"></script>
  <!-- ================== FIN FUNCION JS =============== -->
<?php } ?>
<!-- keys -->
<input type="hidden" id="whatsapp_key_value" value="<?= $_SESSION['businessData']['whatsapp_key'] ?>" />
<input type="hidden" id="whatsapp_api_value" value="<?= $_SESSION['businessData']['whatsapp_api'] ?>" />
<!-- MONEDA -->
<input type="hidden" value="<?= $_SESSION['businessData']['symbol'] ?>" id="moneda_simbol">
</body>

</html>