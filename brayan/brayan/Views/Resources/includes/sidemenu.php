<div id="sidebar" class="sidebar">
    <div data-scrollbar="true" data-height="100%">
        <ul class="nav active">
            <li class="nav-profile">
                <?php
                if (!empty($_SESSION['businessData']['background'])) {
                    $background_url = base_style() . '/images/background/' . $_SESSION['businessData']['background'];
                    if (@getimagesize($background_url)) {
                        $background = base_style() . '/images/background/' . $_SESSION['businessData']['background'];
                    } else {
                        $background = base_style() . '/images/background/bg-1.jpeg';
                    }
                } else {
                    $background = base_style() . '/images/background/bg-1.jpeg';
                }
                ?>
                <div class="cover with-shadow imgsidebarlogin"
                    style="position: absolute;top:0;left:0;right:0;bottom: 0;background:url(<?= $background ?>) no-repeat !important;background-size: cover !important;">
                </div>
                <div class="image">
                    <a href="<?= base_url(); ?>/profile">
                        <?php
                        if (!empty($_SESSION['userData']['image'])) {
                            if ($_SESSION['userData']['image'] == "user_default.png") {
                                $image = base_style() . '/images/default/user_default.png';
                            } else {
                                $url = base_style() . '/uploads/users/' . $_SESSION['userData']['image'];
                                if (@getimagesize($url)) {
                                    $image = $url;
                                } else {
                                    $image = base_style() . '/images/default/user_default.png';
                                }
                            }
                        } else {
                            $image = base_style() . '/images/default/user_default.png';
                        }
                        ?>
                        <img src="<?= $image ?>" alt="<?= $_SESSION['userData']['names'] ?>">
                    </a>
                </div>
                <div class="info">
                    <?= $_SESSION['userData']['names'] ?>
                    <small><?= $_SESSION['userData']['profile'] ?></small>
                </div>
            </li>
        </ul>
        <ul class="nav">
            <?php if (!empty($_SESSION['permits'][DASHBOARD]['v'])) { ?>
                <li class="<?php if ($current[0] == "dashboard")
                    echo "active"; ?>">
                    <a href="<?= base_url() ?>/dashboard">
                        <i class="fa fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (!empty($_SESSION['permits'][RED]['v'])) { ?>
                <li class="has-sub <?php if (in_array($current[0], ["zones", "network", "cajaNap", "apclientes", "apemisor", "apreceptor"]))
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-network-wired"></i>
                        <span>Gestión de Red</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($current[0] == "network")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/network/routers">Routers</a>
                        </li>
                        <!--<li class="<?php if ($current[0] == "zones")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/network/zones">Zonas</a>
                        </li>-->
                        <li class="<?php if ($current[0] == "cajaNap" && empty($current[1]))
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/cajaNap">Mufa y Cajas Nap</a>
                        </li>
                        <li class="<?php if ($current[0] == "apclientes")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/apclientes">AP Clientes</a>
                        </li>
                        <li class="<?php if ($current[0] == "apemisor")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/apemisor">AP Emisor</a>
                        </li>
                        <li class="<?php if ($current[0] == "apreceptor")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/apreceptor">STA Receptor</a>
                        </li>

                        <li class="<?php if ($current[0] == "cajaNap" && $current[1] == "view_map")
                            echo "active"; ?>">
                            <a href="<?= base_url() ?>/cajaNap/view_map">Mapa de Mufa y Caja Nap</a>
                        </li>
                    </ul>
                </li>
            <?php } ?>




            <?php if (!empty($_SESSION['permits'][CLIENTS]['v']) || !empty($_SESSION['permits'][EMAIL]['v']) || !empty($_SESSION['permits'][INSTALLATIONS]['v'])) { ?>
                <li class="has-sub <?php if ($current[0] == "customers" || $current[0] == "advice" || $current[0] == "installations")
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-users"></i>
                        <span>Clientes</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (!empty($_SESSION['permits'][CLIENTS]['v'])) { ?>
                            <li class="<?php
                            if (
                                isset($current[0]) && $current[0] == "customers" &&
                                (empty($current[1]) ||
                                    (isset($current[1]) && in_array($current[1], ["add", "view_client", "customer_location"])))
                            ) {
                                echo "active";
                            }
                            ?>">
                                <a href="<?= base_url() ?>/customers">Clientes</a>
                            </li>

                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][CLIENTS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "customers" && $current[1] == "resumen")
                                echo "active"; ?>"><a href="<?= base_url() ?>/customers/resumen">Clientes
                                    Deuda</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][EMAIL]['v'])) { ?>
                            <li class="<?php if ($current[0] == "advice")
                                echo "active"; ?>"><a href="<?= base_url() ?>/advice">Correos</a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][INSTALLATIONS]['v'])) { ?>
                            <li class="<?php if (
                                $current[0] == "installations" && empty($current[1]) || $current[1] == "tools" || $current[1] == "location" || $current[1] == "attend"
                                || $current[1] == "gallery"
                            )
                                echo "active"; ?>"><a href="<?= base_url() ?>/installations">Instalaciones</a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][CLIENTS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "customers" && $current[1] == "customer_map")
                                    echo "active"; ?>">
                                    <a href="<?= base_url() ?>/customers/customer_map">Mapa de Clientes</a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (!empty($_SESSION['permits'][SERVICES]['v'])) { ?>
                <li class="has-sub <?php if ($current[0] == "plans")
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fas fa-calendar-alt"></i>
                        <span>Planes</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($current[0] == "plans" && $current[1] == "internet")
                            echo "active"; ?>"><a href="<?= base_url() ?>/plans/internet">Internet</a></li>
                        <li class="<?php if ($current[0] == "plans" && $current[1] == "personalized")
                            echo "active"; ?>"><a href="<?= base_url() ?>/plans/personalized">Personalizado</a></li>
                    </ul>
                </li>


            <?php } ?>
            <?php if (!empty($_SESSION['permits'][PRODUCTS]['v']) || !empty($_SESSION['permits'][CATEGORIES]['v']) || !empty($_SESSION['permits'][SUPPLIERS]['v'])) { ?>
                <li class="has-sub <?php if ($current[0] == "products" || $current[0] == "categories" || $current[0] == "providers" || $current[0] == "kardex")
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-archive"></i>
                        <span>Almacén</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (!empty($_SESSION['permits'][PRODUCTS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "products")
                                echo "active"; ?>"><a href="<?= base_url() ?>/products">Productos</a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][CATEGORIES]['v'])) { ?>
                            <li class="<?php if ($current[0] == "categories")
                                echo "active"; ?>"><a href="<?= base_url() ?>/categories">Categorias</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][SUPPLIERS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "providers")
                                echo "active"; ?>"><a href="<?= base_url() ?>/providers">Proveedores</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][PRODUCTS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "kardex")
                                echo "active"; ?>"><a href="<?= base_url() ?>/kardex">Kardex</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (!empty($_SESSION['permits'][TICKETS]['v'])) { ?>
                <li class="has-sub <?php if ($current[0] == "tickets")
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="far fa-life-ring"></i>
                        <span>Tickets</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (!empty($_SESSION['permits'][TICKETS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "tickets" && $current[1] == "current")
                                echo "active"; ?>"><a href="<?= base_url() ?>/tickets/current">Hoy</a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][TICKETS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "tickets" && $current[1] == "expired")
                                echo "active"; ?>"><a href="<?= base_url() ?>/tickets/expired">Vencidos</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][TICKETS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "tickets" && $current[1] == "resolved")
                                echo "active"; ?>"><a href="<?= base_url() ?>/tickets/resolved">Resueltos</a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][TICKETS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "tickets" && empty($current[1]) || $current[1] == "client_location" || $current[1] == "finalize")
                                    echo "active"; ?>">
                                    <a href="<?= base_url() ?>/tickets">Todos</a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (!empty($_SESSION['permits'][BILLS]['v']) || !empty($_SESSION['permits'][PAYMENTS]['v'])) { ?>
                <li
                    class="has-sub <?php echo in_array($current[0], ["bills", "payments", "otrosPagos"]) ? "active" : ""; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-chart-line"></i>
                        <span>Finanzas</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (!empty($_SESSION['permits'][BILLS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "bills" && empty($current[1]))
                                    echo "active"; ?>"><a href="<?= base_url() ?>/bills">Lista de
                                        facturas</a></li>
                            <?php } ?>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][BILLS]['v'])) { ?>
                            <li class="<?php if ($current[0] == "bills" && $current[1] == "pendings")
                                echo "active"; ?>"><a href="<?= base_url() ?>/bills/pendings">Facturas
                                    pendientes</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][PAYMENTS]['r'])) { ?>
                            <li class="<?php if ($current[0] == "payments" && $current[1] == "add_payment")
                                echo "active"; ?>">
                                <a href="<?= base_url() ?>/payments/add_payment">Registrar pago</a>
                            </li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][PAYMENTS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] != TECHNICAL && $_SESSION['userData']['profileid'] != CHARGES) { ?>
                                <li class="<?php if ($current[0] == "payments" && empty($current[1]))
                                    echo "active"; ?>"><a href="<?= base_url() ?>/payments">Cobranzas
                                        realizadas</a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][PAYMENTS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "payments" && $current[1] == "statistics")
                                    echo "active"; ?>"><a href="<?= base_url() ?>/payments/statistics">Estadisticas</a>
                                </li>
                            <?php } ?>



                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][PAYMENTS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "otrosPagos")
                                    echo "active"; ?>">
                                    <a href="<?= base_url() ?>/otrosPagos">Otros Ingresos & Egresos</a>
                                </li>
                            <?php } ?>

                        <?php } ?>
                        <?php if (!empty($_SESSION['permits'][BILLS]['v'])) { ?>
                            <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                                <li class="<?php if ($current[0] == "bills" && $current[1] == "promises")
                                    echo "active"; ?>"><a href="<?= base_url() ?>/bills/promises">Promesas</a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (!empty($_SESSION['permits'][WHATSAPP]['v'])) { ?>
                <li class="has-sub <?php if (in_array($current[0], ["campania"]))
                    echo "active"; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fas fa-compass"></i>
                        <span>Campañas</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (!empty($_SESSION['permits'][WHATSAPP]['v'])) { ?>
                            <li class="<?php if (in_array($current[0], ["campania"]) && $current[1] == "whatsapp")
                                echo "active"; ?>">
                                <a href="<?= base_url() ?>/campania/whatsapp">WSP CAMPAÑA</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (!empty($_SESSION['permits'][BUSINESS]['v']) || !empty($_SESSION['permits'][USERS]['v']) || !empty($_SESSION['permits'][CURRENCYS]['v']) || !empty($_SESSION['permits'][INCIDENTS]['v']) || !empty($_SESSION['permits'][RUNWAY]['v']) || !empty($_SESSION['permits'][VOUCHERS]['v']) || !empty($_SESSION['permits'][UNITS]['v'])) { ?>
                <li class="<?php if ($current[0] == "settings" || $current[0] == "users" || $current[0] == "profiles" || $current[0] == "currencys" || $current[0] == "incidents" || $current[0] == "runway" || $current[0] == "vouchers" || $current[0] == "unit")
                    echo "active"; ?>">
                    <a href="<?= base_url() ?>/settings">
                        <i class="fa fa-cogs"></i>
                        <span>Ajustes</span>
                    </a>
                </li>
            <?php } ?>
            <li class="<?php if ($current[0] == "help")
                echo "active"; ?>">
                <a href="<?= base_url() ?>/help">
                    <i class="fa fa-question-circle"></i>
                    <span>Ayuda<span class="label label-theme">PDF</span></span>
                </a>
            </li>
            <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i
                        class="fa fa-angle-double-left"></i></a></li>
        </ul>
    </div>
</div>
<div class="sidebar-bg"></div>