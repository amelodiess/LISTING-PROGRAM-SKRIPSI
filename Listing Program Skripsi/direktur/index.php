<?php
    
    require_once('../../system/engine.php'); 
    
    define("ON_DASHBOARD", true);
    define("SITE_TITLE", 'Beranda');


    if(!get_session("user_login")){
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }

    require_once('../layout/header.php');


    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Beranda
            </h1> 
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                 <div class="col-md-12">
                     <div class="card"> 
                         <div class="card-body text-center px-5"> 
                         <div class="px-5"> 
                             
                              
                         </div><!-- .box-body -->
                     </div><!-- .div -->
                     </div><!-- .box -->
                 </div><!-- .col -->
             </div><!-- .row -->
        </section>
        <!-- .content -->
    </div>
    <!-- .content-wrapper -->


    <?php  require_once('../layout/footer.php'); ?>
