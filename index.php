<?php
if (!isset($_GET['password']) || $_GET['password'] !== '0SiShHPgNwbYe53333') {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "NO TIENES PERMISO."
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador Ciudadano - ARVOIX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bgDark: '#050505',
                        glass: 'rgba(20, 20, 20, 0.6)',
                        glassBorder: 'rgba(255, 255, 255, 0.1)',
                        neonBlue: '#00f3ff',
                        neonPurple: '#9d00ff',
                        neonRed: '#ff0055',
                        accent: '#1a1a1a',
                        // Nuevo color para los filtros sutiles
                        filterBg: 'rgba(255, 255, 255, 0.05)', 
                        filterBorder: 'rgba(255, 255, 255, 0.15)', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['Fira Code', 'monospace'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Fira+Code:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #020202;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(76, 29, 149, 0.15) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(6, 182, 212, 0.15) 0%, transparent 25%);
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.6;
            cursor: pointer;
        }
        
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .loader-ring {
            display: inline-block;
            width: 24px;
            height: 24px;
        }
        .loader-ring:after {
            content: " ";
            display: block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            border-color: #00f3ff transparent #00f3ff transparent;
            animation: ring 1.2s linear infinite;
        }
        @keyframes ring {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="text-gray-300 min-h-screen flex flex-col items-center py-12 px-4 selection:bg-neonBlue selection:text-black">

    <header class="w-full max-w-6xl mb-12 text-center relative z-10">
        <div class="inline-block mb-4 p-2 rounded-full bg-white/5 border border-white/10">
            <span class="text-xs font-mono text-neonBlue tracking-wider px-2">SECURE API GATEWAY v3.2</span>
        </div>
        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-white mb-4">
            BÚSQUEDA <span class="text-transparent bg-clip-text bg-gradient-to-r from-neonBlue to-neonPurple">PERSONAS</span>
        </h1>
        <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base">
            Interfaz de consultas para encontrar información de personas.
        </p>
    </header>
    <div class="w-full max-w-7xl mb-12 relative z-10">
        <div class="glass-panel rounded-xl p-6 flex flex-col md:flex-row items-center justify-between transition-all hover:scale-[1.01] duration-300 border-neonBlue/10">
            <div class="flex items-center gap-4 mb-3 md:mb-0">
                <svg class="w-8 h-8 text-neonPurple/80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest">
                    <span class="text-white block md:inline-block">BASE DE DATOS REGIONAL:</span>
                    Total de Registros Disponibles
                </p>
            </div>
            <div class="text-3xl sm:text-4xl lg:text-5xl">
                <span class="registro-count">13,004,926</span>
            </div>
        </div>
    </div>
    <main class="w-full max-w-7xl relative z-10">
        
        <section class="glass-panel rounded-3xl p-8 mb-10 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-neonBlue/5 to-neonPurple/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

            <form id="searchForm" class="relative z-10 space-y-8">
                
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-neonBlue/80 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                        <h3 class="text-xs font-bold uppercase tracking-widest">Datos de Identidad y Filtros</h3>

                        
                    </div>
                    
                    

                    <div class="grid grid-cols-1 md:grid-cols-5 lg:grid-cols-9 gap-6">
                    

                        <div class="md:col-span-1 lg:col-span-2">
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Carnet Identidad</label>
                            <input type="text" id="carnet" placeholder="Ej: 940025"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonBlue focus:ring-1 focus:ring-neonBlue transition-all font-mono">
                        </div>
                        
                        <div class="md:col-span-1 lg:col-span-2">
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Nombre(s)</label>
                            <input type="text" id="nombre" placeholder="Nombres"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonPurple focus:ring-1 focus:ring-neonPurple transition-all">
                        </div>
                        
                        <div class="md:col-span-1 lg:col-span-2">
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Ap. Paterno</label>
                            <input type="text" id="paterno" placeholder="Paterno"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonPurple focus:ring-1 focus:ring-neonPurple transition-all">
                        </div>

                        <div class="md:col-span-1 lg:col-span-2">
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Ap. Materno</label>
                            <input type="text" id="materno" placeholder="Materno"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonPurple focus:ring-1 focus:ring-neonPurple transition-all">
                        </div>

                        <div class="md:col-span-1 lg:col-span-1">
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Género</label>
                            <div class="relative">
                                <select id="genero" class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white appearance-none focus:outline-none focus:border-neonBlue focus:ring-1 focus:ring-neonBlue transition-all cursor-pointer">
                                    <option value="">Todos</option>
                                    <option value="Masculino">Masc.</option>
                                    <option value="Femenino">Fem.</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-5 lg:grid-cols-9 gap-6">
                    <div class="md:col-span-1 lg:col-span-2">
                        <label class="block text-xs text-gray-400 mb-2 ml-1">CONTRASEÑA de la API</label>
                        <input type="password" id="password" value="<?php echo $_GET['password']?>" placeholder="..."
                            class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonBlue focus:ring-1 focus:ring-neonBlue transition-all font-mono">
                    </div>
                </div>


                <div class="border-t border-white/5 pt-6">
                    <div class="flex items-center gap-2 text-neonBlue/80 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <h3 class="text-xs font-bold uppercase tracking-widest">Rango de Fechas (Nacimiento)</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Desde</label>
                            <input type="date" id="fechaInicio"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neonBlue transition-all">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 ml-1">Hasta</label>
                            <input type="date" id="fechaFin"
                                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neonBlue transition-all">
                        </div>
                        <div class="flex justify-end pt-4 md:pt-0">
                            <button type="submit" id="btnBuscar"
                                class="group relative px-8 py-4 bg-white text-black font-bold rounded-xl overflow-hidden shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,243,255,0.4)] transition-all transform active:scale-95 w-full lg:w-auto">
                                <div class="absolute inset-0 bg-gradient-to-r from-neonBlue to-neonPurple opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="relative z-10 flex items-center justify-center gap-3">
                                    <span id="btnText">BUSCAR REGISTROS</span>
                                    <div id="btnLoader" class="loader-ring hidden"></div>
                                    <svg id="btnIcon" class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <div id="errorBox" class="hidden mb-8 p-4 bg-red-900/20 border border-red-500/50 rounded-xl flex items-start gap-4 backdrop-blur-md">
            <svg class="w-6 h-6 text-neonRed flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <div>
                <h4 class="text-neonRed font-bold mb-1">Error en la consulta</h4>
                <p id="errorMsg" class="text-red-200 text-sm"></p>
            </div>
        </div>

        <section id="resultsSection" class="hidden opacity-0 transition-opacity duration-700">
            <div class="flex items-center justify-between mb-4 px-2">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-neonBlue animate-pulse"></span>
                    Registros Encontrados
                </h2>
                <span id="countBadge" class="bg-white/10 text-white text-xs font-mono px-3 py-1 rounded-full border border-white/10">0 registros</span>
            </div>

            <div class="glass-panel rounded-2xl overflow-hidden p-0">
                
                <div id="tableContainer" class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1400px]">
                        <thead>
                            <tr class="bg-white/5 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-white/10">
                                

                                <th class="p-4 w-[100px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        COMPUTO OEP  
                                    </div>
                                </th>
                                <th class="p-4 w-[100px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        CI/Compl.
                                        <button data-sort-by="ci_completo" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="ci_completo" placeholder="Buscar CI" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 w-[120px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Paterno
                                        <button data-sort-by="paterno" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="paterno" placeholder="Buscar Ap." 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 w-[120px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Materno
                                        <button data-sort-by="materno" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="materno" placeholder="Buscar Ap." 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 w-[120px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Nombre
                                        <button data-sort-by="nombre" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="nombre" placeholder="Buscar Nomb." 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 w-[80px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Género
                                        <button data-sort-by="genero" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="genero" placeholder="M/F" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 w-[120px] align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        F. Nac.
                                        <button data-sort-by="fecha_nacimiento" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="fecha_nacimiento" placeholder="Fecha" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Est. Civil
                                        <button data-sort-by="estado_civil" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="estado_civil" placeholder="Estado" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden 2xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        RUDE
                                        <button data-sort-by="codigo_rude" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="codigo_rude" placeholder="Buscar RUDE" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Nac. Localidad
                                        <button data-sort-by="nacimiento_localidad" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="nacimiento_localidad" placeholder="Localidad" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden lg:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Comunidad
                                        <button data-sort-by="comunidad" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="comunidad" placeholder="Comunidad" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden lg:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Municipio
                                        <button data-sort-by="municipio" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="municipio" placeholder="Municipio" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden lg:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Provincia
                                        <button data-sort-by="provincia" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="provincia" placeholder="Provincia" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Departamento
                                        <button data-sort-by="departamento" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="departamento" placeholder="Dpto." 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        País
                                        <button data-sort-by="pais" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="pais" placeholder="País" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden 2xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Teléfono
                                        <button data-sort-by="telefono" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="telefono" placeholder="Telf." 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden 2xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        Email
                                        <button data-sort-by="email" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="email" placeholder="Email" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                                <th class="p-4 hidden 2xl:table-cell align-top">
                                    <div class="flex items-center justify-between mb-1 text-neonBlue">
                                        F. Registro
                                        <button data-sort-by="fecha_registro" data-sort-dir="asc" class="sort-btn text-gray-500 hover:text-neonBlue transition-colors">
                                            <i class="fas fa-sort-up text-neonBlue hidden active-sort"></i>
                                            <i class="fas fa-sort-down hidden active-sort"></i>
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                    <input type="text" data-filter-by="fecha_registro" placeholder="Fecha" 
                                        class="filter-input w-full bg-filterBg border border-filterBorder rounded-md px-2 py-1 text-white text-xs placeholder-gray-600 focus:outline-none focus:border-neonBlue transition-all">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyDesktop" class="text-sm text-gray-300 divide-y divide-white/5 font-mono">
                            </tbody>
                    </table>
                </div>

                <div id="tableBodyMobile" class="md:hidden space-y-4 p-4">
                    </div>
                
                <div id="emptyState" class="hidden py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-gray-500">No se encontraron registros coincidentes.</p>
                </div>
            </div>
        </section>

<!-- Modal overlay -->
<div id="imageModal" 
     class="hidden fixed inset-0 bg-black bg-opacity-5 backdrop-blur-md
            flex items-center justify-center z-50 opacity-0 transition-opacity duration-300">

    <!-- Contenedor -->
    <div id="modalContent" 
         class="bg-[#1b1b1b] p-5 rounded-2xl shadow-2xl border border-gray-700 
                max-w-[90%] max-h-[90%] 
                overflow-auto transform scale-75 opacity-0 transition-all duration-300">

        <!-- Cerrar -->
        <button id="closeModal" 
                class="float-right text-red-400 hover:text-red-300 transition text-2xl font-bold">
            &times;
        </button>

        <!-- Loader -->
        <div id="loader" class="flex flex-col items-center justify-center py-10 gap-3">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-500 border-t-transparent"></div>
            <p class="text-gray-300 text-sm">Cargando imagen...</p>
        </div>

        <!-- Imagen -->
        <img id="modalImage" src="" 
             class="hidden mt-4 max-w-full max-h-[80vh] rounded-xl shadow-lg border border-gray-700">
    </div>
</div>


<div class="w-full max-w-2xl mx-auto mb-12 text-center relative z-10">
    
    <!-- Título y subtítulo -->
    <div class="inline-block mb-4 p-2 rounded-full bg-white/5 border border-white/10">
        <span class="text-xs font-mono text-neonBlue tracking-wider px-2">LOOKUP NUMBER API GATEWAY v1.2</span>
    </div>

    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-white mb-4">
        TELEFONÍA DE <span class="text-transparent bg-clip-text bg-gradient-to-r from-neonBlue to-neonPurple">NÚMERO</span>
    </h1>

    <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base mb-8">
        Interfaz de consultas para encontrar telefonía de número.
    </p>

    <!-- Input y botón -->
    <div class="glass-panel rounded-2xl p-6 flex flex-col md:flex-row items-center justify-center gap-4 border-neonBlue/10 transition-all hover:scale-[1.01] duration-300">
        
        <!-- Input de número -->
        <div class="flex-1 min-w-[220px]">
            <label class="block text-xs text-gray-400 mb-2 ml-1">Número (+591)</label>
            <input type="tel" id="telefono" placeholder="60000000"
                pattern="^6[0-9]{7}$|^7[0-9]{7}$|^8[0-9]{7}$"
                title="Número válido entre 60000000 y 80000000"
                class="w-full bg-accent border border-glassBorder rounded-xl px-4 py-3 text-white placeholder-gray-700 focus:outline-none focus:border-neonBlue focus:ring-1 focus:ring-neonBlue transition-all font-mono text-center">
        </div>

        <!-- Botón de consulta centrado -->
        <div class="flex justify-center w-full md:w-auto">
            <button id="btnLookup" 
                class="group relative px-8 py-4 bg-white text-black font-bold rounded-xl overflow-hidden shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,243,255,0.4)] transition-all transform active:scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-neonBlue to-neonPurple opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="relative z-10">CONSULTAR</span>
            </button>
        </div>
    </div>

    <!-- Mensaje de error -->
    <p id="errorTelefono" class="text-red-400 text-sm mt-2 hidden">Número inválido. Debe estar entre 60000000 y 80000000.</p>

    <!-- Contenedor del resultado centrado -->
    <div id="resultadoTelefonia" class="mt-8 flex flex-col items-center justify-center hidden">
        <img id="logoTelefonia" src="" alt="Logo operador" class="w-24 h-24 object-contain mb-2">
        <p id="nombreTelefonia" class="text-white text-xl font-bold text-center"></p>
    </div>
</div>

<script>
    const inputTelefono = document.getElementById('telefono');
    const btnLookup = document.getElementById('btnLookup');
    const errorTelefono = document.getElementById('errorTelefono');


    async function getTelefonia(numero){
        const myHeaders = new Headers();
        myHeaders.append("Content-Type", "application/json");

        const raw = JSON.stringify({
        "numero": numero
        });

        const requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: raw,
        redirect: "follow"
        };

        <?php
        $password = $_GET['password'] ?? '';
        ?>
        const response = await fetch("telefonia.php?password=<?= $password ?>", requestOptions);
        const data = await response.json()

        return { nombre: data.data.marketplace.availability.operatorLookup.operator.name, logo: data.data.marketplace.availability.operatorLookup.operator.logoUrl}
    }

    const resultadoDiv = document.getElementById('resultadoTelefonia');
    const logoImg = document.getElementById('logoTelefonia');
    const nombreP = document.getElementById('nombreTelefonia');

btnLookup.addEventListener('click', async (e) => {
    e.preventDefault();
    const valor = inputTelefono.value.trim();

    if (!/^[6-8][0-9]{7}$/.test(valor)) {
        errorTelefono.classList.remove('hidden');
        resultadoDiv.classList.add('hidden'); // Oculta resultado si hay error
        return;
    }
    errorTelefono.classList.add('hidden');

    const telefonia = await getTelefonia(valor);

    // Mostrar el resultado
    logoImg.src = telefonia.logo;
    nombreP.textContent = telefonia.nombre;
    resultadoDiv.classList.remove('hidden');

    console.log(telefonia.nombre, telefonia.logo);
});

</script>

    </main>

    <script>
        // Almacenamiento global para los datos originales y filtrados/ordenados
        let globalPersonas = [];
        let filteredPersonas = [];
        let currentSort = { column: 'ci_completo', direction: 'asc' }; // Orden inicial

        // CONFIGURACIÓN: Apunta a tu archivo PHP
        const API_ENDPOINT = './api.php?password=<?php echo urlencode($_GET["password"] ?? ""); ?>';


        // ===============================================
        // FUNCIONES AUXILIARES GLOBALES (CORRECCIÓN DEL ERROR)
        // ===============================================

        /**
         * Función auxiliar para formatear y manejar nulls/cadenas vacías/ceros
         * @param {*} value - El valor a formatear.
         * @returns {string} - El valor como cadena o una cadena vacía.
         */
        const formatValue = (value) => {
            return (value === null || value === '0' || value === '') ? '' : String(value);
        };
        
        /**
         * Formato para fechas largas (quita la parte de la hora si existe)
         * @param {string} value - El valor de fecha/hora.
         * @returns {string} - La parte de la fecha o cadena vacía.
         */
        const formatDateTime = (value) => {
            if (!value) return '';
            try {
                // Elimina la parte de la hora y la T, si existe.
                const datePart = String(value).split('T')[0].split(' ')[0]; 
                return datePart;
            } catch (e) {
                return String(value);
            }
        };

        // ===============================================
        // LÓGICA DE BÚSQUEDA Y RENDERING
        // ===============================================

        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const btnIcon = document.getElementById('btnIcon');
            const resultsSection = document.getElementById('resultsSection');
            const errorBox = document.getElementById('errorBox');
            
            // 1. Estado de Carga
            btnText.textContent = "BUSCANDO...";
            btnLoader.classList.remove('hidden');
            btnIcon.classList.add('hidden');
            resultsSection.classList.add('hidden');
            resultsSection.classList.remove('opacity-100');
            errorBox.classList.add('hidden');
            document.getElementById('tableBodyDesktop').innerHTML = ''; // Limpiar tabla
            document.getElementById('tableBodyMobile').innerHTML = '';
            globalPersonas = [];
            filteredPersonas = [];
            document.querySelectorAll('.filter-input').forEach(input => input.value = ''); // Limpiar filtros de la tabla

            // 2. Preparar Payload 
            const payload = {
                carnet_identidad: document.getElementById('carnet').value.trim(),
                password: document.getElementById('password').value.trim(),
                genero: document.getElementById('genero').value,
                nombre: document.getElementById('nombre').value.trim(),
                paterno: document.getElementById('paterno').value.trim(),
                materno: document.getElementById('materno').value.trim(),
                fecha_nacimiento_inicio: document.getElementById('fechaInicio').value,
                fecha_nacimiento_fin: document.getElementById('fechaFin').value
            };

            try {
                // 3. Petición Fetch
                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) throw new Error(`Error del servidor: ${response.status}`);

                const data = await response.json();

                if (data.error) throw new Error(data.error);

                // 4. Procesar Resultados
                if (data.status === 'success') {
                    // Inicializar los datos globales, añadiendo la clave 'ci_completo'
                    globalPersonas = data.personas.map(p => ({
                        ...p,
                        ci_completo: formatValue(p.carnet_identidad) + (formatValue(p.complemento) ? ` / ${p.complemento}` : '')
                    }));
                    filteredPersonas = [...globalPersonas];
                    
                    // Ordenar por CI/Compl. de forma inicial
                    sortColumn('ci_completo', 'asc', false); 

                    // Renderizar e iniciar interactividad
                    renderTable(filteredPersonas);
                    initTableInteractivity();
                } else {
                    throw new Error("Respuesta inesperada de la API");
                }

            } catch (error) {
                document.getElementById('errorMsg').textContent = error.message;
                errorBox.classList.remove('hidden');
            } finally {
                btnText.textContent = "BUSCAR REGISTROS";
                btnLoader.classList.add('hidden');
                btnIcon.classList.remove('hidden');
            }
        });

        // ===============================================
        // LÓGICA DE FILTRADO Y ORDENACIÓN
        // ===============================================

        function initTableInteractivity() {
            // Añadir eventos a los inputs de filtro
            document.querySelectorAll('.filter-input').forEach(input => {
                input.oninput = filterTable;
            });

            // Añadir eventos a los botones de ordenación
            document.querySelectorAll('.sort-btn').forEach(button => {
                button.onclick = function() {
                    const column = this.getAttribute('data-sort-by');
                    // Cambiar dirección: Si la columna es la misma, invertir; si es nueva, usar la dirección por defecto ('asc')
                    const direction = currentSort.column === column && currentSort.direction === 'asc' ? 'desc' : 'asc';
                    sortColumn(column, direction, true); 
                };
            });

            // Restablecer iconos de ordenación al estado inicial si la tabla se carga por primera vez
            updateSortIcons(currentSort.column, currentSort.direction);
        }

        function filterTable() {
            let tempPersonas = [...globalPersonas];
            const filters = {};

            // 1. Recolectar todos los filtros
            document.querySelectorAll('.filter-input').forEach(input => {
                const key = input.getAttribute('data-filter-by');
                const value = input.value.trim().toLowerCase();
                if (value) {
                    filters[key] = value;
                }
            });

            // 2. Aplicar filtros
            if (Object.keys(filters).length > 0) {
                tempPersonas = tempPersonas.filter(p => {
                    return Object.keys(filters).every(key => {
                        const cellValue = formatValue(p[key]).toLowerCase();
                        return cellValue.includes(filters[key]);
                    });
                });
            }

            filteredPersonas = tempPersonas;

            // 3. Re-ordenar por la columna actual
            sortArray(filteredPersonas, currentSort.column, currentSort.direction);

            // 4. Re-renderizar
            renderTable(filteredPersonas);
        }

        function sortColumn(column, direction, reRender = true) {
            // Actualizar el estado de ordenación
            currentSort = { column, direction };

            // 1. Ordenar el arreglo filtrado
            sortArray(filteredPersonas, column, direction);

            // 2. Actualizar la dirección en el botón
            document.querySelector(`.sort-btn[data-sort-by="${column}"]`).setAttribute('data-sort-dir', direction);

            // 3. Actualizar iconos
            updateSortIcons(column, direction);

            // 4. Re-renderizar
            if (reRender) {
                renderTable(filteredPersonas);
            }
        }

        function sortArray(arr, column, direction) {
            // Determina si la columna contiene valores que deberían ordenarse como números/fechas
            const isNumericOrDate = ['fecha_nacimiento', 'fecha_registro', 'carnet_identidad', 'ci_completo'].includes(column);
            
            arr.sort((a, b) => {
                let valA = formatValue(a[column]);
                let valB = formatValue(b[column]);
                
                // Conversión especial para ordenación numérica/de fecha
                if (isNumericOrDate) {
                    // Simplifica las fechas y CI para comparación.
                    valA = valA.replace(/[^0-9]/g, ''); 
                    valB = valB.replace(/[^0-9]/g, '');
                } else {
                    // Para texto, convierte a minúsculas
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }


                // Manejar valores nulos/vacíos. Los vacíos van al final en ASC y al principio en DESC.
                if (!valA && valB) return direction === 'asc' ? 1 : -1;
                if (valA && !valB) return direction === 'asc' ? -1 : 1;
                if (!valA && !valB) return 0;
                
                // Comparación estándar
                if (valA < valB) {
                    return direction === 'asc' ? -1 : 1;
                }
                if (valA > valB) {
                    return direction === 'asc' ? 1 : -1;
                }
                return 0;
            });
        }

        function updateSortIcons(column, direction) {
            document.querySelectorAll('.sort-btn').forEach(btn => {
                const isActive = btn.getAttribute('data-sort-by') === column;
                
                // Ocultar todos los iconos activos por defecto
                btn.querySelectorAll('.active-sort').forEach(icon => icon.classList.add('hidden'));
                btn.querySelector('.fa-sort').classList.remove('hidden'); // Mostrar el icono neutro

                if (isActive) {
                    btn.querySelector('.fa-sort').classList.add('hidden'); // Ocultar neutro
                    if (direction === 'asc') {
                        btn.querySelector('.fa-sort-up').classList.remove('hidden');
                    } else {
                        btn.querySelector('.fa-sort-down').classList.remove('hidden');
                    }
                }
            });
        }


        // ===============================================
        // FUNCIÓN PRINCIPAL DE RENDERIZADO
        // ===============================================

        async function getMesaId(ci,fecha_nacimiento){

            console.log(fecha_nacimiento)
            let payload = {
                numero_documento:ci+"",
                complemento: "",
                fecha_nacimiento: fecha_nacimiento // 22/03/2005
            }
            const response = await fetch('https://computo.oep.org.bo/api/v1/actas/voter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            let data = await response.json()
            console.log(data.type_actas[0].mesa_id)
            return data.type_actas[0].mesa_id
        }

        async function getImageBase64(mesaId){
            const response = await fetch('https://computo.oep.org.bo/api/v1/actas/'+mesaId, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' },
            });

            let data = await response.json()
            console.log(data.adjunto[0].valor)
            return data.adjunto[0].valor
        }
function openImageModal() {
    const modal = document.getElementById("imageModal");
    const content = document.getElementById("modalContent");

    modal.classList.remove("hidden");

    setTimeout(() => {
        modal.classList.remove("opacity-0");
        content.classList.remove("opacity-0", "scale-75");
        content.classList.add("scale-100");
    }, 10);
}

function closeImageModal() {
    const modal = document.getElementById("imageModal");
    const content = document.getElementById("modalContent");

    modal.classList.add("opacity-0");
    content.classList.add("opacity-0", "scale-75");

    setTimeout(() => {
        modal.classList.add("hidden");
    }, 300);
}

document.getElementById("closeModal").onclick = closeImageModal;
async function showModal(ci, fecha_nacimiento) {
    const [year, month, day] = fecha_nacimiento.split("-");
    const fecha_cambiada = `${day}/${month}/${year}`;

    openImageModal();

    const loader = document.getElementById("loader");
    const img = document.getElementById("modalImage");

    loader.classList.remove("hidden");
    img.classList.add("hidden");
    img.src = "";

    const mesaId = await getMesaId(ci + "", fecha_cambiada);
    const imageBase64 = await getImageBase64(mesaId);

    img.src = "data:image/png;base64," + imageBase64;

    setTimeout(() => {
        loader.classList.add("hidden");
        img.classList.remove("hidden");
    }, 300);
}



        function renderTable(personas) {
            const tbodyDesktop = document.getElementById('tableBodyDesktop');
            const tbodyMobile = document.getElementById('tableBodyMobile');
            const section = document.getElementById('resultsSection');
            const empty = document.getElementById('emptyState');
            const badge = document.getElementById('countBadge');
            const tableContainer = document.getElementById('tableContainer');
            
            tbodyDesktop.innerHTML = '';
            tbodyMobile.innerHTML = '';
            badge.innerText = `${personas.length} registros`;

            section.classList.remove('hidden');
            setTimeout(() => section.classList.add('opacity-100'), 50);

            if (personas.length === 0) {
                tableContainer.classList.add('hidden');
                tbodyMobile.classList.add('hidden');
                empty.classList.remove('hidden');
                return;
            }

            tableContainer.classList.remove('hidden');
            tbodyMobile.classList.remove('hidden');
            empty.classList.add('hidden');

            const createGeneroColor = (p) => p.genero && p.genero.toUpperCase() === 'MASCULINO' ? 'text-blue-400' : 'text-pink-400';


            personas.forEach(p => {
                const ciCompleto = p.ci_completo; 
                const generoColor = createGeneroColor(p);
                const nombreCompleto = `${formatValue(p.nombre)} ${formatValue(p.paterno)} ${formatValue(p.materno)}`.trim();
                
                
                // 1. RENDERIZADO PARA ESCRITORIO (TRADICIONAL)
                const tr = document.createElement('tr');
                tr.className = "hover:bg-white/5 transition-colors group cursor-default";

                tr.innerHTML = `
                    <td class="p-4 font-bold text-neonBlue text-xs">
                        <button onclick="showModal(${ciCompleto}, '${p.fecha_nacimiento}')" 
                                class="flex items-center gap-2 hover:text-blue-500 transition">
                            
                            <!-- Ícono de Ojo -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                                    4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            Ver
                        </button>
                    </td>
                    
                    <td class="p-4 font-bold text-neonBlue text-xs">${ciCompleto}</td>
                    <td class="p-4 font-semibold text-white">${formatValue(p.paterno)}</td>
                    <td class="p-4 font-semibold text-white">${formatValue(p.materno)}</td>
                    <td class="p-4 text-gray-300">${formatValue(p.nombre)}</td>
                    <td class="p-4 ${generoColor} font-bold text-xs uppercase tracking-wide">${formatValue(p.genero)}</td>
                    <td class="p-4 text-gray-400 text-xs">${formatValue(p.fecha_nacimiento)}</td>
                    <td class="p-4 text-gray-400 text-xs hidden xl:table-cell">${formatValue(p.estado_civil)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden 2xl:table-cell">${formatValue(p.codigo_rude)}</td>
                    <td class="p-4 text-gray-500 text-xs">${formatValue(p.nacimiento_localidad)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden lg:table-cell">${formatValue(p.comunidad)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden lg:table-cell">${formatValue(p.municipio)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden lg:table-cell">${formatValue(p.provincia)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden xl:table-cell">${formatValue(p.departamento)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden xl:table-cell">${formatValue(p.pais)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden 2xl:table-cell">${formatValue(p.telefono)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden 2xl:table-cell">${formatValue(p.email)}</td>
                    <td class="p-4 text-gray-500 text-xs hidden 2xl:table-cell">${formatDateTime(p.fecha_registro)}</td>
                `;
                tbodyDesktop.appendChild(tr);


                // 2. RENDERIZADO PARA MÓVIL (TARJETA)
                const card = document.createElement('div');
                card.className = "bg-white/5 rounded-xl p-4 border border-white/10 space-y-2 hover:border-neonBlue/50 transition-colors";
                
                card.innerHTML = `
                    <div class="flex justify-between items-start border-b border-white/5 pb-2 mb-2">
                        <div class="text-lg font-extrabold text-neonBlue">${ciCompleto}</div>
                        <div class="text-xs font-bold uppercase p-1 rounded-full ${p.genero && p.genero.toUpperCase() === 'MASCULINO' ? 'bg-blue-900/50 text-blue-300' : 'bg-pink-900/50 text-pink-300'}">${formatValue(p.genero)}</div>
                    </div>
                    
                    <p class="text-white font-semibold text-sm">${nombreCompleto}</p>
                    
                    <div class="grid grid-cols-2 gap-y-1 text-xs">
                        <div class="text-gray-500">F. Nacimiento:</div>
                        <div class="text-gray-300">${formatValue(p.fecha_nacimiento)}</div>
                        
                        <div class="text-gray-500">Localidad Nac.:</div>
                        <div class="text-gray-300">${formatValue(p.nacimiento_localidad)}</div>
                        
                        <div class="text-gray-500">Municipio:</div>
                        <div class="text-gray-300">${formatValue(p.municipio)}</div>
                        
                        <div class="text-gray-500">Provincia:</div>
                        <div class="text-gray-300">${formatValue(p.provincia)}</div>

                        <div class="text-gray-500">Dpto/País:</div>
                        <div class="text-gray-300">${formatValue(p.departamento) || ''} / ${formatValue(p.pais) || ''}</div>
                        
                        <div class="text-gray-500">Est. Civil:</div>
                        <div class="text-gray-300">${formatValue(p.estado_civil)}</div>
                        
                        <div class="text-gray-500">Teléfono:</div>
                        <div class="text-gray-300">${formatValue(p.telefono)}</div>
                        
                        <div class="text-gray-500">Email:</div>
                        <div class="text-gray-300">${formatValue(p.email)}</div>
                        
                        <div class="text-gray-500">RUDE:</div>
                        <div class="text-gray-300">${formatValue(p.codigo_rude)}</div>
                        
                        <div class="text-gray-500">F. Registro:</div>
                        <div class="text-gray-300">${formatDateTime(p.fecha_registro)}</div>
                    </div>
                `;
                tbodyMobile.appendChild(card);
            });
        }
    </script>
</body>
</html>