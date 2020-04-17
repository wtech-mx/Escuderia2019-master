<?php require 'header.php' ?>

	<div class="contenedor">
		<section class="post">
			<article>
				<h2 class="titulo"><?php echo $post['titulo'] ?></h2>
					<p class="fecha"><?php echo fecha($post['fecha']); ?></p>
				<div class="thumb">
					<img src="<?php echo RUTA; ?>/imagenes/<?php echo $post['thumb'] ?>" alt="<?php echo $post['thumb'] ?>">
				</div>
				<!-- Con la funcion nl2br insertamos un salto de linea antes de todas las lineas nuevas de un string. -->
				<p class="extracto"><?php echo $post['texto'] ?></p>
			</article>
		</section>
	</div>

<?php require 'footer.php'; ?>