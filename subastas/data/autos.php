<?php

class autos {

	public function __construct($idSubasta = 0, $idTipoSubasta = 0) {
		$this -> idSubasta = $idEmpresa;
		$this -> idTipoSubasta = $nombreEmpresa;
		$this -> fechaIni = date("Y-m-d");
		$this -> fechaFin = date("Y-m-d");

	}

	// Datos de la tabla "usuario"
	const NOMBRE_TABLA = "autos";
	const IDAUTO = "idAuto";
	const ENVENTA = "enVenta";
	const PRECIO = "precio";
	const MARCA = "marca";
	const MODELO = "modelo";
	const COLOR = "color";
	const ANIO = "anio";
	const KM = "km";
	const TRANSMISION = "transmision";
	const ESTADO = "estado";
	const CIUDAD = "ciudad";
	const DESCRIPCION = "descripcion";
	const ESTATUS = "estatus";
	const PUBLICADO = "publicado";
	const FECHACREACION = "fechaCreacion";

	const SIN_RESULTADOS = "No se encontraron resultados";
	const LISTO = "OK";
	const ESTADO_CREACION_EXITOSA = "OK";
	const ESTADO_CREACION_FALLIDA = "ERROR";

	public static function post($peticion) {

		if ($peticion[0] == 'guardar') {
			return self::registrarOut();
		} else if ($peticion[0] == 'subasta') {
			return self::listarPorSubastas();
		} else if ($peticion[0] == 'listar') {
			return self::listar();
		} else {
			throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
		}
	}

	private function listarPorSubastas() {

		$idsubasta = $_POST['idsubasta'];

		$comando = " SELECT au.idAuto, au.enVenta, au.precio, au.marca as marcaid, marca.descripcion as marca, au.modelo as modeloid, modelo.descripcion as modelo, " . " au.color as colorid, color.descripcion as color, au.anio, au.km, au.km, au.transmision as transmisionid, trans.descripcion as transmision, " . " au.estado as estadoid, est.nombre as estado, au.ciudad as ciudadid, mun.nombre as ciudad, au.descripcion, au.estatus, " . " au.publicado, au.fechaCreacion, aus.subastaId, " . " (select idFoto from auto_fotos where idAuto = au.idAuto limit 1) as foto, " . " (select GROUP_CONCAT(idFoto) from auto_fotos where idAuto = au.idAuto) AS fotos " . " FROM subastas_autos as aus, autos as au, cat_marca as marca, cat_modelo as modelo, cat_colores as color, cat_transmision as trans, estados as est, municipios as mun " . " WHERE aus.subastaId = ?  " . " and aus.autoId = au.idAuto  " . " and au.marca = marca.id  " . " and au.modelo = modelo.id  " . " and au.color = color.id  " . " and au.transmision = trans.id  " . " and au.estado = est.id " . " and au.ciudad = mun.id ";
	

		$sentencia = ConexionBD::obtenerInstancia() -> obtenerBD() -> prepare($comando);

		$sentencia -> bindParam(1, $idsubasta);

		if ($sentencia -> execute())
			return $sentencia -> fetchall(PDO::FETCH_ASSOC);
		else
			return null;

	}

	private function listar() {

		$precioIni = $_POST["precioIni"];
		$precioFin = $_POST["precioFin"];
		$kmIni = $_POST["kmIni"];
		$kmFin = $_POST["kmFin"];
		$anio = $_POST["anio"];
		$marcaId = $_POST["marcaId"];
		$estadoId = $_POST["estadoId"];
		$descripcion = $_POST["descripcion"];

		$comando = "SELECT au.idAuto, au.enVenta, au.precio, au.marca as marcaid, marca.descripcion as marca, au.modelo as modeloid, modelo.descripcion as modelo," . " au.color as colorid, color.descripcion as color, au.anio, au.km, au.km, au.transmision as transmisionid, trans.descripcion as transmision, " . " au.estado as estadoid, est.nombre as estado, au.ciudad as ciudadid, mun.nombre as ciudad, au.descripcion, au.estatus," . " au.publicado, au.fechaCreacion, " . " (select idFoto from auto_fotos where idAuto = au.idAuto limit 1) as foto," . " (select GROUP_CONCAT(idFoto) from auto_fotos where idAuto = au.idAuto) AS fotos " . " FROM  autos as au, cat_marca as marca, cat_modelo as modelo, cat_colores as color, cat_transmision as trans, estados as est, municipios as mun " . " WHERE  au.marca = marca.id" . " and au.modelo = modelo.id" . " and au.color = color.id  and au.transmision = trans.id" . " and au.estado = est.id" . " and au.ciudad = mun.id" . " and au.enVenta = 1" . (($precioIni <= 0 && $precioFin <= 0) ? "" : " and precio between  ? AND ?") . (($anio <= 0) ? "" : " and anio = ?") . (($kmIni <= 0 && $kmFin <= 0) ? "" : " and au.km between ? AND ?") . (($marcaid <= 0) ? "" : " and au.marca = ?") . (($estadoId <= 0) ? "" : " and au.estado = ?") . (($descripcion == '') ? "" : "and au.descripcion like '%?%'");

		$sentencia = ConexionBD::obtenerInstancia() -> obtenerBD() -> prepare($comando);

		$paramNum = 1;

		if ($precioIni <= 0 && $precioFin <= 0) {
			$sentencia -> bindParam($paramNum, $precioIni);
			$paramNum++;
			$sentencia -> bindParam($paramNum, $precioFin);
			$paramNum++;
		}
		if ($anio <= 0) {

			$sentencia -> bindParam($paramNum, $anio);
			$paramNum++;
		}
		if ($kmIni <= 0 && $kmFin <= 0) {

			$sentencia -> bindParam($paramNum, $kmIni);
			$paramNum++;
			$sentencia -> bindParam($paramNum, $kmFin);
			$paramNum++;
		}
		if ($marcaid <= 0) {

			$sentencia -> bindParam($paramNum, $marcaid);
			$paramNum++;
		}
		if ($estadoId <= 0) {

			$sentencia -> bindParam($paramNum, $estadoId);
			$paramNum++;
		}
		if ($descripcion == "") {

			$sentencia -> bindParam($paramNum, $descripcion);

		}
		if ($sentencia -> execute()) {
			return $sentencia -> fetchall(PDO::FETCH_ASSOC);
		} else {
			return null;
		}

	}

	private function registrarOut() {
		$cuerpo = file_get_contents('php://input');
		$usuario = json_decode($cuerpo);

		$resultado = self::registrar($_POST);

		switch ($resultado) {
			case self::ESTADO_CREACION_EXITOSA :
				http_response_code(200);
				return "OK";

				break;
			case self::ESTADO_CREACION_FALLIDA :
				throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
				break;
			default :
				http_response_code(200);
				return $resultado;
		}
	}

	private function registrar($auto) {

		try {

			$pdo = ConexionBD::obtenerInstancia() -> obtenerBD();

			// Sentencia INSERT
			$comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " . self::ENVENTA . "," . self::PRECIO . "," . self::MARCA . "," . self::MODELO . "," . self::COLOR . "," . self::ANIO . "," . self::KM . "," . self::TRANSMISION . "," . self::ESTADO . "," . self::CIUDAD . "," . self::DESCRIPCION . "," . self::ESTATUS . "," . self::PUBLICADO . ")" . " VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$sentencia = $pdo -> prepare($comando);
			$sentencia -> bindParam(1, $auto["enVenta"]);
			$sentencia -> bindParam(2, $auto["precio"]);
			$sentencia -> bindParam(3, $auto["marca"]);
			$sentencia -> bindParam(4, $auto["modelo"]);
			$sentencia -> bindParam(5, $auto["color"]);
			$sentencia -> bindParam(6, $auto["anio"]);
			$sentencia -> bindParam(7, $auto["km"]);
			$sentencia -> bindParam(8, $auto["transmision"]);
			$sentencia -> bindParam(9, $auto["estado"]);
			$sentencia -> bindParam(10, $auto["ciudad"]);
			$sentencia -> bindParam(11, $auto["descripcion"]);
			$sentencia -> bindParam(12, $auto["estatus"]);
			$sentencia -> bindParam(13, $auto["publicado"]);

			//idAuto, enVenta, marca, modelo, color, anio, km, transmision, estado, ciudad, descripcion, estatus, publicado, fechaCreacion

			$resultado = $sentencia -> execute();

			if ($resultado) {
				$idAuto = $pdo -> lastInsertId();

				autosfeatures::registrar($auto["features"], $idAuto);
				autosfotos::registrar($auto["fotos"], $idAuto);

				if ($auto["idSubasta"] > 0) {

					subastasautos::registrar($idAuto, $auto["idSubasta"]);
					subastasautos::programarauto($idAuto, json_decode($auto["horaInicio"]),  json_decode($auto["horaFin"]),  $auto["idSubasta"]); 
				}

				return $idAuto;

			} else {
				return -1;
			}
		} catch (PDOException $e) {

			print_r($e);
			throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e -> getMessage(), 400);

		}

	}

}
