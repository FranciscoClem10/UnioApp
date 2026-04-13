<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Iniciar Sesión - UnioApp</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "outline": "#767777",
              "inverse-surface": "#0c0f0f",
              "primary-fixed-dim": "#9581ff",
              "on-secondary": "#f9efff",
              "tertiary-dim": "#8c2a5b",
              "on-secondary-container": "#563098",
              "on-error": "#ffefef",
              "on-secondary-fixed-variant": "#603aa2",
              "error-container": "#f74b6d",
              "surface-container-lowest": "#ffffff",
              "on-error-container": "#510017",
              "background": "#f6f6f6",
              "surface-tint": "#5a2af7",
              "surface-container-high": "#e1e3e3",
              "error-dim": "#a70138",
              "primary-fixed": "#a292ff",
              "outline-variant": "#acadad",
              "primary": "#5a2af7",
              "on-background": "#2d2f2f",
              "secondary-fixed": "#ddc8ff",
              "surface-container": "#e7e8e8",
              "on-surface-variant": "#5a5c5c",
              "on-tertiary": "#ffeff2",
              "secondary-dim": "#5f39a1",
              "surface-bright": "#f6f6f6",
              "on-surface": "#2d2f2f",
              "primary-dim": "#4e0bec",
              "secondary-container": "#ddc8ff",
              "error": "#b41340",
              "secondary-fixed-dim": "#d2b8ff",
              "surface-variant": "#dbdddd",
              "on-primary-container": "#220076",
              "on-primary-fixed": "#000000",
              "on-tertiary-fixed": "#37001e",
              "on-primary-fixed-variant": "#2b0090",
              "on-tertiary-container": "#63033b",
              "secondary": "#6b46ae",
              "tertiary": "#9b3667",
              "on-tertiary-fixed-variant": "#6f1044",
              "surface-container-low": "#f0f1f1",
              "tertiary-fixed-dim": "#f27db0",
              "on-primary": "#f6f0ff",
              "inverse-on-surface": "#9c9d9d",
              "tertiary-container": "#ff8cbd",
              "tertiary-fixed": "#ff8cbd",
              "surface": "#f6f6f6",
              "on-secondary-fixed": "#431783",
              "inverse-primary": "#927dff",
              "surface-container-highest": "#dbdddd",
              "surface-dim": "#d3d5d5",
              "primary-container": "#a292ff"
            },
            fontFamily: {
              "headline": ["Plus Jakarta Sans"],
              "body": ["Plus Jakarta Sans"],
              "label": ["Plus Jakarta Sans"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
  </script>
<style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .kinetic-prism-bg {
            background: radial-gradient(circle at 0% 0%, rgba(90, 42, 247, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(162, 146, 255, 0.08) 0%, transparent 50%);
        }
</style>
</head>

<body class="bg-background text-on-surface selection:bg-primary-container/30" data-mode="connect">
    <!-- Shell de navegación superior -->
    <nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-md shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16">
      <div class="flex justify-between items-center px-8 h-full w-full max-w-screen-2xl mx-auto">
        <div class="flex items-center gap-2">
          <img alt="Unio Logo" class="h-8 w-auto" src="Assets\imgs\logo.png"/>

        </div>
        <div class="flex items-center gap-4">
          <a href="<?= BASE_URL ?>?c=registro" class="hidden md:block bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm transition-transform active:scale-95 shadow-sm">
            Registrate
          </a>
        </div>
      </div>
      <div class="bg-slate-100 h-[1px] w-full"></div>
    </nav>

    <main class="pt-16 kinetic-prism-bg">
      <section class="relative min-h-[921px] flex items-center px-8 lg:px-24 py-12 max-w-screen-2xl mx-auto">
        <div class="grid lg:grid-cols-12 gap-16 items-center w-full">
          <!-- Hero Content -->
          <div class="lg:col-span-7 space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary font-bold text-xs uppercase tracking-widest">
              <span class="material-symbols-outlined text-[14px]">
                bolt
              </span>
              Red Social de Próxima Generación
            </div>
            <h1 class="text-[3.5rem] md:text-[4.5rem] font-extrabold leading-[1.05] tracking-tighter text-on-surface font-headline">
              Conéctate a través de <br/>
              <span class="bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent italic">
                Eventos Reales
              </span>
            </h1>
            <p class="text-xl text-on-surface-variant max-w-xl leading-relaxed font-body">
              Descubre lo que está pasando a tu alrededor. Desde talleres clandestinos hasta festivales masivos, Unio te conecta con personas que comparten tus mismas pasiones.
            </p>
            <div class="flex flex-wrap gap-4 pt-4">
              <div class="flex -space-x-3">
                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsl7HBUgMS4GxHqck5GPfhDVyuhRvzZEVcLCfOrwC5kvHvvpEmYQhyr6g4sBhyT_unXlK1h-2rkG9FW0cZCWjgRZqr9yPy-W2jRKuUQMt_EgLtXCqX2oSfJq3niHJiWSFETrzx8d9KiTGrs7F5XzHkXlM4qST0MG1QfuT1kCuokeqJ6uBUsMndfJwAf756LHGWp0u44xa0rRYulcvYtPrhYB8urERr6yX2jhubqWC_gaIldSjVcxTlapdgFVYHavQrrywBid8k9Q"/>
                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHIHdHQInixvEvPaHWCjnR9PR227iCNepteXZjwo3YTxXGO9sVZoEKWS8-ci4jjU5TW4HOTJ9z1bIP36ry42RijDswwOHyOIaAHnZhfh6_1NUIfP0BZ9thm7BFWMo9M1BWx8lzOQK__6-piCifashD054cuGW5jPRAOXk2FTzIhIqTqyus6YUyeQYYcZNOfJgHYKV9ykZe7jyw6G85gZiMckEM6Bw2BSnamv0BzAfVAgrKDomfC2NhUiPjnB5ZdRJl10AS6JZltg"/>
                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDL80N68-UCkkUisS_S-TgDXLhx1bB2dszS3hFTkwmnC6SusOXq5y9yb1SadV-Xukw4zB9KrMTiIxaGHBW28PQtu0ChaeojUsQVC5ZjI6paii64j531-wZgV8bBVaTQ944ijD_bfP_O-JkgUs7qvc1IxSaxx-2naoxvVDw00FoduKu1McgFT1PWTDP9M_YU2GkQYyd7T7Lr4mJjNXqZNzxP46QoAFRN9c9NbgnO1hZSJW3xB7qUQr65MRKhKDH6A-nGxeIZGwpqfw"/>
                <div class="w-10 h-10 rounded-full border-2 border-white bg-secondary-container flex items-center justify-center text-[10px] font-bold text-on-secondary-container">
                  +2k
                </div>
              </div>
              <p class="text-sm font-medium text-on-surface-variant self-center">
                Únete a 
                  <span class="text-on-surface font-bold">
                    12,000+ personas
                  </span> 
                conectando hoy.
              </p>
            </div>
          </div>
          
          <!-- Tarjeta de formulario de login - FUNCIONALIDAD PHP MANTENIDA -->
          <div class="lg:col-span-5">
            <div class="bg-surface-container-lowest p-8 md:p-10 rounded-[2rem] shadow-[0_32px_64px_-16px_rgba(45,47,47,0.08)] relative overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
              <div class="relative space-y-6">
                <div class="flex justify-center mb-2">
                  <img alt="Unio Logo" class="h-10 w-auto" src="Assets\imgs\logo.png"/>
                </div>
                <div class="space-y-2">
                  <h3 class="text-2xl font-bold tracking-tight text-on-surface">Bienvenido de nuevo</h3>
                  <p class="text-sm text-on-surface-variant">Accede a tu cuenta para continuar explorando.</p>
                </div>
                
                <!-- Mostrar error de sesión si existe -->
                <?php if (isset($_SESSION['error_login'])): ?>
                  <div class="bg-error-container/20 border-l-4 border-error text-error p-4 rounded-xl text-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-base">error</span>
                    <span><?= htmlspecialchars($_SESSION['error_login']) ?></span>
                  </div>
                  <?php unset($_SESSION['error_login']); ?>
                <?php endif; ?>
                
                <!-- FORMULARIO CON LA FUNCIONALIDAD PHP ORIGINAL -->
                <form action="<?= BASE_URL ?>?c=login&a=verificar" method="POST" class="space-y-4">
                  <div class="space-y-1.5">
                    <label for="email" class="text-[0.75rem] font-bold text-on-surface-variant uppercase tracking-wider ml-1">
                      Correo Electrónico
                    </label>
                    <input 
                      type="email" 
                      name="email" 
                      id="email" 
                      required 
                      autofocus
                      class="w-full px-5 py-4 rounded-xl bg-surface-container-low border-none focus:ring-2 focus:ring-primary/20 placeholder:text-outline-variant transition-all" 
                      placeholder="alex@ejemplo.com"
                    />
                  </div>
                  <div class="space-y-1.5">
                    <label for="password" class="text-[0.75rem] font-bold text-on-surface-variant uppercase tracking-wider ml-1">
                      Contraseña
                    </label>
                    <input 
                      type="password" 
                      name="password" 
                      id="password" 
                      required
                      class="w-full px-5 py-4 rounded-xl bg-surface-container-low border-none focus:ring-2 focus:ring-primary/20 placeholder:text-outline-variant transition-all" 
                      placeholder="••••••••"
                    />
                  </div>
                  <button 
                    type="submit"
                    class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:shadow-xl hover:translate-y-[-2px] active:translate-y-[0] transition-all duration-300"
                  >
                    Iniciar Sesión
                  </button>
                </form>
                
                <div class="text-center mt-4">
                  <a class="text-sm text-primary hover:underline font-medium italic" href="#">
                    ¿Olvidaste tu contraseña?
                  </a>
                </div>
                <div class="pt-4 text-center border-t border-surface-container-high">
                  <p class="text-sm text-on-surface-variant">
                    ¿No tienes una cuenta? 
                    <a class="text-primary font-bold hover:underline" href="<?= BASE_URL ?>?c=registro">
                      Registrarme
                    </a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Sección de características de la cuadrícula Bento -->
      <section class="px-8 lg:px-24 py-24 max-w-screen-2xl mx-auto">
        <div class="text-center mb-16 space-y-4">
          <h2 class="text-4xl md:text-5xl font-black tracking-tight text-on-surface">
            Explora nuevas dimensiones
          </h2>
          <p class="text-lg text-on-surface-variant max-w-2xl mx-auto">
            Diseñamos Unio para que la tecnología facilite el contacto humano real, no para reemplazarlo.
          </p>
        </div>
        <div class="grid md:grid-cols-12 gap-6 h-auto">
          <!-- Característica: Descubrimiento hiperlocal -->
          <div class="md:col-span-8 bg-surface-container-lowest rounded-[2.5rem] p-10 flex flex-col justify-between group overflow-hidden relative shadow-sm hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 p-8">
              <span class="material-symbols-outlined text-primary text-5xl opacity-20 group-hover:opacity-100 transition-opacity duration-500" style="font-variation-settings: 'FILL' 1;">
                location_on
              </span>
            </div>
            <div class="relative z-10 space-y-4 max-w-md">
              <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">
                  explore
                </span>
              </div>
              <h3 class="text-3xl font-bold">
                Descubrimiento Hiper-Local
              </h3>
              <p class="text-on-surface-variant leading-relaxed">
                Nuestra tecnología de geocercas te muestra solo lo que importa. Encuentra eventos ocurriendo a la vuelta de la esquina en tiempo real, desde ferias gastronómicas hasta torneos de e-sports espontáneos.
              </p>
            </div>
            <div class="mt-8 rounded-2xl overflow-hidden h-48 md:h-64">
              <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDI1sCdIy8plEvdwRv77Ro6e8ui9w9_Aqr3YAImu4XIXyRhd9z0ka7J26otG8OXTttMsRF8dFcoZJqxMRtcz_W9cv67WFFtx9QRi3nZEUnsRwfZiAhXiFLMAhWVIhd1MgMpY7bzdeK2qI3_yFkD6CQTje-Gf6QK7qTnB6EZ8vnHHOyh1M2NYo9Esb1J5cVCqudLJnn6AB78KlP03FXKaDrv3o6daQuJI7th1X6Yspl0p3lt8KuT04EvyvOtIKw6UWEbasOwO5du5g"/>
            </div>
          </div>
          <!-- Característica: Gráficos de interés -->
          <div class="md:col-span-4 bg-primary rounded-[2.5rem] p-10 flex flex-col justify-between text-on-primary group shadow-lg shadow-primary/20">
            <div class="space-y-4">
              <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-white">
                  hub
                </span>
              </div>
              <h3 class="text-2xl font-bold">Grafos de Intereses</h3>
              <p class="text-on-primary/80 text-sm leading-relaxed">
                No te sugerimos eventos basados en "clics", sino en la calidad de tus conexiones. Tu grafo personal crece contigo, conectándote con tribus urbanas afines.
              </p>
            </div>
            <div class="mt-auto pt-8 flex items-center justify-center">
                <div class="relative w-32 h-32">
                  <div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div>
                  <div class="absolute inset-4 bg-white/40 rounded-full"></div>
                  <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-4xl">
                    share_reviews
                  </span>
                </div>
            </div>
          </div>
          <!-- Característica visual de ancho completo -->
          <div class="md:col-span-12 bg-surface-container-high rounded-[2.5rem] overflow-hidden relative group min-h-[400px]">
            <img class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFXbXH5y6vSI5l9kqXNpTWNVTwh4wHl2j4tK9752jtxHpVAfvYieEodQh666_92j6xmYQd2O49qg0TJj2OlLWzAB5I9rvbQgvQamg0n4bH9YiJ_gfoDnsf5FzhazywwEihcepBh4gbWOERXjHlwMljI4dPcsckPQQvIS1HG-ryeFO594TF-h2peRCMjY7F6CQPQna6TUBqQH_dzP0KosPiFUTwtqfCPiU0gk8Mi3OEgUZmT5weLmzmlYbTVfz873bvNAHGiIEk9w"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-12 md:p-20 flex flex-col justify-end">
              <h4 class="text-white text-4xl md:text-5xl font-black tracking-tight mb-4">
                Vive la experiencia Unio
              </h4>
              <p class="text-white/80 text-lg md:text-xl max-w-2xl">
                Explora miles de eventos cada fin de semana en tu ciudad y redescubre lo que significa estar presente.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-24 px-8 max-w-screen-xl mx-auto text-center">
        <div class="bg-gradient-to-br from-primary to-primary-dim p-12 md:p-20 rounded-[3rem] text-on-primary space-y-8 relative overflow-hidden shadow-2xl shadow-primary/30">
          <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
          <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-container/20 rounded-full blur-3xl"></div>
          <h2 class="text-4xl md:text-6xl font-black tracking-tight relative z-10">
            ¿Listo para salir de la pantalla?
          </h2>
          <p class="text-xl text-on-primary/80 max-w-2xl mx-auto relative z-10 font-medium">
            Únete a la comunidad de personas que están redefiniendo cómo nos conectamos en el mundo físico.
          </p>
          <div class="flex flex-col sm:flex-row justify-center relative z-10">
            <a href="<?= BASE_URL ?>?c=registro"
              class="bg-white text-primary px-10 py-5 rounded-2xl font-extrabold text-lg flex items-center justify-center gap-2 shadow-xl hover:bg-slate-50 transition-colors">
                <span class="material-symbols-outlined">
                    rocket_launch
                </span>
                Empezar Gratis
            </a>
          </div>
        </div>
      </section>
    </main>

    <!-- Pie de página -->
    <footer class="bg-white py-16 px-8 lg:px-24">
      <div class="max-w-screen-2xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="space-y-6">
          <div class="flex items-center gap-2">
            <img alt="Unio" class="h-6 w-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAF6R6LXwJtSYvGv4izmoGbQgZwo2liMYwESTGBd-YFas5LPiSiWq28hC9qlhkBnk-pPvKyotdyqhq4S1GcJ8TAETSh62QLWALMhCRtm_0TLbwUjRJNvcQKk3jy9KxIUVGTkepJKlVIgSv76OwrUYbHhpCVejJwX1UbHYRM9ZXwnS_EEhGH6UDEuOB8_M1Yewjk5V-cyyYXT9NK3kNFYYcxkI8dFiTrokab1nbGKRtM9Xa1iAdvu0VEQe0o-VoJbNvexDtRlPTv0w"/>
            <span class="text-xl font-black tracking-tighter text-[#5a2af7]">
              Unio
            </span>
          </div>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            Creando un mundo donde la tecnología sirve para unirnos en la vida real. Hecho con ❤️ para exploradores urbanos.
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
          <h5 class="font-bold text-sm uppercase tracking-widest text-on-surface">
            Producto
          </h5>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li><a class="hover:text-primary transition-colors" href="#">Características</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Explorar Ciudades</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Para Organizadores</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Seguridad</a></li>
          </ul>
        </div>
        <div class="space-y-4">
          <h5 class="font-bold text-sm uppercase tracking-widest text-on-surface">
            Comunidad
          </h5>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li><a class="hover:text-primary transition-colors" href="#">Guía de la Comunidad</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Eventos Destacados</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Historias de Éxito</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Blog</a></li>
          </ul>
        </div>
        <div class="space-y-4">
          <h5 class="font-bold text-sm uppercase tracking-widest text-on-surface">
            Legal
          </h5>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li><a class="hover:text-primary transition-colors" href="#">Privacidad</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Términos</a></li>
            <li><a class="hover:text-primary transition-colors" href="#">Cookies</a></li>
          </ul>
        </div>
      </div>
      <div class="max-w-screen-2xl mx-auto pt-16 mt-16 border-t border-surface-container text-center text-xs text-outline-variant uppercase tracking-widest">
        © 2026 Unio - Conectando con la realidad. Todos los derechos reservados.
      </div>
    </footer>
</body>
</html>