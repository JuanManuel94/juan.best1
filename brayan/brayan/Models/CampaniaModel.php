<?php

class CampaniaModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list_users_record(array $filtros = [])
    {
        $query = $this->createQueryBuilder()
            ->from("clients cl")
            ->select("cl.*")
            ->addSelect("cl.mobile_optional", "mobiledos")
            ->addSelect("CONCAT(cl.names, CONCAT(' ', cl.surnames))", "cliente")
            ->innerJoin("contracts c", "c.clientid = cl.id")
            ->innerJoin("document_type d", "cl.documentid = d.id");
        if ($filtros['state']) {
            $condicion = $filtros['state'];
            $query->andWhere("c.state = {$condicion}");
        }
        return $query->getMany();
    }

    public function list_business_wsp()
    {
        return $this->createQueryBuilder()
            ->from("business_wsp")
            ->getMany();
    }

    public function list_keys()
    {
        return [
            "AVISO_INSTALL",
            "PAGO_MASSIVE",
            "SUPPORT_TECNICO"
        ];
    }
}
