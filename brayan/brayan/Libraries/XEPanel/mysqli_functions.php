<?php

$mysqli = (object) array();

$mysqli->prefix = "";
$mysqli->server = DB_HOST;
$mysqli->user = DB_USER;
$mysqli->password = DB_PASSWORD;
$mysqli->database = DB_NAME;

function globalSql($sql)
{
    global $mysqli;
    $con = new mysqli($mysqli->server, $mysqli->user, $mysqli->password, $mysqli->database);
    $con->set_charset("utf8");
    if ($con->connect_errno > 0) {
        //writeLogs("sql", "Unable to connect to database $con->connect_error");
    } else {

        //writeLogs("sql", $sql);

        if (!$result = $con->query($sql)) {
            $mysqli->error = true;
            //writeLogs("sql", "MYSQLi Error: $con->error");
        }
        return $result;
    }
}
function globalSqlCount($sql)
{
    return mysqli_num_rows(globalSql($sql));
}
function globalSqlArray($sql)
{
    return globalSql($sql)->fetch_array();
}
function globalSqlAssoc($sql)
{
    return globalSql($sql)->fetch_assoc();
}
function globalSqlObject($sql)
{
    return globalSql($sql)->fetch_object();
}
function globalSqlInsert($table, $arr)
{
    $columns = "";
    $values = "";

    foreach ($arr as $k => $v) {

        if (!is_numeric($v) || $v[0] == "0") $v = doubleCuotes(htmlentities($v, ENT_QUOTES, 'UTF-8'));

        $columns .= $k . ",";
        $values .= $v . ",";
    }

    $columns = substr($columns, 0, -1);
    $values = substr($values, 0, -1);


    $sql = "INSERT INTO " . $table . " (" . $columns . ") values (" . $values . ")";

    globalSql($sql);
}
function globalSqlUpdate($t, $c, $v, $i)
{
    if (!is_numeric($v) || (is_array($v) && $v[0] == "0")) $v = singleCuotes(htmlentities($v, ENT_QUOTES, 'UTF-8'));

    globalSql("UPDATE " . $t . " SET " . $c . " = " . $v . " WHERE id = " . $i);
}
function globalSqlDelete($t, $i)
{
    globalSql("DELETE FROM " . $t . " WHERE id = " . $i);
}

//MASTER SQLs

function sql($sql)
{
    return globalSql($sql);
}
function sqlCount($sql)
{
    return globalSqlCount($sql);
}
function sqlArray($sql)
{
    return globalSqlArray($sql);
}
function sqlAssoc($sql)
{
    return globalSqlAssoc($sql);
}
function sqlObject($sql)
{
    return globalSqlObject($sql);
}
function sqlInsert($table, $obj)
{
    global $mysqli;
    globalSqlInsert($mysqli->prefix . $table, $obj);
}
function sqlUpdate($t, $c, $v, $i)
{
    global $mysqli;
    globalSqlUpdate($mysqli->prefix . $t, $c, $v, $i);
}
function sqlDelete($t, $i)
{
    global $mysqli;
    globalSqlDelete($mysqli->prefix . $t, $i);
}

//------------

function singleCuotes($str)
{
    return "'" . $str . "'";
}
function doubleCuotes($str)
{
    return '"' . $str . '"';
}
