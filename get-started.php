<?php
session_start();
// Verifica se o usuário está logado para alternar os botões do Header
$is_logged = isset($_SESSION['user_id']);
$user_name = $is_logged ? explode(' ', $_SESSION['user_name'])[0] : "";
?>
<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MicroERP - Gestão Inteligente para Revendedores</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .blue-gradient { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        .glass { background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        
        .feature-card:hover { transform: translateY(-10px); }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased">

    <nav class="fixed w-full z-50 glass border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="./logo.png" class="w-[130px]" alt="">
            </div>
            
            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                <a href="#features" class="hover:text-blue-600 transition">Funcionalidades</a>
                <a href="#testimonials" class="hover:text-blue-600 transition">Depoimentos</a>
                
                <?php if($is_logged): ?>
                    <a href="dashboard.php" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition flex items-center gap-2">
                        <ion-icon name="grid-outline"></ion-icon> Painel de <?= $user_name ?>
                    </a>
                <?php else: ?>
                    <a href="auth.php" class="text-slate-700 hover:text-blue-600 transition font-semibold">Entrar</a>
                    <a href="auth.php" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Começar Grátis</a>
                <?php endif; ?>
            </div>

            <button class="md:hidden text-3xl text-slate-900" onclick="toggleMenu()">
                <ion-icon id="menu-icon" name="menu-outline"></ion-icon>
            </button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-100 px-6 py-6 space-y-4 shadow-2xl absolute w-full left-0">
            <a href="#features" onclick="toggleMenu()" class="block font-medium text-slate-600 text-lg">Funcionalidades</a>
            <a href="#testimonials" onclick="toggleMenu()" class="block font-medium text-slate-600 text-lg">Depoimentos</a>
            <div class="pt-4 flex flex-col gap-3">
                <?php if($is_logged): ?>
                    <a href="dashboard.php" class="w-full bg-blue-600 text-white text-center py-4 rounded-2xl font-bold">Ir para o Painel</a>
                <?php else: ?>
                    <a href="auth.php" class="w-full text-center py-4 font-bold text-slate-700 border border-slate-200 rounded-2xl">Entrar</a>
                    <a href="auth.php" class="w-full bg-blue-600 text-white text-center py-4 rounded-2xl font-bold shadow-lg shadow-blue-100">Criar Conta Grátis</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="pt-32 pb-20 lg:pt-52 lg:pb-32 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto lg:flex items-center gap-16">
            <div class="lg:w-1/2 text-center lg:text-left">
                <span class="inline-block py-2 px-4 rounded-full bg-blue-50 text-blue-600 text-xs font-bold mb-6 tracking-widest uppercase">
                    Simplificando sua Revenda
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 leading-[1.15] mb-6">
                    Gestão Inteligente, <span class="text-blue-600 italic">Resultados</span> Reais.
                </h1>
                <p class="text-lg text-slate-500 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Controle produtos, vendas e clientes em um só lugar. O micro-ERP feito para quem não quer perder tempo com planilhas complicadas.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="auth.php" class="blue-gradient text-white px-10 py-5 rounded-2xl font-bold text-lg shadow-2xl shadow-blue-300 hover:scale-105 transition-all flex items-center justify-center gap-3">
                        Começar Grátis <ion-icon name="rocket-outline" class="text-2xl"></ion-icon>
                    </a>
                </div>
                <div class="mt-8 flex items-center justify-center lg:justify-start gap-4 text-slate-400 text-sm">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-blue-200 border-2 border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-slate-300 border-2 border-white"></div>
                    </div>
                    <p>+500 revendedores ativos hoje</p>
                </div>
            </div>

            <div class="w-full lg:w-1/2 mt-10 lg:mt-0 relative animate-float px-4 sm:px-0">
                <div class="bg-slate-900 rounded-[2rem] sm:rounded-[2.5rem] p-3 sm:p-4 shadow-3xl border border-slate-800">
                    
                    <div class="bg-slate-800/50  rounded-2xl p-4 sm:p-6 border border-slate-700/50">
                        
                        <div class="flex justify-between items-center mb-6 sm:mb-8">
                            <div class="flex gap-1.5 sm:gap-2">
                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-red-500/80"></div>
                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-yellow-500/80"></div>
                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-green-500/80"></div>
                            </div>
                            <div class="h-1.5 sm:h-2 w-16 sm:w-24 bg-slate-700 rounded-full"></div>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-4 rounded-2xl text-white shadow-lg shadow-blue-900/20">
                                    <p class="text-[9px] sm:text-[10px] uppercase font-bold opacity-80 tracking-widest">Vendas / Mês</p>
                                    <p class="text-lg sm:text-xl font-bold mt-1">8.420,00 MT</p>
                                </div>
                                
                                <div class="bg-slate-700/40 p-4 rounded-2xl text-slate-200 border border-slate-600/50 backdrop-blur-md">
                                    <p class="text-[9px] sm:text-[10px] uppercase font-bold opacity-70 tracking-widest text-slate-400">Estoque</p>
                                    <p class="text-lg sm:text-xl font-bold mt-1">142 itens</p>
                                </div>
                            </div>

                            <div class="bg-slate-900/50 h-28 sm:h-32 rounded-2xl border border-slate-700 border-dashed flex flex-col items-center justify-center gap-2 transition-all hover:bg-slate-900/80 group">
                                <ion-icon name="stats-chart" class="text-slate-600 text-3xl sm:text-4xl group-hover:text-blue-500 transition-colors"></ion-icon>
                                <span class="text-slate-500 text-xs font-medium">Relatório em tempo real</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Poderoso por dentro, simples por fora</h2>
                <p class="text-slate-500 italic">Tudo o que você precisa para escalar sua revenda sem dor de cabeça.</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm feature-card transition-all border border-slate-100 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <ion-icon name="cube-outline"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-slate-900">Gestão de Produtos</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Controle total do seu estoque. Saiba exatamente quando repor seus itens mais vendidos.</p>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm feature-card transition-all border border-slate-100 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <ion-icon name="cash-outline"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-slate-900">Vendas e Lucros</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Registre vendas em segundos e visualize seu lucro líquido de forma automática.</p>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm feature-card transition-all border border-slate-100 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-slate-900">Base de Clientes</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Histórico completo de compras por cliente. Fidelize quem compra com você.</p>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm feature-card transition-all border border-slate-100 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <ion-icon name="phone-portrait-outline"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-slate-900">100% Responsivo</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Use no celular, tablet ou computador. Seu negócio sempre com você.</p>
                </div>
            </div>
        </div>
    </section>

<section id="testimonials" class="py-16 md:py-24 bg-slate-50/50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="max-w-xl mb-12">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100">
                <ion-icon name="people"></ion-icon>
                <span>Comunidade MicroERP</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 tracking-tight">Histórias de quem cresce com a gente</h2>
            <p class="text-slate-500 text-lg">Junte-se a mais de 500 revendedores que profissionalizaram a sua gestão.</p>
        </div>

        <div class="relative px-2">
            
            <button onclick="scrollTestimonials('left')" 
                class="absolute -left-2 md:-left-5 top-1/2 -translate-y-1/2 z-30 flex items-center justify-center w-10 h-10 md:w-14 md:h-14 rounded-full bg-white text-blue-600 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-slate-100 hover:bg-blue-600 hover:text-white transition-all active:scale-90"
                aria-label="Anterior">
                <ion-icon name="chevron-back" class="text-xl md:text-2xl"></ion-icon>
            </button>

            <button onclick="scrollTestimonials('right')" 
                class="absolute -right-2 md:-right-5 top-1/2 -translate-y-1/2 z-30 flex items-center justify-center w-10 h-10 md:w-14 md:h-14 rounded-full bg-white text-blue-600 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-slate-100 hover:bg-blue-600 hover:text-white transition-all active:scale-90"
                aria-label="Próximo">
                <ion-icon name="chevron-forward" class="text-xl md:text-2xl"></ion-icon>
            </button>

            <div id="testimonial-container" class="flex overflow-x-auto gap-4 md:gap-6 pb-10 snap-x snap-mandatory scroll-smooth no-scrollbar">
                
                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"Antes eu usava um caderno e sempre esquecia quem me devia. Agora o MicroERP avisa-me tudo pelo WhatsApp. É impecável!"</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">SM</div>
                        <div><p class="font-bold text-slate-900 text-sm">Sérgio Matusse</p><p class="text-[10px] text-slate-400 uppercase font-bold">Revenda de Gadgets</p></div>
                    </div>
                </div>

                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"Consegui organizar o meu stock de capulanas em minutos. O sistema é muito leve e não gasta quase nada de dados."</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold">FL</div>
                        <div><p class="font-bold text-slate-900 text-sm">Fátima Langa</p><p class="text-[10px] text-slate-400 uppercase font-bold">Moda Africana</p></div>
                    </div>
                </div>

                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"O melhor é ver o lucro real em Meticais ao fim do mês. Ajudou-me a entender onde eu estava a perder dinheiro."</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">AC</div>
                        <div><p class="font-bold text-slate-900 text-sm">Américo Chirinda</p><p class="text-[10px] text-slate-400 uppercase font-bold">Venda de Peças</p></div>
                    </div>
                </div>

                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"Uso para gerir as encomendas da minha loja online. Os meus clientes sentem mais confiança quando envio o recibo digital."</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center font-bold">TM</div>
                        <div><p class="font-bold text-slate-900 text-sm">Tânia Mucavele</p><p class="text-[10px] text-slate-400 uppercase font-bold">Beauty Shop</p></div>
                    </div>
                </div>

                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star-outline"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"Excelente suporte técnico. Tive uma dúvida sobre as faturas e resolveram em minutos pelo chat."</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold">EM</div>
                        <div><p class="font-bold text-slate-900 text-sm">Edson Mondlane</p><p class="text-[10px] text-slate-400 uppercase font-bold">Importação</p></div>
                    </div>
                </div>

                <div class="min-w-[88%] md:min-w-[46%] lg:min-w-[31.5%] snap-center p-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex text-yellow-400 mb-5 text-sm"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                        <p class="text-slate-700 font-medium leading-relaxed italic">"O controlo de entradas e saídas é muito intuitivo. Recomendo a qualquer jovem que esteja a começar o seu negócio."</p>
                    </div>
                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-50">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold">BM</div>
                        <div><p class="font-bold text-slate-900 text-sm">Beatriz Mabunda</p><p class="text-[10px] text-slate-400 uppercase font-bold">Acessórios</p></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
    /* Ocultar barra de rolagem mas manter funcionalidade */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    function scrollTestimonials(direction) {
        const container = document.getElementById('testimonial-container');
        // Calcula a largura de um card dinamicamente para o scroll
        const cardWidth = container.querySelector('div').offsetWidth + 24; 
        
        if (direction === 'left') {
            container.scrollBy({ left: -cardWidth, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: cardWidth, behavior: 'smooth' });
        }
    }
</script>

    <section class="py-12 md:py-24 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto blue-gradient rounded-[2.5rem] md:rounded-[4rem] p-8 md:p-20 text-center text-white shadow-3xl shadow-blue-500/20 relative overflow-hidden">
            
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col items-center">
                
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl mb-6 animate-bounce">
                    <ion-icon name="rocket-outline" class="text-3xl md:text-4xl text-blue-200"></ion-icon>
                </div>

                <h2 class="text-2xl sm:text-4xl md:text-6xl font-black mb-6 leading-[1.1] tracking-tight">
                    Chega de planilhas. <br class="hidden sm:block"> 
                    <span class="text-blue-200">Comece a lucrar agora.</span>
                </h2>

                <p class="text-blue-50 text-base md:text-xl mb-10 max-w-2xl mx-auto opacity-90 font-medium leading-relaxed px-2">
                    Junte-se a centenas de revendedores e tenha o controle total do seu negócio na palma da mão, onde quer que você esteja.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="auth.php" class="w-full sm:w-auto bg-white text-blue-600 px-8 md:px-12 py-4 md:py-5 rounded-2xl font-bold text-lg md:text-xl hover:bg-blue-50 transition-all shadow-2xl flex items-center justify-center gap-3 active:scale-95">
                        Criar meu acesso gratuito 
                        <ion-icon name="chevron-forward-circle" class="text-2xl"></ion-icon>
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-2 md:grid-cols-3 gap-6 opacity-70 border-t border-white/10 pt-10 w-full">
                    <div class="flex items-center justify-center gap-2">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        <span class="text-xs md:text-sm font-medium">100% Seguro</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <ion-icon name="flash-outline"></ion-icon>
                        <span class="text-xs md:text-sm font-medium">Acesso Instantâneo</span>
                    </div>
                    <div class="hidden md:flex items-center justify-center gap-2">
                        <ion-icon name="people-outline"></ion-icon>
                        <span class="text-xs md:text-sm font-medium">+500 Revendedores</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div class="space-y-6">
                    <div class="flex items-center gap-2 text-white">
                        <img src="./logo.png" class="w-[130px]" alt="">
                    </div>
                    <p class="text-sm leading-relaxed">A plataforma mais intuitiva para pequenos revendedores gerenciarem estoque e vendas com precisão e facilidade.</p>
                    <div class="flex gap-4 text-xl">
                        <a href="#" class="hover:text-blue-500 transition"><ion-icon name="logo-instagram"></ion-icon></a>
                        <a href="#" class="hover:text-blue-500 transition"><ion-icon name="logo-whatsapp"></ion-icon></a>
                        <a href="#" class="hover:text-blue-500 transition"><ion-icon name="logo-linkedin"></ion-icon></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Produto</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#features" class="hover:text-white transition">Funcionalidades</a></li>
                        <li><a href="#" class="hover:text-white transition">Preços e Planos</a></li>
                        <li><a href="#" class="hover:text-white transition">Segurança</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Suporte</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#" class="hover:text-white transition">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-white transition">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-white transition">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Fique por dentro</h4>
                    <p class="text-xs mb-4">Receba novidades e dicas de gestão no seu e-mail.</p>
                    <form class="flex gap-2">
                        <input type="email" placeholder="Seu e-mail" class="bg-slate-800 border-none rounded-xl px-4 py-3 text-xs w-full focus:ring-2 focus:ring-blue-600 transition">
                        <button class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition font-bold text-xs uppercase">OK</button>
                    </form>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] uppercase tracking-[0.2em]">
                <p>&copy; 2025 MicroERP Software. Todos os direitos reservados.</p>
                <div class="flex items-center gap-1">
                    Desenvolvido com <ion-icon name="heart" class="text-red-500 text-sm"></ion-icon> para revendedores.
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            const isHidden = menu.classList.contains('hidden');
            
            if (isHidden) {
                menu.classList.remove('hidden');
                icon.setAttribute('name', 'close-outline');
            } else {
                menu.classList.add('hidden');
                icon.setAttribute('name', 'menu-outline');
            }
        }
    </script>
</body>
</html>