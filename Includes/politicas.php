<!DOCTYPE html>

<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Unio | Centro de ayuda</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="../Assets/imgs/icono.png">
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary": "#6b46ae",
                        "surface-container-lowest": "#ffffff",
                        "primary-dim": "#4e0bec",
                        "surface-container-highest": "#dbdddd",
                        "on-primary-fixed": "#000000",
                        "background": "#f6f6f6",
                        "on-primary-fixed-variant": "#2b0090",
                        "surface": "#f6f6f6",
                        "tertiary": "#9b3667",
                        "on-tertiary-fixed-variant": "#6f1044",
                        "on-primary-container": "#220076",
                        "outline": "#767777",
                        "secondary-fixed": "#ddc8ff",
                        "error-dim": "#a70138",
                        "primary-container": "#a292ff",
                        "surface-container-low": "#f0f1f1",
                        "outline-variant": "#acadad",
                        "tertiary-dim": "#8c2a5b",
                        "surface-container-high": "#e1e3e3",
                        "on-surface": "#2d2f2f",
                        "error-container": "#f74b6d",
                        "primary": "#5a2af7",
                        "on-secondary-fixed-variant": "#603aa2",
                        "inverse-primary": "#927dff",
                        "on-error-container": "#510017",
                        "tertiary-fixed-dim": "#f27db0",
                        "on-background": "#2d2f2f",
                        "tertiary-fixed": "#ff8cbd",
                        "inverse-surface": "#0c0f0f",
                        "secondary-container": "#ddc8ff",
                        "error": "#b41340",
                        "tertiary-container": "#ff8cbd",
                        "secondary-fixed-dim": "#d2b8ff",
                        "on-secondary": "#f9efff",
                        "surface-container": "#e7e8e8",
                        "on-tertiary": "#ffeff2",
                        "inverse-on-surface": "#9c9d9d",
                        "on-tertiary-fixed": "#37001e",
                        "primary-fixed-dim": "#9581ff",
                        "on-tertiary-container": "#63033b",
                        "primary-fixed": "#a292ff",
                        "surface-tint": "#5a2af7",
                        "on-primary": "#f6f0ff",
                        "surface-dim": "#d3d5d5",
                        "on-secondary-fixed": "#431783",
                        "secondary-dim": "#5f39a1",
                        "surface-bright": "#f6f6f6",
                        "on-surface-variant": "#5a5c5c",
                        "on-secondary-container": "#563098",
                        "surface-variant": "#dbdddd",
                        "on-error": "#ffefef"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Plus Jakarta Sans"],
                        "display": ["Plus Jakarta Sans"],
                        "body": ["Plus Jakarta Sans"],
                        "label": ["Plus Jakarta Sans"]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; color: #2d2f2f; }
        .glass-nav { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .kinetic-gradient { background: linear-gradient(135deg, #5a2af7 0%, #a292ff 100%); }
    </style>
</head>
<body class="antialiased">
<!-- Shell de navegación superior -->
    <nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-md shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16">
        <div class="flex justify-between items-center px-8 h-full w-full max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-2">
                <img alt="Unio Logo" class="h-10 w-auto object-contain" alt="Unio Logo" src="../Assets/imgs/logo.png"/>
            </div>
            <div class="flex items-center gap-4">
                <a href="../index.php" class="block bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm transition-transform active:scale-95 shadow-sm">
                    <span class="material-symbols-outlined">
                        login
                    </span>
                </a>
            </div>
        </div>
        <div class="bg-slate-100 h-[1px] w-full"></div>
    </nav>

        <main class="pt-32 pb-20 px-8 max-w-7xl mx-auto">
            <!-- Hero Section -->
            <header class="mb-16 text-center">
                <h1 class="text-5xl font-extrabold text-on-surface tracking-tight mb-6">
                    Centro de privacidad
                </h1>
                <p class="text-xl text-on-surface-variant max-w-2xl mx-auto leading-relaxed">
                    Documentación técnica, guías de comunidad y marcos legales detallados para potenciar tu experiencia en el ecosistema UNIO.
                </p>
            </header>

            <div class="flex flex-col gap-10">
                <!-- PRODUCTO -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] mb-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl kinetic-gradient flex items-center justify-center text-white">
                            <span class="material-symbols-outlined" data-icon="inventory_2">
                                inventory_2
                            </span>
                        </div>
                        <h2 id="producto" class="text-2xl font-bold tracking-tight">
                            PRODUCTO
                        </h2>
                    </div> 
                    <div class="space-y-4">
                        <!-- Accordion Item 1 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="informacion_general" class="font-semibold text-lg">Información General</span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Sobre UNIO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Bienvenido a UNIO, una red social interactiva y del bienestar común desarrollada por estudiantes del Tecnológico Nacional de México, Campus Tierra Blanca, como proyecto académico de la carrera de Ingeniería en Sistemas Computacionales. UNIO es una plataforma digital que permite a los usuarios crear, visualizar y participar en actividades dentro de su entorno geográfico en tiempo real mediante un mapa interactivo.
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Historia de UNIO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Todo comenzó con una pregunta simple que nadie podía responder: ¿habrá algo interesante sucediendo afuera?
                                        Sin forma de saberlo, la única opción era quedarse en casa. Y ahí estaba el problema, no solo en ese momento, sino en algo mucho más grande que llevaba tiempo observando: la gente cada vez se apoyaba menos, se conectaba menos, se encontraba menos. Una epidemia silenciosa de soledad que avanzaba justo en la era en que más "conectados" se supone que estamos.
                                        Esa contradicción no dejaba de dar vueltas. Las redes sociales abundan, pero las plazas están vacías. Hay grupos en todos lados, pero poca comunidad de verdad.
                                        UNIO nació de esa incomodidad, y de un deseo genuino de cambiarla. De la idea de que si pudieras ver en un mapa lo que está pasando a tu alrededor, quizás te animarías a salir. A participar. A encontrarte con otros.
                                        Porque a veces lo único que falta para dejar de estar solo es saber que afuera, algo está pasando.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Descripción general</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO es una red social interactiva y del bienestar común basada en un mapa digital en tiempo real. La plataforma permite a los usuarios crear, visualizar y participar en actividades dentro de su entorno geográfico, promoviendo la interacción social en espacios físicos y virtuales, fomentando la participación comunitaria y fortaleciendo el sentimiento de pertenencia.
                                        A diferencia de otras redes sociales que se centran en la comunicación virtual, UNIO orienta su propuesta hacia el encuentro real entre personas, facilitando todo el proceso: desde conocerse e interactuar en línea hasta coincidir físicamente en actividades dentro de su entorno cercano.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Funcionalidades implementadas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Sistema de Autenticación, Mapa Interactivo en Tiempo Real, Creación de Actividades, Confirmación de Asistencia y Perfiles de Usuario
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Arquitectura del sistema</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO sigue una arquitectura cliente-servidor con patrón MVC. El cliente (navegador web) se comunica con el servidor mediante peticiones HTTP y llamadas asíncronas a través de la Fetch API. El servidor procesa las solicitudes en PHP, interactúa con la base de datos MariaDB y devuelve las respuestas al cliente. La capa de presentación consume los datos y renderiza el mapa interactivo mediante Leaflet.js con datos geográficos de OpenStreetMap y CartoDB.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Versión de la plataforma</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO se encuentra actualmente en su versión Alpha (v0.1), correspondiente a la fase inicial de desarrollo y pruebas académicas.
                                    </div>
                                </details>
                            </div>
                        </details>
                        <!-- Accordion Item 2 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="cuenta_del_usuario" class="font-semibold text-lg">
                                    Cuenta del usuario
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Registro de usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Para crear tu cuenta en UNIO, accede a la página de registro e introduce tu nombre completo o nombre de usuario, una dirección de correo electrónico válida y una contraseña. Tu contraseña se almacenará de forma cifrada mediante hash criptográfico y nunca se guardará en texto plano.
                                        Al completar el registro confirmas que eres mayor de 18 años, que la información proporcionada es verídica y que aceptas los Términos de Uso y la Política de Privacidad de la plataforma. Una vez registrado, podrás iniciar sesión y comenzar a explorar el mapa, crear actividades y participar en las de tu comunidad.
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Inicio de sesión</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Tu perfil personal muestra tu nombre de usuario y fotografía de perfil. Es visible para otros usuarios en el contexto de las actividades que has creado o a las que has confirmado asistencia. Puedes actualizar tu información desde la configuración de tu cuenta en cualquier momento.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de la cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Eres el único responsable de mantener la confidencialidad de tus credenciales de acceso. Cualquier actividad realizada desde tu cuenta será considerada como realizada por ti. Deberás notificar de inmediato al equipo de UNIO si sospechas de acceso no autorizado a tu cuenta.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Perfil de usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Tu perfil personal muestra tu nombre de usuario y fotografía de perfil. Es visible para otros usuarios en el contexto de las actividades que has creado o a las que has confirmado asistencia. Puedes actualizar tu información desde la configuración de tu cuenta en cualquier momento.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Fotografía de perfil</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Puedes subir una fotografía de perfil desde la configuración de tu cuenta. La imagen es opcional y será visible para otros usuarios en el contexto de las actividades de la plataforma. Para actualizarla, accede a la configuración de tu cuenta y selecciona una nueva imagen desde tu dispositivo.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cancelación de cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Puedes solicitar la cancelación de tu cuenta en cualquier momento. El equipo de UNIO también podrá suspender o cancelar cuentas que incumplan estos Términos, sin previo aviso en casos de infracción grave.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Gestión de cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Desde la configuración de tu cuenta puedes actualizar tu información de perfil, cambiar tu fotografía de perfil y modificar tu contraseña. También puedes solicitar la cancelación definitiva de tu cuenta, acción que resultará en la eliminación de tus datos conforme a nuestra política de privacidad.
                                    </div>
                                </details>
                            </div>
                        </details>
                        <!-- Accordion Item 3 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="tecnologias_y_servicios_externos" class="font-semibold text-lg">
                                    Tecnologías y servicios externos
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Frontend</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        HTML5 y CSS3 para la estructura y estilos de la interfaz.
                                        JavaScript (Vanilla JS) para la interactividad del cliente.
                                        Tailwind CSS como framework de diseño.
                                        Leaflet.js como librería principal para la visualización del mapa interactivo.
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Backend</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        PHP como lenguaje de servidor.
                                        Arquitectura MVC (Modelo-Vista-Controlador) para la organización del código.
                                        MariaDB (compatible con MySQL) como gestor de base de datos relacional.
                                        Apache como servidor web.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>APIs y servicios externos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Web Geolocation API del navegador para obtener la ubicación del usuario.
                                        Fetch API para las comunicaciones asíncronas entre cliente y servidor.
                                        OpenStreetMap como proveedor de datos geográficos del mapa.
                                        CartoDB como proveedor de tiles y estilos visuales del mapa.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Entorno de desarrollo y despliegue</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        IDE: Visual Studio Code.
                                        Hosting: InfinityFree (servidor Apache + MariaDB) para la fase de pruebas.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>OpenStreetMap</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO utiliza OpenStreetMap como proveedor de datos geográficos del mapa interactivo. OpenStreetMap es un proyecto colaborativo de cartografía abierta que proporciona los datos base sobre calles, lugares y geografía que se visualizan en la plataforma. Al usar UNIO, parte de la información del mapa es procesada por OpenStreetMap Foundation conforme a su propia política de privacidad, disponible en openstreetmap.org/copyright.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>CartoDB</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        CartoDB actúa como proveedor de los tiles y estilos visuales que dan apariencia al mapa interactivo de UNIO. Su función es estrictamente visual: suministra las capas gráficas que se renderizan sobre los datos de OpenStreetMap a través de Leaflet.js. CartoDB no instala cookies ni recopila datos personales de los usuarios de UNIO.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Leaflet.js</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Leaflet.js es la librería de JavaScript de código abierto utilizada para construir y renderizar el mapa interactivo de UNIO. Permite la visualización de actividades como marcadores geográficos, el desplazamiento y el zoom sobre el mapa, así como la interacción del usuario con los eventos publicados en la plataforma.
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>InfinityFree</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        InfinityFree es el servicio de hosting gratuito utilizado durante la fase de pruebas académicas de UNIO. Proporciona el servidor Apache y la base de datos MariaDB sobre los que opera la plataforma en esta etapa de desarrollo. 
                                    </div>
                                </details>
                                <!--Subtema 9-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Google Analytics</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO utiliza Google Analytics para recopilar datos agregados y anónimos sobre el uso de la plataforma, como el número de visitas y patrones de navegación. Esta herramienta solo se activa si has aceptado las cookies analíticas. Puedes revocar este consentimiento en cualquier momento desde la opción "Gestionar cookies" en el pie de página.
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                </section>

                <!-- COMUNIDAD -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] mb-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-white">
                        <span class="material-symbols-outlined" data-icon="groups">
                            groups
                        </span>
                        </div>
                        <h2 id="comunidad" class="text-2xl font-bold tracking-tight">
                            COMUNIDAD
                        </h2>
                    </div>
                    <div class="space-y-4">
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="comunidad_y_uso_responsable" class="font-semibold text-lg">
                                    Comunidad y uso responsable
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Normas de la comunidad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Esta Política define comportamientos esperados de todos los usuarios de UNIO, con el objetivo de garantizar un ambiente seguro, respetuoso e inclusivo que proteja el propósito social de la plataforma (facilitar conexiones humanas reales dentro de comunidades para combatir el aislamiento social).
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Comportamientos esperados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Todo usuario de UNIO se compromete a tratar a todos los demás usuarios con respeto, dignidad y cordialidad; publicar únicamente actividades reales con información verídica sobre lugar, hora y propósito; usar la plataforma exclusivamente para fines sociales o comunitarios legítimos; respetar la privacidad de otros usuarios y no compartir información personal de terceros sin consentimiento; reportar contenido o comportamiento inapropiado que identifique en la plataforma; y cumplir con las leyes y regulaciones aplicables al organizar actividades presenciales.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Conductas prohibidas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        En UNIO, se considera prohibido acosar, intimidar, amenazar o discriminar a otros usuarios por cualquier razón; suplantar la identidad de otra persona u organización; publicar información privada de otros usuarios sin su consentimiento; contactar insistentemente a usuarios que han manifestado no desear comunicación; organizar actividades con el fin real de aislar, humillar o dañar a personas sin importar su género u orientación; publicar contenido sexualmente explícito, material que promueva el odio, la violencia o la discriminación; publicar información falsa que pueda causar pánico, desinformación o daño a terceros; así como intentar acceder sin autorización a cuentas ajenas, usar bots o scripts automatizados para extraer datos masivamente, o explotar vulnerabilidades de seguridad sin notificar previamente al equipo de UNIO.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Uso responsable de la plataforma</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        El uso de UNIO implica el compromiso de utilizar la plataforma exclusivamente para los fines sociales, comunitarios y colaborativos para los que fue diseñada. Queda prohibido cualquier uso que comprometa integridad, seguridad o buen funcionamiento de la plataforma y/o que afecte negativamente la experiencia de otros usuarios. El equipo de UNIO se reserva el derecho de suspender cuentas que hagan un uso contrario a estos principios.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Si un usuario identifica comportamientos de otro usuario que violen las normas de la comunidad, puede reportarlo a través de la función de reporte disponible en el perfil del usuario dentro de la plataforma. Todos los reportes son tratados con confidencialidad y revisados por el equipo de UNIO.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar actividad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Si un usuario identifica una actividad publicada en el mapa que contenga información falsa, inadecuada o que viole las normas de la comunidad, puede reportarla a través de la función de reporte disponible junto a cada actividad en la plataforma. El equipo de UNIO revisará el reporte y tomará las medidas correspondientes.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Mecanismo de reporte</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Si un usuario identifica alguna conducta o contenido que viole esta Política, podrá reportarlo a través de la función de reporte disponible en la plataforma junto a cada actividad o perfil de usuario, o mediante los canales de comunicación directa con el equipo de UNIO. Todos los reportes serán tratados con confidencialidad.
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Recomendaciones de seguridad para encuentros presenciales</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Dado que UNIO facilita encuentros en el mundo real, el equipo hace las siguientes recomendaciones: reúnete en lugares públicos y concurridos para los primeros encuentros con personas que no conoces; informa a un familiar o amigo de confianza sobre la actividad a la que asistirás, indicando lugar, hora y descripción; lleva contigo un teléfono cargado con acceso a comunicación de emergencia; verifica la coherencia de la información de la actividad antes de confirmar asistencia; si algo te genera desconfianza, confía en tu instinto y no asistas; en caso de sentirse en peligro, contacta de inmediato a las autoridades locales. UNIO no asume responsabilidad por lo que ocurra en los encuentros presenciales organizados a través de la plataforma.
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="soporte_y_contacto" class="font-semibold text-lg">
                                    Soporte y contacto
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Centro de ayuda</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        En UNIO, este es el espacio donde los usuarios pueden encontrar respuestas a las preguntas frecuentes sobre el uso de la plataforma o resolver dudas sobre su cuenta, entre otros aspectos. Para acceder, consulte los canales disponibles en la plataforma o contacte directamente al equipo de UNIO (unio.oficcial@gmail.com).
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Preguntas frecuentes (FAQ)</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        En esta sección el usuario podrá encontrar respuestas a las consultas más comunes sobre el uso de UNIO, incluyendo cómo crear o unirse a actividades, cómo funciona el mapa interactivo, cómo administrar una cuenta y cómo gestionar preferencias de privacidad. Consulte esta sección antes de contactar al equipo de soporte.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Soporte técnico</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Para problemas técnicos relacionados con el funcionamiento de la plataforma, como errores en el mapa, fallos en el inicio de sesión o problemas con la carga de actividades, contacte al equipo de UNIO a través del correo unio.oficcial@gmail.com describiendo detalladamente el problema encontrado, incluyendo el navegador y dispositivo que utilice.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar problemas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Si el usuario detecta un error, comportamiento inesperado o cualquier problema en la plataforma, podrá reportarlo directamente al equipo de UNIO a través de unio.oficcial@gmail.com incluyendo una descripción del problema y, de ser posible, una captura de pantalla como evidencia fotográfica para su comprensión y solución. El reporte nos ayuda a mejorar la experiencia de todos los usuarios.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar vulnerabilidades</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Si el usuario detecta una vulnerabilidad de seguridad en la plataforma, pedimos que esta sea reportada de inmediato y de manera responsable al equipo de UNIO a través de unio.oficcial@gmail.com antes de divulgar públicamente. Esto nos permite tomar las medidas necesarias para proteger a todos los usuarios. Asimismo, se agradecerá y reconocerá la aportación a la seguridad de la plataforma.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Contacto general</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Para cualquier consulta, comentario o sugerencia general sobre UNIO, el usuario podrá comunicarse con el equipo a través del correo electrónico unio.oficcial@gmail.com directamente en el Tecnológico Nacional de México, Campus Tierra Blanca, con la carrera de Ingeniería en Sistemas Computacionales.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Atención a usuarios</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        El equipo de UNIO se compromete a atender todas las consultas y solicitudes de los usuarios en el menor tiempo posible. Para ejercer derechos de acceso, rectificación, cancelación u oposición o para reportar cualquier situación que afecte la experiencia en la plataforma, escríbenos a unio.oficcial@gmail.com. Atenderemos la solicitud en un plazo máximo de 20 días hábiles conforme a la LFPDPPP.
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="redes_y_comunidad_institucional" class="font-semibold text-lg">
                                    Redes y comunidad institucional
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Tecnológico Nacional de México</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO es un proyecto desarrollado en el marco del Tecnológico Nacional de México (TecNM), red de instituciones de educación superior tecnológica pública más grande de México, comprometida con la formación de profesionales capaces de generar soluciones tecnológicas con impacto social.
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>TecNM Campus Tierra Blanca</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        El proyecto UNIO se desarrolla en el Campus Tierra Blanca del Tecnológico Nacional de México, ubicado en Tierra Blanca, Veracruz, México. Este campus forma parte de la red TecNM y ofrece carreras de ingeniería orientadas al desarrollo tecnológico y la innovación.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Ingeniería en Sistemas Computacionales</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO nace como proyecto académico de la carrera de Ingeniería en Sistemas Computacionales del TecNM Campus Tierra Blanca, programa orientado al diseño, desarrollo e implementación de soluciones tecnológicas que atiendan necesidades reales de la sociedad.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Proyecto académico</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO es un proyecto académico desarrollado en el contexto del certamen InnovaTecNM 2026, bajo la categoría de Bienes de Consumo Final, en la subcategoría de Soluciones y Servicios Digitales. El proyecto busca demostrar que la innovación tecnológica puede generar un impacto social real y positivo dentro de las comunidades.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Equipo desarrollador</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO es desarrollada por un equipo de estudiantes de la carrera de Ingeniería en Sistemas Computacionales del TecNM Campus Tierra Blanca, bajo la asesoría académica de la Dra. Angelita Ventura Sánchez. y el Ing. Carlos Castillo Quezada. Para contactar al equipo, escribe a unio.oficcial@gmail.com.
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                </section>
                    
                <!-- LEGAL -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)]">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-on-surface flex items-center justify-center text-white">
                            <span class="material-symbols-outlined" data-icon="gavel">
                                gavel
                            </span>
                        </div>
                        <h2 id="legal" class="text-2xl font-bold tracking-tight">
                            LEGAL
                        </h2>
                    </div>
                    <div class="space-y-4">
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-on-surface transition-colors">
                                <span id="privacidad_y_seguridad" class="font-semibold text-lg">
                                    Privacidad y Seguridad
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-on-surface/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Política de privacidad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        En UNIO nos tomamos muy en serio la privacidad de nuestros usuarios. Esta Política describe cómo recopilamos, usamos, almacenamos, compartimos y protegemos los datos personales que nos proporcionas. Está diseñada en cumplimiento con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP) de los Estados Unidos Mexicanos y sus principios rectores: licitud, consentimiento, información, calidad, finalidad, lealtad, proporcionalidad y responsabilidad. Al registrarte y usar UNIO, manifiestas tu consentimiento libre, específico e informado para el tratamiento de tus datos personales conforme a lo aquí descrito.
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Datos personales recopilados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Datos que Proporcionas Directamente
                                        Al registrarte y utilizar UNIO recopilamos los siguientes datos:
                                        •	Nombre completo o nombre de usuario.
                                        •	Dirección de correo electrónico.
                                        •	Contraseña (almacenada de forma cifrada; nunca en texto plano).
                                        •	Fotografía de perfil (opcional).
                                        •	Información incluida en las actividades que publiques: nombre, descripción y ubicación del evento.
                                        Datos Recopilados Automáticamente
                                        Durante el uso de la plataforma se recopilan automáticamente:
                                        •	Datos de geolocalización: latitud y longitud de tu dispositivo cuando accedes al mapa interactivo.
                                        •	Dirección IP del dispositivo de acceso.
                                        •	Tipo de navegador y sistema operativo.
                                        •	Fechas y horas de acceso a la plataforma.
                                        •	Actividades creadas y actividades a las que has confirmado asistencia.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Finalidad del tratamiento de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Finalidades Primarias (Necesarias para el Servicio)
                                        •	Crear y gestionar tu cuenta de usuario.
                                        •	Mostrar tu fotografía de perfil en el contexto de las actividades en las que participas.
                                        •	Centrar el mapa interactivo en tu ubicación actual y mostrar actividades cercanas.
                                        •	Permitirte crear y publicar actividades con marcadores en el mapa.
                                        •	Registrar y gestionar tu confirmación de asistencia a actividades.
                                        •	Garantizar la seguridad de la plataforma y prevenir usos indebidos.
                                        •	Cumplir con obligaciones legales aplicables.
                                        Finalidades Secundarias (Opcionales)
                                        •	Mejorar y optimizar los servicios de la plataforma con base en el análisis de patrones de uso.
                                        •	Informarte sobre nuevas funcionalidades o actualizaciones relevantes de UNIO.
                                        Si no deseas que tus datos sean utilizados para finalidades secundarias, puedes manifestarlo contactando al equipo de UNIO.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Tratamiento de geolocalización</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Los datos de geolocalización reciben un tratamiento diferenciado por su carácter sensible:
                                        •	Tu ubicación en tiempo real nunca se almacena de forma permanente en nuestros servidores ni se comparte públicamente.
                                        •	La ubicación solo se utiliza para centrar el mapa y mostrar actividades cercanas durante tu sesión activa.
                                        •	Los marcadores en el mapa representan la ubicación del evento, no la posición en tiempo real del usuario que lo creó.
                                        •	Los datos de ubicación se procesan a través de OpenStreetMap y CartoDB, servicios de terceros con sus propias políticas de privacidad.
                                        •	Puedes revocar el acceso a tu ubicación desde la configuración de tu navegador en cualquier momento, aunque esto afectará la funcionalidad del mapa.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de los datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO almacena los datos en una base de datos MariaDB alojada en un servidor Apache bajo el servicio InfinityFree, utilizado durante la fase de pruebas académicas. La arquitectura sigue el patrón MVC (Modelo-Vista-Controlador), que separa la lógica de negocio de la capa de presentación.
                                        •	Las contraseñas se almacenan mediante algoritmos de hash criptográfico; nunca en texto plano.
                                        •	Las comunicaciones utilizan el protocolo HTTPS.
                                        •	El acceso a la base de datos está restringido y controlado por el backend en PHP.
                                        •	Se aplican validaciones de entrada para prevenir inyecciones SQL y ataques XSS.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Compartición de datos con terceros</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO no vende, alquila ni comercializa tus datos personales a terceros. Para el funcionamiento de la plataforma, los datos pueden ser procesados por: OpenStreetMap Foundation (datos geográficos del mapa), CartoDB (visualización del mapa) e InfinityFree (hosting del servidor durante la fase de pruebas). Solo compartiremos datos adicionales con autoridades competentes cuando sea legalmente requerido.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Derechos ARCO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        De conformidad con la LFPDPPP, tienes los siguientes derechos sobre tus datos personales:
                                        •	Acceso: conocer qué datos tenemos sobre ti y cómo los usamos.
                                        •	Rectificación: solicitar la corrección de datos incorrectos o desactualizados.
                                        •	Cancelación: solicitar la eliminación de tus datos cuando ya no sean necesarios.
                                        •	Oposición: oponerte al tratamiento de tus datos para finalidades secundarias.
                                        Para ejercer cualquiera de estos derechos, contacta al equipo de UNIO a través de los canales disponibles en la plataforma. Atenderemos tu solicitud en los plazos establecidos por la ley.
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Restricción de edad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        UNIO está destinada exclusivamente a personas mayores de 18 años. No recopilamos intencionalmente datos personales de menores de edad. Si tenemos conocimiento o sospecha fundada de que un usuario es menor de 18 años, su cuenta será cancelada de forma inmediata y sus datos eliminados. Si tienes conocimiento de que un menor de edad ha creado una cuenta en UNIO, notifícalo de inmediato al equipo de la plataforma.
                                    </div>
                                </details>
                                <!--Subtema 9-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Eliminación de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Puedes solicitar la eliminación de tus datos personales ejerciendo tu derecho de Cancelación conforme a la LFPDPPP, contactando al equipo de UNIO a través de los canales disponibles en la plataforma. Una vez procesada la solicitud, tus datos serán eliminados de la base de datos, incluyendo tu cuenta, fotografía de perfil y las actividades que hayas creado, salvo que exista una obligación legal que requiera su conservación.
                                    </div>
                                </details>
                                <!--Subtema 10-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de contraseñas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Las contraseñas de todos los usuarios se almacenan mediante algoritmos de hash criptográfico y nunca se guardan en texto plano en la base de datos. Esto garantiza que, incluso en un escenario de acceso no autorizado al servidor, las contraseñas permanezcan protegidas. Se recomienda usar contraseñas únicas y no compartirlas con terceros.
                                    </div>
                                </details>
                                <!--Subtema 12-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Conexión segura HTTPS</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Toda la comunicación entre tu navegador y los servidores de UNIO se realiza a través del protocolo HTTPS, que cifra los datos en tránsito y protege la información que intercambias con la plataforma, incluyendo tus credenciales de acceso y los datos de las actividades.
                                    </div>
                                </details>
                                <!--Subtema 13-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Protección contra accesos no autorizados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        El acceso a la base de datos está restringido exclusivamente al backend en PHP mediante la arquitectura MVC. La plataforma aplica validaciones de entrada en todos los formularios para prevenir inyecciones SQL y ataques XSS. Adicionalmente, se implementan tokens CSRF en los formularios para proteger las acciones del usuario contra solicitudes fraudulentas desde sitios externos.
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-on-surface transition-colors">
                                <span id="cookies" class="font-semibold text-lg">
                                    Cookies
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-on-surface/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Política de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo cuando visitas nuestro sitio web. Permiten que el sitio recuerde información sobre tu visita, como el estado de tu sesión o tus preferencias, con el objetivo de mejorar tu experiencia de navegación. El responsable del tratamiento de datos a través de cookies es el equipo de desarrollo de UNIO, la plataforma digital desarrollada por estudiantes del Instituto Tecnológico Nacional de México, Campus Tierra Blanca. Para cualquier consulta relacionada con esta política puedes contactarnos en unio.oficcial@gmail.com
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Aviso de uso de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Usamos cookies propias y de terceros para que la plataforma funcione correctamente, analizar cómo la usas y mejorar tu experiencia. Te invitamos a aceptarlas todas, elegir cuáles o rechazar las opcionales que no sean de tu agrado. Tu privacidad nos importa. Para más información consulte nuestra Política de Cookies.
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies necesarias</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Son indispensables para el funcionamiento básico del sitio. No almacenan información personal identificable y no requieren tu consentimiento previo.
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies analíticas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Permiten conocer cómo los usuarios interactúan con el sitio de forma agregada y anónima. Requieren consentimiento del usuario.
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies de preferencias</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Permiten que el sitio recuerde configuraciones personalizadas y requieren consentimiento del usuario.
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies de marketing</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Pueden ser instaladas por socios publicitarios para mostrarte contenido relevante. Requieren tu consentimiento. Actualmente esta categoría está reservada para uso futuro conforme UNIO escale sus operaciones.
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Gestión de consentimiento</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        El usuario tiene derecho a aceptar, rechazar o revocar consentimiento en cualquier momento, con excepción de las cookies estrictamente necesarias. Puede hacerlo de dos formas: Desde UNIO, al ingresar por primera vez, se observa un banner de consentimiento donde el usuario elige qué tipos de cookies acepta. Puede cambiar su elección en cualquier momento desde la opción "Gestionar cookies" en el pie de página. 
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Transferencia internacional de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Algunos servicios utilizados por UNIO pueden implicar transferencia de datos fuera de México. Google Analytics opera bajo los marcos de privacidad establecidos por Google LLC. Consulta su política en: https://policies.google.com/privacy. OpenStreetMap es un proyecto de datos abiertos que no recopila datos personales de navegación. CartoDB provee únicamente tiles visuales de mapa sin instalar cookies ni recopilar datos de usuario.
                                    </div>
                                </details>
                                <!--Subtema 10-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Actualizaciones de la política de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Esta política puede actualizarse para reflejar cambios tecnológicos en la plataforma o modificaciones en la legislación aplicable. La fecha de última actualización siempre estará visible al inicio del documento.
                                    </div>
                                </details>
                                <!--Subtema 11-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Legislación aplicable sobre cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        Esta política se rige conforme a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP) de México y su Reglamento, que estipula que los datos personales deberán recabarse y tratarse de manera lícita conforme a las disposiciones establecidas por esta Ley y demás normatividad aplicable. La obtención de datos personales no debe hacerse a través de medios engañosos o fraudulentos. Asimismo, se rige por los Lineamientos del Aviso de Privacidad emitidos por el INAI que establecen el marco obligatorio para informar a los titulares sobre el tratamiento de sus datos personales, basándose en los principios de licitud, lealtad y transparencia.
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                    <div class="mt-12 p-6 rounded-2xl bg-surface-container-low border border-outline-variant/20">
                        <p class="text-sm font-medium text-on-surface-variant mb-4">
                            Última actualización legal:
                        </p>
                        <div class="flex items-center gap-2 text-primary font-bold">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                                calendar_month
                            </span>
                            <span>
                                27 de Abril, 2026
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>