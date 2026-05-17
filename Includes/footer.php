    <?php 
	$ruta = BASE_URL . 'includes/politicas.php';
	?>
		
    <!-- Pie de página -->
    <footer class="bg-white py-16 px-8 lg:px-24">
      <div class="max-w-screen-2xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="space-y-6">
          <div class="flex items-center gap-2">
            <img alt="Unio" class="h-6 w-auto" data-alt="small scale version of the modern Unio tech logo with fluid purple and blue shapes" src="../UnioApp/Assets/imgs/logo.png"/>
          </div>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            Creando un mundo donde la tecnología sirve para unirnos en la vida real. Hecho con ❤️ para exploradores de su entorno.
          </p>
          <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary cursor-pointer hover:bg-primary hover:text-white transition-all">
              <span class="material-symbols-outlined text-lg">public</span>
            </div>
            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary cursor-pointer hover:bg-primary hover:text-white transition-all">
              <span class="material-symbols-outlined text-lg">
                photo_camera
              </span>
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <a href="<?php echo $ruta; ?>#producto" class="font-bold text-sm uppercase tracking-widest text-on-surface hover:text-primary transition-colors">
              Producto
          </a>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li>
              <a href="<?php echo $ruta; ?>#informacion_general" class="hover:text-primary transition-colors" href="#">
                Información general
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#cuenta_del_usuario" class="hover:text-primary transition-colors" href="#">
                Cuenta del usuario
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#tecnologias_y_servicios_externos" class="hover:text-primary transition-colors" href="#">
                Tecnologías y servicios externos
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#seguridad" class="hover:text-primary transition-colors" href="#">
                Seguridad
              </a>
            </li>
          </ul>
        </div>
        <div class="space-y-4">
          <a href="<?php echo $ruta; ?>#comunidad" class="font-bold text-sm uppercase tracking-widest text-on-surface hover:text-primary transition-colors">
              Comunidad
          </a>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li>
              <a href="<?php echo $ruta; ?>#comunidad_y_uso_responsable" class="hover:text-primary transition-colors" href="#">
                Comunidad y uso responsable
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#soporte_y_contacto" class="hover:text-primary transition-colors" href="#">
                Soporte y contacto
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#redes_y_comunidad_institucional" class="hover:text-primary transition-colors" href="#">
                Redes y comunidad institucional
              </a>
            </li>
          </ul>
        </div>
        <div class="space-y-4">
          <a href="<?php echo $ruta; ?>#legal" class="font-bold text-sm uppercase tracking-widest text-on-surface hover:text-primary transition-colors">
              Legal
          </a>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li>
              <a href="<?php echo $ruta; ?>#privacidad_y_seguridad" class="hover:text-primary transition-colors" href="#">
                Privacidad y seguridad
              </a>
            </li>
            <li>
              <a href="<?php echo $ruta; ?>#cookies" class="hover:text-primary transition-colors" href="#">
                Cookies
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="max-w-screen-2xl mx-auto pt-16 mt-16 border-t border-surface-container text-center text-xs text-outline-variant uppercase tracking-widest">
          © 2026 UNIO — Todos los derechos reservados. Desarrollado por estudiantes de 
          <br>
          Ingeniería en Sistemas Computacionales | TecNM Campus Tierra Blanca
      </div>
    </footer>