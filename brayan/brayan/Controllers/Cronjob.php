<?php

class Cronjob extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }
    public function run()
    {
        $res = (object) array();

        $result = sql("SELECT * FROM cronjobs WHERE status = 1");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $t = (object) $row;

            if ($t->lastrun == 0 || (time() - $t->lastrun) > ($t->frequency * 60)) {

                switch ($t->code) {
                    case 'IN001':
                        send_soon_due_invoices($t);
                        break;
                    case 'IN002':
                        send_expired_invoices($t);
                        break;
                    case 'IN003':
                        cut_service_expired_invoices($t);
                        break;
                    case 'CI001':
                        reg_customer_traffic($t);
                        break;

                    default:
                        break;
                }
            }
        }

        sqlUpdate("cronjobs_core", "lastrun", time(), 1);

        $res->result = "success";

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}
