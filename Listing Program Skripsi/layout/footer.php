 </div>
 <!-- ./wrapper -->

 <!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 3 -->
 <script src="<?php echo base_url() ?>assets/plugins/jquery/dist/jquery.min.js"></script>
 <!-- Bootstrap 3.3.7 -->
 <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

 <script src="<?php echo base_url() ?>assets/js/moment.min.js"></script>

 <script src="<?php echo base_url() ?>assets/js/adminlte.min.js"></script>

 <script src="<?php echo base_url() ?>assets/plugins/select2/dist/js/select2.min.js"></script>
 <?php if (isset($js)) {
    foreach ($js as $j) {?>
     <script src="<?php echo base_url($j); ?>" type="text/javascript"></script>
 <?php }
}?>

 <script type="text/javascript">

     //data table
       var data_table = $('#data_table').DataTable({
         "paging": true,
         "ordering": true,
         "bLengthChange": false,
         "info": true,
         columnDefs: [{
           orderable: false,
           targets: -1
         }],
         "oLanguage": {
           "sSearch": "Pencarian : ",
           "sEmptyTable": "Data tidak ditemukan",
           "oPaginate": {
             "sNext": "next",
             "sPrevious": "prev"
           }
         },
         "drawCallback": function() {
           $('.btn_add_mod').appendTo('.dataTables_wrapper  > .row > .col-sm-12:first');
           $('.dataTables_paginate > .pagination').addClass('pagination-sm');
         }
       });


   $('.only-number').on('change paste keyup', function(e) {
     if (/\D/g.test(this.value)) {
       // Filter non-digits from input value.
       this.value = this.value.replace(/\D/g, '');
     }
     if ($(this).attr('max')) {
       var max = parseInt($(this).attr('max'));
       console.log(max);
       if (parseInt(this.value) > max) {
         this.value = max;
       }
     }
   });

   $('.alphaonly').bind('keyup blur', function() {
     var node = $(this);
     node.val(node.val().replace(/[^a-z]/g, ''));
   });


   $('.select2').select2();


   setTimeout(function() {
     $('.alert').remove();
   }, 5000);
 </script>
 </body>

 </html>