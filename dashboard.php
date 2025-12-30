<?php
require_once 'config/db.php';
session_start();

// Proteção de Acesso
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$uid = $_SESSION['user_id'];

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete_id'])) {
    $id_del = intval($_GET['delete_id']);
    // Garante que o usuário só delete o que é dele
    $conn->query("DELETE FROM products WHERE id = $id_del AND user_id = $uid");
    header("Location: dashboard.php?msg=Produto removido");
    exit();
}

// --- BUSCA DE MÉTRICAS GLOBAIS ---
// Investimento e Total de Itens (Valor em estoque hoje)
$est = $conn->query("SELECT SUM(stock * buy_price) as investimento_estoque, SUM(stock) as total_itens FROM products WHERE user_id = $uid")->fetch_assoc();

// Faturamento e Lucro Real (Baseado em vendas efetivas)
$vnd = $conn->query("SELECT SUM(sale_price * quantity) as faturamento, SUM(total_profit) as lucro_real FROM sales WHERE user_id = $uid")->fetch_assoc();

$faturamento = $vnd['faturamento'] ?? 0;
$lucro_real = $vnd['lucro_real'] ?? 0;
$investimento_total = $est['investimento_estoque'] ?? 0;
$itens_estoque = $est['total_itens'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MicroERP | Gestão Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .card-premium { transition: all 0.3s ease; }
        .card-premium:hover { transform: translateY(-5px); border-color: #6366f1; }
    </style>
</head>
<body class="pb-10">

    <nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 md:px-8 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        
        <div class="flex items-center gap-8">
            <a href="dashboard.php" class="group flex items-center gap-3">
                <div class=" group-hover:scale-105 transition-transform duration-300">
                    <img src="./logo.png" class="w-[170px]" alt="">
                </div>
                <div class="hidden xs:block">
                    <span class="block font-extrabold text-slate-900 tracking-tight leading-none text-lg">Micro<span class="text-blue-600">ERP</span></span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1 font-medium text-sm">
                <a href="index.php" class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl transition-all">
                    <ion-icon name="home"></ion-icon>
                    <span>Home</span>
                </a>
                
                <a href="produtos_acao.php" class="flex items-center gap-2 px-4 py-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-xl transition-all">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <span>Novo Produto</span>
                </a>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-6">
            
            <div class="flex items-center gap-3 pl-4 border-l border-slate-100">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-800 leading-tight">
                        <?= explode(' ', $_SESSION['user_name'])[0] ?>
                    </p>
                    <div class="flex items-center justify-end gap-1 mt-0.5">
                         <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                         <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Online</span>
                    </div>
                </div>
                
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 border-2 border-white shadow-md flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                </div>
            </div>

            <div class="h-8 w-[1px] bg-slate-100 mx-1 hidden sm:block"></div>
            
            <a href="logout.php" 
               class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group">
                <ion-icon name="log-out-outline" class="text-xl group-hover:translate-x-1 transition-transform"></ion-icon>
            </a>
        </div>
    </div>

    <div class="md:hidden flex items-center justify-center gap-4 mt-3 pt-3 border-t border-slate-100">
        <a href="dashboard.php" class="text-blue-600 font-bold text-xs flex items-center gap-1">
            <ion-icon name="home"></ion-icon> Home
        </a>
        <a href="produtos_acao.php" class="text-slate-400 font-bold text-xs flex items-center gap-1">
            <ion-icon name="add-circle-outline"></ion-icon> Produto
        </a>
    </div>
</nav>

    <main class="max-w-6xl mx-auto p-4 md:p-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
            <a href="vendas.php" class="flex items-center justify-between p-6 bg-indigo-600 rounded-[8px] shadow-xl shadow-indigo-100 group transition-all">
                <div class="flex items-center gap-4 text-white">
                    <div class="w-14 h-14 bg-white/20 rounded-[2xl] flex items-center justify-center text-3xl">
                        <ion-icon name="cart-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold italic tracking-tight">Nova Venda</h3>
                        <p class="text-indigo-200 text-xs">Registrar saída e lucrar</p>
                    </div>
                </div>
                <ion-icon name="chevron-forward" class="text-white text-2xl group-hover:translate-x-2 transition-transform"></ion-icon>
            </a>

            <a href="produtos_acao.php" class="flex items-center justify-between p-6 bg-white border-2 border-dashed border-indigo-200 rounded-[8px] hover:bg-indigo-50 hover:border-indigo-400 transition-all group">
                <div class="flex items-center gap-4 text-indigo-600">
                    <div class="w-14 h-14 bg-indigo-600 rounded-[2xl] flex items-center justify-center text-3xl text-white">
                        <ion-icon name="add-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-slate-800">Novo Produto</h3>
                        <p class="text-slate-400 text-xs">Abastecer seu estoque agora</p>
                    </div>
                </div>
                <ion-icon name="cube-outline" class="text-slate-200 text-4xl"></ion-icon>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-[8px] border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Investimento (Estoque)</p>
                <h2 class="text-2xl font-bold text-amber-600"><?= number_format($investimento_total, 0, ',', '.') ?> MT</h2>
            </div>

            <div class="bg-white p-6 rounded-[8px] border border-slate-100 shadow-sm overflow-hidden relative">
                <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Lucro Acumulado</p>
                <h2 class="text-2xl font-bold text-emerald-600"><?= number_format($lucro_real, 0, ',', '.') ?> MT</h2>
                <div class="absolute -right-2 -bottom-2 text-6xl text-emerald-50 opacity-50"><ion-icon name="cash"></ion-icon></div>
            </div>

            <div class="bg-white p-6 rounded-[8px] border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Faturamento Total</p>
                <h2 class="text-2xl font-bold text-slate-800"><?= number_format($faturamento, 0, ',', '.') ?> MT</h2>
            </div>

            <div class="bg-white p-6 rounded-[8px] border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-[10px] font-bold uppercase mb-1">Peças em Estoque</p>
                <h2 class="text-2xl font-bold text-indigo-600"><?= $itens_estoque ?> <span class="text-sm font-normal text-slate-300 italic">unid.</span></h2>
            </div>
        </div>

        <div class="flex items-center justify-between px-2 mb-6">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Gerenciar Inventário</h3>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest italic">Acompanhe seu rendimento por item</p>
            </div>
            <button onclick="window.print()" class="text-slate-400 text-2xl"><ion-icon name="print-outline"></ion-icon></button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // Query com contagem de unidades vendidas por produto
            $query = "SELECT p.*, (SELECT SUM(quantity) FROM sales WHERE product_id = p.id) as vendidos FROM products p WHERE p.user_id = $uid ORDER BY p.stock ASC";
            $res = $conn->query($query);
            
            while($p = $res->fetch_assoc()):
                $lucro_un = $p['sell_price'] - $p['buy_price'];
                $vendidos = $p['vendidos'] ?? 0;
                $alerta_estoque = ($p['stock'] <= 3);
            ?>
            <div class="card-premium bg-white p-6 rounded-[8px] border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
                
                <?php if($alerta_estoque): ?>
                    <div class="absolute top-0 right-0 bg-red-500 text-white text-[8px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-widest">Estoque Crítico</div>
                <?php endif; ?>

                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="max-w-[70%]">
                            <h4 class="font-bold text-slate-800 text-lg leading-tight mb-1"><?= $p['name'] ?></h4>
                            <span class="text-[10px] bg-slate-50 text-slate-400 px-2 py-1 rounded-full font-bold">CUSTO: <?= number_format($p['buy_price'], 0, ',', '.') ?> MT</span>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Restam</p>
                            <p class="text-xl font-bold <?= $alerta_estoque ? 'text-red-500' : 'text-slate-700' ?>"><?= $p['stock'] ?></p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-3xl p-4 flex justify-between items-center mb-6">
                        <div>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">Preço Venda</p>
                            <p class="text-emerald-600 font-bold"><?= number_format($p['sell_price'], 0, ',', '.') ?> MT</p>
                        </div>
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="text-right">
                            <p class="text-[9px] text-indigo-400 font-bold uppercase italic">Lucro/Un</p>
                            <p class="text-indigo-600 font-bold">+ <?= number_format($lucro_un, 0, ',', '.') ?> MT</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1 uppercase tracking-tighter">
                            <ion-icon name="bag-handle" class="text-indigo-500"></ion-icon> <?= $vendidos ?> un vendidas
                        </span>
                        <a href="produto_log.php?id=<?= $p['id'] ?>" class="text-[10px] text-indigo-500 font-bold underline mt-1">Ver extrato de vendas</a>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="produtos_acao.php?edit=<?= $p['id'] ?>" class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                        </a>
                        <a href="?delete_id=<?= $p['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir permanentemente este produto do sistema?')" class="w-10 h-10 rounded-2xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                            <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; if($res->num_rows == 0): ?>
            <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                <ion-icon name="file-tray-outline" class="text-6xl text-slate-200 mb-4"></ion-icon>
                <p class="text-slate-400 font-medium">Você ainda não possui produtos cadastrados.</p>
                <a href="produtos_acao.php" class="text-indigo-600 font-bold underline mt-2 inline-block">Comece agora por aqui</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="mt-10 px-6 text-center">
        <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest">&copy; <?= date('Y') ?> MicroERP - Seu negócio na palma da mão</p>
    </footer>

</body>
</html>