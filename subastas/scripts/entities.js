function Usuario(nombre, appaterno, apmaterno, password, verificapassword, dd, mm, yyyy, placa, categorias, email, telefono, idUsuario, idSubasta){
	this.nombre = nombre;
	this.appaterno = appaterno;
	this.apmaterno = apmaterno;
	this.password = password;
	this.verificapassword = verificapassword;
	this.dd = dd;
	this.mm = mm;
	this.yyyy = yyyy;
	this.placa = placa;
	this.categorias = categorias;
	this.fecha_nacimiento = new Date();
	this.email = email;
	this.telefono = telefono;
	this.idUsuario = idUsuario;
	this.idSubasta = idSubasta;
	/*
	for(var i in categorias){
		var foo = new UsuarioCategorias();
		foo.idUsuario = 0;
		foo.idCategoria = categorias[i].idCategoria;
		this.categorias.push(foo);
	}
	*/

}
function UsuarioCategorias(idUsuario, idCategoria){
	this.idUsuario = idUsuario;
	this.idCategoria = idCategoria;

}
function Login(email, password){
	this.email = email;
	this.password = password;
}

function Empresa(id,nombre,estatus){
	this.estatus = estatus;
	this.id = id;
	this.nombre= nombre;
}
function Subastas(idSubasta, nombreSubasta, IdTipoSubasta, fechaInicio, fechaFin, empresas, visible, ofertas_x_usuarios, autos_x_usuario, comentario){
	this.idSubasta = idSubasta;
	this.nombreSubasta = nombreSubasta;
	this.IdTipoSubasta = IdTipoSubasta;
	this.fechaInicio = fechaInicio;
	this.fechaFin = fechaFin;
	this.empresas = empresas;
	this.visible = 1;
	this.ofertas_x_usuarios = ofertas_x_usuarios;
	this.autos_x_usuario = autos_x_usuario;
	this.comentario = comentario;
}	

function Cotizacion(idUsuario,nombre,correo,telefono,marca,modelo,tipo,estatus,subServicios){

	this.idUsuario = idUsuario;
	this.nombre = nombre;
	this.correo = correo;
	this.telefono = telefono;
	this.marca = marca;
	this.modelo = modelo;
	this.tipo = tipo;
	this.estatus = estatus;  
	this.subServicios = subServicios;


}
function CotizacionServicio(idServicio,idSubServicios,nombreSubServicio){

	this.idSubServicios = idSubServicios;
	this.idServicio = idServicio;
	this.nombreSubServicio = nombreSubServicio;

}
function SubServicios (idSubServicio,idServicio,nombre,requisitos,estatus){
	this.idSubServicio = idSubServicio;
	this.idServicio = idServicio;
	this.nombre = nombre;
	this.requisitos = requisitos;
	this.estatus = estatus;
}
function Autos(idAuto, enVenta, precio, marca, modelo, color, anio, km, transmision, estado, ciudad, descripcion, estatus, publicado, fechaCreacion, features, fotos, idSubasta, motivo_precio, placa, serie, nombreContacto, telefonoContacto, celularContacto, correoContacto, infoContacto, horaInicio, horaFin){
 	
    this.idAuto = idAuto;
    this.enVenta = enVenta;
    this.precio = precio;
    this.marca = marca;
    this.modelo = modelo;
    this.color = color;
    this.anio = anio;
    this.km = km;
    this.transmision = transmision;
    this.estado = transmision; 
    this.ciudad = ciudad;
    this.descripcion = descripcion;
    this.estatus  = estatus;
    this.publicado = publicado;
    this.fechaCreacion = fechaCreacion;
    this.features = features;
    this.fotos = fotos;
    this.idSubasta = idSubasta;
    this.motivo_precio = motivo_precio;
	this.placa = placa;
	this.serie = serie;
	this.nombreContacto = nombreContacto;
	this.telefonoContacto = telefonoContacto;
	this.celularContacto = celularContacto;
	this.correoContacto = correoContacto;
	this.infoContacto = infoContacto;
	this.horaInicio = horaInicio;
	this.horaFin = horaFin;
}
function Marca(id, descripcion, estatus){
	this.id = id;
	this.descripcion = descripcion;
	this.estatus = estatus;
}


function Modelo(id,descripcion,estatus,idMarca){
	this.id = id;
	this.descripcion = descripcion;
	this.estatus = estatus;
	this.idMarca = idMarca;
}

function Caracteristicas(id,descripcion,estatus){

	this.id = id;
	this.descripcion = descripcion;
	this.estatus = estatus;

}
function Colores(id,descripcion,estatus){

	this.id = id;
	this.descripcion = descripcion;
	this.estatus = estatus;
}

function Home(banner970x90_01, infoSeguridad, banner300x600){

	this.banner970x90_01 = banner970x90_01;
	this.infoSeguridad = infoSeguridad;
	this.banner300x600 = banner300x600;
}
function busquedaAuto(descripcion,estadoId,marcaId,modeloId,anio,precioIni,precioFin,kmIni,kmFin,fechaIni,fechaFin,correoUsua, esAdmin){
	this.descripcion = descripcion;
	this.estadoId = estadoId;
	this.marcaId = marcaId;
	this.modeloId = modeloId;
	this.anio = anio;
	this.precioIni = precioIni;
	this.precioFin = precioFin;
	this.kmIni = kmIni;
	this.kmFin = kmFin;
	this.kmIni = kmIni;
	this.fechaIni = fechaIni;
	this.fechaFin = fechaFin;
	this.correoUsua = correoUsua;
	this.esAdmin = esAdmin;
}

function miAuto (correoUsua,idMarca,idModelo,numPlaca,estatus){

	this.correoUsua = correoUsua;
	this.idMarca = idMarca;
	this.idModelo = idModelo;
	this.numPlaca = numPlaca;
	this.estatus = estatus;

}
function AdminHome(id, esimg, ubicacion, ancho, alto, tag, url, eslink, link){

	this.id = id;
	this.esimg = esimg;
	this.ubicacion = ubicacion;
	this.ancho = ancho;
	this.alto = alto;
	this.tag = tag;
	this.url = url;
	this.eslink = eslink;
	this.link = link;

}

function Precio(id,descripcion,estatus){
	this.id = id;
	this.descripcion = descripcion;
	this.estatus = estatus;
}

function Contactanos(nombre,mail, telefono, mensaje){
	this.nombre = nombre;
	this.mail  = mail;
	this.telefono = telefono;
	this.mensaje = mensaje;
}
function AutosSubastas(idSubasta, idAuto, fechaIni, fechaFin){
	this.idSubasta = idSubasta;
	this.idAuto = idAuto;
	this.fechaIni = fechaIni;
	this.fechaFin = fechaFin;
}