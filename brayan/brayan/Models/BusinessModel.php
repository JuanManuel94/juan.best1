<?php
class BusinessModel extends Mysql
{
	private $intId, $strTypes, $strDocument, $strBussines, $strTradename, $strSlogan, $strMobile, $strReference;
	private $strAddress, $strDepartment, $strProvince, $strDistrict, $strUbigeo, $strFooter;
	private $intPrinters, $intCurrency, $strLogotype, $strLogoLogin, $strFavicon;
	private $strCountry, $strGoogle, $strReniec;
	private $strEmail, $strPassword, $strServer, $strPort, $intIdBackup, $strLogoEmail, $strBackground;
	public function __construct()
	{
		parent::__construct("business");
	}
	public function list_database()
	{
		$sql = "SELECT *FROM backups ORDER BY id DESC";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function show_business()
	{
		$sql = "SELECT  b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.print_format,b.logo_login,b.logotyope,b.logo_email,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,b.background,c.symbol,c.money,c.money_plural, b.whatsapp_key, b.whatsapp_api, b.month_corte FROM business b JOIN currency c ON b.currencyid = c.id";
		$request = $this->select($sql);
		$_SESSION['businessData'] = $request;
		return $request;
	}
	public function update_general(int $id, string $document, string $business_name, string $tradename, string $mobile, string $reference, string $address, int $month_corte)
	{
		$this->intId = $id;
		$this->strDocument = $document;
		$this->strBussines = $business_name;
		$this->strTradename = $tradename;
		$this->strMobile = $mobile;
		$this->strReference = $reference;
		$this->strAddress = $address;
		$answer = "";
		$sql = "UPDATE business SET ruc=?,business_name=?,tradename=?,mobile=?,mobile_refrence=?,address=?,month_corte=? WHERE id = $this->intId";
		$data = array($this->strDocument, $this->strBussines, $this->strTradename, $this->strMobile, $this->strReference, $this->strAddress, $month_corte);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function update_basic(int $id, string $slogan, string $department, string $province, string $district, string $ubigeo, string $country)
	{
		$this->intId = $id;
		$this->strSlogan = $slogan;
		$this->strDepartment = $department;
		$this->strProvince = $province;
		$this->strDistrict = $district;
		$this->strUbigeo = $ubigeo;
		$this->strCountry = $country;
		$answer = "";
		$sql = "UPDATE business SET slogan=?,department=?,province=?,district=?,ubigeo=?,country_code=? WHERE id = $this->intId";
		$data = array($this->strSlogan, $this->strDepartment, $this->strProvince, $this->strDistrict, $this->strUbigeo, $this->strCountry);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function update_invoice(int $id, string $footer, int $currency, string $printers)
	{
		$this->intId = $id;
		$this->strFooter = $footer;
		$this->intCurrency = $currency;
		$this->intPrinters = $printers;
		$answer = "";
		$sql = "UPDATE business SET footer_text=?,currencyid=?,print_format=? WHERE id = $this->intId";
		$data = array($this->strFooter, $this->intCurrency, $this->intPrinters);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function main_logo(int $id, string $logo)
	{
		$this->intId = $id;
		$this->strLogotype = $logo;
		$answer = "";
		$sql = "UPDATE business SET logotyope = ? WHERE id = $this->intId";
		$data = array($this->strLogotype);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function login_logo(int $id, string $logo)
	{
		$this->intId = $id;
		$this->strLogoLogin = $logo;
		$answer = "";
		$sql = "UPDATE business SET logo_login = ? WHERE id = $this->intId";
		$data = array($this->strLogoLogin);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function favicon(int $id, string $favicon)
	{
		$this->intId = $id;
		$this->strFavicon = $favicon;
		$answer = "";
		$sql = "UPDATE business SET favicon = ? WHERE id = $this->intId";
		$data = array($this->strFavicon);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function background(int $id, string $background)
	{
		$this->intId = $id;
		$this->strBackground = $background;
		$answer = "";
		$sql = "UPDATE business SET background = ? WHERE id = $this->intId";
		$data = array($this->strBackground);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function update_google(int $id, string $spi)
	{
		$this->intId = $id;
		$this->strGoogle = $spi;
		$answer = "";
		$sql = "UPDATE business SET google_apikey = ? WHERE id = $this->intId";
		$data = array($this->strGoogle);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function update_reniec(int $id, string $api)
	{
		$this->intId = $id;
		$this->strReniec = $api;
		$answer = "";
		$sql = "UPDATE business SET reniec_apikey = ? WHERE id = $this->intId";
		$data = array($this->strReniec);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function update_email(int $id, string $email, string $password, string $host, string $port, string $logo_email)
	{
		$this->intId = $id;
		$this->strEmail = $email;
		$this->strPassword = $password;
		$this->strServer = $host;
		$this->strPort = $port;
		$this->strLogoEmail = $logo_email;
		$answer = "";
		$sql = "UPDATE business SET email=?,password=?,server_host=?,port=?,logo_email=? WHERE id = $this->intId";
		$data = array($this->strEmail, $this->strPassword, $this->strServer, $this->strPort, $this->strLogoEmail);
		$request = $this->update($sql, $data);
		if ($request) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}

	public function update_whatsapp($id, $data = [])
	{
		return $this->createQueryBuilder()
			->update()
			->where("id = {$id}")
			->set($data)
			->execute();
	}

	public function create_backup()
	{
		$date = date('d-m-Y');
		$date_consult = date('Y-m-d');
		$business = strtolower($_SESSION['businessData']['business_name']);
		$business = str_replace("-", "", $business);
		$database = "backup_" . $business . "_" . $date . ".sql";
		$database_zip = "backup_" . $business . "_" . $date . ".zip";
		$consult = $this->select_all("SELECT *FROM backups WHERE archive = '$database_zip'");
		if (empty($consult)) {
			$path = "Assets/backups/";
			$error = 0;
			$tables = array();
			$query = "SHOW TABLES";
			$request = $this->select_all($query);
			if (!empty($request)) {
				foreach ($request as $row) {
					$tables[] = $row[TABLES_NAME];
				}
				$sql = 'SET FOREIGN_KEY_CHECKS=0;' . "\n\n";
				$sql .= 'CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ";\n\n";
				$sql .= 'USE ' . DB_NAME . ";\n\n";
				;
				foreach ($tables as $table) {
					$query = "SELECT * FROM " . $table;
					$request = $this->run_simple_query($query);
					if (!empty($request)) {
						$numFields = $request->columnCount();
						$sql .= 'DROP TABLE IF EXISTS ' . $table . ';';
						$query2 = "SHOW CREATE TABLE " . $table;
						$request2 = $this->select($query2);
						$sql .= "\n\n" . $request2['Create Table'] . ";\n\n";
						for ($i = 0; $i < $numFields; $i++) {
							while ($row = $request->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
								$sql .= 'INSERT INTO ' . $table . ' VALUES(';
								for ($j = 0; $j < $numFields; $j++) {
									$row[$j] = addslashes(empty($row[$j]) ? "" : $row[$j]);
									$row[$j] = str_replace("\n", "\\n", $row[$j]);
									if (isset($row[$j])) {
										$sql .= '"' . $row[$j] . '"';
									} else {
										$sql .= '""';
									}
									if ($j < ($numFields - 1)) {
										$sql .= ',';
									}
								}
								$sql .= ");\n";
							}
						}
						$sql .= "\n\n\n";
						$error = 1;
					} else {
						$error = 0;
					}
				}
				if ($error == 0) {
					$answer = 'error';
				} else {
					$sql .= 'SET FOREIGN_KEY_CHECKS=1;';
					$handle = fopen($database, 'w+');
					if (fwrite($handle, $sql)) {
						fclose($handle);
						$zip = new ZipArchive();
						$name_zip = "backup_" . $business . "_" . $date . ".zip";
						if ($zip->open($name_zip, ZipArchive::CREATE) === true) {
							$zip->addFile($database);
							$zip->close();
							chmod($path, 0777);
							$moved = rename($name_zip, $path . $name_zip);
							if ($moved) {
								unlink($database);
								$filesize = filesize_formatted($path . $name_zip);
								$datetime = date("Y-m-d H:i:s");
								$query_insert = "INSERT INTO backups(archive,size,registration_date) VALUES(?,?,?)";
								$data = array($name_zip, $filesize, $datetime);
								$insert = $this->insert($query_insert, $data);
								if ($insert) {
									$answer = 'success';
								} else {
									$answer = 'error';
								}
							} else {
								$answer = 'error';
							}
						} else {
							$answer = 'error';
						}
					} else {
						$answer = 'error';
					}
				}
			} else {
				$answer = 'error';
			}
			$request->closeCursor();
		} else {
			$answer = 'exists';
		}
		return $answer;
	}
	public function remove(int $id)
	{
		$path = "Assets/backups/";
		$this->intIdBackup = $id;
		$answer = "";
		$sql_consult = "SELECT *FROM backups WHERE id  = $this->intIdBackup";
		$request_consult = $this->select($sql_consult);
		if (!empty($request_consult)) {
			$backup = $path . $request_consult['archive'];
			$sql = "DELETE FROM backups WHERE id = $this->intIdBackup";
			$delete = $this->delete($sql);
			if ($delete) {
				unlink($backup);
				$answer = 'success';
			} else {
				$answer = 'error';
			}
		} else {
			$answer = 'error';
		}
		return $answer;
	}
}
